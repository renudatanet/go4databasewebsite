<?php

namespace App\Careers;

use App\Jobs;
use App\JobsCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The career pages' questions about jobs: which roles are open, in which
 * categories, which match a search, and which sit beside a given role. One
 * language at a time. Live holds a few dozen jobs at most, so each request
 * reads the published ones once and answers the rest in memory.
 */
final class JobBoard
{
    /** A ceiling, not a page size: the pages show roles a few at a time. */
    private const MAX_JOBS = 500;

    private ?Collection $published = null;
    private ?Collection $categories = null;

    public function __construct(private string $lang)
    {
    }

    /** Published roles whose deadline hasn't passed, newest first. */
    public function openJobs(): Collection
    {
        return $this->published()->filter(fn (Jobs $job) => $job->isOpen())->values();
    }

    /** @return Collection<int, Jobs> open roles in $category */
    public function inCategory(JobsCategory $category): Collection
    {
        return $this->openJobs()->where('category_id', (string) $category->id)->values();
    }

    /** Open roles matching every word of $query in title, position, location or category. */
    public function search(string $query): Collection
    {
        $words = array_filter(preg_split('/\s+/', mb_strtolower(trim($query))));
        if ($words === []) {
            return $this->openJobs();
        }

        return $this->openJobs()->filter(function (Jobs $job) use ($words) {
            $haystack = mb_strtolower(implode(' ', [$job->title, $job->position, $job->job_location, optional($this->categoryOf($job))->title]));
            foreach ($words as $word) {
                if (!str_contains($haystack, $word)) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    /**
     * Published categories that have open roles, most roles first, each with
     * its count as open_count.
     *
     * @return Collection<int, JobsCategory>
     */
    public function categoriesWithRoles(): Collection
    {
        $counts = $this->openJobs()->countBy('category_id');

        return $this->categories()
            ->filter(fn (JobsCategory $c) => ($counts[(string) $c->id] ?? 0) > 0)
            ->each(function (JobsCategory $c) use ($counts) { $c->open_count = $counts[(string) $c->id]; })
            ->sortByDesc('open_count')
            ->values();
    }

    /** The category at /career/{slug}, by its stored slug or the one made from its name. */
    public function findCategory(string $slug): ?JobsCategory
    {
        return $this->categories()->first(fn (JobsCategory $c) => $c->urlSlug() === $slug);
    }

    /**
     * The role at /career/{slug}, preferring a published one in this language.
     * Drafts are found only for $withDrafts (an admin previewing): before,
     * Jobs::where('slug') showed drafts to anyone, and found them first.
     */
    public function findJob(string $slug, bool $withDrafts = false): ?Jobs
    {
        return Jobs::where('slug', $slug)
            ->when(!$withDrafts, fn ($q) => $q->where('status', 'publish'))
            ->orderByRaw("status = 'publish' desc")
            ->orderByRaw('lang = ? desc', [$this->lang])
            ->orderByDesc('id')
            ->first();
    }

    /** The role's category, or null when it was deleted from under it. */
    public function categoryOf(Jobs $job): ?JobsCategory
    {
        return $this->categories()->firstWhere('id', (int) $job->category_id)
            ?? JobsCategory::find($job->category_id);
    }

    /** Up to $count other open roles, those in the same category first. */
    public function related(Jobs $job, int $count = 3): Collection
    {
        return $this->openJobs()
            ->reject(fn (Jobs $other) => $other->id === $job->id)
            ->sortBy(fn (Jobs $other) => $other->category_id === $job->category_id ? 0 : 1)
            ->take($count)
            ->values();
    }

    private function published(): Collection
    {
        return $this->published ??= Jobs::where(['status' => 'publish', 'lang' => $this->lang])
            ->orderByDesc('id')
            ->limit(self::MAX_JOBS)
            ->get();
    }

    private function categories(): Collection
    {
        return $this->categories ??= JobsCategory::where(['status' => 'publish', 'lang' => $this->lang])->orderBy('title')->get();
    }

    /**
     * A category address from $wanted (or the name), unique among categories
     * and not a job's: both live under /career/, and a category would win.
     */
    public static function uniqueCategorySlug(string $wanted, ?int $ignoreId = null): string
    {
        $base = Str::slug($wanted) ?: 'category';
        $taken = fn (string $slug) => Jobs::where('slug', $slug)->exists()
            || JobsCategory::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists();
        $slug = $base;
        for ($n = 2; $taken($slug); $n++) {
            $slug = $base . '-' . $n;
        }

        return $slug;
    }
}
