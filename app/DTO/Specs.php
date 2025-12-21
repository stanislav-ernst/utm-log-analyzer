<?php

namespace App\DTO;

final class Specs
{
    public function __construct(
        public readonly string $mac,
        public readonly string $architecture,
        public readonly string $machine,
        public readonly int $memoryKb,
        public readonly string $cpu,
        public readonly int $diskRootKb,
        public readonly int $diskDataKb,
        public readonly string $uptime,
        public readonly string $firmwareVersion,
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
