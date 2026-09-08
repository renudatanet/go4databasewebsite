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
       Schema::create('scan_progress', function (Blueprint $table) {
    $table->id();
    $table->integer('total')->default(0);
    $table->integer('processed')->default(0);
    $table->boolean('is_running')->default(0);
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
        Schema::dropIfExists('scan_progress');
    }
};
