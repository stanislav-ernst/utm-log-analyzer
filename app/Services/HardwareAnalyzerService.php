<?php

namespace App\Services;

use App\DTO\LogEntry;
use App\Services\Contracts\AnalyzerInterface;

class HardwareAnalyzerService implements AnalyzerInterface
{
    /**
     * @var array<string, array<string, bool>> Stores licenses grouped by hardware classification and serial number.
     */
    private array $licensesByHardware = [];

    public function __construct() {}

    /**
     * Consumes a log entry and groups licenses by hardware classification.
     *
     * @param  LogEntry  $entry  Instance of the log entry to be processed.
     */
    public function consume(LogEntry $entry): void
    {
        $hardwareClass = $this->classify($entry);
        $serial = $entry->serial;

        $this->licensesByHardware[$hardwareClass][$serial] = true;
    }

    /**
     * Returns a summary of hardware classes and their license counts in descending order.
     *
     * Only includes hardware classes with more than one unique license.
     *
     * @param  int|null  $limit  The maximum number of entries to return. Defaults to 10.
     * @return array<string, int> An associative array of hardware classes and their license counts.
     */
    public function result(?int $limit = null): array
    {
        $result = [];

        foreach ($this->licensesByHardware as $hardwareClass => $serials) {
            $count = count($serials);

            if ($count > 1) {
                $result[$hardwareClass] = $count;
            }
        }

        arsort($result);

        if ($limit === null) {
            return $result;
        }

        return array_slice($result, 0, $limit, true);
    }

    /**
     * Classifies the given log entry based on its specifications.
     *
     * Determines the memory class in gigabytes and the CPU type
     * (Intel, AMD, or other) based on the provided specs.
     * Returns a formatted string containing architecture, machine,
     * memory classification, and CPU type.
     *
     * @param  LogEntry  $entry  The log entry containing specifications to classify.
     * @return string A formatted string detailing the system's architecture,
     *                machine type, RAM classification, and CPU type.
     */
    private function classify(LogEntry $entry): string
    {
        $specs = $entry->specs;

        $ramGb = (int) ceil($specs->memoryKb / 1024 / 1024);

        $ramClass = match (true) {
            $ramGb <= 2 => '≤2GB',
            $ramGb <= 4 => '≤4GB',
            $ramGb <= 8 => '≤8GB',
            default => '>8GB',
        };

        $cpuClass = match (true) {
            str_contains(strtolower($specs->cpu), 'intel') => 'Intel',
            str_contains(strtolower($specs->cpu), 'amd') => 'AMD',
            default => 'Other',
        };

        return sprintf(
            '%s | %s | %s RAM | %s CPU',
            $specs->architecture,
            $specs->machine,
            $ramClass,
            $cpuClass
        );
    }
}
