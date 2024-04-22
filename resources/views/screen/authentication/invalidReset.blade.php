@extends('layouts.app')

@section('title', '- Reset Password Expired')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 w-100 px-4 px-lg-0" style="background-color: #f7f7f7">
        <div class="container col-lg-8 w-100 bg-white p-5 d-flex flex-column justify-content-center align-items-center shadow-md rounded-lg"
            style="height: 500px">
            <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 24 24" fill="none"
                stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-check-circle mx-auto mb-4">
                <circle cx="12" cy="12" r="10" stroke="red" stroke-width="2" fill="none"></circle>
                <path d="M16 8L8 16" style="stroke: red; stroke-width: 2;"></path>
                <path d="M8 8L16 16" style="stroke: red; stroke-width: 2;"></path>
            </svg>
            <h2 class="font-weight-bold" style="color: #3d4653">Failed to Update Password!</h2>
            <p class="text-muted text-center mb-0">The Reset Password link is invalid or has expired.</p>
            <p class="text-muted text-center mb-0">Please ensure that you've clicked the latest link or request a new
                reset password.</p>

            <a href="{{ env('CLIENT_URL') }}">
                <button class="btn btn-dark btn-lg mt-5 " style="width: 200px;">CONTINUE</button>
            </a>

        </div>

    </div>
@endsection
