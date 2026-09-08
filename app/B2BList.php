<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2BList extends Model
{
    protected $table = 'b2blist';
 protected $casts = [
      'faqs' => 'array'
    ];
    protected $fillable = ['title','search_title','search_industry','search_business_category','search_location','meta_tag','icon_type','img_icon','sr_order','meta_description','schema_code','status','slug','lang','icon','image','description','categories_id','subcategories_id','excerpt','price_plan','data_counts','key_decision','accuracy_commitment','buyerlist','faqs'];


    public function category(){
        return $this->belongsTo('App\ListCategory','categories_id');
    }
}
