@extends('layouts.app')

@section('title', 'Worked Hours')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
@include('worked-hours.partials._header')

<div class="card">
    <div class="card-body">
        @include('worked-hours.partials._alerts')
        @include('worked-hours.partials._filters')        

        @if($workedHours->count() > 0)
            @php
                $formatDuration = function (int $hours, int $minutes): string {
                    $hours += intdiv($minutes, 60);
                    $minutes = $minutes % 60;

                    if ($hours === 0 && $minutes === 0) {
                        return '0m';
                    }

                    if ($hours === 0) {
                        return $minutes . 'm';
                    }

                    if ($minutes === 0) {
                        return $hours . 'h';
                    }

                    return $hours . 'h:' . $minutes . 'm';
                };

                $groupedByDate = $workedHours->groupBy(function($item) {
                    return $item->date->format('Y-m-d');
                });
            @endphp
            
            @foreach($groupedByDate as $date => $dateGroup)
                @php
                    $dateTotals = $dateGroup->reduce(function ($carry, $item) {
                        $carry['hours'] += (int) $item->hours;
                        $carry['minutes'] += (int) $item->minutes;
                        return $carry;
                    }, ['hours' => 0, 'minutes' => 0]);

                    $dateTotals['hours'] += intdiv($dateTotals['minutes'], 60);
                    $dateTotals['minutes'] = $dateTotals['minutes'] % 60;
                @endphp
                @include('worked-hours.partials._date-group', [
                    'date' => $date,
                    'dateGroup' => $dateGroup,
                    'dateTotals' => $dateTotals,
                    'formatDuration' => $formatDuration,
                    'loopIndex' => $loop->index,
                    'isFirst' => $loop->first
                ])
            @endforeach

            <div class="mt-3">
                {{ $workedHours->links('pagination::bootstrap-5') }}
            </div>
        @else
            @include('worked-hours.partials._empty-state')
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create custom locale with Monday as first day
        flatpickr.localize({
            firstDayOfWeek: 1 // Monday
        });

        // Initialize Flatpickr for all date inputs with Monday as first day
        const dateConfig = {
            dateFormat: "Y-m-d",
            locale: {
                firstDayOfWeek: 1 // Monday
            }
        };

        flatpickr("#date", dateConfig);
        flatpickr("#start_date", dateConfig);
        flatpickr("#end_date", dateConfig);

        // Handle collapse/expand icon rotation
        const collapseElements = document.querySelectorAll('[data-bs-toggle="collapse"]');
        collapseElements.forEach(function(element) {
            const targetId = element.getAttribute('data-bs-target');
            const iconId = element.querySelector('.collapse-icon')?.id;
            
            if (iconId && targetId) {
                const targetElement = document.querySelector(targetId);
                const icon = document.getElementById(iconId);
                
                if (targetElement && icon) {
                    // Set initial state
                    if (targetElement.classList.contains('show')) {
                        icon.classList.remove('bi-chevron-up');
                        icon.classList.add('bi-chevron-down');
                    } else {
                        icon.classList.remove('bi-chevron-down');
                        icon.classList.add('bi-chevron-up');
                    }
                    
                    // Listen for collapse events
                    targetElement.addEventListener('show.bs.collapse', function() {
                        icon.classList.remove('bi-chevron-up');
                        icon.classList.add('bi-chevron-down');
                    });
                    
                    targetElement.addEventListener('hide.bs.collapse', function() {
                        icon.classList.remove('bi-chevron-down');
                        icon.classList.add('bi-chevron-up');
                    });
                }
            }
        });
    });
</script>
@endpush
