<?php

namespace App\DTO;

use DateTimeImmutable;

final class LogEntry
{
    public function __construct(
        public readonly string $ipAddress,
        public readonly string $updateServer,
        public readonly DateTimeImmutable $timestamp,
        public readonly string $httpMethod,
        public readonly string $url,
        public readonly int $statusCode,
        public readonly int $responseSize,
        public readonly string $proxy,
        public readonly float $responseTime,
        public readonly string $serial,
        public readonly string $version,
        public readonly Specs $specs,
    ) {}
}
