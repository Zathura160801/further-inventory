@extends('layouts.app', ['title' => __('boxes.add_box')])

@section('backUrl', $parent ? route('boxes.show', $parent) : route('boxes.index'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('boxes.index') }}">{{ __('boxes.box_list') }}</a></li>
    @if ($parent)
        <li class="breadcrumb-item"><a href="{{ route('boxes.show', $parent) }}">{{ $parent->code }}</a></li>
    @endif
    <li class="breadcrumb-item active" aria-current="page">{{ __('boxes.add_box') }}</li>
@endsection

@section('content')
    <div class="bg-white border rounded-3 p-4">
        <h1 class="h3 mb-3">{{ __('boxes.add_box') }}</h1>

        @if ($parent)
            <div class="alert alert-info">
                {!! __('boxes.goes_inside', ['code' => e($parent->code)]) !!}
            </div>
        @endif

        <form action="{{ route('boxes.store') }}" method="post" enctype="multipart/form-data">
            @include('boxes._form')
        </form>
    </div>
@endsection
