<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PricePlan;
use App\Testimonial;
use App\Blog;
use App\BlogCategory;
use App\Services;
use App\B2BList;
use App\ListCategory;
use App\ServiceCategory;
use App\CaseStudy;
use App\CaseStudyCategory;
use App\Works;
use App\WorksCategory;
use App\Jobs;
use App\Page;
use App\JobsCategory;
use App\TeamMember;
use App\KeyFeatures;
use Carbon\Carbon;
class SitemapController extends Controller
{
    
    public function index()
    {
        $sitemaps = [
            [
                'loc' => url('/pages_sitemap.xml'),
                'lastmod' => $this->getPagesLastModified(),
            ],
            [
                'loc' => url('/b2b_sitemap.xml'),
                'lastmod' => $this->getB2BLastModified(),
            ],
            [
                'loc' => url('/blog_sitemap.xml'),
                'lastmod' => $this->getBlogLastModified(),
            ],
            [
                'loc' => url('/b2c_sitemap.xml'),
                'lastmod' => $this->getB2CLastModified(),
            ],
             [
                'loc' => url('/list_sitemap.xml'),
                'lastmod' => $this->getListLastModified(),
            ],
            [
                'loc' => url('/case_study_sitemap.xml'),
                'lastmod' => $this->getCaseStudyLastModified(),
            ],
            [
                'loc' => url('/career_sitemap.xml'),
                'lastmod' => $this->getCareerLastModified(),
            ],

        ];

        return response()->view('frontend.sitemap.sitemap', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }
     private function getBlogLastModified()
    {
        return $this->formatLastMod(Blog::max('updated_at'));
    }
    
       private function getB2BLastModified()
    {
        return $this->formatLastMod(Works::max('updated_at'));
    }
        private function getPagesLastModified()
    {
        return $this->formatLastMod(Page::max('updated_at'));
    }
     private function getB2CLastModified()
    {
        return $this->formatLastMod(Services::max('updated_at'));
    }
     private function getListLastModified()
    {
        return $this->formatLastMod(B2BList::max('updated_at'));
    }
         private function getCaseStudyLastModified()
    {
        return $this->formatLastMod(CaseStudy::max('updated_at'));
    }
         private function getCareerLastModified()
    {
        return $this->formatLastMod(jobs::max('updated_at'));
    }
    
    private function formatLastMod($date)
    {
    return $date ? Carbon::parse($date)->toAtomString() : null;
    }
    public function b2b()
    {
        $work = Works::latest()->get();
     
      $worksCategory= WorksCategory::latest()->get();
        $xml = response()->view('frontend.sitemap.b2b', [
            'b2b' => $work,
            'b2bcategory' => $worksCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
    
    public function b2c()
    {
        $service = Services::latest()->get();
     
      $serviceCategory = ServiceCategory::latest()->get();
//      dd($serviceCategory);
        $xml = response()->view('frontend.sitemap.b2c', [
            'b2c' => $service,
            'serviceCategory' => $serviceCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }

        public function list()
    {
        $service = B2BList::latest()->get();
     
      $serviceCategory = ListCategory::latest()->get();
//      dd($serviceCategory);
        $xml = response()->view('frontend.sitemap.list', [
            'list' => $service,
            'listCategory' => $serviceCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
       
               
     public function blog()
    {
        $blogs = Blog::latest()->get();
        
        $blogsCategory = BlogCategory::latest()->get();
     
        $xml = response()->view('frontend.sitemap.blog', [
            'blogs' => $blogs,
            'blogsCategory' => $blogsCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
    
    public function case_study()
    {
        $casestudies = CaseStudy::latest()->get();
     
        $casestudyCategory = CaseStudyCategory::latest()->get();
        $xml = response()->view('frontend.sitemap.casestudies', [
            'casestudies' => $casestudies,
            'caseCategory' => $casestudyCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
    
      public function career()
    {
        $careers = jobs::latest()->get();
     $careersCategory = JobsCategory::latest()->get();
        $xml = response()->view('frontend.sitemap.career', [
            'careers' => $careers,
            'careersCategory' => $careersCategory
        ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
    
    
        public function pages()
    {
     
      $pages = Page::latest()->get();
     // dd($pages);
        $xml = response()->view('frontend.sitemap.pages', [
            'pages' => $pages
            ]);

        $xml->header('Content-Type', 'text/xml');

        return $xml;
    }
    
    
    

}
