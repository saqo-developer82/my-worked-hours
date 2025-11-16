<?php

namespace App\Http\Controllers;

use App\Models\WorkedHour;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task' => 'required|string',
            'hours' => 'required|integer|min:0',
            'minutes' => 'required|integer|min:0|max:59',
            'date' => 'required|date',
        ]);

        WorkedHour::create($validated);

        return redirect()->route('worked-hours.index')
            ->with('success', 'Worked hour record created successfully.');
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
