<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Author;
use App\Blog;

class Author extends Model 
{
     protected $table = 'author_new';
         protected $fillable = [
        'name',
        'position',
        'slug',
        'content',
        'image',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'lang',
        'status',
        'meta_tags',
        'meta_description'
    ];
public function blogs()
{
    return $this->hasMany(Blog::class, 'author_id');
}

}
