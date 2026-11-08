<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DossierResource;
use App\Models\Dossier;
use Illuminate\Http\Request;

class DossierController extends Controller
{
    public function index(Request $request)
    {
        $query = Dossier::query()->withCount('reports');

        if ($request->boolean('with_reports')) {
            $query->with('reports');
        }

        $dossiers = $query->latest()->get();

        return DossierResource::collection($dossiers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_niche' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $dossier = Dossier::create($validated)->loadCount('reports');

        return (new DossierResource($dossier))->response()->setStatusCode(201);
    }

    public function show(Request $request, Dossier $dossier)
    {
        $dossier->loadCount('reports');

        if ($request->boolean('with_reports')) {
            $dossier->load('reports');
        }

        return new DossierResource($dossier);
    }

    public function update(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'client_niche' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        if ($validated) {
            $dossier->update($validated);
        }

        return new DossierResource($dossier->fresh()->loadCount('reports'));
    }

    public function destroy(Dossier $dossier)
    {
        $dossier->delete();

        return response()->noContent();
    }
}
