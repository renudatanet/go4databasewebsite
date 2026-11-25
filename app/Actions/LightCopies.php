<?php

namespace App\Actions;

use App\Author;
use App\Blog;
use App\MediaUpload;
use Intervention\Image\Facades\Image;

/**
 * Small WebP copies of the images pages show small, so a 28px author photo is
 * not a 9 KB JPG and the header logo not a 13 KB picture:
 *
 *   icon    64px square: author photos and "Most read" thumbnails (about 1 KB)
 *   banner  740px wide: blog card banners (about half the large- JPG)
 *   logo    34px high, and logo2x 68px high for sharp screens (about 2 and 5 KB)
 *
 * They sit in media-uploader/small/ as {kind}-{file}.webp. Pages ask for them
 * as image sizes (get_attachment_image_by_id($id, 'icon')) and get the usual
 * size until the copy exists, so nothing breaks before `php artisan
 * media:light` (hourly, see App\Console\Kernel) has made it.
 */
final class LightCopies
{
    public const FOLDER = MediaCopies::FOLDER . 'small/';

    /** kind => [width, height (null keeps the shape), WebP quality, the size used until it exists] */
    public const KINDS = [
        'icon' => ['width' => 64, 'height' => 64, 'quality' => 70, 'fallback' => 'thumb'],
        'banner' => ['width' => 740, 'height' => null, 'quality' => 75, 'fallback' => 'large'],
        'logo' => ['width' => null, 'height' => 34, 'quality' => 75, 'fallback' => 'full'],
        'logo2x' => ['width' => null, 'height' => 68, 'quality' => 75, 'fallback' => 'full'],
    ];

    /** Where one image's copy of one kind lives, relative to the web root. */
    public static function file(string $path, string $kind): string
    {
        return self::FOLDER . $kind . '-' . basename($path) . '.webp';
    }

    /** The copy's address, or null while it has not been made. */
    public static function url(?string $path, string $kind): ?string
    {
        if (empty($path) || !isset(self::KINDS[$kind])) {
            return null;
        }
        $file = self::file($path, $kind);
        return is_file($file) ? asset($file) : null;
    }

    /**
     * Makes one copy from the original. Returns false when there is nothing
     * to make (it exists, the original is missing or is not a picture GD
     * reads, such as an SVG) and throws when the original can't be decoded.
     */
    public static function make(string $path, string $kind): bool
    {
        $source = MediaCopies::FOLDER . $path;
        $target = self::file($path, $kind);
        if (is_file($target) || !is_file($source) || empty(@getimagesize($source))) {
            return false;
        }
        if (!is_dir(self::FOLDER)) {
            mkdir(self::FOLDER, 0755, true);
        }
        $size = self::KINDS[$kind];
        $image = Image::make($source);
        if ($size['height'] !== null && $size['width'] !== null) {
            $image->fit($size['width'], $size['height']);
        } else {
            $image->resize($size['width'], $size['height'], function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        $image->save($target, $size['quality'], 'webp');
        $image->destroy();
        return true;
    }

    /**
     * The copies the site uses, as media id => kinds: every post's banner
     * and thumbnail, every author's photo and the site logo.
     */
    public static function wanted(): array
    {
        $wanted = [];
        $add = function ($id, array $kinds) use (&$wanted) {
            if (is_numeric($id) && (int) $id > 0) {
                $wanted[(int) $id] = array_values(array_unique(array_merge($wanted[(int) $id] ?? [], $kinds)));
            }
        };
        foreach (Blog::query()->pluck('image') as $id) {
            $add($id, ['banner', 'icon']);
        }
        foreach (Author::query()->pluck('image') as $id) {
            $add($id, ['icon']);
        }
        $add(get_static_option('site_logo'), ['logo', 'logo2x']);
        return $wanted;
    }

    /**
     * Makes every wanted copy that is missing and reports what it did.
     * Existing copies are left alone, so it can run any time.
     */
    public static function run(?callable $progress = null): array
    {
        $made = $failed = 0;
        $wanted = self::wanted();
        foreach (MediaUpload::query()->whereIn('id', array_keys($wanted))->get(['id', 'path']) as $img) {
            foreach ($wanted[$img->id] as $kind) {
                try {
                    if (self::make((string) $img->path, $kind)) {
                        $made++;
                        if ($progress) {
                            $progress(self::file($img->path, $kind));
                        }
                    }
                } catch (\Throwable $e) {
                    $failed++;
                }
            }
        }
        return ['made' => $made, 'failed' => $failed];
    }
}
