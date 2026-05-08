@extends('layouts.app', ['title' => $box->code])

@section('backUrl', $box->parent ? route('boxes.show', $box->parent) : route('boxes.index'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('boxes.index') }}">{{ __('boxes.box_list') }}</a></li>
    @foreach ($ancestors as $ancestor)
        <li class="breadcrumb-item"><a href="{{ route('boxes.show', $ancestor) }}">{{ $ancestor->code }}</a></li>
    @endforeach
    <li class="breadcrumb-item active" aria-current="page">{{ $box->code }}</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white border rounded-3 p-4 mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
                    <div>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge text-bg-dark">{{ $box->code }}</span>
                            <span
                                class="badge text-bg-light border">{{ __('boxes.level', ['level' => $box->level]) }}</span>
                            <span class="badge text-bg-secondary">{{ $box->status_label }}</span>
                        </div>
                        <h1 class="h3 mb-1">{{ $box->code }}</h1>
                        @if ($box->parent)
                            <div class="text-muted">
                                {{ __('boxes.inside') }} <a
                                    href="{{ route('boxes.show', $box->parent) }}">{{ $box->parent->code }}</a>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex w-100 justify-content-end gap-2 align-self-start ms-md-auto">
                        <a class="btn btn-outline-secondary"
                            href="{{ route('boxes.edit', $box) }}">{{ __('boxes.edit') }}</a>
                    </div>
                </div>

                <h2 class="h5">{{ __('boxes.contents_heading') }}</h2>
                <p class="mb-0 text-break">{{ $box->description ?: __('boxes.no_contents') }}</p>
            </div>

            <div class="bg-white border rounded-3 p-4 mb-4">
                <h2 class="h5 mb-3">{{ __('boxes.photos') }}</h2>
                <form class="border rounded-3 p-3 mb-3" action="{{ route('boxes.images.store', $box) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <h3 class="h6">{{ __('boxes.batch_upload_photos') }}</h3>
                    @include('boxes._photo_uploader', ['id' => 'box-detail-photos'])
                    <button class="btn btn-primary mt-3" type="submit">{{ __('boxes.upload_photos') }}</button>
                </form>

                @if ($box->images->isEmpty())
                    <p class="text-muted mb-0">{{ __('boxes.no_photos') }}</p>
                @else
                    <div class="row g-3">
                        @foreach ($box->images as $image)
                            <div class="col-6 col-md-4">
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                                    <img class="img-fluid rounded border object-cover" style="aspect-ratio: 1 / 1;"
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ __('boxes.photos') }} {{ $box->code }}">
                                </a>
                                <form action="{{ route('boxes.images.destroy', $image) }}" method="post"
                                    onsubmit="return confirm(@json(__('boxes.delete_photo_confirm')))" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger w-100"
                                        type="submit">{{ __('boxes.delete_photo') }}</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white border rounded-3 p-4">
                <h2 class="h5 mb-3">{{ __('boxes.children_heading') }}</h2>
                @if ($box->children->isEmpty())
                    <p class="text-muted mb-0">{{ __('boxes.no_children') }}</p>
                @else
                    <div class="list-group">
                        @foreach ($box->children as $child)
                            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                href="{{ route('boxes.show', $child) }}">
                                <span>{{ $child->code }}</span>
                                <span
                                    class="badge text-bg-light border">{{ __('boxes.level', ['level' => $child->level]) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white border rounded-3 p-4 mb-4 text-center">
                <h2 class="h5">{{ __('boxes.qr_heading') }}</h2>
                <div id="qr-code" class="d-inline-block my-3" aria-label="QR {{ $box->code }}"></div>
                <div class="fw-semibold fs-5">{{ $box->code }}</div>
                <div class="small text-muted text-break">{{ $box->qr_uuid }}</div>
                <a id="download-qr" class="btn btn-outline-primary mt-3" href="#"
                    download="{{ $box->code }}-qr.png">{{ __('boxes.download_qr') }}</a>
            </div>

            <div class="bg-white border rounded-3 p-4">
                <h2 class="h5">{{ __('boxes.actions') }}</h2>
                <div class="d-grid gap-2">
                    <a class="btn btn-outline-primary text-start text-md-center"
                        href="{{ route('boxes.create', ['parent_id' => $box->id]) }}">{{ __('boxes.add_child_box') }}</a>
                    <a class="btn btn-outline-secondary"
                        href="{{ route('boxes.index') }}">{{ __('boxes.back_to_list') }}</a>
                    <form action="{{ route('boxes.destroy', $box) }}" method="post"
                        onsubmit="return confirm(@json(__('boxes.delete_confirm')))">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger w-100" type="submit">{{ __('boxes.delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        const qrElement = document.getElementById('qr-code');
        const downloadLink = document.getElementById('download-qr');

        new QRCode(qrElement, {
            text: @json($box->qr_uuid),
            width: 220,
            height: 220,
        });

        const buildQrDownload = () => {
            const canvas = qrElement.querySelector('canvas');
            const image = qrElement.querySelector('img');
            const sourceSize = 220;
            const padding = 10;
            const textHeight = 30;
            const exportCanvas = document.createElement('canvas');
            const context = exportCanvas.getContext('2d');

            exportCanvas.width = sourceSize + (padding * 2);
            exportCanvas.height = sourceSize + (padding * 2) + textHeight;
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, exportCanvas.width, exportCanvas.height);

            if (canvas) {
                context.drawImage(canvas, padding, padding, sourceSize, sourceSize);
            } else if (image) {
                context.drawImage(image, padding, padding, sourceSize, sourceSize);
            } else {
                return;
            }

            context.fillStyle = '#111827';
            context.font = '700 24px Arial, sans-serif';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            context.fillText(@json($box->code), exportCanvas.width / 2, padding + sourceSize + (textHeight /
                2));
            downloadLink.href = exportCanvas.toDataURL('image/png');
        };

        setTimeout(buildQrDownload, 100);
    </script>
@endpush
