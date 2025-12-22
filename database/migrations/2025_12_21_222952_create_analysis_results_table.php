<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_run_id')
                ->constrained('analysis_runs')
                ->onDelete('cascade');
            $table->string('result_type');
            $table->json('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
