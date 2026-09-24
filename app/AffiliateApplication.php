<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliateApplication extends Model
{
    protected $table = 'affiliate_applications';

    protected $fillable = [
        'name',
        'email',
        'site',
        'promotion',
        'status',
        'admin_note',
        'ip',
    ];

    public const STATUSES = [
        'new' => 'New',
        'approved' => 'Approved',
        'declined' => 'Declined',
    ];
}
