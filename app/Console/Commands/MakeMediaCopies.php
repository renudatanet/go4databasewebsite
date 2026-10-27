<?php

namespace App\Console\Commands;

use App\Actions\MediaCopies;
use Illuminate\Console\Command;

/**
 * General Settings > Regenerate Media Image, from the command line and to the
 * end in one go: `php artisan media:copies`. Run it after posts are added
 * by anything other than the media uploader, or any time; existing copies
 * are left alone.
 *
 * Image paths are relative to the web root. On live that is public_html, the
 * folder above @core (whose own public/ is a separate, unserved copy), so
 * that folder is used when it holds the media library, else public/.
 */
class MakeMediaCopies extends Command
{
    protected $signature = 'media:copies {--root= : The web root holding assets/uploads/media-uploader}';
    protected $description = 'Make the large-, grid- and thumb- copies of every media image that lacks them';

    public function handle()
    {
        $root = $this->option('root');
        if (!$root) {
            $above = dirname(base_path());
            $root = is_dir($above . '/' . MediaCopies::FOLDER) ? $above : public_path();
        }
        if (!is_dir(rtrim($root, '/') . '/' . MediaCopies::FOLDER)) {
            $this->error('No media library at ' . rtrim($root, '/') . '/' . MediaCopies::FOLDER);
            return 1;
        }
        chdir($root);
        @ini_set('memory_limit', '512M');
        $this->info('Media library: ' . getcwd() . '/' . MediaCopies::FOLDER);

        $result = MediaCopies::run(null, function ($img, $made) {
            $this->line("   {$made}. {$img->path}");
        });

        $this->info("Done: made copies of {$result['made']} images" . ($result['failed'] ? ", {$result['failed']} could not be read" : '') . '.');
        return 0;
    }
}
