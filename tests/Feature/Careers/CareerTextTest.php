<?php

namespace Tests\Feature\Careers;

use App\Careers\CareerText;

class CareerTextTest extends CareersTestCase
{
    public function test_an_empty_setting_falls_back_to_the_default(): void
    {
        $this->setOption('career_page_en_heading', '   ');

        $this->assertSame(CareerText::DEFAULTS['heading'], (new CareerText('en'))->get('heading'));
    }

    public function test_a_saved_setting_wins_and_fills_its_placeholders(): void
    {
        $this->setOption('career_page_en_subtitle', '{roles} roles open');

        $this->assertSame('9 roles open', (new CareerText('en'))->get('subtitle', ['{roles}' => 9]));
    }

    public function test_perks_are_title_and_text_pairs_and_none_hides_them(): void
    {
        $text = new CareerText('en');
        $this->assertCount(4, $text->pairs('perks'));

        $this->setOption('career_page_en_perks', "Lunch | On us\nGym\n\n");
        $this->assertSame([['Lunch', 'On us'], ['Gym', '']], (new CareerText('en'))->pairs('perks'));

        $this->setOption('career_page_en_perks', 'None');
        $this->assertSame([], (new CareerText('en'))->pairs('perks'));
    }

    public function test_role_page_labels_come_from_single_page_settings(): void
    {
        $this->setOption('job_single_page_en_job_context_label', 'About Us');

        $text = new CareerText('en');
        $this->assertSame('About Us', $text->label('job_context_label'));
        $this->assertSame('What you’ll do', $text->label('job_responsibility_label'));
    }
}
