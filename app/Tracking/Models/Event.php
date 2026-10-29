<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Something a visitor did on a page: a click ("click"), a named site event
 * such as view_email ("custom"), or a step in the app ("app").
 */
class Event extends Model
{
    public const TYPE_CLICK = 'click';
    public const TYPE_CUSTOM = 'custom';
    public const TYPE_APP = 'app';

    protected $table = 'tracking_events';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['occurred_at' => 'datetime', 'properties' => 'array'];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
