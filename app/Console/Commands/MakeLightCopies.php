<?php

namespace App\Console\Commands;

use App\Actions\LightCopies;
use App\Actions\MediaCopies;
use Illuminate\Console\Command;

/**
 * `php artisan media:light`: makes the small WebP copies (App\Actions\LightCopies)
 * of post banners, thumbnails, author photos and the logo that lack them.
 * Runs hourly, so a new post gets its copies within the hour; existing
 * copies are left alone. Paths are relative to the web root, found as in
 * media:copies (public_html on live).
 */
class MakeLightCopies extends Command
{
    protected $signature = 'media:light {--root= : The web root holding assets/uploads/media-uploader}';
    protected $description = 'Make the small WebP copies of blog banners, thumbnails, author photos and the logo';

    public function handle()
    {
        $root = MediaCopies::webRoot($this->option('root'));
        if ($root === null) {
            $this->error('No media library found.');
            return 1;
        }
        chdir($root);
        @ini_set('memory_limit', '512M');
        $this->info('Media library: ' . getcwd() . '/' . MediaCopies::FOLDER);

        $result = LightCopies::run(function ($file) {
            $this->line("   {$file} (" . round(filesize($file) / 1024, 1) . ' KB)');
        });

        $this->info("Done: made {$result['made']} small copies" . ($result['failed'] ? ", {$result['failed']} could not be read" : '') . '.');
        return 0;
    }
}
