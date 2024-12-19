@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/index.css') }}">
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
    <h1>{{ $user->name ?? '未登録' }}さんお疲れ様です!</h1>
</div>

@php
use App\Models\Attendance;
use Carbon\Carbon;

// 現在のログインユーザーを取得
$user = Auth::user();

$workStatus = $user->workstatus ?? false;
$restStatus = $user->reststatus ?? false;

// 今日の日付を取得
$today = Carbon::today()->toDateString();

// 今日の日付の出勤レコードを確認
$hasAttendanceToday = Attendance::where('user_id', $user->id)
->whereDate('date', $today)
->exists();

// 今日の出勤記録がない場合はすべてのクラスを 'notavailable' に設定
if ($hasAttendanceToday === true) {
$startWorkClass = 'notavailable';
$endWorkClass = 'notavailable';
$startBreakClass = 'notavailable';
$endBreakClass = 'notavailable';
} else {
$startWorkClass = (!$workStatus && !$restStatus)
? 'available'
: 'notavailable';

$endWorkClass = ($workStatus && !$restStatus)
? 'available'
: 'notavailable';

$startBreakClass = ($workStatus && !$restStatus)
? 'available'
: 'notavailable';

$endBreakClass = ($workStatus && $restStatus)
? 'available'
: 'notavailable';

// 勤務中でない場合は、休憩関連のクラスを 'notavailable' に設定
if (!$workStatus) {
$startBreakClass = 'notavailable';
$endBreakClass = 'notavailable';
}
}
@endphp
<!-- dd($startWorkClass,$endWorkClass,$startBreakClass,$endBreakClass); -->


<div class="app__grid">
    <div class="app__grid--item1">
        <form method="POST" action="{{ route('attendance.start') }}">
            @csrf
            <button class="{{ $startWorkClass }}" type="submit">勤務開始</button>
        </form>
    </div>
    <div class="app__grid--item2">
        <form method="POST" action="{{ route('attendance.end') }}">
            @csrf
            <button class="{{ $endWorkClass }}" type="submit">勤務終了</button>
        </form>
    </div>
    <div class="app__grid--item3">
        <form method="POST" action="{{ route('attendance.startBreak') }}">
            @csrf
            <button class="{{ $startBreakClass }}" type="submit">休憩開始</button>
        </form>
    </div>
    <div class="app__grid--item4">
        <form method="POST" action="{{ route('attendance.endBreak') }}">
            @csrf
            <button class="{{ $endBreakClass }}" type="submit">休憩終了</button>
        </form>
    </div>
</div>


@endsection