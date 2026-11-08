<?php

namespace App\Support;

use Illuminate\Support\Str;

class ReportTitle
{
    public static function defaultForType(string $type): string
    {
        $label = Str::of($type)->replace('_', ' ')->title();

        return sprintf('%s - %s', $label, now()->toDateString());
    }
}
