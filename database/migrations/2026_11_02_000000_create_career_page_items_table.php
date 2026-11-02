<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The repeatable blocks of the "Working at Go4Database" page (Jobs > Working
 * at Go4Database): what the company does, how it works, quotes from the
 * team, and the offices. Seeded with copy drawn from the site's own pages
 * and job posts (and the one office they name), so the page says something
 * true before anyone edits it; quotes start empty, as they must be real.
 */
class CreateCareerPageItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('career_page_items')) {
            Schema::create('career_page_items', function (Blueprint $table) {
                $table->id();
                $table->string('section', 32);              // offering | value | quote | office
                $table->string('title');                    // product | value | name | city
                $table->string('subtitle')->nullable();     // - | - | their role | the address
                $table->text('body')->nullable();           // the text | the quote
                $table->string('image')->nullable();        // media library id: their photo | the office
                $table->tinyInteger('cutout')->default(0);  // quotes: the photo is a cut-out (transparent background)
                $table->string('link')->nullable();         // offerings: the page it opens, e.g. /email-verifier
                $table->string('filter')->nullable();       // offices: the word that picks its roles, e.g. Noida
                $table->integer('sr_order')->default(0);
                $table->string('lang', 32)->nullable();
                $table->string('status', 32)->default('publish');
                $table->timestamps();
                $table->index(['section', 'lang', 'status']);
            });
        }

        if (DB::table('career_page_items')->exists()) {
            return;
        }
        $lang = Schema::hasTable('languages')
            ? (optional(DB::table('languages')->where('default', 1)->first())->slug ?: 'en')
            : 'en';
        $now = now();
        $rows = [];
        $add = function (string $section, string $title, ?string $body, array $extra = []) use (&$rows, $lang, $now) {
            $rows[] = $extra + [
                'section' => $section, 'title' => $title, 'subtitle' => null, 'body' => $body,
                'image' => null, 'cutout' => 0, 'link' => null, 'filter' => null, 'sr_order' => count($rows),
                'lang' => $lang, 'status' => 'publish', 'created_at' => $now, 'updated_at' => $now,
            ];
        };

        $add('offering', 'Verified B2B contact data', 'Email lists and direct dials for decision makers across industries, verified before they reach you.', ['link' => '/list']);
        $add('offering', 'Email Verifier', 'Checks the syntax, the domain and the live mailbox, so teams send only to addresses that exist.', ['link' => '/email-verifier']);
        $add('offering', 'Email Finder', 'Finds a person’s work email from their name and company domain, and says honestly when it is a best guess.', ['link' => '/email-finder']);
        $add('offering', 'Lead search', 'Search by job title, industry and location, and export whole lists instead of looking people up one by one.', ['link' => '/']);

        $add('value', 'Own your results', 'Take ownership from early on. Small teams here are trusted with real decisions, not just tasks.');
        $add('value', 'Grow as fast as your work', 'Results, not titles, decide how quickly you move up, and in sales roles the more you close, the more you earn.');
        $add('value', 'Low hierarchy', 'Work directly with the founders, share ideas openly and see them go live.');
        $add('value', 'A global outlook', 'Our customers are sales and marketing teams across the US, the UK and beyond.');

        $add('office', 'Noida, India', null, ['subtitle' => 'Sector 2, Noida, Uttar Pradesh', 'filter' => 'Noida']);

        DB::table('career_page_items')->insert($rows);
    }

    public function down()
    {
        Schema::dropIfExists('career_page_items');
    }
}
