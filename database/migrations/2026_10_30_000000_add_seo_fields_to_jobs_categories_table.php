<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Job categories have their own page (/career/{slug}), so they need an
 * address and SEO text. Live's table already has these columns (added by
 * hand, never written by the admin until now); this adds only those missing,
 * so it is safe on both.
 */
class AddSeoFieldsToJobsCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('jobs_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('jobs_categories', 'slug')) {
                $table->string('slug', 191)->nullable();
            }
            if (!Schema::hasColumn('jobs_categories', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (!Schema::hasColumn('jobs_categories', 'meta_tags')) {
                $table->text('meta_tags')->nullable();
            }
        });
    }

    /** Nothing is dropped: on live these columns predate this migration and hold its category addresses. */
    public function down()
    {
    }
}
