<?php

namespace App\Services;

use App\DTO\LogEntry;
use App\Services\Contracts\AnalyzerInterface;

class LicenseAccessAnalyzerService implements AnalyzerInterface
{
    /**
     * Counted serials.
     *
     * @var array<string, int>
     */
    private array $counts = [];

    public function __construct() {}

    /**
     * Consumes a log entry and updates the count for the associated serial.
     *
     * @param  LogEntry  $entry  Instance of the log entry to be processed.
     */
    public function consume(LogEntry $entry): void
    {
        $serial = $entry->serial;

        $this->counts[$serial] = ($this->counts[$serial] ?? 0) + 1;
    }

    /**
     * Returns the top serial numbers based on their count in descending order.
     *
     * @param  int  $limit  The maximum number of entries to return. Defaults to 10.
     * @return array<string, int> An associative array of serials and their respective counts.
     */
    public function result(int $limit = 10): array
    {
        arsort($this->counts);

        return array_slice($this->counts, 0, $limit, true);
    }
}
