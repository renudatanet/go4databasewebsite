<?php

namespace Tests\Unit\Careers;

use App\Careers\JobContent;
use PHPUnit\Framework\TestCase;

class JobContentTest extends TestCase
{
    public function test_google_docs_styling_is_dropped_and_bold_kept(): void
    {
        $pasted = '<b style="font-weight:normal;" id="docs-internal-guid-1"><p dir="ltr" style="line-height:1.38;margin-top:12pt">'
            . '<span style="font-size:14pt;font-family:Arial;color:rgb(67,67,67)">Go4Database, a brand of </span>'
            . '<span style="font-size:14pt;font-weight:700">TJMGLOBAL</span><span style="font-style:italic">, since 2012.</span></p></b>';

        $this->assertSame('<p>Go4Database, a brand of <strong>TJMGLOBAL</strong><em>, since 2012.</em></p>', JobContent::clean($pasted));
    }

    public function test_empty_paragraphs_and_edge_line_breaks_go(): void
    {
        $html = '<p>&nbsp;</p><p><br>First line<br></p><p> <br> </p><ul><li>&nbsp;</li><li>Item</li></ul><ul><li></li></ul>';

        $this->assertSame('<p>First line</p><ul><li>Item</li></ul>', JobContent::clean($html));
    }

    public function test_a_section_of_only_whitespace_is_blank(): void
    {
        $this->assertTrue(JobContent::isBlank("<p>&nbsp;</p>\n<p><span style=\"color:red\"> </span><br></p>"));
        $this->assertTrue(JobContent::isBlank(null));
        $this->assertSame('', JobContent::clean('<p>&nbsp;</p>'));
        $this->assertFalse(JobContent::isBlank('<p><img src="/a.png"></p>'));
    }

    public function test_headings_start_at_h2_and_scripts_are_removed(): void
    {
        $html = '<h1 style="color:red">Perks</h1><script>alert(1)</script><p onclick="x()">Text</p>';

        $this->assertSame('<h2>Perks</h2><p>Text</p>', JobContent::clean($html));
    }

    public function test_links_keep_their_address_only(): void
    {
        $html = '<p><a href="https://example.com/x" style="color:blue" target="_blank" class="btn">site</a> <a href="javascript:alert(1)">bad</a></p>';

        $this->assertSame('<p><a href="https://example.com/x" target="_blank" rel="noopener">site</a> <a>bad</a></p>', JobContent::clean($html));
    }

    public function test_text_outside_any_paragraph_survives(): void
    {
        $this->assertSame('Any graduate/MBA or equivalent', JobContent::clean('Any graduate/MBA or equivalent'));
        $this->assertSame('Café – 5K', JobContent::clean('<span>Café – 5K</span>'));
    }
}
