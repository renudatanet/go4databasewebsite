<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobsCategory extends Model
{
    protected $table = 'jobs_categories';
    protected $fillable = ['title','status','lang','slug','meta_description','meta_tags'];

    /** The category's address under /career: its own slug, or one made from its name. */
    public function urlSlug(): string
    {
        return !empty($this->slug) ? $this->slug : Str::slug($this->title);
    }
}
