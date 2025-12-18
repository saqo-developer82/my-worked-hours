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

