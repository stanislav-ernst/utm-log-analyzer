<?php

namespace App\Parsers;

use App\DTO\LogEntry;
use App\DTO\Specs;
use DateTimeImmutable;
use JsonException;
use RuntimeException;

class NginxAccessLogParser
{
    /**
     * Parses a log line and extracts relevant components such as IP address, server, timestamp,
     * HTTP method, URL, status code, response size, proxy information, response time, serial,
     * version, and specifications.
     *
     * @param  string  $line  The log line in a specified format to be parsed.
     * @return LogEntry Extracted and processed log components encapsulated in a LogEntry object.
     *
     * @throws RuntimeException|JsonException If the log line does not match the expected format.
     */
    public function parse(string $line): LogEntry
    {
        // Splitt log line into parts.
        if (! preg_match(
            '/^(?<ip>\S+)\s+(?<server>\S+)\s+\[(?<time>[^\]]+)]\s+"(?<method>\S+)\s+(?<url>\S+)/',
            $line,
            $matches
        )) {
            throw new RuntimeException('Invalid log line format.');
        }

        /*
         *
         * DateTimeImmutable ensures that the object is immutable after it has been created. Therefore, no carbon is used.
         * Slightly lighter than the extensive carbon library.
         * Comfortable Carbon helper methods are not required.
         */
        $timestamp = new DateTimeImmutable($matches['time']);

        preg_match('/\s(?<status>\d{3})\s(?<size>\d+)/', $line, $response);
        preg_match('/proxy=(?<proxy>\S+)/', $line, $proxy);
        preg_match('/rt=(?<rt>[0-9.]+)/', $line, $rt);
        preg_match('/serial=(?<serial>\S+)/', $line, $serial);
        preg_match('/version=(?<version>\S+)/', $line, $version);
        preg_match('/specs=(?<specs>\S+)/', $line, $specs);

        $specsDto = $this->decodeSpecs($specs['specs']);

        return new LogEntry(
            ipAddress: $matches['ip'],
            updateServer: $matches['server'],
            timestamp: $timestamp,
            httpMethod: $matches['method'],
            url: $matches['url'],
            statusCode: (int) $response['status'],
            responseSize: (int) $response['size'],
            proxy: $proxy['proxy'],
            responseTime: (float) $rt['rt'],
            serial: $serial['serial'],
            version: $version['version'],
            specs: $specsDto,
        );
    }

    /**
     * Decodes the provided encoded string and returns a Specs object.
     *
     * The input string is base64 decoded, decompressed using gzip, and then
     * parsed as a JSON object. The resulting data is mapped into a Specs
     * instance with the respective properties populated.
     *
     * @param  string  $encoded  The encoded string containing the specifications data.
     * @return Specs Returns an instance of the Specs class populated with the
     *               decoded data.
     *
     * @throws JsonException If the JSON decoding fails due to invalid syntax or data.
     */
    private function decodeSpecs(string $encoded): Specs
    {
        $json = gzdecode(base64_decode($encoded));
        $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

        return new Specs(
            mac: $data['mac'],
            architecture: $data['architecture'],
            machine: $data['machine'],
            memoryKb: (int) str_replace('kB', '', $data['mem']),
            cpu: $data['cpu'],
            diskRootKb: (int) $data['disk_root'],
            diskDataKb: (int) $data['disk_data'],
            uptime: $data['uptime'],
            firmwareVersion: $data['fwversion'],
        );
    }
}
