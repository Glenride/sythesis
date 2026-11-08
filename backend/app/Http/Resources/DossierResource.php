<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DossierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_niche' => $this->client_niche,
            'notes' => $this->notes,
            'reports_count' => $this->when(isset($this->reports_count), $this->reports_count),
            'reports' => ReportResource::collection($this->whenLoaded('reports')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
