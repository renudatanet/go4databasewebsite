<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateApplicationsTable extends Migration
{
    /**
     * Applications sent from the form on /affiliate.
     *
     * The page has to do something when someone clicks Apply. Until there is
     * a real affiliate platform to hand people to, applications land here and
     * are read in the admin panel. Setting an Apply Button Link in Affiliate
     * Settings points the buttons at that platform instead and this form
     * stops being used, without any code change.
     */
    public function up()
    {
        if (Schema::hasTable('affiliate_applications')) {
            return;
        }

        Schema::create('affiliate_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('site')->nullable();       // website, channel or profile
            $table->text('promotion')->nullable();    // how they plan to promote
            $table->string('status')->default('new'); // new | approved | declined
            $table->text('admin_note')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliate_applications');
    }
}
