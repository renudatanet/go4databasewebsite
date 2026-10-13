<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimiseB2blistForListPages extends Migration
{
    /**
     * Index helper: adding an index that already exists aborts the migration,
     * and these tables predate this file on every environment.
     */
    private function hasIndex($table, $index)
    {
        return count(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index])) > 0;
    }

    public function up()
    {
        if (Schema::hasTable('b2blist')) {
            // key_decision holds "Label=Value" pairs joined by commas. The live
            // healthcare/staffing-agencies value is 257 characters, which does
            // not fit varchar(191): MySQL truncates it silently outside strict
            // mode, so the last decision-maker rows disappear from the page.
            DB::statement('ALTER TABLE `b2blist` MODIFY `key_decision` TEXT NULL');

            Schema::table('b2blist', function (Blueprint $table) {
                // Every list page load looks the row up by slug; the table had
                // no index but the primary key, so this was a full scan.
                if (!$this->hasIndex('b2blist', 'b2blist_slug_index')) {
                    $table->index('slug', 'b2blist_slug_index');
                }
                if (!$this->hasIndex('b2blist', 'b2blist_categories_id_index')) {
                    $table->index('categories_id', 'b2blist_categories_id_index');
                }
                if (!$this->hasIndex('b2blist', 'b2blist_status_index')) {
                    $table->index('status', 'b2blist_status_index');
                }
            });
        }

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                // The related-blogs query filters on lang + status.
                if (!$this->hasIndex('blogs', 'blogs_lang_status_index')) {
                    $table->index(['lang', 'status'], 'blogs_lang_status_index');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('b2blist')) {
            Schema::table('b2blist', function (Blueprint $table) {
                if ($this->hasIndex('b2blist', 'b2blist_slug_index')) {
                    $table->dropIndex('b2blist_slug_index');
                }
                if ($this->hasIndex('b2blist', 'b2blist_categories_id_index')) {
                    $table->dropIndex('b2blist_categories_id_index');
                }
                if ($this->hasIndex('b2blist', 'b2blist_status_index')) {
                    $table->dropIndex('b2blist_status_index');
                }
            });

            DB::statement('ALTER TABLE `b2blist` MODIFY `key_decision` VARCHAR(191) NULL');
        }

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                if ($this->hasIndex('blogs', 'blogs_lang_status_index')) {
                    $table->dropIndex('blogs_lang_status_index');
                }
            });
        }
    }
}
