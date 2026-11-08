<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DossierControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_list_dossiers(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/dossiers', [
            'name' => 'Acme Co',
            'client_niche' => 'Dental',
            'notes' => 'Primary client.',
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Acme Co');

        $this->getJson('/api/dossiers')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_show_with_reports_and_update(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $dossier = Dossier::create([
            'name' => 'Acme Co',
        ]);

        $report = Report::create([
            'dossier_id' => $dossier->id,
            'title' => 'Market Radar - 2026-01-15',
            'type' => 'market_radar',
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => [],
        ]);

        $this->getJson("/api/dossiers/{$dossier->id}?with_reports=1")
            ->assertOk()
            ->assertJsonPath('data.reports.0.id', $report->id);

        $this->patchJson("/api/dossiers/{$dossier->id}", [
            'notes' => 'Updated notes.',
        ])->assertOk()
            ->assertJsonPath('data.notes', 'Updated notes.');
    }
}
