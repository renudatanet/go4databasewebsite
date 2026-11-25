<?php

namespace Tests\Feature\Media;

use App\Actions\LightCopies;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The small WebP copies of blog banners, thumbnails, author photos and the
 * logo (App\Actions\LightCopies, `php artisan media:light`), and pages using
 * them only once they exist. Works in a throwaway web root.
 */
class LightCopiesTest extends TestCase
{
    private string $root;
    private string $cwd;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        $GLOBALS['__g4_static_options'] = [];
        $this->artisan('migrate:fresh', ['--path' => [
            'database/migrations/2019_12_07_082524_create_static_options_table.php',
            'database/migrations/2020_04_14_082508_create_media_uploads_table.php',
        ], '--force' => true]);
        foreach (['blogs', 'author_new'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        $this->cwd = getcwd();
        $this->root = sys_get_temp_dir() . '/g4-light-' . uniqid();
        mkdir($this->root . '/assets/uploads/media-uploader', 0777, true);
    }

    protected function tearDown(): void
    {
        chdir($this->cwd);
        exec('rm -rf ' . escapeshellarg($this->root));
        $GLOBALS['__g4_static_options'] = [];
        parent::tearDown();
    }

    public function test_it_makes_small_webp_copies_of_what_the_blog_shows(): void
    {
        $banner = $this->media('banner.jpg', 1600, 900);
        $photo = $this->media('photo.jpg', 400, 400);
        $logo = $this->media('logo.png', 500, 111);
        DB::table('blogs')->insert([['image' => (string) $banner], ['image' => null]]);
        DB::table('author_new')->insert(['image' => (string) $photo]);
        DB::table('static_options')->insert(['option_name' => 'site_logo', 'option_value' => (string) $logo]);

        $this->artisan('media:light', ['--root' => $this->root])->assertExitCode(0);

        $folder = $this->root . '/' . LightCopies::FOLDER;
        $this->assertSame([740, 416], $this->size($folder . 'banner-banner.jpg.webp'));
        $this->assertSame([64, 64], $this->size($folder . 'icon-banner.jpg.webp'));
        $this->assertSame([64, 64], $this->size($folder . 'icon-photo.jpg.webp'));
        $this->assertSame([153, 34], $this->size($folder . 'logo-logo.png.webp'));
        $this->assertSame([306, 68], $this->size($folder . 'logo2x-logo.png.webp'));
        $this->assertFileDoesNotExist($folder . 'banner-photo.jpg.webp', 'an author photo is never a banner');
        $this->assertLessThan(2048, filesize($folder . 'icon-photo.jpg.webp'));

        $made = glob($folder . '*');
        $this->artisan('media:light', ['--root' => $this->root])->expectsOutputToContain('made 0 small copies');
        $this->assertSame($made, glob($folder . '*'), 'a second run leaves the copies alone');
    }

    public function test_pages_use_a_copy_once_it_exists_and_the_usual_size_until_then(): void
    {
        $id = $this->media('post.jpg', 1600, 900);
        $logo = $this->media('logo.png', 500, 111);
        chdir($this->root);
        copy('assets/uploads/media-uploader/post.jpg', 'assets/uploads/media-uploader/large-post.jpg');

        $this->assertStringEndsWith('/media-uploader/large-post.jpg', get_attachment_image_by_id($id, 'banner')['img_url']);
        $this->assertStringEndsWith('/media-uploader/post.jpg', get_attachment_image_by_id($id, 'icon')['img_url'], 'no thumb- either: the original');
        $this->assertStringNotContainsString('srcset', render_logo_image_markup_by_attachment_id($logo));

        LightCopies::make('post.jpg', 'banner');
        LightCopies::make('post.jpg', 'icon');
        LightCopies::make('logo.png', 'logo');
        LightCopies::make('logo.png', 'logo2x');

        $this->assertStringEndsWith('/media-uploader/small/banner-post.jpg.webp', get_attachment_image_by_id($id, 'banner')['img_url']);
        $this->assertStringEndsWith('/media-uploader/small/icon-post.jpg.webp', get_attachment_image_by_id($id, 'icon')['img_url']);
        $tag = render_logo_image_markup_by_attachment_id($logo);
        $this->assertStringContainsString('src="' . asset('assets/uploads/media-uploader/small/logo-logo.png.webp') . '"', $tag);
        $this->assertStringContainsString('small/logo2x-logo.png.webp 2x"', $tag);
    }

    public function test_what_is_not_a_picture_or_is_missing_is_skipped(): void
    {
        chdir($this->root);
        file_put_contents('assets/uploads/media-uploader/mark.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');

        $this->assertFalse(LightCopies::make('mark.svg', 'icon'));
        $this->assertFalse(LightCopies::make('gone.jpg', 'icon'));
        $this->assertNull(LightCopies::url('gone.jpg', 'icon'));
        $this->assertNull(LightCopies::url('mark.svg', 'nonsense'));
    }

    private function media(string $name, int $width, int $height): int
    {
        $image = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x += 40) {
            imagefilledrectangle($image, $x, 0, $x + 19, $height, imagecolorallocate($image, ($x * 7) % 255, 120, 200));
        }
        $file = $this->root . '/assets/uploads/media-uploader/' . $name;
        str_ends_with($name, '.png') ? imagepng($image, $file) : imagejpeg($image, $file, 90);
        imagedestroy($image);

        return DB::table('media_uploads')->insertGetId(['title' => $name, 'path' => $name, 'alt' => '', 'size' => '1', 'dimensions' => "{$width}x{$height}"]);
    }

    private function size(string $file): array
    {
        $this->assertFileExists($file);
        $info = getimagesize($file);
        $this->assertSame('image/webp', $info['mime']);

        return [$info[0], $info[1]];
    }
}
