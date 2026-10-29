<?php

namespace App\Tracking\Maintenance;

use App\Tracking\Models\Visitor;
use Illuminate\Support\Facades\DB;

/** "Forget this visitor": removes every row held about them. */
final class VisitorEraser
{
    public function erase(Visitor $visitor): void
    {
        DB::transaction(function () use ($visitor) {
            $sessionIds = DB::table('tracking_sessions')->where('visitor_id', $visitor->id)->pluck('id');
            DB::table('tracking_events')->whereIn('session_id', $sessionIds)->delete();
            DB::table('tracking_page_views')->whereIn('session_id', $sessionIds)->delete();
            DB::table('tracking_sessions')->where('visitor_id', $visitor->id)->delete();
            DB::table('tracking_identities')->where('visitor_id', $visitor->id)->delete();
            $visitor->delete();
        });
    }
}
