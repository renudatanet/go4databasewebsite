<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ServiceCategory;

class ServiceSubcategory extends Model
{
    protected $table = 'service_subcategories';
    protected $fillable = ['category_id','name','slug','status','meta_tags','meta_description','schema_code','lang','icon_type','icon','img_icon'];
    
    // Relationship
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
