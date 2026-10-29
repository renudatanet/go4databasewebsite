<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The tracking store. Raw rows only: reports aggregate them through the
 * indexes below (see App\Tracking\Reports). All times are UTC.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_pages', function (Blueprint $table) {
            $table->id();
            $table->char('path_hash', 40)->unique();
            $table->string('path', 500);
            $table->string('title', 255)->nullable();
            $table->string('section', 64)->index();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
        });

        Schema::create('tracking_links', function (Blueprint $table) {
            $table->id();
            $table->char('href_hash', 40)->unique();
            $table->string('href', 1000)->nullable();
            $table->string('host', 191)->nullable();
            $table->string('kind', 16)->index();
            $table->string('label', 255)->nullable();
        });

        Schema::create('tracking_visitors', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->unsignedInteger('sessions_count')->default(0);
            $table->unsignedInteger('page_views_count')->default(0);
            $table->string('first_channel', 16)->nullable();
            $table->string('first_source', 150)->nullable();
            $table->foreignId('first_page_id')->nullable()->constrained('tracking_pages')->nullOnDelete();
            $table->string('name', 150)->nullable();
            $table->string('email', 191)->nullable()->index();
            $table->string('phone', 40)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('identified_via', 32)->nullable();
            $table->timestamp('identified_at')->nullable()->index();
            $table->string('app_user_id', 64)->nullable()->index();
        });

        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('tracking_visitors')->cascadeOnDelete();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('last_activity_at')->nullable()->index();
            $table->foreignId('landing_page_id')->nullable()->constrained('tracking_pages')->nullOnDelete();
            $table->foreignId('exit_page_id')->nullable()->constrained('tracking_pages')->nullOnDelete();
            $table->unsignedSmallInteger('page_views')->default(0);
            $table->unsignedSmallInteger('clicks')->default(0);
            $table->unsignedSmallInteger('events_count')->default(0);
            $table->unsignedInteger('engaged_ms')->default(0);
            $table->boolean('is_anonymous')->default(false);

            $table->string('channel', 16)->default('direct');
            $table->string('source', 150)->nullable();
            $table->string('medium', 150)->nullable();
            $table->string('campaign', 150)->nullable();
            $table->string('term', 150)->nullable();
            $table->string('content', 150)->nullable();
            $table->string('referrer_host', 191)->nullable();
            $table->string('referrer_path', 255)->nullable();

            $table->string('ip', 45)->nullable();
            $table->string('ip_masked', 45)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->decimal('latitude', 8, 4)->nullable();
            $table->decimal('longitude', 8, 4)->nullable();
            $table->unsignedInteger('asn')->nullable();
            $table->string('network_org', 190)->nullable();
            $table->string('network_type', 16)->default('unknown');

            $table->string('device_type', 16)->default('desktop');
            $table->string('browser', 40)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('os', 40)->nullable();
            $table->string('os_version', 20)->nullable();
            $table->unsignedSmallInteger('screen_width')->nullable();
            $table->unsignedSmallInteger('screen_height')->nullable();
            $table->string('language', 16)->nullable();

            $table->index(['visitor_id', 'last_activity_at']);
            $table->index(['country_code', 'started_at']);
            $table->index(['channel', 'started_at']);
        });

        Schema::create('tracking_page_views', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36)->unique();
            $table->foreignId('session_id')->constrained('tracking_sessions')->cascadeOnDelete();
            $table->unsignedBigInteger('visitor_id')->index();
            $table->foreignId('page_id')->constrained('tracking_pages');
            $table->foreignId('previous_page_id')->nullable()->constrained('tracking_pages')->nullOnDelete();
            $table->timestamp('viewed_at')->nullable()->index();
            $table->unsignedInteger('engaged_ms')->default(0);
            $table->unsignedTinyInteger('max_scroll')->default(0);
            $table->boolean('is_entrance')->default(false);

            $table->index(['page_id', 'viewed_at']);
            $table->index(['session_id', 'viewed_at']);
            $table->index(['previous_page_id', 'viewed_at']);
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('tracking_sessions')->cascadeOnDelete();
            $table->unsignedBigInteger('visitor_id')->index();
            $table->unsignedBigInteger('page_view_id')->nullable()->index();
            $table->foreignId('page_id')->nullable()->constrained('tracking_pages')->nullOnDelete();
            $table->string('type', 16);
            $table->string('name', 64);
            $table->foreignId('link_id')->nullable()->constrained('tracking_links')->nullOnDelete();
            $table->string('label', 255)->nullable();
            $table->string('section', 64)->nullable();
            $table->text('properties')->nullable();
            $table->timestamp('occurred_at')->nullable()->index();

            $table->index(['page_id', 'occurred_at']);
            $table->index(['link_id', 'occurred_at']);
            $table->index(['session_id', 'occurred_at']);
            $table->index(['type', 'name', 'occurred_at']);
        });

        Schema::create('tracking_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('tracking_visitors')->cascadeOnDelete();
            $table->string('source', 32);
            $table->string('name', 150)->nullable();
            $table->string('email', 191)->nullable()->index();
            $table->string('phone', 40)->nullable();
            $table->string('company', 150)->nullable();
            $table->string('reference', 64)->nullable();
            $table->timestamp('occurred_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_identities');
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('tracking_page_views');
        Schema::dropIfExists('tracking_sessions');
        Schema::dropIfExists('tracking_visitors');
        Schema::dropIfExists('tracking_links');
        Schema::dropIfExists('tracking_pages');
    }
};
