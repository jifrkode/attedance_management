<!-- resources/views/users/show.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
</head>
<body>
    <h1>{{ $user->name }}</h1>

    <h2>Attendances</h2>
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
