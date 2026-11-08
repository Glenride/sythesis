<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Support\ReportTitle;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query();

        if ($request->boolean('with_sources')) {
            $query->with('sources');
        }

        if ($request->filled('dossier_id')) {
            $query->where('dossier_id', $request->input('dossier_id'));
        }

        if ($request->boolean('unassigned')) {
            $query->whereNull('dossier_id');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return ReportResource::collection($query->latest()->get());
    }

    public function show(Report $report)
    {
        return new ReportResource($report->load('sources'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
        ]);

        if ($validated) {
            $report->update($validated);
        }

        return new ReportResource($report->fresh()->load('sources'));
    }

    public function assign(Request $request, Report $report)
    {
        $validated = $request->validate([
            'dossier_id' => ['nullable', 'integer', 'exists:dossiers,id'],
        ]);

        $report->update([
            'dossier_id' => $validated['dossier_id'] ?? null,
        ]);

        return new ReportResource($report->fresh()->load('sources'));
    }

    public function refine(Request $request, Report $report)
    {
        if ($report->refinements()->exists()) {
            return response()->json([
                'message' => 'Refinement already exists for this report.',
            ], 409);
        }

        $validated = $request->validate([
            'prompt' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $refinement = Report::create([
            'dossier_id' => $report->dossier_id,
            'parent_report_id' => $report->id,
            'title' => $validated['title'] ?? ReportTitle::defaultForType($report->type),
            'type' => $report->type,
            'status' => 'pending',
            'progress' => 0,
            'input_payload' => [
                'prompt' => $validated['prompt'],
                'base_report_id' => $report->id,
            ],
        ]);

        return (new ReportResource($refinement))->response()->setStatusCode(201);
    }

    public function history(Report $report)
    {
        $root = $report;

        while ($root->parent) {
            $root = $root->parent;
        }

        $history = new EloquentCollection();
        $current = $root;

        while ($current) {
            $history->push($current);
            $current = $current->refinements()->orderBy('created_at')->first();
        }

        $history->load('sources');

        return ReportResource::collection($history);
    }
}
