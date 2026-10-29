<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;

/** A page of the site, keyed by its path. Created the first time it is viewed. */
class Page extends Model
{
    protected $table = 'tracking_pages';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['first_seen_at' => 'datetime', 'last_seen_at' => 'datetime'];

    public static function hashPath(string $path): string
    {
        return sha1($path);
    }
}
