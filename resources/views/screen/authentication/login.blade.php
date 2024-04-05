<!-- resources/views/child.blade.php -->

@extends('layouts.app')

@section('title', '- Login')

@section('content')

    <div class="demo-container">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto shadow-lg  p-md-4 p-5">
                    <div class="row align-items-center">
                        <div class="col-6 d-none d-md-inline-block"> <img
                                src="https://images.pexels.com/photos/3987011/pexels-photo-3987011.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260"
                                class="img-fluid"> </div>
                        <div class="col-md-6 col-12">
                            <h1 class="mb-4">Evaluation Form: Admin Panel</h1>
                            <p>No worries, I've created a free checklist of everything you need to do & when to do it.</p>

                            <form>
                                <input class="form-control form-control-lg mb-3" id="username" name="username"
                                    placeholder="Username">
                                <input class="form-control form-control-lg mb-3" id="password" name="password"
                                    placeholder="Password">
                                <button class="btn btn-dark btn-lg w-100">SIGN IN</button>
                            </form>
                            <p class="small text-muted m-0 pt-3">Copyright © Evaluation Admin Panel 2024</p>
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

    .bg-image {
        background: linear-gradient(rgba(64, 57, 86, 0.5), rgba(64, 57, 86, 0.9)), url(https://images.pexels.com/photos/3021601/pexels-photo-3021601.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=640&w=960);
        background-size: cover;
        background-position: center;
    }

    .demo-container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 760px;
        margin: 0 auto;
    }
</style>
