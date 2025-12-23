<?php

namespace App\DTO;

use DateTimeImmutable;

final readonly class LogEntry
{
    public function __construct(
        public string $ipAddress,
        public string $updateServer,
        public DateTimeImmutable $timestamp,
        public string $httpMethod,
        public string $url,
        public int $statusCode,
        public int $responseSize,
        public string $proxy,
        public float $responseTime,
        public string $serial,
        public string $version,
        public Specs $specs,
    ) {}
}
