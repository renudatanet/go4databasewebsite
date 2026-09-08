<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    protected $table = 'faq_categories';
    protected $fillable = ['name', 'lang', 'status', 'sr_order'];

    public function faqs()
    {
        return $this->hasMany('App\Faq', 'category_id');
    }
}
