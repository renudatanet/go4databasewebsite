<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One page opened during a visit, with how long it held attention and how far it was read. */
class PageView extends Model
{
    protected $table = 'tracking_page_views';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['viewed_at' => 'datetime', 'is_entrance' => 'boolean'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
