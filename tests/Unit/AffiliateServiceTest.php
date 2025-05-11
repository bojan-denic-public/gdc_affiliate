<?php

namespace Tests\Unit;

use App\Services\AffiliateService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AffiliateServiceTest extends TestCase
{

    /**
     * Test the great-circle distance calculation.
     */
    public function test_great_circle_distance_is_accurate()
    {
        $service = new AffiliateService();

        $distance = $this->invokeMethod($service, 'greatCircleDistance', [
            53.3340285,
            -6.2535495,
            53.3340285,
            -6.2535495
        ]);
        $this->assertEquals(0, $distance);

        $distance = $this->invokeMethod($service, 'greatCircleDistance', [
            53.3340285,
            -6.2535495,
            54.3340285,
            -6.2535495
        ]);
        $this->assertGreaterThan(100, $distance);
    }

    /**
     * Test the extraction of valid nearby affiliates.
     */
    public function test_extract_valid_nearby_affiliates()
    {
        $service = new AffiliateService();

        $lines = [
            '{"latitude": "53.339428", "affiliate_id": 1, "name": "Bojan", "longitude": "-6.257664"}',
            '{"latitude": "52.986375", "affiliate_id": 2, "name": "Ana", "longitude": "-6.043701"}',
            '{"latitude": "51.92893", "affiliate_id": 3, "name": "Charlie", "longitude": "-10.27699"}'
        ];

        $result = $service->extractValidNearbyAffiliates($lines);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);
        $this->assertEquals([1, 2], $result->pluck('affiliate_id')->all());
    }

    /**
     * Test loading affiliates from source when file is not found.
     */
    public function test_load_affiliates_throws_exception_if_source_file_not_found()
    {
        $service = new AffiliateService();

        Storage::shouldReceive('exists')
            ->with('affiliates/affiliates.txt')
            ->andReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Affiliates file not found.');

        $service->loadAffiliates();
    }

    /**
     * Test loading affiliates with an invalid uploaded file.
     */
    public function test_load_affiliates_throws_exception_if_file_not_valid()
    {
        $service = new AffiliateService();

        /** @var UploadedFile&\PHPUnit\Framework\MockObject\MockObject $file */
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file->method('isValid')
            ->willReturn(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Uploaded file is not valid.');

        $service->loadAffiliates($file);
    }

    // Helper to call protected/private methods
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
