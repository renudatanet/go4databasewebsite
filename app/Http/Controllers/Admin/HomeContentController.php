<?php

namespace App\Http\Controllers\Admin;

use App\HomePage\HomePageFields;
use App\HomePage\HomePageText;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;

/**
 * Page Manage > Home > Homepage content: the homepage's wording, links,
 * lists, images and section switches, per language, saved as
 * home_page_text_{lang}_{key} (App\HomePage\HomePageText). An empty field
 * gives the page its built-in wording back.
 */
class HomeContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        return view('backend.home-content.index', [
            'languages' => Language::all(),
            'sections' => HomePageFields::sections(),
            'defaults' => HomePageText::DEFAULTS,
        ]);
    }

    public function update(Request $request)
    {
        $types = HomePageFields::types();
        $languages = Language::all()->pluck('slug');

        $rules = [];
        foreach ($languages as $lang) {
            foreach ($types as $key => $type) {
                $rules[HomePageText::optionName($lang, $key)] = HomePageFields::rule($type);
            }
        }
        $this->validate($request, $rules);

        foreach ($languages as $lang) {
            foreach ($types as $key => $type) {
                $name = HomePageText::optionName($lang, $key);
                if ($type === 'switch') {
                    // An unticked box is not sent at all: the section is hidden.
                    update_static_option($name, $request->input($name) === '1' ? '1' : '0');
                } elseif ($request->has($name)) {
                    update_static_option($name, $request->input($name));
                }
            }
        }

        return redirect()->back()->with(['msg' => __('Homepage content saved.'), 'type' => 'success']);
    }
}
