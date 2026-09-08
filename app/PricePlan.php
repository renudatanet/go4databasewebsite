<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PricePlan extends Model
{
    protected $table = 'price_plans';
    protected $fillable = ['title','sub_title','url_status','price','monthly_price','annual_price','monthly_original_price','annual_original_price','monthly_bill_text','annual_bill_text','type','status','highlight','lang','features','btn_text','btn_url','categories_id'];

    public function category(){
        return $this->belongsTo('App\PricePlanCategory','categories_id');
    }
}
