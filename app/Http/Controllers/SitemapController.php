<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\PricePlan;
use App\Testimonial;
use App\Blog;
use App\BlogCategory;
use App\Services;
use App\ServiceCategory;
use App\CaseStudy;
use App\CaseStudyCategory;
use App\Works;
use App\WorksCategory;
use App\Jobs;
use App\Page;
use App\JobsCategory;
use App\Sitemaps\ListSitemap;
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
            // The list pages' industry sitemaps, listed here one by one: a
            // sitemap index may not contain another index, which is what
            // /list_sitemap.xml is (the same industries, for people and for
            // submitting in Search Console on its own).
            ...(new ListSitemap())->industries()->map(fn (array $industry) => [
                'loc' => $industry['loc'],
                'lastmod' => $industry['lastmod'],
            ])->all(),
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

    /** /list_sitemap.xml: the index of the industries' list sitemaps. */
    public function list(ListSitemap $lists)
    {
        $sitemaps = $lists->industries()->map(fn (array $industry) => [
            'loc' => $industry['loc'],
            'lastmod' => $industry['lastmod'],
        ])->all();

        return response()->view('frontend.sitemap.sitemap', compact('sitemaps'))
            ->header('Content-Type', 'application/xml');
    }

    /** /list_sitemap_{industry}.xml: one industry's list pages. */
    public function list_industry(ListSitemap $lists, string $industry)
    {
        $urls = $lists->urls($industry);
        abort_if($urls === null, 404);

        return response()->view('frontend.sitemap.list', compact('urls'))
            ->header('Content-Type', 'application/xml');
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
        // Only pages Google may list: published roles still open (a closed
        // role's page is noindex, a draft's is not public) and published teams.
        $careers = Jobs::where('status', 'publish')->latest()->get()->filter(fn (Jobs $job) => $job->isOpen());
        $careersCategory = JobsCategory::where('status', 'publish')->latest()->get();
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
