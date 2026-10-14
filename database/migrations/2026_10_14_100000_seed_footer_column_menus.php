<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Menu;

/**
 * The footer's four link columns were hardcoded inline HTML, so nothing in
 * them could be edited from the admin panel. This lifts the exact same forty
 * links into four menus and points the footer settings at them, after which
 * they are managed from Appearance Settings > Menus Manage like every other
 * menu on the site.
 *
 * Labels are unchanged. The only change to a destination is that the bare
 * "#features" and "#search-section" anchors become "@url/#features" -- those
 * two ids exist only on the homepage, so as bare anchors they did nothing on
 * any other page. Prefixed, they travel to the homepage and scroll there.
 */
class SeedFooterColumnMenus extends Migration
{
    private function columns()
    {
        return [
            [
                'key'   => 'one',
                'title' => 'Footer - Product Features',
                'head'  => 'Product Features',
                'items' => [
                    ['B2B Prospecting Search', '@url/#features'],
                    ['Real-Time Email Verification', '@url/#features'],
                    ['Buyer Intent Data', '@url/#features'],
                    ['Technographics Search', '@url/#features'],
                    ['Chrome Extension', '@url/#features'],
                    ['Contact Database Enrichment', '@url/#features'],
                    ['CRM Integrations', '@url/#features'],
                    ['Developer API Access', '@url/#features'],
                    ['Lookalike Audience Builder', '@url/#features'],
                    ['Career Opportunities', '@url/career'],
                ],
            ],
            [
                'key'   => 'two',
                'title' => 'Footer - Popular Datasets',
                'head'  => 'Popular Datasets',
                'items' => [
                    ['CEO & Executive Email List', '@url/#search-section'],
                    ['Hospital Mailing List', '@url/#search-section'],
                    ['Physicians & Doctors List', '@url/#search-section'],
                    ['VP Sales & Marketing List', '@url/#search-section'],
                    ['CTO & IT Decision Makers', '@url/#search-section'],
                    ['Real Estate Agents List', '@url/#search-section'],
                    ['HR & Recruiter Email List', '@url/#search-section'],
                    ['Software Engineering Leads', '@url/#search-section'],
                    ['Construction Companies', '@url/#search-section'],
                    ['Finance & CFO Email List', '@url/#search-section'],
                ],
            ],
            [
                'key'   => 'three',
                'title' => 'Footer - Healthcare Industry Lists',
                'head'  => 'Healthcare Industry Lists',
                'items' => [
                    ['Hospitals Mailing List', '@url/#search-section'],
                    ['Medical Industry Mailing List', '@url/#search-section'],
                    ['Physicians Mailing List', '@url/#search-section'],
                    ['Cardiologists Mailing List', '@url/#search-section'],
                    ['Healthcare Companies List', '@url/#search-section'],
                    ['Dentists Mailing List', '@url/#search-section'],
                    ['Nurse Practitioners List', '@url/#search-section'],
                    ['Chiropractors Email List', '@url/#search-section'],
                    ['Pharmacies & Pharmacists', '@url/#search-section'],
                    ['Veterinary Medicine List', '@url/#search-section'],
                ],
            ],
            [
                'key'   => 'four',
                'title' => 'Footer - Compliances',
                'head'  => 'Compliances',
                'items' => [
                    ['Privacy Policy', '@url/privacy-policy'],
                    ['Terms of Services', '@url/terms-of-services'],
                    ['GDPR - CCPA', '@url/gdpr-ccpa'],
                    ['ISO & SO2', '@url/iso'],
                    ['Reseller', '@url/reseller'],
                    ['Vendor Code of Conduct', '@url/vendor-code-of-conduct'],
                    ['Database Affiliate', '@url/database-affiliate'],
                    ['Pricing', '@url/pricing'],
                    ['Customized Payment', 'https://paypal.com/'],
                    ['Contact', '@url/contact'],
                ],
            ],
        ];
    }

    public function up()
    {
        if (!Schema::hasTable('menus') || !Schema::hasTable('static_options')) {
            return;
        }

        $lang = function_exists('get_default_language') ? get_default_language() : 'en';

        foreach ($this->columns() as $col) {
            $optionMenu = 'footer_column_' . $col['key'] . '_menu_id';
            $optionHead = 'footer_column_' . $col['key'] . '_title';

            // Only create what is not already there, so a re-run is a no-op
            // and an admin's later edits are never overwritten.
            $menu = Menu::where('title', $col['title'])->first();

            if (is_null($menu)) {
                $content = [];
                $id = 2;
                foreach ($col['items'] as $item) {
                    $content[] = [
                        'ptype'     => 'custom',
                        'id'        => $id++,
                        'menutitle' => '',
                        'antarget'  => '',
                        'icon'      => '',
                        'pname'     => $item[0],
                        'purl'      => $item[1],
                    ];
                }
                $menu = Menu::create([
                    'title'   => $col['title'],
                    'lang'    => $lang,
                    'status'  => '',
                    'content' => json_encode($content),
                ]);
            }

            if (empty(get_static_option($optionMenu))) {
                update_static_option($optionMenu, $menu->id);
            }
            if (empty(get_static_option($optionHead))) {
                update_static_option($optionHead, $col['head']);
            }
        }
    }

    public function down()
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        foreach ($this->columns() as $col) {
            Menu::where('title', $col['title'])->delete();
            update_static_option('footer_column_' . $col['key'] . '_menu_id', '');
            update_static_option('footer_column_' . $col['key'] . '_title', '');
        }
    }
}
