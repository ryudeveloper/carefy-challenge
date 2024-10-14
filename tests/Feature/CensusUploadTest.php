<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CensusUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_upload_a_csv_file()
    {
        $file = new \Illuminate\Http\UploadedFile(
            resource_path('test-files/test_census.csv'),
            'test_census.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->post('/api/census/upload', [
            'census_file' => $file,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('patients', [
            'var_nome' => 'Maria',
        ]);
    }
}
