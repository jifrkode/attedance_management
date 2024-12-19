@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/list.css')}}">
@endsection

@section('link')
<nav>
    <ul class="header__nav">
        <li><a href="{{ route('attendance.index') }}">ホーム</a></li>
        <li><a href="{{ route('attendance.dayslist') }}">日付一覧</a></li>
        @php
        $userRole = auth()->user()->role; // 現在のユーザーのロールを取得
        @endphp
        @if ($userRole === 'admin')
        <li><a href="{{ route('attendance.userslist') }}">勤務者一覧</a></li>
        @endif
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
  <!-- 日付選択 -->
  <h1>
    <a href="{{ route('attendance.dayslist', ['date' => $previousDate]) }}" class="date-nav"><</a>
    {{ $date }}
    <a href="{{ route('attendance.dayslist', ['date' => $nextDate]) }}" class="date-nav">></a>
  </h1>

  <!-- 日付選択フォーム -->
  <form action="{{ route('attendance.dayslist') }}" method="GET" id="date-form">
    <input type="hidden" id="date" name="date" value="{{ $date }}" onchange="this.form.submit()" required>
  </form>

  <table>
    <thead>
      <tr>
        <th>名前</th>
        <th>勤務開始</th>
        <th>勤務終了</th>
        <th>休憩時間</th>
        <th>勤務時間</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($attendances as $attendance)
      <tr>
        <td>{{ $attendance->user->name }}</td>
        <td>{{ $attendance->start_time }}</td>
        <td>{{ $attendance->end_time }}</td>
        <td>
          @if ($attendance->formattedBreakDuration)
          {{ $attendance->formattedBreakDuration }}
          @endif
        </td>
        <td>
          @if ($attendance->formattedWorkDuration)
          {{ $attendance->formattedWorkDuration }}
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="pagination">
  {{ $attendances->links('vendor.pagination.custom') }}
</div>
@endsection