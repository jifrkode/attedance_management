<!-- resources/views/attendance/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Attendance</title>
</head>
<body>
    <h1>Attendance</h1>

    <form method="POST" action="{{ route('attendance.start') }}">
        @csrf
        <button type="submit">Start Work</button>
    </form>

    <form method="POST" action="{{ route('attendance.end') }}">
        @csrf
        <button type="submit">End Work</button>
    </form>

    <form method="POST" action="{{ route('attendance.startBreak') }}">
        @csrf
        <button type="submit">Start Break</button>
    </form>

    <form method="POST" action="{{ route('attendance.endBreak') }}">
        @csrf
        <button type="submit">End Break</button>
    </form>

    <h2>Today's Attendances</h2>
    <ul>
        @foreach($attendances as $attendance)
            <li>
                Start Time: {{ $attendance->start_time }}
                @if ($attendance->end_time)
                    , End Time: {{ $attendance->end_time }}
                @endif
                @if ($attendance->break_start)
                    , Break Start: {{ $attendance->break_start }}
                @endif
                @if ($attendance->break_end)
                    , Break End: {{ $attendance->break_end }}
                @endif
            </li>
        @endforeach
    </ul>
</body>
</html>
