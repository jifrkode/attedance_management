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
use Illuminate\Support\Facades\Auth;

// 現在のログインユーザーを取得
$user = Auth::user();

// 勤務状態と休憩状態を取得（nullの場合はfalseを設定）
$workStatus = $user->workstatus ?? false;
$restStatus = $user->reststatus ?? false;

// 今日の日付を取得
$today = Carbon::today()->toDateString();

// 今日の日付の出勤レコードを確認
$hasAttendanceToday = Attendance::where('user_id', $user->id)
    ->whereDate('date', $today)
    ->exists();

// ボタンの初期クラスを設定
$startWorkClass = 'notavailable';
$endWorkClass = 'notavailable';
$startBreakClass = 'notavailable';
$endBreakClass = 'notavailable';

// 今日の出勤記録がない場合（初回勤務開始が可能）
if (!$hasAttendanceToday) {
    $startWorkClass = 'available';
} else {
    // 勤務中の状態によるボタンの制御
    if ($workStatus && !$restStatus) {
        // 勤務中で休憩していない場合
        $endWorkClass = 'available';
        $startBreakClass = 'available';
    } elseif ($workStatus && $restStatus) {
        // 勤務中で休憩中の場合
        $endWorkClass = 'available';
        $endBreakClass = 'available';
    } else {
        // 勤務が終了している場合
        $startWorkClass = 'available';
    }
}

// 勤務中でない場合は休憩関連のクラスを無効化
if (!$workStatus) {
    $startBreakClass = 'notavailable';
    $endBreakClass = 'notavailable';
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