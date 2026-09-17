<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets an admin choose which posts appear in the homepage blog strip.
 *
 * Off for every existing post, so the homepage keeps showing the latest three
 * until someone picks; see FrontendController@index.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('blogs', 'show_on_homepage')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->boolean('show_on_homepage')->default(0)->after('breaking_news');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('blogs', 'show_on_homepage')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropColumn('show_on_homepage');
            });
        }
    }
};
