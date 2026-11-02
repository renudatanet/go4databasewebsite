<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/** A block of the "Working at Go4Database" page: what we do, how we work, a quote from the team, or an office. */
class CareerPageItem extends Model
{
    protected $table = 'career_page_items';

    protected $fillable = ['section', 'title', 'subtitle', 'body', 'image', 'cutout', 'link', 'filter', 'sr_order', 'lang', 'status'];

    protected $casts = ['cutout' => 'boolean'];

    /**
     * Each section, in page order, with what its fields mean on the admin
     * screen; a field left out (null) isn't asked for in that section.
     */
    public const SECTIONS = [
        'offering' => ['name' => 'What we do', 'title' => 'Name', 'subtitle' => null, 'body' => 'What it does', 'image' => null, 'cutout' => false, 'link' => 'Page it opens (optional)', 'filter' => null, 'add' => 'an offering'],
        'value' => ['name' => 'How we work', 'title' => 'Value', 'subtitle' => null, 'body' => 'What it means', 'image' => null, 'cutout' => false, 'link' => null, 'filter' => null, 'add' => 'a value'],
        'quote' => ['name' => 'Quotes from the team', 'title' => 'Name', 'subtitle' => 'Role', 'body' => 'Quote (the first line is shown large, the next lines smaller)', 'image' => 'Photo', 'cutout' => true, 'link' => null, 'filter' => null, 'add' => 'a quote'],
        'office' => ['name' => 'Offices', 'title' => 'City', 'subtitle' => 'Address', 'body' => 'A line about the office (optional)', 'image' => 'Photo', 'cutout' => false, 'link' => null, 'filter' => 'Filter word', 'add' => 'an office'],
    ];

    /** @return \Illuminate\Support\Collection<int, self> the published items of $section in $lang, in order */
    public static function shown(string $section, string $lang)
    {
        return static::where(['section' => $section, 'lang' => $lang, 'status' => 'publish'])
            ->orderBy('sr_order')->orderBy('id')->get();
    }
}
