@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="app">
    <h1>会員登録</h1>
    <div class="app__form">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="app__form--item">
                <input type="text" name="name" value="{{ old('name') }}" placeholder="名前" autofocus>
                @error('name')
                <span>{{ $message }}</span>
                @enderror
            </div>
            <div class="app__form--item">
                <input type="email" name="email" value="{{ old('email') }}" placeholder="メールアドレス">
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
            <div class="app__form--item">
                <input type="password" name="password_confirmation" placeholder="確認用メールアドレス">
                @error('password_confirmation')
                <span>{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">会員登録</button>
        </form>
    </div>
    <p class="submit__explain">アカウントをお持ちの方こちらから</p>
    <a class="submit__login" href="{{ route('login') }}">ログイン</a>
</div>

@endsection('content')