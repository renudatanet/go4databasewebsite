<?php

namespace App\Careers;

/**
 * The "Working at Go4Database" page's wording, from Jobs > Working at
 * Go4Database (working_page_{lang}_{key}), else the default here. A section
 * whose title default is empty (the optional band) shows only once a title
 * is written for it.
 */
final class WorkingPageText extends PageText
{
    protected const PREFIX = 'working_page_';

    /** The page's address under the career page: /career/working-at-go4database. */
    public const SLUG = 'working-at-go4database';

    public const DEFAULTS = [
        // Header
        'title' => 'Working at Go4Database',
        'intro' => 'Go4Database is a fast-growing B2B data company where small teams own real results. You will work closely with the founders, take ownership early, and grow as fast as your work does.',
        'hero_button' => 'See open roles',
        'meta_description' => 'What it is like to work at Go4Database: who we are, what we do, how we work and what our customers say about us.',
        // Quotes from the team, under the header (shown once one is added)
        'quotes_title' => 'In our team’s words',
        // Who we are
        'story_title' => 'Who we are',
        'story_text' => "Go4Database, a brand of TJMGLOBAL Digitech Pvt. Ltd., has been a trusted name in data-driven marketing since 2012. We deliver verified B2B and B2C contact data that powers marketers, sales teams and agencies around the world.\nOur mission is simple: help businesses connect smarter, market faster and sell better.",
        // What we do (cards from the admin)
        'offer_title' => 'What we do',
        'offer_text' => 'Everything we build helps sales and marketing teams reach the right people.',
        'offer_link' => 'Learn more',
        // How we work (cards from the admin)
        'values_title' => 'How we work',
        'values_text' => 'What our team values, every day.',
        // Customer testimonials (Testimonial in the admin)
        'customers_title' => 'What our customers say',
        'customers_text' => 'The work we do matters to the teams who use it.',
        'customers_count' => '4',
        // Offices
        'where_title' => 'Where we work',
        'where_text' => 'Our team works together from our office in Noida, with customers across the US, the UK and beyond.',
        'office_link' => 'See open roles here',
        // Teams
        'teams_title' => 'Our teams',
        'teams_text' => 'The teams that make Go4Database work.',
        'team_count' => '{count} open roles',
        'team_count_one' => '1 open role',
        'team_link' => 'Meet the team’s roles',
        // Optional band, hidden until it has a title
        'remote_title' => '',
        'remote_text' => '',
        'remote_button' => 'See open roles',
        // Closing banner
        'join_title' => 'Join the team',
        'join_text' => 'Bring your ambition. We will bring the room to grow.',
        'join_button' => 'See open roles',
    ];
}
