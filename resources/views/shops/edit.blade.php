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
    <h1 class="display-4">Магазин</h1>

</div>
<div class="container-lg">

{{ Form::model($shop, ['url' => route('shops.update', $shop), 'class' => 'w-50', 'method' => 'PATCH']) }}
    @csrf
    <div class="form-group">
    {{ Form::label('id', 'ID') }}
    {{ Form::text('id', $shop->id, ['class' => 'form-control', 'type' => 'text', 'id' => 'id']) }}
    {{ Form::label('address', 'Адрес') }}
    {{ Form::text('address', $shop->display_name, ['class' => 'form-control', 'type' => 'text', 'id' => 'address']) }}
    {{ Form::label('address', 'ID Bitrix') }}
    {{ Form::text('bitrix_id', $shop->bitrix_id, ['class' => 'form-control', 'type' => 'text', 'id' => 'bitrix_id']) }}
    {{ Form::label('accountable_id', 'Ответственный') }}
    {{ Form::text('accountable_id', $shop->accountable_id, ['class' => 'form-control', 'type' => 'text', 'id' => 'accountable_id']) }}
    </div>
    {{ Form::submit('Обновить', ['class' => "btn btn-primary"]) }}
{{ Form::close() }}

</div>
@endsection
