<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingFieldsToPricePlansTable extends Migration
{
    public function up()
    {
        Schema::table('price_plans', function (Blueprint $table) {
            $table->string('monthly_price')->nullable()->after('price');
            $table->string('annual_price')->nullable()->after('monthly_price');
            $table->string('monthly_original_price')->nullable()->after('annual_price');
            $table->string('annual_original_price')->nullable()->after('monthly_original_price');
            $table->string('monthly_bill_text')->nullable()->after('annual_original_price');
            $table->string('annual_bill_text')->nullable()->after('monthly_bill_text');
        });
    }

    public function down()
    {
        Schema::table('price_plans', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_price',
                'annual_price',
                'monthly_original_price',
                'annual_original_price',
                'monthly_bill_text',
                'annual_bill_text',
            ]);
        });
    }
}
