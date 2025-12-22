<?php

namespace App\Services;

use App\DTO\LogEntry;
use App\Services\Contracts\AnalyzerInterface;

class LicenseDeviceAnalyzerService implements AnalyzerInterface
{
    /**
     * serial => [deviceId => true]
     *
     * @var array<string, array<string, bool>>
     */
    private array $devicesBySerial = [];

    public function __construct() {}

    /**
     * Consumes a log entry and tracks unique devices associated with a serial.
     *
     * @param  LogEntry  $entry  Instance of the log entry to be processed.
     */
    public function consume(LogEntry $entry): void
    {
        $serial = $entry->serial;
        $deviceId = $entry->specs->deviceId();

        $this->devicesBySerial[$serial][$deviceId] = true;
    }

    /**
     * Returns serials with multiple associated devices, ranked by count in descending order.
     *
     * @param  int  $limit  The maximum number of entries to return. Defaults to 10.
     * @return array<string, int> An associative array of serials and their respective device counts.
     */
    public function result(int $limit = 10): array
    {
        $violations = [];

        foreach ($this->devicesBySerial as $serial => $devices) {
            $count = count($devices);

            if ($count > 1) {
                $violations[$serial] = $count;
            }
        }

        arsort($violations);

        return array_slice($violations, 0, $limit, true);
    }
}
