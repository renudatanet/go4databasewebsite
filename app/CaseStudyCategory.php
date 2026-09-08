<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudyCategory extends Model
{
    use HasFactory;
    protected $table = 'case_study_categories';
    protected $fillable = ['name','status','meta_tags','meta_description','schema_code','lang'];
}
