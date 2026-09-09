<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerifierItem extends Model
{
    protected $table = 'email_verifier_items';

    protected $fillable = [
        'section',
        'badge_key',
        'icon',
        'title',
        'description',
        'is_highlight',
        'sr_order',
        'lang',
        'status',
    ];

    public const SECTIONS = [
        'trust' => 'Hero Trust Strip',
        'check' => 'What We Check',
        'glossary' => 'Status Glossary',
        'step' => 'Why It Matters',
    ];
}
