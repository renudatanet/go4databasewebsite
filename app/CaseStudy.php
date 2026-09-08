<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use HasFactory;
    protected $table = 'case_studies';
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
        'image'
    ];

    public function getCategoriesIdAttribute($value){
        return unserialize($value);
    }
}
