<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SourceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_returns_raw_content(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $report = Report::create([
            'title' => 'Market Radar - 2026-01-15',
            'type' => 'market_radar',
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => [],
        ]);

        $source = $report->sources()->create([
            'type' => 'url',
            'url' => 'https://competitor-one.test',
            'raw_content' => 'Sample raw content.',
        ]);

        $this->getJson("/api/sources/{$source->id}/preview")
            ->assertOk()
            ->assertJsonPath('raw_content', 'Sample raw content.');
    }

    public function test_preview_returns_not_found_without_raw_content(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $report = Report::create([
            'title' => 'Market Radar - 2026-01-15',
            'type' => 'market_radar',
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => [],
        ]);

        $source = $report->sources()->create([
            'type' => 'url',
            'url' => 'https://competitor-two.test',
        ]);

        $this->getJson("/api/sources/{$source->id}/preview")
            ->assertNotFound();
    }
}
