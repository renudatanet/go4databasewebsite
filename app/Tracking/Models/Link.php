<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;

/** A distinct click target: a link destination, or a named button. */
class Link extends Model
{
    protected $table = 'tracking_links';
    public $timestamps = false;
    protected $guarded = [];
}
