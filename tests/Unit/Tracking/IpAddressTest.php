<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Support\IpAddress;
use PHPUnit\Framework\TestCase;

class IpAddressTest extends TestCase
{
    public function test_masks_the_last_octet_of_ipv4(): void
    {
        $this->assertSame('203.0.113.0', IpAddress::mask('203.0.113.77'));
    }

    public function test_keeps_only_the_first_48_bits_of_ipv6(): void
    {
        $this->assertSame('2a02:4780:2d::', IpAddress::mask('2a02:4780:2d:d203::1'));
    }

    public function test_masking_garbage_gives_null(): void
    {
        $this->assertNull(IpAddress::mask('not-an-ip'));
    }

    public function test_matches_single_addresses_and_ranges(): void
    {
        $this->assertTrue(IpAddress::matches('203.0.113.7', '203.0.113.7'));
        $this->assertFalse(IpAddress::matches('203.0.113.8', '203.0.113.7'));
        $this->assertTrue(IpAddress::matches('203.0.113.200', '203.0.113.0/24'));
        $this->assertFalse(IpAddress::matches('203.0.114.1', '203.0.113.0/24'));
        $this->assertTrue(IpAddress::matches('10.20.30.40', '10.16.0.0/12'));
        $this->assertFalse(IpAddress::matches('10.32.0.1', '10.16.0.0/12'));
        $this->assertTrue(IpAddress::matches('2001:db8::1', '2001:db8::/32'));
        $this->assertFalse(IpAddress::matches('2001:db9::1', '2001:db8::/32'));
    }

    public function test_never_matches_across_ip_versions_or_malformed_rules(): void
    {
        $this->assertFalse(IpAddress::matches('203.0.113.7', '2001:db8::/32'));
        $this->assertFalse(IpAddress::matches('203.0.113.7', '203.0.113.0/40'));
        $this->assertFalse(IpAddress::matches('203.0.113.7', 'office'));
    }
}
