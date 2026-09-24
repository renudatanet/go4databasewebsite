<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedAffiliateFaqCategory extends Migration
{
    /**
     * Gives the Affiliate page its own Faq category, so its questions are
     * managed in the normal Faq admin screens. Same approach as the Email
     * Verifier and Email Finder pages.
     *
     * The answers here deliberately describe the mechanism and never quote a
     * rate, a cookie window or a payout figure: those are set once in
     * Affiliate Settings and rendered from there, so there is only ever one
     * copy of them to keep true.
     */
    public function up()
    {
        if (!Schema::hasTable('faq_categories') || !Schema::hasTable('faqs')) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $name = 'Affiliate';

        $existing = DB::table('faq_categories')->where(['name' => $name, 'lang' => $lang])->first();
        if ($existing) {
            update_static_option('af_' . $lang . '_faq_category_id', $existing->id);
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
            ['Does it cost anything to join?', 'No. Joining is free, there is nothing to buy, and there are no targets you have to hit to stay in the programme.'],
            ['When exactly do I get paid?', 'Commission is confirmed once a referral\'s payment clears and the refund window closes. Your confirmed balance is paid out monthly, provided you are above the minimum payout shown above.'],
            ['Do I keep earning if my referral renews?', 'Yes, for as long as the commission term above lasts. You are paid on their renewals during that period, not only on their first payment.'],
            ['Can I refer customers outside my country?', 'Yes. Go4Database sells worldwide and referrals count wherever the customer is based.'],
            ['What happens if someone asks for a refund?', 'Commission on a refunded payment is reversed, because it was never really earned. This is why there is a short holding period before a balance is marked confirmed.'],
            ['Can I bid on the Go4Database brand name in ads?', 'No. Paid search on our brand name and close variants is not allowed, because it competes with our own ads and bids up the cost of traffic we would have received anyway.'],
            ['How do I know a signup was credited to me?', 'Every click on your link is tracked and shown in your dashboard, along with signups, conversions and the commission each one earned.'],
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

        update_static_option('af_' . $lang . '_faq_category_id', $categoryId);
    }

    public function down()
    {
        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $category = DB::table('faq_categories')->where(['name' => 'Affiliate', 'lang' => $lang])->first();
        if ($category) {
            DB::table('faqs')->where('category_id', $category->id)->delete();
            DB::table('faq_categories')->where('id', $category->id)->delete();
        }
    }
}
