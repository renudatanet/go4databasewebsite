<?php

namespace App\Tracking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A moment a visitor told us who they are: a form, the newsletter, or the app. */
class Identity extends Model
{
    public const CONTACT_FORM = 'contact_form';
    public const QUOTE_FORM = 'quote_form';
    public const NEWSLETTER = 'newsletter';
    public const JOB_APPLICATION = 'job_application';
    public const APP_SIGNUP = 'app_signup';
    public const APP_LOGIN = 'app_login';
    // The list pages' forms (App\FormLeads\FormKind values).
    public const LIST_EXPERT = 'list_expert';
    public const LIST_SAMPLE = 'list_sample';
    public const LIST_SIGNUP = 'list_signup';
    public const HOME_EXPERT = 'home_expert';

    public const LABELS = [
        self::CONTACT_FORM => 'Contact form',
        self::QUOTE_FORM => 'Quote request',
        self::NEWSLETTER => 'Newsletter',
        self::JOB_APPLICATION => 'Job application',
        self::APP_SIGNUP => 'Signed up in the app',
        self::APP_LOGIN => 'Logged in to the app',
        self::LIST_EXPERT => 'List page: talk to an expert',
        self::LIST_SAMPLE => 'List page: sample request',
        self::LIST_SIGNUP => 'List page: free credits sign-up',
        self::HOME_EXPERT => 'Homepage: talk to an expert',
    ];

    protected $table = 'tracking_identities';
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['occurred_at' => 'datetime'];

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }
}
