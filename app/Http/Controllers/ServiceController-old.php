<?php

namespace App\Http\Controllers;

use App\PricePlan;
use App\ServiceCategory;
use App\ServiceSubcategory;
use App\Services;
use App\Works;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ServiceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $all_services = Services::all()->groupBy('lang');
        $service_category = ServiceCategory::where(['status' => 'publish', 'lang' => get_default_language()])->get();
        return view('backend.pages.service.index')->with(['all_services' => $all_services, 'service_category' => $service_category]);
    }

    public function new_service()
    {
        $service_category = ServiceCategory::where(['status' => 'publish', 'lang' => get_default_language()])->get();
        $price_plans = PricePlan::where(['status' => 'publish', 'lang' => get_default_language()])->get();
        return view('backend.pages.service.new-service')->with(['service_category' => $service_category,'price_plans' => $price_plans]);
    }

    public function edit_service($id)
    {
        $service = Services::find($id);
        $service_category = ServiceCategory::where(['status' => 'publish', 'lang' => $service->lang])->get();
        $price_plans = PricePlan::where(['status' => 'publish', 'lang' =>  $service->lang])->get();

        return view('backend.pages.service.edit-service')->with(['service_category' => $service_category,'service' => $service,'price_plans' => $price_plans]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'icon' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'slug' => 'nullable|string',
            'description' => 'required|string',
            'excerpt' => 'required|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'categories_id' => 'required|string',
            'icon_type' => 'required|string',
            'img_icon' => 'nullable|string|max:191',
            'sr_order' => 'nullable|string|max:191',
            'image' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191',
            'price_plan' => 'nullable',
        ]);
        $price_plan = !empty($request->price_plan) ? $request->price_plan : [];
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
        Services::create([
            'title' => $request->title,
            'lang' => $request->lang,
            'icon' => $request->icon,
            'description' => $request->description,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'meta_tag' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'categories_id' => $request->categories_id,
            'image' => $request->image,
            'status' => $request->status,
            'sr_order' => $request->sr_order,
            'img_icon' => $request->img_icon,
            'icon_type' => $request->icon_type,
            'price_plan' =>  serialize($price_plan),
        ]);

        return redirect()->back()->with(['msg' => __('New service Added...'), 'type' => 'success']);
    }

    public function update(Request $request)
    {

        $this->validate($request, [
            'title' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'icon' => 'required|string|max:191',
            'description' => 'required|string',
            'slug' => 'nullable|string',
            'excerpt' => 'required|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'categories_id' => 'required|string',
            'image' => 'nullable|string|max:191',
            'sr_order' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191',
            'price_plan' => 'nullable',
        ]);
        $price_plan = !empty($request->price_plan) ? $request->price_plan : [];
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
        Services::find($request->id)->update([
            'title' => $request->title,
            'lang' => $request->lang,
            'icon' => $request->icon,
            'description' => $request->description,
            'slug' => $slug,
            'excerpt' => $request->excerpt,
            'meta_tag' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'categories_id' => $request->categories_id,
            'image' => $request->image,
            'status' => $request->status,
            'sr_order' => $request->sr_order,
            'img_icon' => $request->img_icon,
            'icon_type' => $request->icon_type,
            'price_plan' => serialize($price_plan),
        ]);

        return redirect()->back()->with(['msg' => __('Service Item Updated...'), 'type' => 'success']);
    }

    public function clone_service_as_draft(Request $request)
    {

        $service = Services::find($request->item_id);
        Services::create([
            'title' => $service->title,
            'lang' => $service->lang,
            'icon' => $service->icon,
            'description' => $service->description,
            'slug' => $service->slug,
            'excerpt' => $service->excerpt,
            'meta_tag' => $service->meta_tag,
            'meta_description' => $service->meta_description,
            'schema_code' => $request->schema_code,
            'categories_id' => $service->categories_id,
            'image' => $service->image,
            'img_icon' => $service->img_icon,
            'icon_type' => $service->icon_type,
            'sr_order' => $service->sr_order,
            'price_plan' => $service->price_plan,
            'status' => 'draft',
        ]);

        return redirect()->back()->with(['msg' => __('Service Item Cloned Success...'), 'type' => 'success']);
    }

    public function delete($id)
    {
        Services::find($id)->delete();

        return redirect()->back()->with(['msg' => __('Delete Success...'), 'type' => 'danger']);
    }

    public function category_index()
    {
        $all_category = ServiceCategory::all()->groupBy('lang');
        return view('backend.pages.service.category')->with(['all_category' => $all_category]);
    }

    public function category_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'icon_type' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'img_icon' => 'nullable|string|max:191',
            'status' => 'required|string|max:191'
        ]);

        ServiceCategory::create($request->all());

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
            'status' => 'required|string|max:191',
            'icon_type' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'img_icon' => 'nullable|string|max:191'
        ]);

        ServiceCategory::find($request->id)->update([
            'name' => $request->name,
            'lang' => $request->lang,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'status' => $request->status,
            'img_icon' => $request->img_icon,
            'icon' => $request->icon,
            'icon_type' => $request->icon_type,
        ]);

        return redirect()->back()->with([
            'msg' => __('Category Update Success...'),
            'type' => 'success'
        ]);
    }

    public function category_delete(Request $request, $id)
    {
        if (Services::where('categories_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Service...'),
                'type' => 'danger'
            ]);
        }
        ServiceCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Category Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function category_by_slug(Request $request)
    {
        $service_category = ServiceCategory::where(['status' => 'publish', 'lang' => $request->lang])->get();
        return response()->json($service_category);
    }
    
    /************************Sub Category **********************/
    
    
      public function sub_category_index()
    {
        $all_sub_category = ServiceSubcategory::with('category')
                ->get()
                ->groupBy('lang');
        $all_category = ServiceCategory::all();
        return view('backend.pages.service.sub_category')->with(['all_sub_category' => $all_sub_category,'all_category' => $all_category]);
    }

    public function sub_category_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            
            'category_id' => 'required|string|max:191',
            'icon_type' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'img_icon' => 'nullable|string|max:191',
            'status' => 'required|string|max:191'
        ]);

        ServiceSubcategory::create($request->all());

        return redirect()->back()->with([
            'msg' => __('New Sub-Category Added...'),
            'type' => 'success'
        ]);
    }

    public function sub_category_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
             'category_id' => 'required|string|max:191',
            'status' => 'required|string|max:191',
            'icon_type' => 'required|string|max:191',
            'icon' => 'nullable|string|max:191',
            'img_icon' => 'nullable|string|max:191'
        ]);
        
        ServiceSubcategory::find($request->id)->update([
            'name' => $request->name,
            'lang' => $request->lang,
            
            'category_id' => $request->category_id,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'status' => $request->status,
            'img_icon' => $request->img_icon,
            'icon' => $request->icon,
            'icon_type' => $request->icon_type,
        ]);

        return redirect()->back()->with([
            'msg' => __('Subcategory Update Success...'),
            'type' => 'success'
        ]);
    }

    public function sub_category_delete(Request $request, $id)
    {
        if (Services::where('sub_categories_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Service...'),
                'type' => 'danger'
            ]);
        }
        ServiceCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Subategory Delete Success...'),
            'type' => 'danger'
        ]);
    }
    
        public function sub_category_bulk_action(Request $request)
    {
        ServiceSubcategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }
    
    public function price_plan_by_slug(Request $request)
    {
        $service_category = PricePlan::where(['status' => 'publish', 'lang' => $request->lang])->get();
        return response()->json($service_category);
    }

    public function bulk_action(Request $request)
    {
        Services::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function category_bulk_action(Request $request)
    {
        ServiceCategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function slug_check(Request $request){
        $this->validate($request,[
            'slug' => 'required|string',
            'type' => 'required|string',
            'lang' => 'required|string',
        ]);
        $user_given_slug = $request->slug;
        $query = Services::where(['slug' => $user_given_slug]);
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
    
    
    
    public function datatable(Request $request)
{
    $columns = [
        0 => 'id',
        1 => 'title',
        2 => 'status',
        3 => 'image',
        4 => 'icon_type',
        5 => 'categories_id',
        6=> 'created_at'
    ];

    $query = Services::query();

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

    $totalData = Services::count();
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
        $icon = get_attachment_image_by_id($row->icon,null,true);
        
        $data[] = [
            'checkbox' => '<input type="checkbox" value="'.$row->id.'">',
            'id' => $row->id,
            'title' => $row->title,
            
            'status' => $row->status == 'draft'
                ? '<span class="alert alert-warning">Draft</span>'
                : '<span class="alert alert-success">Publish</span>',
            'image' =>$img,
            'icon_type' =>$icon,
            'categories_id' => get_service_category_by_id($row->categories_id),
              'created_at' => date_format($row->created_at,'d M Y'),
            'action' => '
            
    <button class="btn btn-xs btn-danger delete-blog" data-id="'.$row->id.'">
        <i class="ti-trash"></i>
    </button>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       href="'.route('admin.services.edit',$row->id).'">
        <i class="ti-pencil"></i>
    </a>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       target="_blank"
       href="'.route('frontend.services.single',$row->slug).'">
        <i class="ti-eye"></i>
    </a>

    <form action="'.route('admin.services.clone').'" method="POST" 
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

    $services = Services::where('description', 'LIKE', "%{$oldUrl}%")
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
        'count' => Services::where('description', 'LIKE', "%{$oldUrl}%")->count(),
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
    \DB::table('services')
        ->where('description', 'LIKE', "%{$oldUrl}%")
        ->update([
            'description' => \DB::raw("REPLACE(description, '{$oldUrl}', '{$newUrl}')")
        ]);

    return response()->json([
        'message' => 'URLs replaced successfully in all Services!'
    ]);
}


}
