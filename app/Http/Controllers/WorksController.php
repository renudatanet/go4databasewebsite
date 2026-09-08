<?php

namespace App\Http\Controllers;

use App\Actions\SlugChecker;
use App\Events;
use App\Http\Requests\SlugCheckRequest;
use App\Language;
use App\Works;
use App\WorksCategory;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class WorksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $all_works = Works::all()->groupBy('lang');
        $work_category = WorksCategory::where(['status'=> 'publish','lang' => get_default_language()])->get();
        $all_language = Language::all();

        return view('backend.pages.works.work-index')->with(['all_works' => $all_works, 'works_category' => $work_category,'all_language' => $all_language]);
    }
        public function manage()
    {
        $all_works = Works::all()->groupBy('lang');
        $work_category = WorksCategory::where(['status'=> 'publish','lang' => get_default_language()])->get();
        $all_language = Language::all();

        return view('backend.pages.works.manage-index')->with(['all_works' => $all_works, 'works_category' => $work_category,'all_language' => $all_language]);
    }
    

    public function new()
    {
       
        $work_category = WorksCategory::where(['status'=> 'publish','lang' => get_default_language()])->get();
        $all_language = Language::all();
 //echo "hello"; die;
        return view('backend.pages.works.new-work')->with([ 'works_category' => $work_category,'all_language' => $all_language]);
    }

    public function edit($id)
    {
        $work_details = Works::find($id);
        $work_category = WorksCategory::where(['status'=> 'publish','lang' => $work_details->lang])->get();
        $all_language = Language::all();

        return view('backend.pages.works.edit-work')->with([ 'work_details' => $work_details, 'works_category' => $work_category,'all_language' => $all_language]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'excerpt' => 'nullable|string|max:191',
            'lang' => 'nullable|string|max:191',
            'clients' => 'nullable|string',
            'meta_tag' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'gallery' => 'nullable|string',
            'description' => 'required|string',
            'duration' => 'required|string',
            'budget' => 'required|string',
            'status' => 'required|string',
            'categories_id' => 'required',
            'image' => 'nullable|string|max:191',
        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
        Works::create([
            'title' => $request->title,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'meta_tag' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'gallery' => $request->gallery,
            'lang' => $request->lang,
            'clients' => $request->clients,
            'duration' => $request->duration,
            'budget' => $request->budget,
            'status' => $request->status,
            'description' => $request->description,
            'image' => $request->image,
            'categories_id' => serialize($request->categories_id),
        ]);

        return redirect()->back()->with(['msg' => __('New Case Study Added...'), 'type' => 'success']);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'excerpt' => 'nullable|string|max:191',
            'lang' => 'nullable|string|max:191',
            'clients' => 'nullable|string',
            'gallery' => 'nullable|string',
            'meta_tag' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'description' => 'required|string',
            'duration' => 'required|string',
            'budget' => 'required|string',
            'status' => 'required|string',
            'categories_id' => 'required',
            'image' => 'nullable|string|max:191',
        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
        Works::find($request->id)->update(
            [
                'title' => $request->title,
                'slug' => $slug,
                'excerpt' => $request->excerpt,
                'meta_tag' => $request->meta_tags,
                'meta_description' => $request->meta_description,
                'schema_code' => $request->schema_code,
                'gallery' => $request->gallery,
                'lang' => $request->lang,
                'clients' => $request->clients,
                'duration' => $request->duration,
                'budget' => $request->budget,
                'status' => $request->status,
                'description' => $request->description,
                'image' => $request->image,
                'categories_id' => serialize($request->categories_id),
            ]
        );
        return redirect()->back()->with(['msg' => __('Case Study Item Updated...'), 'type' => 'success']);
    }

    public function clone_new_draft(Request $request){
        $single_work = Works::find($request->item_id);
        Works::create(
            [
                'title' => $single_work->title,
                'slug' => $single_work->slug.random_int(999,9999),
                'excerpt' => $single_work->excerpt,
                'meta_tag' => $single_work->meta_tag,
                'meta_description' => $single_work->meta_description,
                'schema_code' => $request->schema_code,
                'lang' => $single_work->lang,
                'clients' => $single_work->clients,
                'duration' => $single_work->duration,
                'gallery' => $single_work->gallery,
                'budget' => $single_work->budget,
                'status' => 'draft',
                'description' => $single_work->description,
                'image' => $single_work->image,
                'categories_id' => serialize($single_work->categories_id),
            ]
        );
        return redirect()->back()->with(['msg' => __('Case Study Item Clone Success...'), 'type' => 'success']);
    }

    public function delete($id)
    {
        Works::find($id)->delete();
        return redirect()->back()->with(['msg' => __('Delete Success...'), 'type' => 'danger']);
    }

    public function category_index()
    {
        $all_category = WorksCategory::all()->groupBy('lang');
        return view('backend.pages.works.category')->with(['all_category' => $all_category]);
    }

    public function category_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191'
        ]);

        WorksCategory::create($request->all());

        return redirect()->back()->with([
            'msg' => __('New Category Added...'),
            'type' => 'success'
        ]);
    }

    public function category_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191'
        ]);

        WorksCategory::find($request->id)->update([
            'name' => $request->name,
            'status' => $request->status,
            'lang' => $request->lang,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
        ]);

        return redirect()->back()->with([
            'msg' => __('Category Update Success...'),
            'type' => 'success'
        ]);
    }

    public function category_delete(Request $request, $id)
    {
        if (Works::where('categories_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Case Study ...'),
                'type' => 'danger'
            ]);
        }
        WorksCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Category Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function category_by_slug(Request $request){
        $all_category = WorksCategory::where('lang',$request->lang)->get();
        return response()->json($all_category);
    }

    public function bulk_action(Request $request){
        Works::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function category_bulk_action(Request $request){
        WorksCategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function slug_check(SlugCheckRequest $request){
        $user_given_slug = $request->slug;
        $query = Events::Works(['slug' => $user_given_slug]);

        return SlugChecker::Check($request,$query);
    }
    
        
    
    
    public function datatable(Request $request)
{
    $columns = [
        0 => 'id',
        1 => 'title',
        2 => 'status',
        3 => 'image',
        4 => 'category',
        5=> 'created_at'
    ];

    $query = Works::query();

    // 🔍 Keyword
if (!empty($request->keyword)) {

    $keyword = strtolower(trim($request->keyword));

    $query->where(function ($q) use ($keyword) {

        // Title search
        $q->whereRaw('LOWER(title) LIKE ?', ["%{$keyword}%"]);

        // Content search (text only, no HTML, no href)
        $q->orWhereRaw("
            LOWER(
                REGEXP_REPLACE(description, '<[^>]*>', ' ')
            ) LIKE ?
        ", ["%{$keyword}%"]);
    });
}

$search = $request->input('search.value');

if (!empty($search)) {
    $query->where('title', 'like', "%{$search}%");
}

    $totalData = Works::count();
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
$category = '';

if (!empty($row->categories_id)) {

    $ids = is_array($row->categories_id)
        ? $row->categories_id
        : json_decode($row->categories_id, true);

    if (!empty($ids)) {
        $category = WorksCategory::whereIn('id', $ids)
            ->pluck('name')
            ->implode(', ');
    }
}
        $img = get_attachment_image_by_id($row->image,null,true);
        
        $data[] = [
            'checkbox' => '<input type="checkbox" value="'.$row->id.'">',
            'id' => $row->id,
            'title' => $row->title,
            
            'status' => $row->status == 'draft'
                ? '<span class="alert alert-warning">Draft</span>'
                : '<span class="alert alert-success">Publish</span>',
            
            'category' => $category,
            'image' =>$img,
              'created_at' => date_format($row->created_at,'d M Y'),
            'action' => '
            
    <button class="btn btn-xs btn-danger delete-blog" data-id="'.$row->id.'">
        <i class="ti-trash"></i>
    </button>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       href="'.route('admin.work.edit',$row->id).'">
        <i class="ti-pencil"></i>
    </a>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       target="_blank"
       href="'.route('frontend.work.single',$row->slug).'">
        <i class="ti-eye"></i>
    </a>

    <form action="'.route('admin.work.clone').'" method="POST" 
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

    $services = Works::where('description', 'LIKE', "%{$oldUrl}%")
        ->select('id', 'title', 'description')
        ->limit(20) // limit for performance
        ->get();

    $data = [];

    foreach ($services as $service) {

        // show small preview with highlight
        $preview = str_replace(
            $oldUrl,
            '<span style="color:red;">'.$oldUrl.'</span> → <span style="color:green;">'.$newUrl.'</span>',
            $service->description
        );

        $data[] = [
            'id' => $service->id,
            'title' => $service->title,
            'preview' => substr(strip_tags($preview), 0, 150) . '...'
        ];
    }

    return response()->json([
        'count' => Works::where('description', 'LIKE', "%{$oldUrl}%")->count(),
        'data' => $data
    ]);
}

public function replaceUrl(Request $request)
{
    $oldUrl = trim($request->old_url);
    $newUrl = trim($request->new_url);

    if (!$oldUrl || !$newUrl) {
        return response()->json(['message' => 'Service URLs are required'], 422);
    }

    // 🚀 Fast bulk update
    \DB::table('works')
        ->where('description', 'LIKE', "%{$oldUrl}%")
        ->update([
            'description' => \DB::raw("REPLACE(description, '{$oldUrl}', '{$newUrl}')")
        ]);

    return response()->json([
        'message' => 'URLs replaced successfully in all B2b!'
    ]);
}
}


