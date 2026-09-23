<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEmailFinderItemsTable extends Migration
{
    /**
     * Repeatable blocks on the Email Finder page. Same shape as
     * email_verifier_items so the two Solutions pages are managed the same
     * way, plus bar_width, which the pattern table needs.
     */
    public function up()
    {
        if (!Schema::hasTable('email_finder_items')) {
            Schema::create('email_finder_items', function (Blueprint $table) {
                $table->id();
                $table->string('section');                 // trust | step | pattern
                $table->string('icon')->nullable();        // lucide icon name
                $table->string('title');                   // chip text / step heading / email format
                $table->text('description')->nullable();   // step body / example address
                $table->string('badge_key')->nullable();   // pattern rows: the "how common" label
                // Bar length for the pattern table, 0-100. Deliberately NOT the
                // same number as the percentage label: the bars are scaled so the
                // smaller shares stay visible, so an admin sets the two separately.
                $table->unsignedTinyInteger('bar_width')->nullable();
                $table->tinyInteger('is_highlight')->default(0);
                $table->integer('sr_order')->default(0);
                $table->string('lang')->nullable();
                $table->string('status')->default('publish');
                $table->timestamps();
            });
        }

        // Seed with the copy the page ships today, so moving it into the admin
        // panel changes nothing visually until someone edits it.
        if (DB::table('email_finder_items')->count() > 0) {
            return;
        }

        $lang = optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en';
        $now = now();
        $rows = [];
        $add = function ($section, $icon, $title, $description = null, $badge = null, $bar = null, $highlight = 0) use (&$rows, $lang, $now) {
            $rows[] = [
                'section' => $section,
                'icon' => $icon,
                'title' => $title,
                'description' => $description,
                'badge_key' => $badge,
                'bar_width' => $bar,
                'is_highlight' => $highlight,
                'sr_order' => count($rows),
                'lang' => $lang,
                'status' => 'publish',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };

        $add('trust', 'check', 'No signup needed');
        $add('trust', 'check', 'Checked against the live mail server');
        $add('trust', 'check', 'We never send a test email');

        $add('step', null, 'Build the likely addresses', 'From the name and domain we generate the formats companies actually use, ordered by how common each one is in the real world.');
        $add('step', null, 'Ask the mail server', "We open a conversation with the company's mail server and ask whether each address would be accepted, starting with the most likely. Nothing is ever delivered.");
        $add('step', null, 'Tell you what we actually know', 'If the server confirms a mailbox, you get it marked confirmed. If the server refuses to answer, or accepts everything, we say so instead of dressing a guess up as a fact.');

        $add('pattern', null, 'first.last@', 'jane.doe@acme.com', '~60%', 100);
        $add('pattern', null, 'first@', 'jane@acme.com', '~15–20%', 30);
        $add('pattern', null, 'flast@', 'jdoe@acme.com', '~10–15%', 21);
        $add('pattern', null, 'everything else', 'janedoe@, j.doe@, doe.jane@…', '<10%', 16, 1);

        DB::table('email_finder_items')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('email_finder_items');
    }
}
