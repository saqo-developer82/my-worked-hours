<div class="card mb-4">
    <div class="card-header bg-primary text-white" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loopIndex }}" aria-expanded="{{ $isFirst ? 'true' : 'false' }}" aria-controls="collapse-{{ $loopIndex }}">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="mb-0 d-flex align-items-center gap-2">
                <i class="bi {{ $isFirst ? 'bi-chevron-down' : 'bi-chevron-up' }} collapse-icon" id="icon-{{ $loopIndex }}"></i>
                {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                <span class="badge bg-light text-dark ms-2">{{ $dateGroup->count() }} {{ $dateGroup->count() === 1 ? 'task' : 'tasks' }}</span>
            </h5>
            <span class="badge bg-dark">
                Total: {{ $formatDuration($dateTotals['hours'], $dateTotals['minutes']) }}
            </span>
        </div>
    </div>
    <div id="collapse-{{ $loopIndex }}" class="collapse {{ $isFirst ? 'show' : '' }}">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Task/Work</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dateGroup as $workedHour)
                            @include('worked-hours.partials._task-row', ['workedHour' => $workedHour, 'formatDuration' => $formatDuration])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

