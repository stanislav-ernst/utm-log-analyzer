<?php

namespace App\Models;

use App\Enums\ResultType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisResult extends Model
{
    protected $fillable = [
        'analysis_run_id',
        'result_type',
        'payload',
    ];

    protected $casts = [
        'result_type' => ResultType::class,
        'payload' => 'array',
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(AnalysisRun::class);
    }

    /**
     * Scope a query to filter results by the specified result type.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  ResultType  $type  The result type to filter by.
     * @return Builder The modified query builder instance.
     */
    public function scopeOfType(Builder $query, ResultType $type): Builder
    {
        return $query->where('result_type', $type->value);
    }
}
