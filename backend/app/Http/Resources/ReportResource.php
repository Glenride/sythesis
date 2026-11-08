<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'status' => $this->status,
            'progress' => $this->progress,
            'dossier_id' => $this->dossier_id,
            'parent_report_id' => $this->parent_report_id,
            'input_payload' => $this->input_payload,
            'result_markdown' => $this->result_markdown,
            'sources' => SourceResource::collection($this->whenLoaded('sources')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
