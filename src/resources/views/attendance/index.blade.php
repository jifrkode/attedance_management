@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/index.css') }}">
@endsection

@section('link')
<nav>
    <ul class="header__nav">
        <li><a href="{{ route('attendance.index') }}">ホーム</a></li>
        <li><a href="{{ route('attendance.list') }}">日付一覧</a></li>
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
$workStatus = session('workstatus', false);
$restStatus = session('reststatus', false);

$startWorkClass = (!$workStatus && !$restStatus)
? 'ableable'
: 'notableable';

$endWorkClass = ($workStatus && !$restStatus)
? 'ableable'
: 'notableable';

$startBreakClass = ($workStatus && !$restStatus)
? 'ableable'
: 'notableable';

$endBreakClass = ($workStatus && $restStatus)
? 'ableable'
: 'notableable';

// 勤務中でない場合は、休憩関連のクラスを 'notableable' に設定
if (!$workStatus) {
$startBreakClass = 'notableable';
$endBreakClass = 'notableable';
}
@endphp


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