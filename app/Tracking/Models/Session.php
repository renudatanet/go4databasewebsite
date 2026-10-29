<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** One visit: a run of activity that ends after 30 minutes without any. */
class Session extends Model
{
    protected $table = 'tracking_sessions';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'is_anonymous' => 'boolean',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'landing_page_id');
    }

    public function exitPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'exit_page_id');
    }

    public function pageViews(): HasMany
    {
        return $this->hasMany(PageView::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /** "Austin, Texas, United States", leaving out the parts that are unknown. */
    public function placeName(): string
    {
        $parts = array_filter([$this->city, $this->region, $this->country]);

        return $parts !== [] ? implode(', ', array_unique($parts)) : 'Unknown location';
    }
}
