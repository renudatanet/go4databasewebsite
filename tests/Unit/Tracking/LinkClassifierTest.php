<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Ingestion\LinkClassifier;
use PHPUnit\Framework\TestCase;

class LinkClassifierTest extends TestCase
{
    private function classifier(): LinkClassifier
    {
        return new LinkClassifier('www.go4database.com', 'app.go4database.com');
    }

    public function test_names_the_kind_of_each_link(): void
    {
        $c = $this->classifier();

        $this->assertSame(LinkClassifier::BUTTON, $c->kind(null));
        $this->assertSame(LinkClassifier::INTERNAL, $c->kind('https://www.go4database.com/pricing'));
        $this->assertSame(LinkClassifier::INTERNAL, $c->kind('https://go4database.com/pricing'));
        $this->assertSame(LinkClassifier::APP, $c->kind('https://app.go4database.com/register'));
        $this->assertSame(LinkClassifier::OUTBOUND, $c->kind('https://www.linkedin.com/company/x'));
        $this->assertSame(LinkClassifier::EMAIL, $c->kind('mailto:hello@example.com'));
        $this->assertSame(LinkClassifier::PHONE, $c->kind('tel:+18005550100'));
        $this->assertSame(LinkClassifier::DOWNLOAD, $c->kind('https://www.go4database.com/files/sample.csv'));
    }

    public function test_app_links_are_split_into_sign_up_and_log_in(): void
    {
        $c = $this->classifier();

        $this->assertSame('signup_click', $c->eventName(LinkClassifier::APP, 'https://app.go4database.com/register?utm_source=x'));
        $this->assertSame('signup_click', $c->eventName(LinkClassifier::APP, 'https://app.go4database.com/sign-up'));
        $this->assertSame('login_click', $c->eventName(LinkClassifier::APP, 'https://app.go4database.com/login'));
        $this->assertSame('app_click', $c->eventName(LinkClassifier::APP, 'https://app.go4database.com/pricing'));
        $this->assertSame('outbound_click', $c->eventName(LinkClassifier::OUTBOUND, 'https://example.com/'));
    }
}
