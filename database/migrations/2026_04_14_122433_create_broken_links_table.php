<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
   
    Schema::create('broken_links', function (Blueprint $table) {
    $table->id();
    $table->string('module');
    $table->unsignedBigInteger('record_id');
    $table->string('title')->nullable();
    $table->text('url');
    $table->string('status');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('broken_links');
    }
};
