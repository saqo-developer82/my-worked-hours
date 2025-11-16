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

        @if($workedHours->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Task</th>
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
                                    <form action="{{ route('worked-hours.destroy', $workedHour->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
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
