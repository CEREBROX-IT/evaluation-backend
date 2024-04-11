@extends('layouts.app')

@section('title', '- Login')

@section('content')

    <body>
        <h1>Reset Your Password</h1>
        <form action="{{ route('set-new-password') }}" method="post">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="password">New Password</label>
                <input type="password" name="password" id="password">
            </div>
            <div>
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </body>

@endsection
