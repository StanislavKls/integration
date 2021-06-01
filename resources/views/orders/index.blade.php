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
    <h1 class="display-4">Заказы</h1>

</div>

    <table class="table mt-2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Покупатель</th>
                <th>Сумма</th>
                <th>Загружено в Bitrix</th>
                <th>Действие</th>
            </tr>
        </thead>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->date_created }}</td>
                    <td>{{ $order->customer->display_name }}</td>
                    <td>{{ $order->total }}</td>
                    @if ($order->uploaded_to_bitrix == false)
                        <td> Нет </td>
                    @else
                        <td> Да </td>
                    @endif
                    <td><a href="{{ route('orders.show', $order->id) }}"> Посмотреть  </a>
                    @if ($order->uploaded_to_bitrix == false)
                         || <a href="{{ route('bitrix.upload', $order->id) }}"> Загрузить </a>
                    @endif
                    || <a class="text-danger" href="{{ route('orders.destroy', $order->id) }}"
                                data-method="delete"
                                data-confirm="Вы уверены?"
                        >Удалить</a>
                    </td>
                </tr>
            @endforeach
    </table>
@endsection
