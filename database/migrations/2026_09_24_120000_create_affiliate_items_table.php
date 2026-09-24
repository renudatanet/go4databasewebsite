<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateItemsTable extends Migration
{
    /**
     * Repeatable blocks on the Affiliate page. Same shape as
     * email_finder_items so all the Solutions pages are managed alike, plus
     * alt_text, which the affiliate-vs-reseller table needs for its second
     * column.
     */
    public function up()
    {
        if (!Schema::hasTable('affiliate_items')) {
            Schema::create('affiliate_items', function (Blueprint $table) {
                $table->id();
                $table->string('section');                // trust | step | compare | who | get
                $table->string('icon')->nullable();       // lucide icon name, or a single letter
                $table->string('title');
                $table->text('description')->nullable();  // body text, or the affiliate column
                $table->text('alt_text')->nullable();     // compare rows: the reseller column
                $table->string('badge_key')->nullable();
                $table->tinyInteger('is_highlight')->default(0);
                $table->integer('sr_order')->default(0);
                $table->string('lang')->nullable();
                $table->string('status')->default('publish');
                $table->timestamps();
            });
        }

        // Seed with the copy the page ships with, so moving it into the admin
        // panel changes nothing visually until someone edits it.
        if (DB::table('affiliate_items')->count() > 0) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $now = now();
        $rows = [];
        $add = function ($section, $icon, $title, $description = null, $alt = null) use (&$rows, $lang, $now) {
            $rows[] = [
                'section' => $section,
                'icon' => $icon,
                'title' => $title,
                'description' => $description,
                'alt_text' => $alt,
                'badge_key' => null,
                'is_highlight' => 0,
                'sr_order' => count($rows),
                'lang' => $lang,
                'status' => 'publish',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        $add('trust', 'check', 'Free to join');
        $add('trust', 'check', 'Recurring commission');
        $add('trust', 'check', 'Paid monthly');

        $add('step', null, 'Apply in two minutes', 'Tell us where your audience is. We review by hand, usually the same working day, and there is no minimum following.');
        $add('step', null, 'Share your link', 'You get a tracked link plus banners, screenshots and copy you can lift straight into a post, newsletter or video description.');
        $add('step', null, 'Get paid every month', 'Watch clicks, signups and earnings in your dashboard. We pay out monthly for as long as your referrals keep their plan.');

        $add('compare', null, 'Who owns the customer', 'We do. You refer and hand off.', 'You do. You hold the billing relationship.');
        $add('compare', null, 'Who does the selling', 'Nobody. A link does the work.', 'You sell, quote and close.');
        $add('compare', null, 'Who gives support', 'Our team.', 'You, as first line.');
        $add('compare', null, 'How you earn', 'Commission on their payments.', 'Margin on wholesale pricing.');
        $add('compare', null, 'Effort to start', 'Minutes. Apply and share.', 'A conversation with our partnerships team.');

        $add('who', 'A', 'Agencies', 'You already run outreach for clients. Refer the data source you use anyway.');
        $add('who', 'C', 'Creators', 'Newsletters, YouTube and podcasts covering sales, marketing or startups.');
        $add('who', 'K', 'Consultants', 'You advise on pipeline and GTM. This fits into advice you already give.');
        $add('who', 'S', 'Software partners', 'CRM, outreach and automation tools whose users constantly need contacts.');

        $add('get', null, 'Tracked referral link', 'One link, works anywhere, attributes every signup back to you.');
        $add('get', null, 'Live dashboard', 'Clicks, signups, conversions and confirmed balance, updated as they happen.');
        $add('get', null, 'Ready-made creative', 'Banners, product screenshots and copy blocks you can use unchanged.');
        $add('get', null, 'A named contact', 'A real person to email about a campaign or a payout question.');
        $add('get', null, 'Deal support', 'For bigger referrals we will join a call and help you close it.');
        $add('get', null, 'No earnings cap', 'No ceiling, no limit on referrals, no clawback on renewals.');

        DB::table('affiliate_items')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_items');
    }
}
