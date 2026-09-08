<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ServiceSubcategory;
class ServiceCategory extends Model
{
    protected $table = 'service_categories';
    protected $fillable = ['name','slug','status','meta_tags','meta_description','schema_code','lang','icon_type','icon','img_icon'];
    
     // Relationship
    public function subcategories()
    {
        return $this->hasMany(ServiceSubcategory::class, 'category_id');
    }
}
