<?php

use App\Tracking\Http\Controllers\Admin\ClicksController;
use App\Tracking\Http\Controllers\Admin\LiveController;
use App\Tracking\Http\Controllers\Admin\LocationsController;
use App\Tracking\Http\Controllers\Admin\OverviewController;
use App\Tracking\Http\Controllers\Admin\PagesController;
use App\Tracking\Http\Controllers\Admin\SettingsController;
use App\Tracking\Http\Controllers\Admin\SourcesController;
use App\Tracking\Http\Controllers\Admin\VisitorsController;
use App\Tracking\Http\Controllers\AppEventController;
use App\Tracking\Http\Controllers\CollectController;
use App\Tracking\Http\Middleware\MarkBrowserAsInternal;
use Illuminate\Support\Facades\Route;

/*
| Public endpoints. Deliberately outside the "web" group: no session, CSRF
| or cookie encryption. Both are rate limited per IP.
*/
Route::post(config('tracking.routes.collect'), CollectController::class)
    ->middleware('throttle:tracking-collect')
    ->name('tracking.collect');

Route::post(config('tracking.routes.app_event'), AppEventController::class)
    ->middleware('throttle:tracking-app')
    ->name('tracking.app-event');

/*
| Admin > Tracking. Behind the admin login and the "Tracking" permission.
| Opening any of these screens also stops this browser being tracked.
*/
Route::prefix(config('tracking.routes.admin_prefix'))
    ->middleware(['web', 'auth:admin', 'adminPermissionCheck:Tracking', 'setlang:backend', MarkBrowserAsInternal::class])
    ->name('admin.tracking.')
    ->group(function () {
        Route::get('/', [OverviewController::class, 'index'])->name('overview');
        Route::get('/live', [LiveController::class, 'index'])->name('live');
        Route::get('/live/feed', [LiveController::class, 'feed'])->name('live.feed');
        Route::get('/pages', [PagesController::class, 'index'])->name('pages');
        Route::get('/pages/{page}', [PagesController::class, 'show'])->whereNumber('page')->name('pages.show');
        Route::get('/clicks', [ClicksController::class, 'index'])->name('clicks');
        Route::get('/visitors', [VisitorsController::class, 'index'])->name('visitors');
        Route::get('/visitors/{visitor}', [VisitorsController::class, 'show'])->whereNumber('visitor')->name('visitors.show');
        Route::delete('/visitors/{visitor}', [VisitorsController::class, 'destroy'])->whereNumber('visitor')->name('visitors.destroy');
        Route::get('/locations', [LocationsController::class, 'index'])->name('locations');
        Route::get('/sources', [SourcesController::class, 'index'])->name('sources');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/app-secret', [SettingsController::class, 'regenerateSecret'])->name('settings.secret');
        Route::post('/settings/geo-update', [SettingsController::class, 'updateGeo'])->name('settings.geo');
    });
