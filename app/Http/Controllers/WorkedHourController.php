<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkedHourRequest;
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
        $workedHours = $this->service->getPaginatedWorkedHours(10);

        return view('worked-hours.index', compact('workedHours'));
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
     * Export worked hours data.
     */
    public function export()
    {
        $workedHours = $this->service->getAllWorkedHours();

        $filename = 'worked_hours_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($workedHours) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['ID', 'Task', 'Hours', 'Minutes', 'Date']);
            
            // Add data rows
            foreach ($workedHours as $workedHour) {
                fputcsv($file, [
                    $workedHour->id,
                    $workedHour->task,
                    $workedHour->hours,
                    $workedHour->minutes,
                    $workedHour->date->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
