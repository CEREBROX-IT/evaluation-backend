<!-- resources/views/child.blade.php -->

@extends('layouts.app')

@section('title', '- API ONLINE')

@section('content')


    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-11 mx-auto shadow-lg  p-md-4 p-5 bg-white">
                    <div class="row align-items-center bg-white">
                        <div class="col-12">
                            <h3 class="text-center mb-4">RESTFUL API IS ONLINE</h3>
                            <p class="small text-muted m-0 pt-3 text-center">Copyright © Evaluation Admin Panel 2024
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


<style>
    @import url('https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&display=swap');

    html,
    body {
        height: 100%;
    }

    body {
        font-family: 'Archivo', sans-serif;
    }

    .btn-lg {
        padding: 14px 24px;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 1px;
        border-radius: 0;
    }

    ::placeholder {
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .form-control-lg {
        font-size: 16px;
        padding: 25px 20px;
        border-radius: 0;
    }

    .main-container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 760px;
        margin: 0 auto;
    }
</style>
