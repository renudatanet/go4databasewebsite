<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ServiceSubcategory;
class ListCategory extends Model
{
    protected $table = 'list_categories';
    protected $fillable = ['name','slug','status','meta_tags','meta_description','schema_code','lang','icon_type','icon','img_icon'];
    
      
}
