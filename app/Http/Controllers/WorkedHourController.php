<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkedHourRequest;
use App\Models\WorkedHour;
use Carbon\Carbon;

class WorkedHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workedHours = WorkedHour::orderBy('date', 'desc')
            ->paginate(10);

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
        $validated = $request->validated();

        $insertedCount = 0;

        // Check if bulk insert is provided
        if (!empty($validated['bulk_insert'])) {
            $lines = array_filter(array_map('trim', explode("\n", $validated['bulk_insert'])));
            
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }

                // Parse the line - could be comma-separated or just task title
                $parts = array_map('trim', explode(',', $line));
                
                $taskTitle = $parts[0] ?? '';
                $hours = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : ($validated['hours'] ?? 0);
                $minutes = isset($parts[2]) && is_numeric($parts[2]) ? (int)$parts[2] : ($validated['minutes'] ?? 0);
                $date = isset($parts[3]) && !empty($parts[3]) ? $parts[3] : ($validated['date'] ?? date('Y-m-d'));

                // Validate date format
                if (!empty($date)) {
                    try {
                        $dateObj = Carbon::createFromFormat('Y-m-d', $date);
                        $date = $dateObj->format('Y-m-d');
                    } catch (\Exception $e) {
                        $date = $validated['date'] ?? date('Y-m-d');
                    }
                } else {
                    $date = $validated['date'] ?? date('Y-m-d');
                }

                if (!empty($taskTitle)) {
                    WorkedHour::create([
                        'task' => $taskTitle,
                        'hours' => $hours,
                        'minutes' => $minutes,
                        'date' => $date,
                    ]);
                    $insertedCount++;
                }
            }
        } else {
            // Single insert
            WorkedHour::create([
                'task' => $validated['task'],
                'hours' => $validated['hours'] ?? 0,
                'minutes' => $validated['minutes'] ?? 0,
                'date' => $validated['date'] ?? date('Y-m-d'),
            ]);
            $insertedCount = 1;
        }

        $message = $insertedCount > 1 
            ? "Successfully inserted {$insertedCount} worked hour records."
            : 'Worked hour record created successfully.';

        return redirect()->route('worked-hours.index')
            ->with('success', $message);
    }

    /**
     * Export worked hours data.
     */
    public function export()
    {
        $workedHours = WorkedHour::orderBy('date', 'desc')->get();

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
