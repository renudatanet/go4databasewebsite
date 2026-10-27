<?php

namespace App\Actions;

use App\MediaUpload;
use Intervention\Image\Facades\Image;

/**
 * The smaller copies pages ask for, made for any image missing them: large-
 * (740px wide; blog cards and lead stories), grid- (350px) and thumb- (150px
 * square). Images added without the media uploader (the blog agent) had none,
 * and pages then loaded the full original, 2.4 MB for a 400px card.
 *
 * Paths are relative to the web root, as everywhere else in this app; on
 * live that is public_html, not @core/public, which is a separate copy.
 * Used by General Settings > Regenerate Media Image, `php artisan
 * media:copies` and the blog agent.
 */
class MediaCopies
{
    public const FOLDER = 'assets/uploads/media-uploader/';
    public const SIZES = ['large-' => 740, 'grid-' => 350, 'thumb-' => 150];

    /**
     * The copies one image lacks, as prefix => width; empty when it needs
     * none, including images too small for copies and files GD can't read (an SVG).
     */
    public static function missing(MediaUpload $img): array
    {
        $source = self::FOLDER . $img->path;
        if (empty($img->path) || !is_file($source)) {
            return [];
        }
        $missing = [];
        foreach (self::SIZES as $prefix => $width) {
            if (!is_file(self::FOLDER . $prefix . $img->path)) {
                $missing[$prefix] = $width;
            }
        }
        if (empty($missing)) {
            return [];
        }
        $size = @getimagesize($source);
        return (empty($size) || $size[0] <= 150) ? [] : $missing;
    }

    /**
     * Makes whichever copies one image lacks. Returns true when copies were
     * made, false when none were needed, and throws when the image can't be read.
     */
    public static function forImage(MediaUpload $img): bool
    {
        $missing = self::missing($img);
        if (empty($missing)) {
            return false;
        }
        $source = self::FOLDER . $img->path;
        foreach ($missing as $prefix => $width) {
            $copy = $prefix === 'thumb-'
                ? Image::make($source)->fit(150, 150)
                : Image::make($source)->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            $copy->save(self::FOLDER . $prefix . $img->path, 82);
            $copy->destroy();
        }
        return true;
    }

    /**
     * Works through the library for up to $seconds (null: until done) and
     * reports what it did. Copies that already exist are left alone, so it can
     * be run again at any time.
     */
    public static function run(?float $seconds = 20, ?callable $progress = null): array
    {
        $started = microtime(true);
        $made = $failed = $waiting = 0;
        foreach (MediaUpload::orderBy('id', 'desc')->cursor() as $img) {
            if ($seconds !== null && microtime(true) - $started > $seconds) {
                if (self::missing($img)) {
                    $waiting++;
                }
                continue;
            }
            try {
                if (self::forImage($img)) {
                    $made++;
                    if ($progress) {
                        $progress($img, $made);
                    }
                }
            } catch (\Throwable $e) {
                $failed++;
            }
        }
        return ['made' => $made, 'failed' => $failed, 'waiting' => $waiting];
    }
}
