<?php

namespace App\Services;

use App\DTO\LogEntry;

class LicenseDeviceAnalyzerService
{
    /**
     * serial => [deviceId => true]
     *
     * @var array<string, array<string, bool>>
     */
    private array $devicesBySerial = [];

    public function __construct() {}

    /**
     * Consumes a log entry and updates the devices by their serial number.
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
     * Identifies serial numbers with multiple associated devices and ranks them
     * in descending order of count, returning a limited subset of violations.
     *
     * @param  int  $limit  The maximum number of violations to return. Defaults to 10.
     * @return array<string, int> An associative array of serial numbers as keys and their respective counts as values.
     */
    public function violations(int $limit = 10): array
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
