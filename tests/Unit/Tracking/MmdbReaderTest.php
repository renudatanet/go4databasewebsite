<?php

namespace Tests\Unit\Tracking;

use App\Tracking\Geo\DbIpAsnLocator;
use App\Tracking\Geo\DbIpCityLocator;
use App\Tracking\Geo\MmdbReader;
use App\Tracking\Geo\NetworkClassifier;
use RuntimeException;
use Tests\TestCase;
use Tests\Unit\Tracking\Support\TinyMmdbWriter;

class MmdbReaderTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = tempnam(sys_get_temp_dir(), 'mmdb');
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        parent::tearDown();
    }

    private function cityRecord(string $code, string $country, string $region, string $city, float $lat, float $lng): array
    {
        return [
            'country' => ['iso_code' => $code, 'names' => ['en' => $country]],
            'subdivisions' => ['array', [['names' => ['en' => $region]]]],
            'city' => ['names' => ['en' => $city]],
            'location' => ['latitude' => $lat, 'longitude' => $lng],
            'is_test' => true,
        ];
    }

    /** @dataProvider recordSizes */
    public function test_finds_records_for_every_record_size(int $recordSize): void
    {
        $writer = new TinyMmdbWriter($recordSize);
        $austin = $writer->add('203.0.113.0/24', $this->cityRecord('US', 'United States', 'Texas', 'Austin', 30.2672, -97.7431));
        $writer->add('198.51.100.0/24', $this->cityRecord('IN', 'India', 'Maharashtra', 'Mumbai', 19.076, 72.8777));
        $writer->add('192.0.2.0/24', [], $austin); // a pointer to the Austin record
        $writer->write($this->path);

        $reader = new MmdbReader($this->path);

        $this->assertSame('Test-City', $reader->metadata()['database_type']);
        $this->assertSame('Austin', $reader->get('203.0.113.77')['city']['names']['en']);
        $this->assertSame('Texas', $reader->get('203.0.113.1')['subdivisions'][0]['names']['en']);
        $this->assertSame(-97.7431, $reader->get('203.0.113.1')['location']['longitude']);
        $this->assertTrue($reader->get('203.0.113.1')['is_test']);
        $this->assertSame('Mumbai', $reader->get('198.51.100.9')['city']['names']['en']);
        $this->assertSame('Austin', $reader->get('192.0.2.50')['city']['names']['en'], 'followed the pointer');
        $this->assertNull($reader->get('8.8.8.8'));
        $this->assertNull($reader->get('not an ip'));
    }

    public static function recordSizes(): array
    {
        return ['24-bit' => [24], '28-bit' => [28], '32-bit' => [32]];
    }

    public function test_locators_turn_records_into_results_and_never_throw(): void
    {
        $writer = new TinyMmdbWriter();
        $writer->add('203.0.113.0/24', $this->cityRecord('US', 'United States', 'Texas', 'Austin', 30.2672, -97.7431));
        $writer->write($this->path);

        $geo = (new DbIpCityLocator($this->path))->locate('203.0.113.5');
        $this->assertSame(['US', 'United States', 'Texas', 'Austin'], [$geo->countryCode, $geo->country, $geo->region, $geo->city]);
        $this->assertSame(30.2672, $geo->latitude);

        $missing = new DbIpCityLocator('/nonexistent/file.mmdb');
        $this->assertNull($missing->locate('203.0.113.5')->countryCode);
    }

    public function test_network_records(): void
    {
        $writer = new TinyMmdbWriter();
        $writer->add('203.0.113.0/24', ['autonomous_system_number' => ['uint32', 64500], 'autonomous_system_organization' => 'Acme Corp']);
        $writer->write($this->path);

        $network = (new DbIpAsnLocator($this->path, new NetworkClassifier()))->locate('203.0.113.5');

        $this->assertSame(64500, $network->asn);
        $this->assertSame('Acme Corp', $network->networkOrg);
        $this->assertSame('business', $network->networkType);
    }

    public function test_refuses_files_that_are_not_mmdb(): void
    {
        file_put_contents($this->path, str_repeat('x', 1000));

        $this->expectException(RuntimeException::class);
        new MmdbReader($this->path);
    }

    public function test_reads_the_real_dbip_files_when_present(): void
    {
        $city = __DIR__ . '/../../../storage/app/tracking/geo/dbip-city-lite.mmdb';
        if (!is_file($city)) {
            $this->markTestSkipped('The DB-IP city file has not been downloaded here.');
        }

        $record = (new MmdbReader($city))->get('8.8.8.8');
        $this->assertSame('US', $record['country']['iso_code']);
    }
}
