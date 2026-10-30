<?php

namespace App\Careers;

use App\Jobs;
use App\JobsCategory;
use Illuminate\Support\Collection;

/**
 * schema.org data for the career pages, as arrays for json_encode (the
 * hand-written blocks it replaces in og-meta were invalid JSON, and keyed on
 * words in the URL, so a role called "B2B Sales Leader" got the B2B lists'
 * markup). Each role page gets a JobPosting, for Google's job search.
 */
final class JobSchema
{
    public function __construct(private JobBoard $board)
    {
    }

    /** /career, a category or a search: the page, its roles and the breadcrumb. */
    public function board(string $pageName, Collection $roles, ?JobsCategory $category = null): array
    {
        $url = url()->current();
        $crumbs = [[__('Home'), url('/')], [$pageName, route('frontend.jobs')]];
        if ($category) {
            $crumbs[] = [$category->title, $url];
        }

        return $this->graph([
            [
                '@type' => 'CollectionPage',
                '@id' => $url . '#page',
                'url' => $url,
                'name' => $category ? $category->title : $pageName,
                'isPartOf' => ['@id' => url('/') . '#website'],
                'breadcrumb' => ['@id' => $url . '#breadcrumb'],
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => $roles->count(),
                    'itemListElement' => $roles->values()->map(fn (Jobs $job, int $i) => [
                        '@type' => 'ListItem',
                        'position' => $i + 1,
                        'url' => route('frontend.jobs.single', $job->slug),
                        'name' => $job->title,
                    ])->all(),
                ],
            ],
            $this->breadcrumb($url, $crumbs),
        ]);
    }

    /** A role's page: its JobPosting while it is open, and the breadcrumb either way. */
    public function role(Jobs $job, string $pageName, array $sections): array
    {
        $url = route('frontend.jobs.single', $job->slug);
        $category = $this->board->categoryOf($job);
        $crumbs = [[__('Home'), url('/')], [$pageName, route('frontend.jobs')]];
        if ($category) {
            $crumbs[] = [$category->title, route('frontend.jobs.single', $category->urlSlug())];
        }
        $crumbs[] = [$job->title, $url];

        $nodes = [$this->breadcrumb($url, $crumbs)];
        if ($job->isOpen()) {
            $nodes[] = $this->posting($job, $url, $sections, $category);
        }

        return $this->graph($nodes);
    }

    private function posting(Jobs $job, string $url, array $sections, ?JobsCategory $category): array
    {
        $description = collect($sections)->map(fn ($html, $label) => '<h2>' . e($label) . '</h2>' . $html)->implode('');
        $posting = [
            '@type' => 'JobPosting',
            '@id' => $url . '#job',
            'url' => $url,
            'title' => $job->title,
            'description' => $description !== '' ? $description : e($job->title),
            'datePosted' => optional($job->created_at)->toDateString(),
            'hiringOrganization' => array_filter([
                '@type' => 'Organization',
                'name' => $job->company_name ?: get_static_option('site_en_title', 'Go4Database'),
                'sameAs' => url('/'),
                'logo' => $this->logo(),
            ]),
            'jobLocation' => [
                '@type' => 'Place',
                'address' => array_filter([
                    '@type' => 'PostalAddress',
                    'streetAddress' => $job->job_location ?: null,
                    'addressLocality' => $this->setting('career_schema_locality', 'Noida'),
                    'addressRegion' => $this->setting('career_schema_region', 'Uttar Pradesh'),
                    'postalCode' => $this->setting('career_schema_postal_code', '201301'),
                    'addressCountry' => strtoupper($this->setting('career_schema_country', 'IN')),
                ]),
            ],
            'directApply' => !empty(get_static_option('job_single_page_apply_form')),
        ];
        if ($closes = $job->closesAt()) {
            $posting['validThrough'] = $closes->toIso8601String();
        }
        if ($type = $job->schemaType()) {
            $posting['employmentType'] = $type;
        }
        if ($category) {
            $posting['occupationalCategory'] = $category->title;
        }
        if ($job->vacancy && ctype_digit((string) $job->vacancy)) {
            $posting['totalJobOpenings'] = (int) $job->vacancy;
        }
        if ($range = $job->salaryRange()) {
            $posting['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => strtoupper($this->setting('career_salary_currency', 'INR')),
                'value' => ['@type' => 'QuantitativeValue', 'minValue' => $range[0], 'maxValue' => $range[1], 'unitText' => 'MONTH'],
            ];
        }

        return $posting;
    }

    /** @param list<array{0: string, 1: string}> $crumbs */
    private function breadcrumb(string $url, array $crumbs): array
    {
        return [
            '@type' => 'BreadcrumbList',
            '@id' => $url . '#breadcrumb',
            'itemListElement' => array_map(fn ($crumb, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb[0],
                'item' => $crumb[1],
            ], $crumbs, array_keys($crumbs)),
        ];
    }

    private function graph(array $nodes): array
    {
        return ['@context' => 'https://schema.org', '@graph' => array_values($nodes)];
    }

    private function logo(): ?string
    {
        $id = get_static_option('site_logo');
        if (empty($id)) {
            return null;
        }
        $logo = get_attachment_image_by_id($id, 'full', false);

        return is_array($logo) && !empty($logo['img_url']) ? $logo['img_url'] : null;
    }

    private function setting(string $key, string $default): string
    {
        $value = trim((string) get_static_option($key));

        return $value !== '' ? $value : $default;
    }
}
