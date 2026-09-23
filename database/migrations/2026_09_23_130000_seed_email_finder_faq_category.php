<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedEmailFinderFaqCategory extends Migration
{
    /**
     * Gives the Email Finder page its own Faq category, so its questions are
     * managed in the normal Faq admin screens instead of living in a PHP
     * array inside FrontendController. Same approach as the Email Verifier.
     */
    public function up()
    {
        if (!Schema::hasTable('faq_categories') || !Schema::hasTable('faqs')) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $name = 'Email Finder';

        $existing = DB::table('faq_categories')->where(['name' => $name, 'lang' => $lang])->first();
        if ($existing) {
            update_static_option('ef_' . $lang . '_faq_category_id', $existing->id);
            return;
        }

        $now = now();
        $categoryId = DB::table('faq_categories')->insertGetId([
            'name' => $name,
            'lang' => $lang,
            'status' => 'publish',
            'sr_order' => (int) DB::table('faq_categories')->max('sr_order') + 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // The questions the page shipped with, moved across unchanged.
        $questions = [
            ['Is the Email Finder free?', 'Yes. You can look up addresses here without an account, a card, or a trial. Bulk lookups and exporting whole lists are part of the paid Go4Database platform.'],
            ['How accurate is it?', 'When we mark an address "Confirmed", the company\'s own mail server told us that mailbox exists, which is as close to certain as you get without sending an email. When it says "Best guess", we are showing the most common format because the server would not confirm either way, and we label it that way rather than pretending otherwise.'],
            ['What does "catch-all" mean?', 'Some companies configure their mail server to accept mail sent to any address at their domain, real or not. On those domains no single address can be confirmed, because the server says yes to everything, so we tell you it is a catch-all instead of claiming we verified something.'],
            ['Do you send a test email to the person?', 'No. We start the delivery conversation with the mail server and ask whether the address would be accepted, then stop before anything is sent. The person never receives a message and never sees the check.'],
            ['Why can\'t it find some addresses?', 'Large providers like Microsoft and Google often refuse to answer address checks from senders they do not recognise. When that happens we cannot confirm anything, so you get the most likely pattern marked as a guess. The company may also simply not use any common format.'],
            ['Can I look up a Gmail or Yahoo address?', 'No, and that is deliberate. Personal inboxes do not follow a company pattern, so there is nothing to work out. The finder is built for work addresses at company domains.'],
        ];

        $rows = [];
        foreach ($questions as $q) {
            $rows[] = [
                'title' => $q[0],
                'description' => $q[1],
                'lang' => $lang,
                'status' => 'publish',
                'is_open' => '',
                'category_id' => $categoryId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('faqs')->insert($rows);

        update_static_option('ef_' . $lang . '_faq_category_id', $categoryId);
    }

    public function down()
    {
        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $category = DB::table('faq_categories')->where(['name' => 'Email Finder', 'lang' => $lang])->first();
        if ($category) {
            DB::table('faqs')->where('category_id', $category->id)->delete();
            DB::table('faq_categories')->where('id', $category->id)->delete();
        }
    }
}
