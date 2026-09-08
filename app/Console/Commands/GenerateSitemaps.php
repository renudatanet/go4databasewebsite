<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Blog;
use App\Models\B2bPage;
use App\Models\Career;

class GenerateSitemaps extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate parent index and child sitemaps natively';

    public function handle()
    {
        $publicPath = public_path();

        // 1. Build child sitemaps (Example: Blog)
        if (class_exists(Blog::class)) {
            $blogs = Blog::all();
            $blogXml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $blogXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
            foreach ($blogs as $blog) {
                $lastmod = $blog->updated_at ? $blog->updated_at->toAtomString() : now()->toAtomString();
                $blogXml .= "  <url>\n";
                $blogXml .= "    <loc>".url("/blog/{$blog->slug}")."</loc>\n";
                $blogXml .= "    <lastmod>{$lastmod}</lastmod>\n";
                $blogXml .= "  </url>\n";
            }
            $blogXml .= '</urlset>';
            file_put_contents($publicPath . '/blog_sitemap.xml', $blogXml);
        }

        // 2. Build Parent Sitemap Index (sitemap.xml)
        $childFiles = [
            'pages_sitemap.xml',
            'b2b_sitemap.xml',
            'blog_sitemap.xml',
            'b2c_sitemap.xml',
            'list_sitemap.xml',
            'case_study_sitemap.xml',
            'career_sitemap.xml',
        ];

        $parentXml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $parentXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($childFiles as $file) {
            $filePath = $publicPath . '/' . $file;
            $lastmod = file_exists($filePath) ? date('c', filemtime($filePath)) : now()->toAtomString();

            $parentXml .= "  <sitemap>\n";
            $parentXml .= "    <loc>https://www.go4database.com/{$file}</loc>\n";
            $parentXml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $parentXml .= "  </sitemap>\n";
        }
        $parentXml .= '</sitemapindex>';

        file_put_contents($publicPath . '/sitemap.xml', $parentXml);

        $this->info('Parent and child sitemaps generated successfully!');
    }
}