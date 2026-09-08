<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;

class WorkSinglePageController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function work_single_page_settings(){
        $all_language = Language::all();
        return view('backend.pages.works.work-single-settings')->with(['all_languages' => $all_language]);
    }
    public function about_page_update_section_manage(Request $request){

        $this->validate($request,[
            'about_page_about_us_section_status' => 'nullable|string',
            'about_page_brand_logo_section_status' => 'nullable|string',
            'about_page_team_member_section_status' => 'nullable|string',
            'about_page_testimonial_section_status' => 'nullable|string',
            'about_page_experience_section_status' => 'nullable|string',
            'about_page_key_feature_section_status' => 'nullable|string',
            'about_page_global_network_section_status' => 'nullable|string',
        ]);
        $fields = [
            'about_page_testimonial_section_status',
            'about_page_about_us_section_status',
            'about_page_brand_logo_section_status',
            'about_page_team_member_section_status',
            'about_page_experience_section_status',
            'about_page_key_feature_section_status',
            'about_page_global_network_section_status',
        ];
        foreach ($fields as $field){
            update_static_option($field,$request->$field);
        }

        return redirect()->back()->with(NexelitHelpers::settings_update());

    }
    public function update_work_single_page_settings(Request $request){

        $all_languages = Language::all();
        foreach ($all_languages as $lang){

            $this->validate($request,[
                'case_study_'.$lang->slug.'_read_more_text' => 'nullable|string',
                'case_study_'.$lang->slug.'_query_title' => 'nullable|string',
                'case_study_'.$lang->slug.'_related_title' => 'nullable|string',
                'case_study_'.$lang->slug.'_gallery_title' => 'nullable|string'
            ]);

            $fields = [
                'case_study_'.$lang->slug.'_read_more_text',
                'case_study_'.$lang->slug.'_query_title',
                'case_study_'.$lang->slug.'_gallery_title',
                'case_study_'.$lang->slug.'_related_title',
                'case_study_'.$lang->slug.'_query_button_text'
            ];
            foreach ($fields as $field){
                update_static_option($field,$request->$field);
            }
        }
            update_static_option('case_study_query_form_mail',$request->case_study_query_form_mail);

        return redirect()->back()->with(['msg' => __('Case Study Page Settings Update...'),'type' => 'success']);
    }
}
