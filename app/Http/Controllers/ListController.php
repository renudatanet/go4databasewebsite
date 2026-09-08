<?php

namespace App\Http\Controllers;

use App\PricePlan;
use App\ListCategory;
use App\B2BList;
use App\Works;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ListController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $all_B2Blist = B2Blist::all()->groupBy('lang');
        $service_category = ListCategory::where(['status' => 'publish', 'lang' => get_default_language()])->get();
       
        return view('backend.pages.list.index')->with(['all_B2Blist' => $all_B2Blist, 'service_category' => $service_category]);
    }

    public function new_list()
    {
        $service_category = ListCategory::where(['status' => 'publish', 'lang' => get_default_language()])->get();
        $price_plans = PricePlan::where(['status' => 'publish', 'lang' => get_default_language()])->get();
        return view('backend.pages.list.new-list')->with(['service_category' => $service_category,'price_plans' => $price_plans]);
    }

    public function edit_list($id)
    {
        $service = B2Blist::find($id);
        $service_category = ListCategory::where(['status' => 'publish', 'lang' => $service->lang])->get();
        $price_plans = PricePlan::where(['status' => 'publish', 'lang' =>  $service->lang])->get();

        return view('backend.pages.list.edit-list')->with(['service_category' => $service_category,'service' => $service,'price_plans' => $price_plans]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:191',
            'data_counts' => 'required|string|max:191',
            'accuracy_commitment' => 'required|string|max:191',
            'icon' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'slug' => 'nullable|string',
            'description' => 'required|string',
            'buyers_list'  => 'nullable|string',
            'excerpt' => 'required|string',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'categories_id' => 'required|string',
            'subcategories_id' => 'required|string',
            'icon_type' => 'required|string',
            'img_icon' => 'nullable|string|max:191',
            'sr_order' => 'nullable|string|max:191',
            'image' => 'nullable|string|max:191',
            'status' => 'nullable|string|max:191',
            'price_plan' => 'nullable',
        ]);
        $price_plan = !empty($request->price_plan) ? $request->price_plan : [];
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->title,$request->lang);
          $faqs = [];
            if ($request->has('faqs')) {
            $faqs = collect($request->faqs)
            ->filter(function ($faq) {
            return !empty($faq['question']) && !empty($faq['answer']);
            })
            ->values()
            ->toArray();
            }
            
        B2Blist::create([
            'title' => $request->title,
            'lang' => $request->lang,
            'icon' => $request->icon,
            'description' => $request->description,
            'buyerlist' =>$request->buyers_list,
            'slug' => $slug,
            'faqs' => !empty($faqs) ? $faqs : null,
            'excerpt' => $request->excerpt,
            'meta_tag' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'categories_id' => $request->categories_id,
            'subcategories_id' => $request->subcategories_id,
            'image' => $request->image,
            
            'data_counts' => $request->data_counts,
            
             'accuracy_commitment' => $request->accuracy_commitment,
            
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
            'buyers_list'  => 'nullable|string',
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
          $faqs = [];

if ($request->has('faqs')) {
    $faqs = collect($request->faqs)
        ->filter(fn($faq) => !empty($faq['question']) && !empty($faq['answer']))
        ->values()
        ->toArray();
}

        B2Blist::find($request->id)->update([
            'title' => $request->title,
            'lang' => $request->lang,
            'icon' => $request->icon,
            'description' => $request->description,
            
            'buyerlist' =>$request->buyers_list,
            'slug' => $slug,
            'data_counts' => $request->data_counts,
            
             'accuracy_commitment' => $request->accuracy_commitment,
            
            'excerpt' => $request->excerpt,
            'meta_tag' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            'schema_code' => $request->schema_code,
            'categories_id' => $request->categories_id,
             'subcategories_id' => $request->subcategories_id,
             'faqs' => !empty($faqs) ? $faqs : null,
            'image' => $request->image,
            'status' => $request->status,
            'sr_order' => $request->sr_order,
            'img_icon' => $request->img_icon,
            'icon_type' => $request->icon_type,
            'price_plan' => serialize($price_plan),
        ]);

        return redirect()->back()->with(['msg' => __('List Item Updated...'), 'type' => 'success']);
    }

    public function clone_service_as_draft(Request $request)
    {

        $service = B2Blist::find($request->item_id);
        B2Blist::create([
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
        B2Blist::find($id)->delete();

        return redirect()->back()->with(['msg' => __('Delete Success...'), 'type' => 'danger']);
    }

    public function category_index()
    {
        $all_category = ListCategory::all()->groupBy('lang');
        return view('backend.pages.list.category')->with(['all_category' => $all_category]);
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

      $data = $request->all();

    // Generate Slug
    $data['slug'] = Str::slug($request->name);
       ListCategory::create($data);
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

        $slug = Str::slug($request->name);
       //dd($request->all());
        ListCategory::find($request->id)->update([
            'name' => $request->name,
             'slug' =>$slug,
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
        if (B2Blist::where('categories_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Service...'),
                'type' => 'danger'
            ]);
        }
        ListCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Category Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function category_by_slug(Request $request)
    {
        $service_category = ListCategory::where(['status' => 'publish', 'lang' => $request->lang])->get();
        return response()->json($service_category);
    }
    
    /************************Sub Category **********************/
    
    
      public function sub_category_index()
    {
        $all_sub_category = B2Blistubcategory::with('category')
                ->get()
                ->groupBy('lang');
        $all_category = ListCategory::all();
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

$data = $request->all();

    // Generate Slug
    $data['slug'] = Str::slug($request->name);
       B2Blistubcategory::create($data);

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
        $slug = Str::slug($request->name);
        B2Blistubcategory::find($request->id)->update([
            'name' => $request->name,
            'lang' => $request->lang,
            'slug' =>$slug,
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
        //dd($id);
        if (B2Blist::where('subcategories_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Service...'),
                'type' => 'danger'
            ]);
        }
        B2Blistubcategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Subategory Delete Success...'),
            'type' => 'danger'
        ]);
    }
    
        public function sub_category_bulk_action(Request $request)
    {
        B2Blistubcategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }
    
    public function getSubCategories(Request $request)
    {
        $subcategories = B2Blistubcategory::where(
            'category_id',
            $request->category_id
        )->get();

        return response()->json($subcategories);
    }
    public function price_plan_by_slug(Request $request)
    {
        $service_category = PricePlan::where(['status' => 'publish', 'lang' => $request->lang])->get();
        return response()->json($service_category);
    }

    public function bulk_action(Request $request)
    {
        B2Blist::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function category_bulk_action(Request $request)
    {
        ListCategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    public function slug_check(Request $request){
        $this->validate($request,[
            'slug' => 'required|string',
            'type' => 'required|string',
            'lang' => 'required|string',
        ]);
        $user_given_slug = $request->slug;
        $query = B2Blist::where(['slug' => $user_given_slug]);
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
        5=> 'created_at',
        6=> 'action'
    ];

    $query = B2BList::query();

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

    $totalData = B2BList::count();
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
            'checkbox' => '<input type="checkbox" class="bulk-checkbox" value="'.$row->id.'">',
            'id' => $row->id,
            'title' => $row->title,
            
            'status' => $row->status == 'draft'
                ? '<span class="alert alert-warning">Draft</span>'
                : '<span class="alert alert-success">Publish</span>',
            'image' =>$img,
            'icon_type' =>$icon,
              'created_at' => date_format($row->created_at,'d M Y'),
            'action' => '
            
    <button class="btn btn-xs btn-danger delete-blog" data-id="'.$row->id.'">
        <i class="ti-trash"></i>
    </button>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       href="'.route('admin.list.edit',$row->id).'">
        <i class="ti-pencil"></i>
    </a>

    <a class="btn btn-xs btn-primary mb-1 mr-1" 
       target="_blank"
       href="'.route('frontend.list.single', [
    'service_slug' => ltrim($row->slug, '/')
]).'">
        <i class="ti-eye"></i>
    </a>

    <form action="'.route('admin.list.clone').'" method="POST" 
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

    $B2Blist = B2Blist::where('description', 'LIKE', "%{$oldUrl}%")
        ->select('id', 'title', 'description')
        ->limit(20) // limit for performance
        ->get();

    $data = [];

    foreach ($B2Blist as $service) {

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
        'count' => B2Blist::where('description', 'LIKE', "%{$oldUrl}%")->count(),
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
    \DB::table('B2Blist')
        ->where('description', 'LIKE', "%{$oldUrl}%")
        ->update([
            'description' => \DB::raw("REPLACE(description, '{$oldUrl}', '{$newUrl}')")
        ]);

    return response()->json([
        'message' => 'URLs replaced successfully in all B2Blist!'
    ]);
}


public function importCsv(Request $request)
{
    $request->validate([
        'csv_file' => 'required|mimes:csv,txt'
    ]);

    $file = $request->file('csv_file');

    if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

        // Read Header Row
        $header = fgetcsv($handle);

        // Remove UTF-8 BOM if present
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        // Trim all headers
        $header = array_map('trim', $header);
        $count = 0;
       while (($row = fgetcsv($handle, 100000, ',')) !== false) {

    // Skip broken rows
    if (count($header) != count($row)) {
        continue;
    }

    $data = array_combine($header, $row);

    // Convert all CSV values to UTF-8
    $data = array_map(function ($value) {

        if (!is_string($value)) {
            return $value;
        }

        return mb_convert_encoding(
            trim($value),
            'UTF-8',
            'UTF-8, Windows-1252, ISO-8859-1'
        );

    }, $data);

   
   $categoryName = trim($data['Categories'] ?? '');

    // Skip row if category missing
    if (empty($categoryName)) {
        continue;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Category
    |--------------------------------------------------------------------------
    */

    $category = ListCategory::updateOrCreate(
        [
            'name' => $categoryName
        ],
        [
            'slug' => Str::slug($categoryName),
            'meta_tags' => null,
            'meta_description' => null,
            'status' => 1
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | FAQs
    |--------------------------------------------------------------------------
    */

    $faqs = [];

    for ($i = 1; $i <= 10; $i++) {

        $question = $data["FAQ {$i}"] ?? null;
        $answer = $data["FAQ {$i} Answer"] ?? null;

        if (!empty($question)) {

            $faqs[] = [
                'question' => $question,
                'answer' => $answer
            ];
        }
    }

    // Validate FAQ JSON before saving
    try {
        json_encode($faqs, JSON_THROW_ON_ERROR);
    } catch (\JsonException $e) {

        dd([
            'title' => $data['Title'] ?? null,
            'slug' => $data['Slug'] ?? null,
            'faqs' => $faqs,
            'error' => $e->getMessage()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Service
    |--------------------------------------------------------------------------
    */
//dd($data); 
    B2Blist::updateOrCreate(
        [
            'slug' => trim($data['Slug'] ?? Str::slug($data['Title'] ?? ''))
        ],
        [
            'title' => $data['Title'] ?? null,

            'icon' => $data['icon'] ?? null,
           
            'categories_id' => $category->id,

            'lang' => 'en_US',

           
            'search_title' => $data['Search Title'] ?? null,
            'search_industry' => $data['Search Industry'] ?? null,
            'search_business_category' => $data['Search Business Category'] ?? null,
            'search_location' => $data['Search Location'] ?? null,


            'image' => $data['Image'] ?? null,
            'meta_tag' => $data['Meta Title'] ?? null,
            'meta_description' => $data['Meta Description'] ?? null,
            'excerpt' => $data['Excerpt'] ?? null,
            'status' => $data['Status'] ?? 1,


            'description' => $data['Product Description'] ?? '',

            'icon_type' => $data['Icon Type'] ?? null,
            
            'img_icon' => $data['Img Icon'] ?? null,
            
           'sr_order' => !empty($data['Sr Order']) ? (int) $data['Sr Order'] : 0,
            'price_plan' => $data['Price Plan'] ?? null,

            'data_counts' => $data['Data Count'] ?? null,

            'accuracy_commitment' => $data['Accuracy Commitment'] ?? null,

            'buyerlist' => $data['Buyers List'] ?? null,
            
            'key_decision' => $data['Key Decision'] ?? null,

            'faqs' => !empty($faqs) ? $faqs : null,
        ]
    );
    
    $count++;
}

        fclose($handle);
    }

    return redirect()->back()->with('success', $count . ' records imported successfully.');
}
}
