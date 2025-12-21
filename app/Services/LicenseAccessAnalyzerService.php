<?php

namespace App\Services;

class LicenseAccessAnalyzerService
{
    public function __construct() {}

    /**
     * @param  iterable<LogEntry>  $entries
     * @return array<string, int> serial => access count
     */
    public function topSerials(iterable $entries, int $limit = 10): array
    {
        $counts = [];

        foreach ($entries as $entry) {
            $serial = $entry->serial;
            $counts[$serial] = ($counts[$serial] ?? 0) + 1;
        }

        arsort($counts);

        return array_slice($counts, 0, $limit, true);
    }
}
