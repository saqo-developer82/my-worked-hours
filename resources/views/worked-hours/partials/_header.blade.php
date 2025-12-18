<div class="card mb-4">
    <div class="card-body">
        <h1 class="card-title mb-4">My Worked Hours</h1>
        <div class="d-flex gap-3 align-items-center flex-wrap">
            <div class="d-flex gap-2">
                <a href="{{ route('worked-hours.create') }}" class="btn btn-success">Add New Task(s)</a>
                <a href="{{ route('worked-hours.export') }}" class="btn btn-info">Export Data</a>
            </div>
            @if(isset($totalWorkedHours))
                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="text-muted">Total:</span>
                    <span class="fs-5 fw-bold text-primary">
                        {{ $totalWorkedHours['formatted'] }}
                    </span>
                    @if(request('start_date') && request('end_date'))
                        <small class="text-muted">
                            ({{ request('start_date') }} to {{ request('end_date') }})
                        </small>
                    @elseif(request('date'))
                        <small class="text-muted">
                            ({{ request('date') }})
                        </small>
                    @elseif(request('task'))
                        <small class="text-muted">
                            (Filtered: "{{ request('task') }}")
                        </small>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

