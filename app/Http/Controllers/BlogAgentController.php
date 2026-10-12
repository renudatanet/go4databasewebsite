<?php

namespace App\Http\Controllers;

use App\Author;
use App\Blog;
use App\BlogCategory;
use App\MediaUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Lets the AI Blog Agent publish a finished post straight into the blog,
 * exactly as "Blogs > Add New Post" in the admin panel would (status publish).
 *
 * Protected by a shared secret: the agent sends it in the X-Blog-Agent-Token
 * header and it must match BLOG_AGENT_TOKEN in .env. With no token set, both
 * endpoints refuse every request.
 */
class BlogAgentController extends Controller
{
    private function authorized(Request $request): bool
    {
        $token = (string) config('services.blog_agent.token');
        return $token !== '' && hash_equals($token, (string) $request->header('X-Blog-Agent-Token'));
    }

    /** Categories and authors the agent can choose from (for its settings form). */
    public function options(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        return response()->json([
            'categories' => BlogCategory::where(['lang' => 'en_US', 'status' => 'publish'])->orderBy('name')->get(['id', 'name']),
            'authors' => Author::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function publish(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['error' => 'unauthorized'], 401);
        }
        $data = $request->validate([
            'title' => 'required|string|max:500',
            'content' => 'required|string',
            'excerpt' => 'required|string',
            'category_id' => 'required|integer|exists:blog_categories,id',
            'author_id' => 'required|integer|exists:author_new,id',
            'slug' => 'nullable|string|max:191',
            'meta_tags' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|string|max:191',
            'faqs' => 'nullable|array',
            'faqs.*.question' => 'required_with:faqs|string',
            'faqs.*.answer' => 'required_with:faqs|string',
            'image_url' => 'nullable|url',
            'image_alt' => 'nullable|string|max:191',
            'show_on_homepage' => 'nullable|boolean',
        ]);

        $slug = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $imageId = !empty($data['image_url']) ? $this->storeImage($data['image_url'], $data['image_alt'] ?? $data['title']) : null;
        $author = Author::find($data['author_id']);

        $blog = Blog::create([
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'excerpt' => $data['excerpt'],
            'faqs' => !empty($data['faqs']) ? array_values($data['faqs']) : null,
            'tags' => $data['tags'] ?? null,
            'meta_tags' => $data['meta_tags'] ?? $data['title'],
            'meta_description' => $data['meta_description'] ?? $data['excerpt'],
            'blog_categories_id' => $data['category_id'],
            'categories_id' => serialize([(string) $data['category_id']]),
            'author_id' => $data['author_id'],
            'author' => $author ? $author->name : null,
            'image' => $imageId,
            'status' => 'publish',
            'lang' => 'en_US',
            'user_id' => 1,
            'breaking_news' => 0,
            'show_on_homepage' => !empty($data['show_on_homepage']) ? 1 : 0,
            'publish_date' => now()->format('Y-m-d'),
            'total_visitors' => 0,
        ]);

        return response()->json([
            'id' => $blog->id,
            'slug' => $blog->slug,
            'url' => route('frontend.blog.single', $blog->slug),
            'image_id' => $imageId,
        ], 201);
    }

    private function uniqueSlug(string $source): string
    {
        $base = Str::slug($source) ?: 'post';
        $slug = $base;
        $n = 2;
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $n++;
        }
        return $slug;
    }

    /**
     * Downloads the featured image into the media library, the same folder the
     * admin media uploader uses. A failed download just means no featured image.
     */
    private function storeImage(string $url, string $alt): ?int
    {
        try {
            $response = Http::timeout(20)->get($url);
            $type = strtolower(explode(';', (string) $response->header('Content-Type'))[0]);
            $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'][$type] ?? null;
            if (!$response->successful() || !$ext || strlen($response->body()) > 8 * 1024 * 1024) {
                return null;
            }
            $folder = 'assets/uploads/media-uploader/';   // relative to the web root, like MediaUploadController
            if (!is_dir($folder)) {
                @mkdir($folder, 0755, true);
            }
            $name = Str::slug(Str::limit($alt, 60, '')) . time() . '.' . $ext;
            file_put_contents($folder . $name, $response->body());
            $size = @getimagesize($folder . $name);
            $media = MediaUpload::create([
                'title' => $alt . '.' . $ext,
                'alt' => $alt,
                'path' => $name,
                'size' => round(strlen($response->body()) / 1024, 2) . ' KB',
                'dimensions' => $size ? $size[0] . ' x ' . $size[1] . ' pixels' : null,
            ]);
            return $media->id;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
