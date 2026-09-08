<?php

namespace App\Http\Controllers;

use App\Blog;
use App\BlogCategory;
use App\Language;
use App\Page;
use App\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(){
        $all_page = Page::all()->groupBy('lang');
        $all_language = Language::all();
        return view('backend.pages.page.index')->with([
            'all_page' => $all_page,
            'all_languages' => $all_language,
        ]);
    }
    public function new_page(){
        $all_language = Language::all();
        return view('backend.pages.page.new')->with(['all_languages' => $all_language]);
    }

    public function slug_check(Request $request){
        $this->validate($request,[
           'slug' => 'required|string',
           'type' => 'required|string',
           'lang' => 'required|string',
        ]);

        $pre_made_pages_slug = ['video_gallery','about','service','work','team','faq','price_plan','blog','contact','career_with_us','events','knowledgebase','donation','product','our-testimonial','feedback','clients_feedback','image_gallery','donor','appointment','quote','courses','support_ticket'];
        $matched_pre_made_page_slug = false;
        $user_given_slug = $request->slug;
        foreach($pre_made_pages_slug as $page_slug){
            if ($request->slug === get_static_option($page_slug.'_page_slug')){
                $matched_pre_made_page_slug = true;
            }
        }

        if ($matched_pre_made_page_slug){
            $user_given_slug .= '-'.random_int(1,9);
        }

        $query = Page::where(['slug' => $user_given_slug]);
        if (!empty($request->lang)){
            $query->where('lang' , $request->lang);
        }
        $slug_count = $query->count();

        if ($request->type === 'new' && $slug_count > 0){
            return $user_given_slug.'-'.$slug_count;
        }elseif ($request->type === 'update' && $slug_count > 1){
            return $user_given_slug.'-'.$slug_count;
        }
        return $user_given_slug;
    }


    public function store_new_page(Request $request){

        $this->validate($request,[
            'content' => 'nullable',
            'meta_tags' => 'nullable',
            'meta_description' => 'nullable',
            'lang' => 'nullable',
            'title' => 'required',
            'slug' => 'nullable',
            'visibility' => 'nullable',
            'status' => 'required|string|max:191',
        ]);

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);

        Page::create([
            'lang' => $request->lang,
            'breadcrumb_status' => $request->breadcrumb_status,
            'slug' => $slug,
            'status' => $request->status,
            'content' => $request->page_content,
            'title' => $request->title,
            'visibility' => $request->visibility,
            'page_builder_status' => $request->page_builder_status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
        ]);

        return redirect()->back()->with([
            'msg' => __('New Page Created...'),
            'type' => 'success'
        ]);
    }
    public function edit_page($id){
        $page_post = Page::find($id);
        $all_language = Language::all();
        return view('backend.pages.page.edit')->with([
            'page_post' => $page_post,
            'all_languages' => $all_language
        ]);
    }
    public function update_page(Request $request,$id){

        $this->validate($request,[
            'content' => 'nullable',
            'meta_tags' => 'nullable',
            'meta_description' => 'nullable',
            'lang' => 'nullable',
            'title' => 'required',
            'slug' => 'nullable',
            'visibility' => 'nullable',
            'status' => 'required|string|max:191',
        ]);

        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);

        Page::where('id',$id)->update([
            'lang' => $request->lang,
            'status' => $request->status,
            'content' => $request->page_content,
            'visibility' => $request->visibility,
            'page_builder_status' => $request->page_builder_status,
            'breadcrumb_status' => $request->breadcrumb_status,
            'title' => $request->title,
            'slug' => $slug,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
        ]);


        return redirect()->back()->with([
            'msg' => __('Page updated...'),
            'type' => 'success'
        ]);
    }
    public function delete_page(Request $request,$id){
        Page::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Page Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function bulk_action(Request $request){
        Page::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }
    
    /*********************************************************/
    
    
public function datatable(Request $request)
{
    $columns = [
        0 => 'id',
        1 => 'title',
        2 => 'created_at',
        3 => 'status',
    ];

    $query = Page::query();

    // 🔍 Keyword
if (!empty($request->keyword)) {

    $keyword = strtolower(trim($request->keyword));

    $query->where(function ($q) use ($keyword) {

        // Title search
        $q->whereRaw("
            LOWER(
                REGEXP_REPLACE(content, '<[^>]*>', ' ')
            ) LIKE ?
        ", ["%{$keyword}%"]);

    });
}
   

    $search = $request->input('search.value');

if (!empty($search)) {
    $query->where('title', 'like', "%{$search}%");
}

    $totalData = Page::count();
    $totalFiltered = $query->count();

    $limit = $request->length;
    $start = $request->start;
    $order = $columns[$request->order[0]['column']] ?? 'id';
    $dir = $request->order[0]['dir'] ?? 'desc';

    $pages = $query->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

    $data = [];

    foreach ($pages as $row) {


        $data[] = [
            'checkbox' => '<input type="checkbox" value="'.$row->id.'">',
            'id' => $row->id,
            'title' => $row->title,
            'created_at' => date_format($row->created_at,'d M Y'),
            'status' => $row->status == 'draft'
                ? '<span class="alert alert-warning">Draft</span>'
                : '<span class="alert alert-success">Publish</span>',
            'action' => '
            
    <button class="btn btn-xs btn-danger delete-blog" data-id="'.$row->id.'">
        <i class="ti-trash"></i>
    </button>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       href="'.route('admin.page.edit',$row->id).'">
        <i class="ti-pencil"></i>
    </a>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       target="_blank"
       href="'.route('frontend.dynamic.page',$row->slug).'">
        <i class="ti-eye"></i>
    </a>

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

    $blogs = Page::where('content', 'LIKE', "%{$oldUrl}%")
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
        'count' => Page::where('content', 'LIKE', "%{$oldUrl}%")->count(),
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
    \DB::table('pages')
        ->where('content', 'LIKE', "%{$oldUrl}%")
        ->update([
            'content' => \DB::raw("REPLACE(content, '{$oldUrl}', '{$newUrl}')")
        ]);

    return response()->json([
        'message' => 'URLs replaced successfully in all pages!'
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
