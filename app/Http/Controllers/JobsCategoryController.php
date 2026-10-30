<?php

namespace App\Http\Controllers;

use App\Careers\JobBoard;
use App\Jobs;
use App\JobsCategory;
use App\Language;
use Illuminate\Http\Request;

class JobsCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function all_jobs_category(){

        $all_category = JobsCategory::all()->groupBy('lang');
        $all_languages = Language::all();
        return view('backend.jobs.all-jobs-category')->with(['all_category' => $all_category,'all_languages' => $all_languages] );
    }

    public function store_jobs_category(Request $request){
        $this->validate($request,[
            'title' => 'required|string|max:191|unique:jobs_categories',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:500',
            'meta_tags' => 'nullable|string|max:191',
        ]);

        JobsCategory::create([
            'title' => $request->title,
            'lang' => $request->lang,
            'status' => $request->status,
            'slug' => JobBoard::uniqueCategorySlug($request->slug ?: $request->title),
            'meta_description' => $request->meta_description,
            'meta_tags' => $request->meta_tags,
        ]);

        return redirect()->back()->with([
            'msg' => __('New Category Added...'),
            'type' => 'success'
        ]);
    }

    public function update_jobs_category(Request $request){
        $this->validate($request,[
            'id' => 'required|integer|exists:jobs_categories,id',
            'title' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191',
            'meta_description' => 'nullable|string|max:500',
            'meta_tags' => 'nullable|string|max:191',
        ]);

        // Its address stays as it is unless changed here, so links to it keep working.
        $category = JobsCategory::find($request->id);
        $wanted = $request->slug ?: ($category->slug ?: $request->title);
        $category->update([
            'title' => $request->title,
            'status' => $request->status,
            'lang' => $request->lang,
            'slug' => JobBoard::uniqueCategorySlug($wanted, $category->id),
            'meta_description' => $request->meta_description,
            'meta_tags' => $request->meta_tags,
        ]);

        return redirect()->back()->with([
            'msg' => __('Category Update Success...'),
            'type' => 'success'
        ]);
    }

    public function delete_jobs_category(Request $request,$id){
        if (Jobs::where('category_id',$id)->first()){
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Associated With A Post...'),
                'type' => 'danger'
            ]);
        }
        JobsCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => __('Category Delete Success...'),
            'type' => 'danger'
        ]);
    }

    public function Language_by_slug(Request $request){
        $all_category = JobsCategory::where('lang',$request->lang)->get();

        return response()->json($all_category);
    }

    /** Deletes the selected categories, except those still holding a job (as the single delete does). */
    public function bulk_action(Request $request){
        $in_use = Jobs::whereIn('category_id', (array) $request->ids)->pluck('category_id')->map('intval')->all();
        foreach(JobsCategory::find((array) $request->ids) as $item){
            if (!in_array($item->id, $in_use, true)) {
                $item->delete();
            }
        }
        return response()->json(['status' => 'ok', 'kept' => array_values(array_unique($in_use))]);
    }

}
