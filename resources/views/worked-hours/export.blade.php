@extends('layouts.app')

@section('title', 'Export Data')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title mb-4">Export Worked Hours</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('worked-hours.export.process') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="start_date" class="form-label">Start Date *</label>
                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}" required>
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="end_date" class="form-label">End Date *</label>
                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', \Carbon\Carbon::now()->subDays(7)->addDays(6)->format('Y-m-d')) }}" required>
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <script>
                document.getElementById('start_date').addEventListener('change', function() {
                    const startDate = new Date(this.value);
                    if (startDate && !isNaN(startDate.getTime())) {
                        const endDate = new Date(startDate);
                        endDate.setDate(endDate.getDate() + 6);
                        document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
                    }
                });
            </script>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Export</button>
                <a href="{{ route('worked-hours.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

