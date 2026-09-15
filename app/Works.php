<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Works extends Model
{
    protected $table = 'works';
    protected $casts = [
      'faqs' => 'array'
    ];
    protected $fillable = [
        'title',
        'gallery',
        'status',
        'lang',
        'categories_id',
        'slug',
        'excerpt',
        'meta_tag',
        'meta_description',
        'schema_code',
        'duration',
        'clients',
        'budget',
        'description',
        'gallery',
        'image',
        'faqs'
    ];

    public function getCategoriesIdAttribute($value){
        return unserialize($value);
    }

}
