<?php

namespace App\Careers;

use App\CareerPageItem;
use App\Jobs;
use App\JobsCategory;
use App\Testimonial;
use Illuminate\Support\Collection;

/**
 * What the "Working at Go4Database" page shows, from data the site already
 * has: its blocks from Jobs > Working at Go4Database, the customer
 * testimonials (Testimonial), and the teams and their open roles (JobBoard,
 * so the counts match /career).
 */
final class WorkingPage
{
    /** A team's photo is saved as working_page_team_image_{category id} (same in every language). */
    public const TEAM_IMAGE_OPTION = 'working_page_team_image_';

    public function __construct(private JobBoard $board, private string $lang)
    {
    }

    /** @return Collection<int, CareerPageItem> the published blocks of $section */
    public function items(string $section): Collection
    {
        return CareerPageItem::shown($section, $this->lang);
    }

    /** @return Collection<int, Testimonial> the newest published customer testimonials, at most $limit */
    public function testimonials(int $limit): Collection
    {
        if ($limit < 1) {
            return collect();
        }

        return Testimonial::where(['lang' => $this->lang, 'status' => 'publish'])->orderByDesc('id')->limit($limit)->get();
    }

    /**
     * The offices, each with how many open roles mention its filter word.
     *
     * @return Collection<int, array{item: CareerPageItem, filter: string, count: int}>
     */
    public function offices(): Collection
    {
        $roles = $this->board->openJobs();

        return $this->items('office')->map(function (CareerPageItem $office) use ($roles) {
            $filter = trim((string) ($office->filter ?: $office->title));

            return [
                'item' => $office,
                'filter' => $filter,
                'count' => $roles->filter(fn (Jobs $job) => self::matches($job, $filter))->count(),
            ];
        });
    }

    /**
     * Teams with open roles, most roles first, each with its photo id.
     *
     * @return Collection<int, array{category: JobsCategory, count: int, image: ?string}>
     */
    public function teams(): Collection
    {
        return $this->board->categoriesWithRoles()->map(fn (JobsCategory $team) => [
            'category' => $team,
            'count' => (int) $team->open_count,
            'image' => get_static_option(self::TEAM_IMAGE_OPTION . $team->id) ?: null,
        ]);
    }

    /** Does the role's location mention $word (case-insensitive)? */
    public static function matches(Jobs $job, string $word): bool
    {
        return $word !== '' && str_contains(mb_strtolower((string) $job->job_location), mb_strtolower($word));
    }
}
