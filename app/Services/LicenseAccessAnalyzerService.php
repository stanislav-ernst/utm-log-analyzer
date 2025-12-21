<?php

namespace App\Services;

use App\DTO\LogEntry;

class LicenseAccessAnalyzerService
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
     * @param  LogEntry  $entry  Log entry object containing the serial.
     */
    public function consume(LogEntry $entry): void
    {
        $serial = $entry->serial;
        $this->counts[$serial] = ($this->counts[$serial] ?? 0) + 1;
    }

    /**
     * Retrieves the top serial numbers based on their count in descending order.
     *
     * @param  int  $limit  The maximum number of top serials to return. Defaults to 10.
     * @return array An associative array of the top serials with their counts.
     */
    public function topSerials(int $limit = 10): array
    {
        arsort($this->counts);

        return array_slice($this->counts, 0, $limit, true);
    }
}
