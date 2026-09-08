<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;


class Blog extends Model implements Feedable
{
    protected $table = 'blogs';
 
    protected $fillable = ['title','lang','status','author','author_id','slug','meta_description','schema_code','meta_tags','excerpt','content','blog_categories_id','categories_id','tags','image','user_id','breaking_news','video_url','publish_date','total_visitors','faqs'];

    public function category(){
        return $this->belongsTo('App\BlogCategory','blog_categories_id');
    }
        public function authorData(){
        return $this->belongsTo('App\Author','author_id');
    }
    public function user(){
        return $this->belongsTo('App\Admin','user_id');
    }

    protected $casts = [
      'breaking_news' => 'integer',
      'user_id' => 'integer',
      'faqs' => 'array'
    ];

    public function toFeedItem() : FeedItem
    {
        return FeedItem::create([
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->excerpt,
            'updated' => $this->updated_at,
            'link' => route('frontend.blog.single',$this->slug),
            'author' => $this->author,
        ]);
    }

    public static function getAllFeedItems()
    {
        return Blog::all();
    }
    public function backlinks()
{
    return $this->hasMany(BlogBacklink::class, 'blog_id');
}
}
