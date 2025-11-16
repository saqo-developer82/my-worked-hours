<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportWorkedHourRequest;
use App\Http\Requests\StoreWorkedHourRequest;
use App\Http\Requests\UpdateWorkedHourRequest;
use App\Services\WorkedHourService;

class WorkedHourController extends Controller
{
    public function __construct(
        private WorkedHourService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = [
            'task' => request()->get('task'),
            'date' => request()->get('date'),
            'start_date' => request()->get('start_date'),
            'end_date' => request()->get('end_date'),
        ];

        $workedHours = $this->service->getPaginatedWorkedHours(10, $filters);

        // Calculate totals if date range is provided
        $totalWorkedHours = null;
        $startDate = request()->get('total_start_date');
        $endDate = request()->get('total_end_date');
        
        if ($startDate && $endDate) {
            $totalWorkedHours = $this->service->getTotalWorkedHoursForDateRange($startDate, $endDate);
        }

        return view('worked-hours.index', compact('workedHours', 'totalWorkedHours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('worked-hours.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkedHourRequest $request)
    {
        $data = $request->validated();
        $insertedCount = $this->service->storeWorkedHours($data);

        $message = $insertedCount > 1 
            ? "Successfully inserted {$insertedCount} worked hour records."
            : 'Worked hour record created successfully.';

        return redirect()->route('worked-hours.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $workedHour = $this->service->getWorkedHourById($id);

        if (!$workedHour) {
            return redirect()->route('worked-hours.index')
                ->with('error', 'Worked hour record not found.');
        }

        return view('worked-hours.edit', compact('workedHour'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkedHourRequest $request, int $id)
    {
        $data = $request->validated();
        $updated = $this->service->updateWorkedHour($id, $data);

        if ($updated) {
            return redirect()->route('worked-hours.index')
                ->with('success', 'Worked hour record updated successfully.');
        }

        return redirect()->route('worked-hours.index')
            ->with('error', 'Failed to update worked hour record.');
    }

    /**
     * Delete a worked hour record.
     */
    public function destroy(int $id)
    {
        $deleted = $this->service->deleteWorkedHour($id);

        if ($deleted) {
            return redirect()->route('worked-hours.index')
                ->with('success', 'Worked hour record deleted successfully.');
        }

        return redirect()->route('worked-hours.index')
            ->with('error', 'Failed to delete worked hour record.');
    }

    /**
     * Show the export form.
     */
    public function export()
    {
        return view('worked-hours.export');
    }

    /**
     * Process and download the export.
     */
    public function processExport(ExportWorkedHourRequest $request)
    {
        $validated = $request->validated();
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $exportData = $this->service->generateExportFile($startDate, $endDate);

        return response()->download($exportData['filePath'], $exportData['filename'])->deleteFileAfterSend(true);
    }
}
