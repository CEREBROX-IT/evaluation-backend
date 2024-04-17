@extends('layouts.app')

@section('title', '- Email Verified')

@section('content')
    <div class="d-flex justify-content-center align-items-center vh-100 w-100 px-4 px-lg-0" style="background-color: #f7f7f7">
        <div class="container col-lg-8 w-100 bg-white p-5 d-flex flex-column justify-content-center align-items-center shadow-md rounded-lg"
            style="height: 500px">
            <svg xmlns="http://www.w3.org/2000/svg" width="124" height="124" viewBox="0 0 24 24" fill="none"
                stroke="#00aa6c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="feather feather-check-circle mx-auto mb-4">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <h2 class="font-weight-bold" style="color: #3d4653">Email Verified!</h2>
            <p class="text-muted text-center">You have successfully verified your email address.</p>

            <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">
                <button class="btn btn-dark btn-lg mt-5 " style="width: 200px;">CONTINUE</button>
            </a>

        </div>

    </div>
@endsection
