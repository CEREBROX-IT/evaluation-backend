@extends('layouts.app')

@section('title', '- Reset Password')

@section('content')

    <div class="d-flex justify-content-center align-items-center vh-100 w-100 px-4 px-lg-0" style="background-color: #f7f7f7">
        <div class="container col-lg-4 w-100 bg-white p-4 d-flex flex-column justify-content-center align-items-center shadow-md"
            style="border-radius: 10px;">
            <h2 style="margin-bottom: 25px ">SETUP NEW PASSWORD</h2>

            <form class="w-100 p-2" action="{{ route('set-new-password') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Password field with error check -->
                <input class="form-control form-control-lg mb-3 @error('password') is-invalid @enderror" id="password"
                    type="password" name="password" placeholder="New Password">

                <!-- Password confirmation field with error check -->
                <input class="form-control form-control-lg mb-2 @error('password') is-invalid @enderror"
                    id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password"
                    required>

                <!-- Display error message for password confirmation field -->
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }} </span>
                @enderror

                <button type="submit" class="btn btn-dark btn-lg w-100" style="margin-top: 25px;">Reset
                    Password</button>
            </form>


            <p class="medium text-center mt-3">Click here to proceed to login <a href="{{ env('BASE_URL') }}">Click
                    Here</a></p>
        </div>
    </div>


@endsection
