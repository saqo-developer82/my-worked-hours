<tr>
    <td>{{ $workedHour->id }}</td>
    <td>{{ $workedHour->task }}</td>
    <td>{{ $formatDuration((int) $workedHour->hours, (int) $workedHour->minutes) }}</td>
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

