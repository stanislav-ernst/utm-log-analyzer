<?php

namespace App\DTO;

final readonly class Specs
{
    public function __construct(
        public string $mac,
        public string $architecture,
        public string $machine,
        public int $memoryKb,
        public string $cpu,
        public int $diskRootKb,
        public int $diskDataKb,
        public string $uptime,
        public string $firmwareVersion,
    ) {}

    /**
     * Eindeutige Geräte-ID.
     * Business-Regel: Die MAC-Adresse identifiziert ein physisches Gerät eindeutig.
     */
    public function deviceId(): string
    {
        return strtolower($this->mac);
    }
}
