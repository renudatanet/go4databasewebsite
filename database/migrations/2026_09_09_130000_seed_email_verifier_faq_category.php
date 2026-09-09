<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedEmailVerifierFaqCategory extends Migration
{
    /**
     * Gives the Email Verifier page its own Faq category so its questions are
     * managed in the normal Faq admin screens instead of living in the blade.
     */
    public function up()
    {
        if (!Schema::hasTable('faq_categories') || !Schema::hasTable('faqs')) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $name = 'Email Verifier';

        $existing = DB::table('faq_categories')->where(['name' => $name, 'lang' => $lang])->first();
        if ($existing) {
            update_static_option('ev_' . $lang . '_faq_category_id', $existing->id);
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

        $questions = [
            ['Is checking a single email free?', 'Yes. Single checks on this page are free and need no account. Bulk list verification uses credits from a paid plan.'],
            ['Do you actually send an email to check it?', "No. We open the delivery handshake with the recipient's mail server and stop before any message is sent. Nothing lands in their inbox."],
            ['Why did I get "Unknown" instead of Valid or Invalid?', "Some mail servers block or rate-limit this kind of live check, especially for senders they don't recognise. When we can't confirm the mailbox, we say Unknown rather than guess."],
            ['What does "Catch-All" mean?', "Some domains accept mail for any address at all, even ones that don't exist. When we detect that, your specific mailbox can't be confirmed, so we flag it rather than call it valid."],
            ['Is my data stored?', 'Single checks on this page are processed in real time and are not written to a database.'],
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

        update_static_option('ev_' . $lang . '_faq_category_id', $categoryId);
    }

    public function down()
    {
        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $category = DB::table('faq_categories')->where(['name' => 'Email Verifier', 'lang' => $lang])->first();
        if ($category) {
            DB::table('faqs')->where('category_id', $category->id)->delete();
            DB::table('faq_categories')->where('id', $category->id)->delete();
        }
    }
}
