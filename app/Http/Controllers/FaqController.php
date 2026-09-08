<?php

namespace App\Http\Controllers;

use App\Faq;
use App\FaqCategory;
use App\Language;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    public function index(){
        $all_faqs = Faq::all()->groupBy('lang');
        $all_language = Language::all();
        $all_category = FaqCategory::where('status', 'publish')->orderBy('sr_order')->get();
        return view('backend.pages.faqs')->with(['all_faqs' => $all_faqs,'all_languages' => $all_language,'all_category' => $all_category]);
    }
    public function store(Request $request){
        $this->validate($request,[
            'title' => 'required|string',
            'description' => 'required|string',
            'lang' => 'required|string',
            'status' => 'nullable|string|max:191',
            'category_id' => 'nullable|integer',
        ]);

        Faq::create([
            'title' => $request->title,
            'description' => $request->description,
            'lang' => $request->lang,
            'status' => $request->status,
            'is_open' => !empty($request->is_open) ? 'on' : '',
            'category_id' => $request->category_id,
        ]);


        return redirect()->back()->with(['msg' => __('New Faq Added...'),'type' => 'success']);
    }

    public function update(Request $request){

        $this->validate($request,[
            'title' => 'required|string',
            'description' => 'required|string',
            'lang' => 'required|string',
            'status' => 'nullable|string|max:191',
            'category_id' => 'nullable|integer',
        ]);

        Faq::find($request->id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'lang' => $request->lang,
            'is_open' => !empty($request->is_open) ? 'on' : '',
            'category_id' => $request->category_id,
        ]);

        return redirect()->back()->with(['msg' => __('Faq Updated...'),'type' => 'success']);
    }

    public function delete($id){
        Faq::find($id)->delete();
        return redirect()->back()->with(['msg' => __('Delete Success...'),'type' => 'danger']);
    }

    public function clone(Request $request){
        $faq_item = Faq::find($request->item_id);
        Faq::create([
            'title' => $faq_item->title,
            'description' => $faq_item->description,
            'status' => 'draft',
            'lang' => $faq_item->lang,
            'is_open' => !empty($faq_item->is_open) ? 'on' : '',
            'category_id' => $faq_item->category_id,
        ]);
        return redirect()->back()->with(['msg' => __('Clone Success...'),'type' => 'success']);
    }

    public function bulk_action(Request $request){
        $all = Faq::find($request->ids);
        foreach($all as $item){
            $item->delete();
        }
        return response()->json(['status' => 'ok']);
    }

    public function category_index(){
        $all_languages = Language::all();
        $all_category = FaqCategory::all()->groupBy('lang');
        return view('backend.pages.faq-category')->with(['all_languages' => $all_languages,'all_category' => $all_category]);
    }

    public function category_store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'lang' => 'required|string|max:191',
            'status' => 'required|string|max:191'
        ]);

        FaqCategory::create($request->all());

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

        FaqCategory::find($request->id)->update([
            'name' => $request->name,
            'status' => $request->status,
            'lang' => $request->lang,
        ]);

        return redirect()->back()->with([
            'msg' => __('Category Update Success...'),
            'type' => 'success'
        ]);
    }

    public function category_delete(Request $request, $id)
    {
        if (Faq::where('category_id', $id)->first()) {
            return redirect()->back()->with([
                'msg' => __('You Can Not Delete This Category, It Already Has Faqs Assigned To It...'),
                'type' => 'danger'
            ]);
        }
        FaqCategory::find($id)->delete();
        return redirect()->back()->with([
            'msg' => 'Category Delete Success...',
            'type' => 'danger'
        ]);
    }

    public function category_bulk_action(Request $request){
        FaqCategory::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

}
