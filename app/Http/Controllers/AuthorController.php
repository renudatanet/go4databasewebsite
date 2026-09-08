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
use App\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Jobs\CheckBrokenLinksJob;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    

    public function index(){
        $all_author = Author::all();
        return view('backend.pages.author.index')->with([
            'all_author' => $all_author,
        ]);
    }
    
    // AJAX DataTables

    public function new_author(){
        $all_language = Language::all();
        return view('backend.pages.author.new')->with([
            'all_languages' => $all_language,
        ]);
    }
    public function store_new_author(Request $request){
        $this->validate($request,[
           'name' => 'required',
           'position' => 'required',
           'author_content' => 'required',
          
           'lang' => 'required',
           'status' => 'required',
           'slug' => 'nullable',
           'meta_tags' => 'nullable|string',
           'meta_description' => 'nullable|string',
           'image' => 'nullable|string|max:191',
        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->name,$request->lang);

        Author::create([
            'name' => $request->name,
            'position' =>$request->position,
            'since_date' =>$request->since_date,
            'slug' => $slug ,
            'content' => $request->author_content,
            'image' => $request->image,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'status' => $request->status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            
            'lang' => $request->lang,
        ]);
        return redirect()->back()->with([
            'msg' => __('New Author  Added...'),
            'type' => 'success'
        ]);
    }
    public function clone_author(Request $request)
    {
        $author_details = Author::find($request->item_id);
        Author::create([
             'name' => $author_details->name,
            'position' =>$author_details->position,
            'since_date' =>$author_details->since_date,
            'slug' => $author_details->slug ,
            'content' => $author_details->author_content,
            'image' => $author_details->image,
            'facebook' => $author_details->facebook,
            'twitter' => $author_details->twitter,
            'linkedin' => $author_details->linkedin,
            'instagram' => $author_details->instagram,
            'status' => $author_details->status,
            'meta_tags' => $author_details->meta_tags,
            'meta_description' => $author_details->meta_description,
        ]);

        return redirect()->back()->with([
            'msg' => __('Author cloned success...'),
            'type' => 'success'
        ]);
    }

    public function edit_author($id){
        $authors = Author::find($id);
        $all_language = Language::all();
        return view('backend.pages.author.edit')->with([
            'author' => $authors,
            'all_languages' => $all_language,
        ]);
    }
    public function update_author(Request $request,$id){
        $this->validate($request,[
              'name' => 'required',
           'position' => 'required',
           'author_content' => 'required',
          
           'lang' => 'required',
           'status' => 'required',
           'slug' => 'nullable',
           'meta_tags' => 'nullable|string',
           'meta_description' => 'nullable|string',
           'image' => 'nullable|string|max:191',

        ]);
        $slug = !empty($request->slug) ? $request->slug : Str::slug($request->name,$request->lang);
        Author::where('id',$id)->update([
             'name' => $request->name,
            'position' =>$request->position,
            'since_date' =>$request->since_date,
            'slug' => $slug ,
            'content' => $request->author_content,
            'image' => $request->image,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'instagram' => $request->instagram,
            'status' => $request->status,
            'meta_tags' => $request->meta_tags,
            'meta_description' => $request->meta_description,
            
            'lang' => $request->lang,
        ]);

        return redirect()->back()->with([
            'msg' => __('Author updated...'),
            'type' => 'success'
        ]);
    }
    public function delete_author(Request $request,$id){
        Author::find($id)->delete();

        return redirect()->back()->with([
            'msg' => __('Author Delete Success...'),
            'type' => 'danger'
        ]);
    }

    
    public function Language_by_slug(Request $request){
        $all_category = BlogCategory::where('lang',$request->lang)->get();

        return response()->json($all_category);
    }

  
    public function bulk_action(Request $request){
        Author::whereIn('id',$request->ids)->delete();
        return response()->json(['status' => 'ok']);
    }

    

    public function slug_check(SlugCheckRequest $request){
        $user_given_slug = $request->slug;
        $query = Events::Author(['slug' => $user_given_slug]);

        return SlugChecker::Check($request,$query);
    }
   

}
