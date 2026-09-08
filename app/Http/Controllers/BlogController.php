<?php

namespace App\Http\Controllers;
use App\Actions\SlugChecker;

use App\Author;
use App\Blog;
use App\BlogCategory;

use App\Events;
use App\Helpers\SanitizeInput;
use App\Http\Requests\SlugCheckRequest;
use App\Language;
use App\Page;
use App\Services;
use App\ServiceCategory;
use App\WorksCategory;
use App\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Jobs\CheckBrokenLinksJob;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    

    public function index(){
        $all_blog = Blog::all()->groupBy('lang');
         $categories = BlogCategory::where('lang',get_default_language())->get();
        return view('backend.pages.blog.index')->with([
            'all_blog' => $all_blog,
            'categories' =>$categories,
        ]);
    }
    // AJAX DataTables

    public function new_blog(){
        $all_category = BlogCategory::where('lang',get_default_language())->get();
        $work_category = ServiceCategory::where(['status'=> 'publish','lang' => get_default_language()])->get();
        $all_author = Author::where('lang',get_default_language())->get();
        $all_language = Language::all();
        return view('backend.pages.blog.new')->with([
            'all_category' => $all_category,
            'all_author' => $all_author,
            'all_languages' => $all_language,
            'works_category' => $work_category,
        ]);
    }
    public function store_new_blog(Request $request){
     //   dd($request->faqs);
        $this->validate($request,[
           'category' => 'required',
           'blog_content' => 'required',
        //   'tags' => 'required',
           'excerpt' => 'required',
           'title' => 'required',
           'lang' => 'required',
           'status' => 'required',
           'author' => 'required',
           'slug' => 'nullable',
            'categories_id' => 'required',
           'video_url' => 'nullable|string',
           'breaking_news' => 'nullable|string',
           'meta_tags' => 'nullable|string',
           'meta_description' => 'nullable|string',
           'image' => 'nullable|string|max:191',
           'publish_date' => 'required',
        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
            // ✅ Clean FAQ data
            $faqs = [];
            if ($request->has('faqs')) {
            $faqs = collect($request->faqs)
            ->filter(function ($faq) {
            return !empty($faq['question']) && !empty($faq['answer']);
            })
            ->values()
            ->toArray();
            }
            
        Blog::create([
            'blog_categories_id' => $request->category,
            'slug' => $slug ,
            'content' => $request->blog_content,
            'faqs' => !empty($faqs) ? $faqs : null,
            'tags' => $request->tags,
            'title' => $request->title,
            'status' => $request->status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'excerpt' => $request->excerpt,
            'lang' => $request->lang,
            'image' => $request->image,
            'user_id' => Auth::user()->id,
            'author_id' => $request->author,
            'video_url' => $request->video_url,
            'breaking_news' => !empty($request->breaking_news) ? 1 : 0,
            'publish_date' => $request->publish_date,
            'total_visitors' => $request->total_visitors? 1 : 0,
            'categories_id' => serialize($request->categories_id),
        ]);
        return redirect()->back()->with([
            'msg' => __('New Blog Post Added...'),
            'type' => 'success'
        ]);
    }
    public function clone_blog(Request $request)
    {
        $blog_details = Blog::find($request->item_id);
        Blog::create([
            'blog_categories_id' => $blog_details->blog_categories_id,
            'slug' => $blog_details->slug.'33',
            'content' => $blog_details->content,
            'faq_content' => $blog_details->faq_content,
            'tags' => $blog_details->tags,
            'title' => $blog_details->title,
            'status' => 'draft',
            'meta_tags' => $blog_details->meta_tags,
            'meta_description' => $blog_details->meta_description,
            'schema_code' => $request->schema_code,
            'excerpt' => $blog_details->excerpt,
            'lang' => $blog_details->lang,
            'image' => $blog_details->image,
            'video_url' => $blog_details->video_url,
            'user_id' => null,
            'author_id' => $blog_details->author,
            'breaking_news' => $blog_details->breaking_news,
            'publish_date' => $request->publish_date,
            'total_visitors' => $request->total_visitors,
        ]);

        return redirect()->back()->with([
            'msg' => __('Blog Post cloned success...'),
            'type' => 'success'
        ]);
    }

    public function edit_blog($id){
        $blog_post = Blog::find($id);
        $all_category = BlogCategory::where('lang',$blog_post->lang)->get();
        
        $all_author = Author::where('lang',$blog_post->lang)->get();
        $all_industrycategory = ServiceCategory::where('lang',$blog_post->lang)->get();
       // dd($all_industrycategory);
        $all_language = Language::all();
        return view('backend.pages.blog.edit')->with([
            'all_category' => $all_category,
            'all_author' => $all_author,
            'blog_post' => $blog_post,
            'all_languages' => $all_language,
            'all_industrycategory' => $all_industrycategory,
        ]);
    }
    public function update_blog(Request $request,$id){
        $this->validate($request,[
            'category' => 'required',
            'blog_content' => 'required',
            'categories_id' => 'required',
            // 'tags' => 'required',
            'excerpt' => 'required',
            'title' => 'required',
            'lang' => 'required',
            'status' => 'required',
            'author' => 'required',
            'slug' => 'nullable',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'image' => 'nullable|string|max:191',
            'publish_date' => 'required',

        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
        $faqs = [];

if ($request->has('faqs')) {
    $faqs = collect($request->faqs)
        ->filter(fn($faq) => !empty($faq['question']) && !empty($faq['answer']))
        ->values()
        ->toArray();
}

        Blog::where('id',$id)->update([
            'blog_categories_id' => $request->category,
            'slug' => $slug,
            'content' => $request->blog_content,
            'faqs' => !empty($faqs) ? $faqs : null,
            'tags' => $request->tags,
            'title' => $request->title,
            'status' => $request->status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'excerpt' => $request->excerpt,
            'lang' => $request->lang,
            'video_url' => $request->video_url,
            'image' => $request->image,
            'user_id' => Auth::user()->id,
            'author_id' => $request->author,
            'breaking_news' => !empty($request->breaking_news) ? 1 : 0,
            'publish_date' => $request->publish_date,
            'total_visitors' => $request->total_visitors,
            'categories_id' => serialize($request->categories_id),
        ]);

        return redirect()->back()->with([
            'msg' => __('Blog Post updated...'),
            'type' => 'success'
        ]);
    }
    public function delete_blog(Request $request,$id){
        Blog::find($id)->delete();

        return redirect()->back()->with([
            'msg' => __('Blog Post Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function category(){
        $all_category = BlogCategory::all()->groupBy('lang');
        $all_language = Language::all(); 
        return view('backend.pages.blog.category')->with([
            'all_category' => $all_category,
            'all_languages' => $all_language
        ]);
    }
    public function new_category(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191|unique:blog_categories',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191',
            'image' => 'nullable|string|max:191'
        ]); 
        //print_r($request->all());die;
        BlogCategory::create($request->all());

        return redirect()->back()->with([
            'msg' => __('New Category Added...'),
            'type' => 'success'
        ]);
    }

    public function update_category(Request $request){
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191',
            'image' => 'nullable|string|max:191'
        ]);

        BlogCategory::find($request->id)->update([
            'name' => $request->name,
            'status' => $request->status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'lang' => $request->lang,
            'image' => $request->image,
        ]);

        return redirect()->back()->with([
            'msg' => __('Category Update Success...'),
            'type' => 'success'
        ]);
    }

    public function delete_category(Request $request,$id){
        if (Blog::where('blog_categories_id',$id)->first()){
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Post...'),
                'type' => 'danger'
            ]);
        }
        BlogCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Category Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function Language_by_slug(Request $request){
        $all_category = BlogCategory::where('lang',$request->lang)->get();

        return response()->json($all_category);
    }

    public function blog_page_settings(){
        $all_languages = Language::all();
        return view('backend.pages.blog.page-settings.blog')->with(['all_languages' => $all_languages]);
    }
    public function blog_single_page_settings(){
        $all_languages = Language::all();
        return view('backend.pages.blog.page-settings.blog-single')->with(['all_languages' => $all_languages]);
    }

    public function update_blog_single_page_settings(Request $request){
        $this->validate($request,[
            'blog_single_page_recent_post_item' => 'nullable|string|max:191'
        ]);
        $all_languages = Language::all();

        foreach ($all_languages as $lang){
            $this->validate($request, [
                'blog_single_page_'.$lang->slug.'_related_post_title' => 'nullable|string',
                'blog_single_page_'.$lang->slug.'_share_title' => 'nullable|string',
                'blog_single_page_'.$lang->slug.'_category_title' => 'nullable|string',
                'blog_single_page_'.$lang->slug.'_recent_post_title' => 'nullable|string',
                'blog_single_page_'.$lang->slug.'_tags_title' => 'nullable|string'
            ]);

            $fields = [
                'blog_single_page_'.$lang->slug.'_related_post_title',
                'blog_single_page_'.$lang->slug.'_share_title',
                'blog_single_page_'.$lang->slug.'_category_title',
                'blog_single_page_'.$lang->slug.'_recent_post_title',
                'blog_single_page_'.$lang->slug.'_tags_title'
            ];

            foreach ($fields as $field){
                update_static_option($field, $request->$field);
            }
        }
        update_static_option('blog_single_page_recent_post_item',$request->blog_single_page_recent_post_item);

        return redirect()->back()->with([
            'msg' => __('Settings Update Success...'),
            'type' => 'success'
        ]);
    }

    public function update_blog_page_settings(Request $request){

        $this->validate($request,[
           'blog_page_recent_post_widget_items' => 'nullable|string|max:191',
           'blog_page_item' => 'nullable|string|max:191'
        ]);

        $all_languages = Language::all();
        foreach ($all_languages as $lang){
            $this->validate($request, [
                'blog_page_'.$lang->slug.'_read_more_btn_text' => 'nullable|string',
            ]);
            $read_more_btn_text = 'blog_page_'.$lang->slug.'_read_more_btn_text';
            update_static_option($read_more_btn_text, $request->$read_more_btn_text);
        }

        update_static_option('blog_page_item',$request->blog_page_item);
        update_static_option('blog_page_recent_post_widget_items',$request->blog_page_recent_post_widget_items);

        return redirect()->back()->with([
            'msg' => __('Settings Update Success...'),
            'type' => 'success'
        ]);
    }

    public function bulk_action(Request $request){
        Blog::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function category_bulk_action(Request $request){
        BlogCategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }


    public function slug_check(SlugCheckRequest $request){
        $user_given_slug = $request->slug;
        $query = Events::Blog(['slug' => $user_given_slug]);

        return SlugChecker::Check($request,$query);
    }
   
public function datatable(Request $request)
{
    $columns = [
        0 => 'id',
        1 => 'id',
        2 => 'title',
        3 => 'author',
        4 => 'blog_categories_id',
        5 => 'status',
        6 => 'publish_date',
        7=> 'updated_at'
    ];

    $query = Blog::query();

    // 🔍 Keyword
if (!empty($request->keyword)) {

    $keyword = strtolower(trim($request->keyword));

    $query->where(function ($q) use ($keyword) {

        // Title search
        $q->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]);

        // Content search (text only, no HTML, no href)
        $q->orWhereRaw("
            LOWER(
                REGEXP_REPLACE(content, '<[^>]*>', ' ')
            ) LIKE ?
        ", ["%{$keyword}%"]);
    });
}
// if (!empty($request->keyword)) {

//     $keyword = strtolower(trim($request->keyword));

//     $query->where(function ($q) use ($keyword) {

//         // Title search
//         $q->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]);

//         // Content search (remove basic HTML noise)
//         $q->orWhereRaw("
//             LOWER(
//                 REPLACE(
//                     REPLACE(
//                         REPLACE(
//                             REPLACE(content, '&nbsp;', ' '),
//                         '&rsquo;', \"'\"),
//                     '<', ' '),
//                 '>', ' ')
//             ) LIKE ?
//         ", ["%{$keyword}%"]);
//         // 🔗 Search inside href links
//         $q->orWhereRaw("
//             LOWER(
//                 SUBSTRING_INDEX(
//                     SUBSTRING_INDEX(content, 'href=\"', -1),
//                 '\"', 1)
//             ) LIKE ?
//         ", ["%{$keyword}%"]);
//     });
// }

    // 📌 Status
    if (!empty($request->status)) {
        $query->where('status', $request->status);
    }

    // 📂 Category
    if (!empty($request->category)) {
        $query->where('blog_categories_id', $request->category);
    }

    // ✍️ Author
    if (!empty($request->author)) {
        $query->where('author', 'LIKE', "%{$request->author}%");
    }

    // 🔗 Backlink (IMPORTANT)
    if (!empty($request->backlink)) {
        $query->whereExists(function ($q) use ($request) {
            $q->select(\DB::raw(1))
              ->from('blog_backlinks') // 👈 table name
              ->whereRaw('blog_backlinks.blog_id = blogs.id')
              ->where('url', 'LIKE', "%{$request->backlink}%");
        });
    }
    $search = $request->input('search.value');

if (!empty($search)) {
    $query->where('title', 'like', "%{$search}%");
}

    $totalData = Blog::count();
    $totalFiltered = $query->count();

    $limit = $request->length;
    $start = $request->start;
    $order = $columns[$request->order[0]['column']] ?? 'id';
    $dir = $request->order[0]['dir'] ?? 'desc';

    $blogs = $query->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

    $data = [];

    foreach ($blogs as $row) {

        $img = get_attachment_image_by_id($row->image,null,true);

        $data[] = [
            'checkbox' => '<input type="checkbox" value="'.$row->id.'">',
            'id' => $row->id,
            'title' => $row->title,
          
            'author' => optional($row->authorData)->name ?? $row->authorData ?? '-',
            'category' => get_blog_category_by_id($row->blog_categories_id),
            'status' => $row->status == 'draft'
                ? '<span class="alert alert-warning">Draft</span>'
                : '<span class="alert alert-success">Publish</span>',
            'publish_date' => $row->publish_date,
              'updated_date' => date_format($row->updated_at,'d M Y'),
            'action' => '
            
    <button class="btn btn-xs btn-danger delete-blog" data-id="'.$row->id.'">
        <i class="ti-trash"></i>
    </button>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       href="'.route('admin.blog.edit',$row->id).'">
        <i class="ti-pencil"></i>
    </a>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       target="_blank"
       href="'.route('frontend.blog.single',$row->slug).'">
        <i class="ti-eye"></i>
    </a>

    <form action="'.route('admin.blog.clone').'" method="POST" 
          style="display:inline-block;" class="clone-form">
        <input type="hidden" name="_token" value="'.csrf_token().'">
        <input type="hidden" name="item_id" value="'.$row->id.'">
        <button type="submit" 
                title="clone this to new draft" 
                class="btn btn-xs btn-secondary mb-1 mr-1">
            <i class="far fa-copy"></i>
        </button>
    </form>
'
        ];
    }

    return response()->json([
        "draw" => intval($request->draw),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ]);
}
public function replacePreview(Request $request)
{
    $oldUrl = trim($request->old_url);
    $newUrl = trim($request->new_url);

    if (!$oldUrl || !$newUrl) {
        return response()->json(['count' => 0, 'data' => []]);
    }

    $blogs = Blog::where('content', 'LIKE', "%{$oldUrl}%")
        ->select('id', 'title', 'content')
        ->limit(20) // limit for performance
        ->get();

    $data = [];

    foreach ($blogs as $blog) {

        // show small preview with highlight
        $preview = str_replace(
            $oldUrl,
            '<span style="color:red;">'.$oldUrl.'</span> → <span style="color:green;">'.$newUrl.'</span>',
            $blog->content
        );

        $data[] = [
            'id' => $blog->id,
            'title' => $blog->title,
            'preview' => substr(strip_tags($preview), 0, 150) . '...'
        ];
    }

    return response()->json([
        'count' => Blog::where('content', 'LIKE', "%{$oldUrl}%")->count(),
        'data' => $data
    ]);
}
public function replaceUrl(Request $request)
{
    $oldUrl = trim($request->old_url);
    $newUrl = trim($request->new_url);

    if (!$oldUrl || !$newUrl) {
        return response()->json(['message' => 'Both URLs are required'], 422);
    }

    // 🚀 Fast bulk update
    \DB::table('blogs')
        ->where('content', 'LIKE', "%{$oldUrl}%")
        ->update([
            'content' => \DB::raw("REPLACE(content, '{$oldUrl}', '{$newUrl}')")
        ]);

    return response()->json([
        'message' => 'URLs replaced successfully in all blogs!'
    ]);
}


public function runFullScan()
{
    DB::table('scan_progress')->updateOrInsert(
        ['id' => 1],
        [
            'total' => 0,
            'processed' => 0,
            'total_jobs' => 4,
            'completed_jobs' => 0,
            'is_running' => 1,
            'updated_at' => now()
        ]
    );

    dispatch(new CheckBrokenLinksJob('blogs', 'title', 'content')); 
    dispatch(new CheckBrokenLinksJob('works', 'title', 'description'));
    dispatch(new CheckBrokenLinksJob('services', 'title', 'description'));
    dispatch(new CheckBrokenLinksJob('case_studies', 'title', 'description'));

    return response()->json(['status' => 'started']);
}

 
public function getScanProgress()
{
    $data = DB::table('scan_progress')->first();

    $percent = 0;

    if ($data && $data->total > 0) {
        $percent = round(($data->processed / $data->total) * 100);
    }

    return response()->json([
        'percent' => $percent,
        'processed' => $data->processed ?? 0,
        'total' => $data->total ?? 0,
        'running' => $data->is_running ?? 0
    ]);
}
public function showResults()
{
    $results = DB::table('broken_links')->latest()->get();

    return view('backend.pages.blog.404', compact('results'));
}

}
