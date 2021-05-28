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
    <h1 class="display-4">Заказ</h1>

</div>
<div class="container-lg">
    <table class="table table-bordered table-hover text-nowrap">
        <tr>
            <td>ID</td>
            <td colspan="5">{{ $order->id }}</td>
        </tr>
        <tr>
            <td>Дата</td>
            <td colspan="5"> {{ $order->date_created }}</td>
        </tr>
        <tr>
            <td>Покупатель</td>
            <td colspan="5">{{ $order->customer->display_name }}</td>
        </tr>
        <tr>
            <td>Доставка</td>
            <td colspan="5">{{ $order->delivery_type }}</td>
        </tr>
        <tr>
            <td>Магазин</td>
            <td colspan="5">{{ $order->shop->display_name ?? '' }}</td>
        </tr>
        <tr>
            <td>Адрес доставки</td>
            <td colspan="5">{{ $order->delivery_address ?? '' }}</td>
        </tr>
        <tr>
            <td>Сумма</td>
            <td colspan="5">{{ $order->total }}</td>
        </tr>
        <tr>
            <td colspan="2" align="center"> Товары </td>
        </tr>
        <tr>
            <td>Товар</td>
            <td>Количесвто</td>
            <td>Цена</td>
            <td>Сумма</td>
        </tr>
        @foreach ($order->items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->pivot->qty }}</td>
            <td>{{ $item->pivot->price }}</td>
            <td>{{ $item->pivot->qty * $item->pivot->price }}</td>
        </tr>
        @endforeach
    </table>
    Сумма заказа: {{ $sum }}
</div>
@endsection
