<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use Exception;

class AffiliateService
{
    /**
     * The path to the source file.
     *
     * @var string
     */
    protected $filePath = 'affiliates/affiliates.txt';

    // Dublin office coordinates
    protected $officeLat = 53.3340285;
    protected $officeLng = -6.2535495;

    // Earth radius in kilometers
    protected $earthRadius = 6371;

    /**
     * Load affiliates from either an uploaded file or the source file.
     *
     * @param UploadedFile|null $file The uploaded file (optional).
     * @return Collection The valid affiliates.
     * @throws Exception If the file is not found or cannot be read.
     */
    public function loadAffiliates(?UploadedFile $file = null): Collection
    {
        $lines = [];

        if ($file) {
            if (!$file->isValid()) {
                throw new Exception('Uploaded file is not valid.');
            }
            $lines = explode("\n", file_get_contents($file->getRealPath()));
        } else {
            if (!Storage::exists($this->filePath)) {
                throw new Exception('Affiliates file not found.');
            }
            $lines = explode("\n", Storage::get($this->filePath));
        }

        return $this->extractValidNearbyAffiliates($lines);
    }

    /**
     * Extract valid affiliates from the given lines.
     *
     * @param array $lines The lines to extract affiliates from.
     * @return Collection The valid affiliates.
     */
    public function extractValidNearbyAffiliates(array $lines): Collection
    {
        $affiliates = collect();

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            try {
                $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                if (!isset($data['affiliate_id'], $data['name'], $data['latitude'], $data['longitude'])) {
                    throw new Exception("Missing required fields at line " . ($lineNumber + 1));
                }
                $distance = $this->greatCircleDistance(
                    $this->officeLat,
                    $this->officeLng,
                    (float)$data['latitude'],
                    (float)$data['longitude']
                );
                if ($distance <= 100) {
                    $affiliates->push([
                        'affiliate_id' => $data['affiliate_id'],
                        'name' => $data['name'],
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning("Invalid JSON at line " . ($lineNumber + 1) . ": " . $e->getMessage());
                continue;
            }
        }

        if ($affiliates->isEmpty()) {
            throw new Exception('No valid affiliates found within 100km.');
        }

        return $affiliates->sortBy('affiliate_id')->values();
    }

    /**
     * Calculate the  distance between two points using the Haversine formula.
     */
    protected function greatCircleDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $deltaLat = $lat2 - $lat1;
        $deltaLng = $lng2 - $lng1;

        $a = sin($deltaLat / 2) ** 2 +
            cos($lat1) * cos($lat2) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $this->earthRadius * $c;
    }
}
