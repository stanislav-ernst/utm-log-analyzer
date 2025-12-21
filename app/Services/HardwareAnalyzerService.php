<?php

namespace App\Services;

use App\DTO\LogEntry;

class HardwareAnalyzerService
{
    /**
     * @var array<string, array<string, bool>> Stores licenses grouped by hardware classification and serial number.
     */
    private array $licensesByHardware = [];

    public function __construct() {}

    /**
     * Processes the given log entry and stores it based on hardware classification and serial number.
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
     * Generates a summary of hardware classes and their associated license counts.
     *
     * Iterates through the licenses grouped by hardware class and calculates the number
     * of licenses for each class. Only includes hardware classes with more than one license
     * in the result. The summary is sorted in descending order based on the counts.
     *
     * @return array<string, int> An associative array where the keys are hardware classes and the values
     *                            are the counts of licenses for each class, sorted in descending order.
     */
    public function summary(): array
    {
        $result = [];

        foreach ($this->licensesByHardware as $hardwareClass => $serials) {
            $count = count($serials);

            if ($count > 1) {
                $result[$hardwareClass] = $count;
            }
        }

        arsort($result);

        return $result;
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
