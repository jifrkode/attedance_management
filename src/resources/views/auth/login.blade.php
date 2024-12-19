@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="app">
    <h1>ログイン</h1>
    <div class="app__form">
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf
            <div class="app__form--item">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス" autofocus>
                @error('email')
                <span>{{ $message }}</span>
                @enderror
            </div>
            <div class="app__form--item">
                <input type="password" name="password" placeholder="パスワード">
                @error('password')
                <span>{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">ログイン</button>

            <p class="submit__explain">アカウントをお持ちでない方こちらから</p>
            <a class="submit__login" href="{{ route('register') }}">会員登録</a>
        </form>
    </div>
</div>
@endsection