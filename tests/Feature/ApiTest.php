<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API lead ingestion with valid API key
     */
    public function test_api_lead_ingestion_with_valid_key(): void
    {
        config(['app.api_key_salt' => 'test-salt']);
        $apiKey = hash('sha256', 'test-salt' . 'test-key');

        $response = $this->postJson('/api/leads/ingest', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'source' => 'API Test',
        ], [
            'X-API-KEY' => $apiKey,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Lead ingested successfully',
        ]);
    }

    /**
     * Test API lead ingestion without API key
     */
    public function test_api_lead_ingestion_without_key(): void
    {
        $response = $this->postJson('/api/leads/ingest', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test API lead ingestion with invalid API key
     */
    public function test_api_lead_ingestion_with_invalid_key(): void
    {
        $response = $this->postJson('/api/leads/ingest', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
        ], [
            'X-API-KEY' => 'invalid-key',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test API validation for required fields
     */
    public function test_api_validation_for_required_fields(): void
    {
        config(['app.api_key_salt' => 'test-salt']);
        $apiKey = hash('sha256', 'test-salt' . 'test-key');

        $response = $this->postJson('/api/leads/ingest', [], [
            'X-API-KEY' => $apiKey,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['company_name', 'email']);
    }

    /**
     * Test API rate limiting
     */
    public function test_api_rate_limiting(): void
    {
        config(['app.api_key_salt' => 'test-salt']);
        $apiKey = hash('sha256', 'test-salt' . 'test-key');

        // Make 61 requests (rate limit is 60/minute)
        for ($i = 0; $i < 61; $i++) {
            $response = $this->postJson('/api/leads/ingest', [
                'company_name' => 'Test Company',
                'email' => "test{$i}@example.com",
            ], [
                'X-API-KEY' => $apiKey,
            ]);
        }

        // Last request should be rate limited
        $response->assertStatus(429);
    }
}
