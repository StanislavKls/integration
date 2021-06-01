@extends('layouts.app')

@section('title', 'Главная')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        @include('flash::message')
    </div>
@endif

<div class="jumbotron">
    <h1 class="display-4">CRM</h1>
</div>


<form action="{{ route('getcustomer') }}"  class='w-50' method='post'>
    @csrf
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">ID клиента</label>
    <input type="text" class="form-control" name="id">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

@if (Auth::user()->isAdmin())
    <a class="nav-link" href="{{ route('register') }}">{{ __('Регистрация пользователя') }}</a>
@endif
@endsection
