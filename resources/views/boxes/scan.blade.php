@extends('layouts.app', ['title' => __('boxes.scan_heading')])

@section('backUrl', route('home'))

@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('boxes.scan_qr') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="bg-white border rounded-3 p-4">
                <h1 class="h3 mb-3">{{ __('boxes.scan_heading') }}</h1>
                <div id="reader" class="border rounded overflow-hidden"></div>
                <div id="scan-result" class="alert alert-info mt-3 mb-0">{{ __('boxes.scan_waiting') }}</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const result = document.getElementById('scan-result');
        const scanner = new Html5QrcodeScanner('reader', { fps: 10, qrbox: 250 });

        scanner.render((decodedText) => {
            result.className = 'alert alert-success mt-3 mb-0';
            result.textContent = @json(__('boxes.scan_success'));

            if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                window.location.href = decodedText;
                return;
            }

            window.location.href = `/qr/${encodeURIComponent(decodedText)}`;
        }, () => {});
    </script>
@endpush
