<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateItem extends Model
{
    protected $table = 'affiliate_items';

    protected $fillable = [
        'section',
        'icon',
        'title',
        'description',
        'alt_text',
        'badge_key',
        'is_highlight',
        'sr_order',
        'lang',
        'status',
    ];

    public const SECTIONS = [
        'trust' => 'Hero Tick Line',
        'step' => 'How It Works',
        'compare' => 'Affiliate vs Reseller Table',
        'who' => 'Who It Suits',
        'get' => 'What Partners Get',
    ];
}
