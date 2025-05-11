<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AffiliateApiTest extends TestCase
{
    /**
     * Test the load of affiliates from the source file.
     */
    public function test_load_affiliates_from_source()
    {
        Storage::fake('local');
        Storage::put('affiliates/affiliates.txt', implode("\n", [
            '{"latitude": "53.339428", "affiliate_id": 1, "name": "Ana", "longitude": "-6.257664"}',
            '{"latitude": "51.92893", "affiliate_id": 2, "name": "Bojan", "longitude": "-10.27699"}'
        ]));

        $response = $this->postJson('/api/invite-affiliates', []);
        $response->assertStatus(200)
            ->assertJsonStructure(['affiliates'])
            ->assertJsonFragment(['affiliate_id' => 1, 'name' => 'Ana'])
            ->assertJsonMissing(['affiliate_id' => 2, 'name' => 'Bojan']);
    }

    /**
     * Test the upload of affiliates from an uploaded file.
     */
    public function test_upload_affiliates_file()
    {
        $fileContent = implode("\n", [
            '{"latitude": "53.339428", "affiliate_id": 1, "name": "Ana", "longitude": "-6.257664"}',
            '{"latitude": "51.92893", "affiliate_id": 2, "name": "Bojan", "longitude": "-10.27699"}'
        ]);
        $file = UploadedFile::fake()->createWithContent('affiliates.txt', $fileContent);

        $response = $this->postJson('/api/invite-affiliates', [
            'affiliates' => $file,
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(['affiliates'])
            ->assertJsonFragment(['affiliate_id' => 1, 'name' => 'Ana'])
            ->assertJsonMissing(['affiliate_id' => 2, 'name' => 'Bojan']);
    }

    /**
     * Test the upload of affiliates from an uploaded file with an invalid extension returns an error.
     */
    public function test_upload_affiliates_file_with_invalid_extension_returns_error()
    {
        $file = UploadedFile::fake()->create('affiliates.csv', 10);

        $response = $this->postJson('/api/invite-affiliates', [
            'affiliates' => $file,
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['affiliates']);
    }

    /**
     * Test the upload of affiliates from an uploaded file with an empty file returns an error.
     */
    public function test_upload_affiliates_file_with_empty_file_returns_error()
    {
        $file = UploadedFile::fake()->createWithContent('affiliates.txt', '');

        $response = $this->postJson('/api/invite-affiliates', [
            'affiliates' => $file,
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'No valid affiliates found within 100km.']);
    }
}
