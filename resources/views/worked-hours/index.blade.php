@extends('layouts.app')

@section('title', 'Worked Hours')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title mb-4">My Worked Hours</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('worked-hours.create') }}" class="btn btn-success">Add New Task(s)</a>
            <a href="{{ route('worked-hours.export') }}" class="btn btn-info">Export Data</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Filters</h5>
                <form method="GET" action="{{ route('worked-hours.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="task" class="form-label">Task</label>
                        <input type="text" name="task" id="task" class="form-control" value="{{ request('task') }}" placeholder="Search by task name...">
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date (Single)</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}" placeholder="Select a single date">
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start date">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End date">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
                <small class="text-muted mt-2 d-block">Note: Date interval (Start/End) takes precedence over single date filter.</small>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Total Worked Hours</h5>
                <form method="GET" action="{{ route('worked-hours.index') }}" class="row g-3">
                    <input type="hidden" name="task" value="{{ request('task') }}">
                    <input type="hidden" name="date" value="{{ request('date') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <div class="col-md-4">
                        <label for="total_start_date" class="form-label">Start Date</label>
                        <input type="date" name="total_start_date" id="total_start_date" class="form-control" value="{{ request('total_start_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="total_end_date" class="form-label">End Date</label>
                        <input type="date" name="total_end_date" id="total_end_date" class="form-control" value="{{ request('total_end_date') }}" required>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-flex gap-2 w-100">
                            <button type="submit" class="btn btn-success">Calculate Total</button>
                            <a href="{{ route('worked-hours.index', ['task' => request('task'), 'date' => request('date'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
                
                @if(isset($totalWorkedHours))
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6 class="mb-2">Total Worked Hours:</h6>
                        <p class="mb-0 fs-4 fw-bold text-primary">
                            {{ $totalWorkedHours['formatted'] }}
                            <small class="text-muted fs-6">
                                ({{ $totalWorkedHours['total_hours'] }}h {{ $totalWorkedHours['total_minutes'] }}m)
                            </small>
                        </p>
                        <p class="text-muted mb-0 mt-2">
                            <small>From {{ request('total_start_date') }} to {{ request('total_end_date') }}</small>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if($workedHours->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Task/Work</th>
                            <th>Hours</th>
                            <th>Minutes</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workedHours as $workedHour)
                            <tr>
                                <td>{{ $workedHour->id }}</td>
                                <td>{{ $workedHour->task }}</td>
                                <td>{{ $workedHour->hours }}</td>
                                <td>{{ $workedHour->minutes }}</td>
                                <td>{{ $workedHour->date->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('worked-hours.edit', $workedHour->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <form action="{{ route('worked-hours.destroy', $workedHour->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $workedHours->links('pagination::bootstrap-5') }}
            </div>
        @else
            <p class="text-muted">No worked hours records found. <a href="{{ route('worked-hours.create') }}">Add your first task</a>.</p>
        @endif
    </div>
</div>
@endsection
