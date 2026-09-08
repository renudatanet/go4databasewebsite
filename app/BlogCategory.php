<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table ='blog_categories';
    protected $fillable = ['name','slug','lang','status','meta_tags','meta_description','schema_code','image'];
}
