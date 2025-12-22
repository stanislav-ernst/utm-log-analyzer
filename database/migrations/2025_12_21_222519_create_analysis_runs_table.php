<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source_file');
            $table->dateTime('started_at');
            $table->dateTime('finished_at')
                ->nullable();
            $table->integer('duration_seconds')
                ->nullable();
            $table->integer('parsed_lines')
                ->default(0);
            $table->integer('error_lines')
                ->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_runs');
    }
};
