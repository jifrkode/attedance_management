@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="app">
    <h1>ログイン</h1>
    <div class="app__form">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" required autofocus>
            @error('email')
            <span>{{ $message }}</span>
            @enderror

            <input id="password" type="password" name="password" placeholder="パスワード" required>
            @error('password')
            <span>{{ $message }}</span>
            @enderror
            <button type="submit">ログイン</button>

            <p class="submit__explain">アカウントをお持ちでない方こちらから</p>
            <a class="submit__login" href="{{ route('register') }}">会員登録</a>
        </form>
    </div>
</div>
@endsection