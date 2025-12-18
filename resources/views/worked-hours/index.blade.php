@extends('layouts.app')

@section('title', 'Worked Hours')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@vite(['resources/css/worked-hours.css'])
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
@vite(['resources/js/worked-hours.js'])
@endpush
