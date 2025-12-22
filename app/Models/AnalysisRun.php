<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisRun extends Model
{
    protected $fillable = [
        'source_file',
        'started_at',
        'finished_at',
        'duration_seconds',
        'parsed_lines',
        'error_lines',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(AnalysisResult::class);
    }
}
