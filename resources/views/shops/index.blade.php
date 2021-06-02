@extends('layouts.app')

@section('title', 'Главная')

@section('content')
@include('flash::message')
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
    <h1 class="display-4">Магазины</h1>

</div>

    <table class="table mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Адрес</th>
                <th>ID Bitrix</th>
                <th>Ответственный</th>
                <th>Действие</th>
            </tr>
        </thead>
            @foreach ($shops as $shop)
                <tr>
                    <td>{{ $shop->id }}</td>
                    <td>{{ $shop->display_name }}</td>
                    <td>{{ $shop->bitrix_id }}</td>
                    <td>{{ $shop->accountable_id }}</td>
                    <td> <a href="{{ route('shops.edit', $shop->id) }}"> Редактировать </a>
                         || <a class="text-danger" href="{{ route('shops.destroy', $shop->id) }}"
                                data-method="delete"
                                data-confirm="Вы уверены?">Удалить</a>
                    </td>
                </tr>
            @endforeach
    </table>
@endsection
