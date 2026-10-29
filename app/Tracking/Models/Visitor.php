<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** One browser, recognised by its first-party cookie (or, anonymously, for one day). */
class Visitor extends Model
{
    protected $table = 'tracking_visitors';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        'is_anonymous' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'identified_at' => 'datetime',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }

    public function firstPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'first_page_id');
    }

    public function isIdentified(): bool
    {
        return $this->identified_at !== null;
    }

    /** The name to show: their name, else their email, else null. */
    public function displayName(): ?string
    {
        return $this->name ?: $this->email;
    }
}
