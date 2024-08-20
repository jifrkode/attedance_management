<!-- resources/views/auth/two-factor-challenge.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Two-Factor Authentication</h1>
        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf
            <div class="form-group">
                <label for="two_factor_code">Authentication Code</label>
                <input type="text" id="two_factor_code" name="two_factor_code" class="form-control" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>
@endsection
