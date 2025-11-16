@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title mb-4">Edit Worked Hours</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('worked-hours.update', $workedHour->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="task" class="form-label">Task Title *</label>
                <input type="text" name="task" id="task" class="form-control @error('task') is-invalid @enderror" value="{{ old('task', $workedHour->task) }}" required>
                @error('task')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="hours" class="form-label">Hours</label>
                <input type="number" name="hours" id="hours" class="form-control @error('hours') is-invalid @enderror" min="0" max="24" value="{{ old('hours', $workedHour->hours) }}">
                @error('hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="minutes" class="form-label">Minutes</label>
                <input type="number" name="minutes" id="minutes" class="form-control @error('minutes') is-invalid @enderror" min="0" max="59" value="{{ old('minutes', $workedHour->minutes) }}">
                @error('minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $workedHour->date ? $workedHour->date->format('Y-m-d') : '') }}">
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

