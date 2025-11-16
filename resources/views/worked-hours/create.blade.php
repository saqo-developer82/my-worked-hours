@extends('layouts.app')

@section('title', 'Add New Task')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title mb-4">Add New Worked Hours</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('worked-hours.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="task" class="form-label">Task Title *</label>
                <input type="text" name="task" id="task" class="form-control @error('task') is-invalid @enderror" value="{{ old('task') }}" required>
                @error('task')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="hours" class="form-label">Hours</label>
                <input type="number" name="hours" id="hours" class="form-control @error('hours') is-invalid @enderror" min="0" value="{{ old('hours', 0) }}">
                @error('hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="minutes" class="form-label">Minutes</label>
                <input type="number" name="minutes" id="minutes" class="form-control @error('minutes') is-invalid @enderror" min="0" value="{{ old('minutes', 0) }}">
                @error('minutes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}">
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="bulk_insert" class="form-label">Bulk Insert</label>
                <textarea name="bulk_insert" id="bulk_insert" class="form-control @error('bulk_insert') is-invalid @enderror" rows="10" placeholder="Enter multiple tasks, one per line. Format: Task Title, Hours, Minutes, Date (YYYY-MM-DD)&#10;Example:&#10;Task 1, 2, 30, 2024-01-15&#10;Task 2, 1, 45, 2024-01-16&#10;Or just task titles (hours, minutes, date will use form values or defaults):&#10;Task 3&#10;Task 4">{{ old('bulk_insert') }}</textarea>
                <small class="form-text text-muted">
                    Enter multiple tasks, one per line. Format: Task Title, Hours, Minutes, Date (YYYY-MM-DD). 
                    Or just task titles (will use form values above or defaults).
                </small>
                @error('bulk_insert')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Save</button>
                <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
