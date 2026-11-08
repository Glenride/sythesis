<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Source;

class SourceController extends Controller
{
    public function preview(Source $source)
    {
        if (! $source->raw_content) {
            return response()->json([
                'message' => 'No preview available for this source.',
            ], 404);
        }

        return response()->json([
            'id' => $source->id,
            'report_id' => $source->report_id,
            'raw_content' => $source->raw_content,
        ]);
    }
}
