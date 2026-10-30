<?php

namespace App;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $table = 'jobs';
    protected $fillable = [
        'title',
        'position',
        'company_name',
        'category_id',
        'vacancy',
        'job_responsibility',
        'employment_status',
        'education_requirement',
        'experience_requirement',
        'additional_requirement',
        'job_location',
        'salary',
        'other_benefits',
        'email',
        'deadline',
        'meta_tags',
        'meta_description',
        'status',
        'lang',
        'slug',
        'application_fee_status',
        'application_fee',
        'job_context',
        ];

    /** schema.org employmentType for each of the admin's Employment Status options. */
    private const SCHEMA_TYPES = [
        'full_time' => 'FULL_TIME',
        'part_time' => 'PART_TIME',
        'project_based' => 'CONTRACTOR',
    ];

    public function category(){
        return $this->hasOne('App\JobsCategory','id','category_id');
    }

    /**
     * The end of the deadline day, in the site's timezone. The admin field is
     * a date picker, but the column is free text, so anything unreadable
     * counts as no deadline rather than an error.
     */
    public function closesAt(): ?CarbonImmutable
    {
        if (trim((string) $this->deadline) === '') {
            return null;
        }
        try {
            return CarbonImmutable::parse($this->deadline, config('app.timezone'))->endOfDay();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function isOpen(): bool
    {
        $closes = $this->closesAt();

        return $closes === null || $closes->isFuture();
    }

    /** "full_time" => "Full time" */
    public function typeLabel(): string
    {
        return ucfirst(strtolower(str_replace('_', ' ', (string) $this->employment_status)));
    }

    public function schemaType(): ?string
    {
        return self::SCHEMA_TYPES[$this->employment_status] ?? null;
    }

    /**
     * The salary as [min, max] monthly amounts, when written as a range such
     * as "25K-60K" or "5k - 15k"; null for anything else ("Good hike from
     * current"), which is shown as written but not sent to Google.
     *
     * @return array{0: int, 1: int}|null
     */
    public function salaryRange(): ?array
    {
        $unit = '\s*(k|l|lakh|lac)?';
        if (!preg_match('/^\s*(?:rs\.?|inr|₹)?\s*(\d+(?:\.\d+)?)' . $unit . '\s*(?:-|–|to)\s*(?:rs\.?|inr|₹)?\s*(\d+(?:\.\d+)?)' . $unit . '\s*(?:\/\s*month|per month|pm)?\s*$/iu', (string) $this->salary, $m)) {
            return null;
        }
        $amount = function (string $number, ?string $suffix) {
            $multiplier = ['k' => 1000, 'l' => 100000, 'lakh' => 100000, 'lac' => 100000][strtolower((string) $suffix)] ?? 1;

            return (int) round((float) $number * $multiplier);
        };
        // "5-15k": the unit written once applies to both.
        $min = $amount($m[1], ($m[2] ?? '') !== '' ? $m[2] : ($m[4] ?? null));
        $max = $amount($m[3], $m[4] ?? null);

        return $min > 0 && $max >= $min ? [$min, $max] : null;
    }

    public function hasApplicationFee(): bool
    {
        return !empty($this->application_fee_status) && $this->application_fee > 0;
    }
}
