<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Support\ReportTitle;
use Illuminate\Http\Request;

class MarketRadarController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dossier_id' => ['nullable', 'integer', 'exists:dossiers,id'],
            'niche' => ['required', 'string', 'max:255'],
            'competitor_urls' => ['required', 'array', 'min:1', 'max:3'],
            'competitor_urls.*' => ['required', 'url', 'max:2048', 'distinct'],
            'geographic_focus' => ['nullable', 'string', 'max:255'],
        ]);

        $inputPayload = [
            'niche' => $validated['niche'],
            'competitor_urls' => $validated['competitor_urls'],
        ];

        if (isset($validated['geographic_focus'])) {
            $inputPayload['geographic_focus'] = $validated['geographic_focus'];
        }

        $report = Report::create([
            'dossier_id' => $validated['dossier_id'] ?? null,
            'title' => ReportTitle::defaultForType('market_radar'),
            'type' => 'market_radar',
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => $inputPayload,
        ]);

        foreach ($validated['competitor_urls'] as $url) {
            $report->sources()->create([
                'type' => 'url',
                'url' => $url,
                'metadata' => [
                    'category' => 'competitor',
                ],
            ]);
        }

        return (new ReportResource($report->load('sources')))->response()->setStatusCode(201);
    }
}
