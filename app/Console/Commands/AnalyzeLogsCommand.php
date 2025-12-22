<?php

namespace App\Console\Commands;

use App\Enums\ResultType;
use App\Models\AnalysisResult;
use App\Models\AnalysisRun;
use App\Parsers\NginxAccessLogParser;
use App\Services\HardwareAnalyzerService;
use App\Services\LicenseAccessAnalyzerService;
use App\Services\LicenseDeviceAnalyzerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SplFileObject;
use Throwable;

class AnalyzeLogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utm:analyze {path : Path to UTM access log.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze UTM access logs.';

    public function __construct(
        private readonly NginxAccessLogParser $parser,
        private readonly LicenseAccessAnalyzerService $licenseAccessAnalyzer,
        private readonly LicenseDeviceAnalyzerService $licenseDeviceAnalyzer,
        private readonly HardwareAnalyzerService $hardwareAnalyzer,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Handles the processing of a file provided via command argument.
     *
     * Validates the provided file path for readability. Reads the file line
     * by line, parsing each line using the parser. Tracks and logs the count
     * of successfully parsed lines and failed parsing attempts.
     * Also analyzes license access and provides statistics.
     * Returns an appropriate status code indicating success or failure.
     *
     * @return int The status code indicating the result of the file processing.
     * @throws Throwable
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_readable($path)) {
            $this->error("File not readable: {$path}");

            return self::FAILURE;
        }

        /**
         * ----------------------------
         * Create new Analysis Run
         * ----------------------------
         */
        $analysisRun = AnalysisRun::create([
            'source_file' => $path,
            'started_at' => now(),
        ]);

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

        $parsedCount = 0;
        $errorCount = 0;

        $fileSize = filesize($path);

        /**
         * ----------------------------
         * Progress Bar
         * ----------------------------
         */
        $progressBar = $this->output->createProgressBar($fileSize);
        $progressBar->setMessage('Lines: 0', 'status');
        $progressBar->setFormat('%status% | %percent%% [%bar%]');
        $progressBar->start();

        /**
         * ----------------------------
         * Streaming Processing
         * ----------------------------
         */
        foreach ($file as $line) {
            try {
                $entry = $this->parser->parse(trim($line));

                $this->licenseAccessAnalyzer->consume($entry);
                $this->licenseDeviceAnalyzer->consume($entry);
                $this->hardwareAnalyzer->consume($entry);

                $parsedCount++;
            } catch (Throwable) {
                // Here you could process the invalid lines.
                $errorCount++;
            }

            // Update progress based on file position
            $progressBar->setProgress($file->ftell());
            $progressBar->setMessage("Lines: {$parsedCount}", 'status');
            $progressBar->setFormat(
                '%status% | %percent%% [%bar%]'
            );
        }

        $progressBar->finish();
        $this->newLine(2);

        /**
         * ----------------------------
         * Results persist
         * ----------------------------
         */
        DB::transaction(function () use (
            $analysisRun,
            $parsedCount,
            $errorCount
        ) {
            // Update Metadata
            $analysisRun->update([
                'finished_at' => now(),
                'duration_seconds' => $analysisRun->started_at->diffInSeconds(now()),
                'parsed_lines' => $parsedCount,
                'error_lines' => $errorCount,
            ]);

            // Task 1 – Top Serials
            AnalysisResult::create([
                'analysis_run_id' => $analysisRun->id,
                'result_type' => ResultType::LICENSE_ACCESS,
                'payload' => [
                    'items' => collect($this->licenseAccessAnalyzer->result())
                        ->map(fn (int $count, string $serial) => [
                            'serial' => $serial,
                            'count' => $count,
                        ])
                        ->values()
                        ->all(),
                ],
            ]);

            // Task 2 – Multiple license use
            AnalysisResult::create([
                'analysis_run_id' => $analysisRun->id,
                'result_type' => ResultType::MULTI_DEVICE,
                'payload' => [
                    'items' => collect($this->licenseDeviceAnalyzer->result())
                        ->map(fn (int $devices, string $serial) => [
                            'serial' => $serial,
                            'devices' => $devices,
                        ])
                        ->values()
                        ->all(),
                ],
            ]);

            // Task 3 – Hardware classes
            AnalysisResult::create([
                'analysis_run_id' => $analysisRun->id,
                'result_type' => ResultType::HARDWARE_CLASS,
                'payload' => [
                    'items' => collect($this->hardwareAnalyzer->result())
                        ->map(fn (int $licenses, string $hardwareClass) => [
                            'hardware_class' => $hardwareClass,
                            'licenses' => $licenses,
                        ])
                        ->values()
                        ->all(),
                ],
            ]);
        });

        /**
         * ----------------------------
         * Console output (optional)
         * ----------------------------
         */
        $this->info("Parsed lines: {$parsedCount}");
        $this->warn("Failed lines: {$errorCount}");
        $this->info("Analysis run #{$analysisRun->id} stored successfully.");
        $this->info('Top 10 license serials by access count:');

        foreach ($this->licenseAccessAnalyzer->result() as $serial => $count) {
            $this->line(sprintf('%s → %d requests', $serial, $count));
        }

        $this->info('Top 10 license serials with multiple devices:');

        foreach ($this->licenseDeviceAnalyzer->result() as $serial => $deviceCount) {
            $this->line(sprintf('%s → %d distinct devices', $serial, $deviceCount));
        }

        $this->info('Hardware classes and active license count:');

        foreach ($this->hardwareAnalyzer->result() as $hardwareClass => $count) {
            $this->line(sprintf('%s → %d licenses', $hardwareClass, $count));
        }

        return self::SUCCESS;
    }
}
