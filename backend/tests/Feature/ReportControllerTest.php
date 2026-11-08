<?php

namespace Tests\Feature;

use App\Models\Dossier;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_report_to_dossier(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $dossier = Dossier::create([
            'name' => 'Acme Co',
        ]);
        $report = $this->createReport();

        $this->patchJson("/api/reports/{$report->id}/assign", [
            'dossier_id' => $dossier->id,
        ])->assertOk()
            ->assertJsonPath('data.dossier_id', $dossier->id);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'dossier_id' => $dossier->id,
        ]);
    }

    public function test_refine_creates_child_and_blocks_duplicates(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $report = $this->createReport();

        $this->postJson("/api/reports/{$report->id}/refine", [
            'prompt' => 'Tighten the summary.',
            'title' => 'Refined Radar',
        ])->assertCreated()
            ->assertJsonPath('data.parent_report_id', $report->id);

        $this->postJson("/api/reports/{$report->id}/refine", [
            'prompt' => 'Another take.',
        ])->assertStatus(409);
    }

    public function test_history_returns_linear_chain(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $base = $this->createReport([
            'title' => 'Base Report',
        ]);

        $child = $this->createReport([
            'parent_report_id' => $base->id,
            'title' => 'Refinement One',
        ]);

        $grandchild = $this->createReport([
            'parent_report_id' => $child->id,
            'title' => 'Refinement Two',
        ]);

        $this->getJson("/api/reports/{$grandchild->id}/history")
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.id', $base->id)
            ->assertJsonPath('data.1.id', $child->id)
            ->assertJsonPath('data.2.id', $grandchild->id);
    }

    private function createReport(array $overrides = []): Report
    {
        return Report::create(array_merge([
            'title' => 'Market Radar - 2026-01-15',
            'type' => 'market_radar',
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => [],
        ], $overrides));
    }
}
