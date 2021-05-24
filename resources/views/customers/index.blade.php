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
    <h1 class="display-4">Customers</h1>
</div>

<table class="table mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Количество баллов</th>
                <th>Дата создания</th>
                <th>Дата обновления</th>
            </tr>
        </thead>
        @foreach ($customers as $customer)
            <tr>
                <td>{{ $customer->customer_id }}</td>
                <td>{{ $customer->display_name}}</td>
                <td>{{ $customer->points }}</a></td>
                <td>{{ $customer->created_at }}</td>
                <td>{{ $customer->updated_at}}</td>
            </tr>
        @endforeach
    </table>

@endsection
