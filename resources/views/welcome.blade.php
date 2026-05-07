@extends('layouts.app', ['title' => __('boxes.app_name')])

@section('content')
    <div class="row g-4 align-items-stretch">
        <div class="col-lg-6">
            <a class="card h-100 border-0 shadow-sm text-decoration-none text-reset" href="{{ route('boxes.scan') }}">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                        <h1 class="h3 mb-0">{{ __('boxes.scan_qr') }}</h1>
                        <span class="badge text-bg-dark">QR</span>
                    </div>
                    <p class="text-muted mb-4">{{ __('boxes.scan_page_intro') }}</p>
                    <span class="btn btn-primary">{{ __('boxes.open_scan_page') }}</span>
                </div>
            </a>
        </div>

        <div class="col-lg-6">
            <a class="card h-100 border-0 shadow-sm text-decoration-none text-reset" href="{{ route('boxes.index') }}">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                        <h2 class="h3 mb-0">{{ __('boxes.manage_items') }}</h2>
                        <span class="badge text-bg-light border">{{ __('boxes.total_boxes', ['count' => $totalBoxes]) }}</span>
                    </div>
                    <p class="text-muted mb-4">{{ __('boxes.manage_page_intro', ['count' => $rootBoxes]) }}</p>
                    <span class="btn btn-outline-primary">{{ __('boxes.open_manage_page') }}</span>
                </div>
            </a>
        </div>
    </div>
@endsection
