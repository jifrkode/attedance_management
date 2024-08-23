@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/index.css')}}">
@endsection

@section('link')
<nav>
    <ul class="header__nav">
        <li><a href="{{ route('attendance.index') }}">ホーム</a></li>
        <li><a href="{{ route('users.admin') }}">日付一覧</a></li>
        <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">ログアウト</a></li>
    </ul>
    <!-- ログアウト用のフォーム -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</nav>
@endsection

@section('content')
<div class="app">
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
</div>
@endsection('content')