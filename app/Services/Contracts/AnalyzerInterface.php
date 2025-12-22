<?php

namespace App\Services\Contracts;

use App\DTO\LogEntry;

interface AnalyzerInterface
{
    /**
     * Consumes a log entry for further analysis.
     *
     * @param  LogEntry  $entry  The log entry instance to be processed.
     */
    public function consume(LogEntry $entry): void;

    /**
     * Returns the aggregated result of the analysis.
     *
     * @return array<string, int> An associative array of the analysis results.
     */
    public function result(): array;
}
