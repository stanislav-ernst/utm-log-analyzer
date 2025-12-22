<?php

namespace App\Http\Controllers;

use App\Models\AnalysisRun;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    /**
     * Displays a paginated list of analysis runs with specific attributes.
     * If no analysis runs are available, renders an empty state view.
     *
     * @return View The rendered view displaying the list of analysis runs or an empty state.
     */
    public function index(): View
    {
        $runs = AnalysisRun::query()
            ->latest()
            ->select([
                'id',
                'source_file',
                'started_at',
                'finished_at',
                'parsed_lines',
                'error_lines',
                'created_at',
            ])
            ->paginate(15);

        if ($runs->isEmpty()) {
            return view('analysis.empty');
        }

        return view('analysis.runs', [
            'runs' => $runs,
        ]);
    }

    /**
     * Displays the specified analysis run along with its associated results, grouped by result type.
     *
     * @param  AnalysisRun  $analysisRun  The instance of the analysis run to display.
     * @return View The rendered view displaying the analysis run and grouped results.
     */
    public function show(AnalysisRun $analysisRun): View
    {
        // Load results with eager
        $analysisRun->load('results');

        // Group results by type
        $resultsByType = $analysisRun->results
            ->keyBy('result_type');

        return view('analysis.show', [
            'run' => $analysisRun,
            'results' => $resultsByType,
        ]);
    }
}
