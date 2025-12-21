<?php

namespace App\Console\Commands;

use App\Parsers\NginxAccessLogParser;
use App\Services\LicenseAccessAnalyzerService;
use Illuminate\Console\Command;
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

    private array $entries = [];

    public function __construct(
        private readonly NginxAccessLogParser $parser,
        private readonly LicenseAccessAnalyzerService $licenseAccessAnalyzer,
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
     */
    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_readable($path)) {
            $this->error("File not readable: {$path}");

            return self::FAILURE;
        }

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

        $parsedCount = 0;
        $errorCount = 0;

        foreach ($file as $line) {
            try {
                $entry = $this->parser->parse(trim($line));
                $this->licenseAccessAnalyzer->consume($entry);
                $parsedCount++;
            } catch (Throwable $e) {
                $errorCount++;
            }
        }

        $this->info("Parsed lines: {$parsedCount}");
        $this->warn("Failed lines: {$errorCount}");
        $this->info('Top 10 license serials by access count:');

        foreach ($this->licenseAccessAnalyzer->topSerials() as $serial => $count) {
            $this->line(sprintf('%s → %d requests', $serial, $count));
        }

        return self::SUCCESS;
    }
}
