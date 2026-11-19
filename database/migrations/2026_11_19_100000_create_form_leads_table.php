<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Form leads: every submission of the list pages' forms (talk to an expert,
 * sample request, free-credits sign-up), kept so no lead depends on an email
 * arriving. See App\FormLeads.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('form_leads')) {
            return;
        }
        Schema::create('form_leads', function (Blueprint $table) {
            $table->id();
            $table->string('form', 40)->index();          // App\FormLeads\FormKind
            $table->string('name', 191)->nullable();
            $table->string('email', 191)->nullable()->index();
            $table->string('phone', 60)->nullable();
            $table->string('company', 500)->nullable();   // website / business details
            $table->text('message')->nullable();
            $table->unsignedBigInteger('list_id')->nullable()->index();
            $table->string('list_title', 191)->nullable(); // as it was when the form was sent
            $table->string('page_url', 500)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('status', 20)->default('new')->index(); // new, contacted, done
            $table->boolean('emailed')->nullable();       // null: no email for this form
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_leads');
    }
};
