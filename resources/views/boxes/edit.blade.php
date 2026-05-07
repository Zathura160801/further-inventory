@extends('layouts.app', ['title' => __('boxes.edit_box', ['code' => $box->code])])

@section('backUrl', route('boxes.show', $box))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('boxes.index') }}">{{ __('boxes.box_list') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('boxes.show', $box) }}">{{ $box->code }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('boxes.edit') }}</li>
@endsection

@section('content')
    <div class="bg-white border rounded-3 p-4">
        <h1 class="h3 mb-3">{{ __('boxes.edit_box', ['code' => $box->code]) }}</h1>

        <form action="{{ route('boxes.update', $box) }}" method="post" enctype="multipart/form-data">
            @method('PUT')
            @include('boxes._form')
        </form>
    </div>
@endsection
