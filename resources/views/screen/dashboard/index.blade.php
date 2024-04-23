@extends('layouts.app')

@section('title', '- Dashboard')

@section('content')


    <p>Hello this is dashboard</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4">
            Logout
        </button>
    </form>


@endsection
