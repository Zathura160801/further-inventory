@extends('layouts.app', ['title' => __('boxes.box_list')])

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('boxes.box_list') }}</li>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ __('boxes.box_list') }}</h1>
            <p class="text-muted mb-0">{{ __('boxes.box_list_intro') }}</p>
        </div>
        <div class="d-flex justify-content-end w-100 w-md-auto gap-2">
            <a class="btn btn-primary" href="{{ route('boxes.create') }}">{{ __('boxes.add_box') }}</a>
        </div>
    </div>

    @if ($boxes->isEmpty())
        <div class="bg-white border rounded-3 p-4 text-center">
            <h2 class="h5">{{ __('boxes.empty_title') }}</h2>
            <p class="text-muted">{{ __('boxes.empty_body') }}</p>
            <a class="btn btn-primary" href="{{ route('boxes.create') }}">{{ __('boxes.add_first_box') }}</a>
        </div>
    @else
        @include('boxes._tree', ['boxes' => $boxes])
    @endif
@endsection
