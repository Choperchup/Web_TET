@extends('layouts.main')
@section('content')
    <h1>Danh sách User</h1>
    <a href="{{ route('users.create') }}">
        <button class="btn btn-primary mb-3">Tạo User Mới</button>
    </a>
    <ul>
        @foreach ($users as $user)
            <li>{{ $user->name }}</li>
        @endforeach
    </ul>
@endsection