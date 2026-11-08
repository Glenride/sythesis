<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MarketRadarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_market_radar_creates_report_and_sources(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $dossier = Dossier::create([
            'name' => 'Acme Co',
        ]);

        $payload = [
            'dossier_id' => $dossier->id,
            'niche' => 'Cosmetic Dentistry',
            'competitor_urls' => [
                'https://competitor-one.test',
                'https://competitor-two.test',
            ],
            'geographic_focus' => 'Austin',
        ];

        $response = $this->postJson('/api/synthesis/market-radar', $payload)
            ->assertCreated();

        $response->assertJsonPath('data.type', 'market_radar')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.progress', 0)
            ->assertJsonCount(2, 'data.sources');

        $this->assertDatabaseCount('sources', 2);
    }
}
