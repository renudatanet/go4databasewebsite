<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailFinderItem extends Model
{
    protected $table = 'email_finder_items';

    protected $fillable = [
        'section',
        'icon',
        'title',
        'description',
        'badge_key',
        'bar_width',
        'is_highlight',
        'sr_order',
        'lang',
        'status',
    ];

    public const SECTIONS = [
        'trust' => 'Tool Trust Line',
        'step' => 'How It Works',
        'pattern' => 'Email Format Table',
    ];
}
