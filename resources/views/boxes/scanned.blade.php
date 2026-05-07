@extends('layouts.app', ['title' => $box->code])

@section('backUrl', route('boxes.scan'))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('boxes.scan') }}">{{ __('boxes.scan_qr') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $box->code }}</li>
@endsection

@section('content')
    @php
        $statusBadgeClass = match ($box->status) {
            'packed' => 'text-bg-success',
            'unpacked' => 'text-bg-warning text-dark',
            default => 'text-bg-info',
        };
    @endphp

    <style>
        .box-hierarchy-track {
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .box-hierarchy-track a,
        .box-hierarchy-track span {
            white-space: nowrap;
        }

        .box-hierarchy-sep {
            color: #adb5bd;
        }
    </style>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-white border rounded-3 p-3 p-md-4 mb-4">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge rounded-pill text-bg-primary">{{ $box->code }}</span>
                    <span class="badge rounded-pill text-bg-info">{{ __('boxes.level', ['level' => $box->level]) }}</span>
                    <span class="badge rounded-pill {{ $statusBadgeClass }}">{{ $box->status_label }}</span>
                </div>
                <h1 class="h3 mb-3 text-break">{{ $box->code }}</h1>

                <h2 class="h5">{{ __('boxes.hierarchy') }}</h2>
                <div class="d-flex align-items-center gap-2 pb-1 box-hierarchy-track">
                    @forelse ($ancestors as $ancestor)
                        <a class="badge rounded-pill text-bg-light border text-decoration-none"
                            href="{{ route('boxes.qr.show', $ancestor->qr_uuid) }}">{{ $ancestor->code }}</a>
                        <span class="box-hierarchy-sep">/</span>
                    @empty
                    @endforelse
                    <span class="badge rounded-pill text-bg-dark">{{ $box->code }}</span>
                </div>
            </div>

            <div class="bg-white border rounded-3 p-3 p-md-4 mb-4">
                <h2 class="h5 mb-3">{{ __('boxes.notes') }}</h2>
                <form action="{{ route('boxes.notes.update', $box) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $box->description) }}</textarea>
                    <button class="btn btn-primary mt-3 w-100 w-sm-auto"
                        type="submit">{{ __('boxes.save_notes') }}</button>
                </form>
            </div>

            <div class="bg-white border rounded-3 p-3 p-md-4">
                <h2 class="h5 mb-3">{{ __('boxes.photos') }}</h2>
                <form class="border rounded-3 p-2 p-sm-3 mb-3" action="{{ route('boxes.images.store', $box) }}"
                    method="post" enctype="multipart/form-data">
                    @csrf
                    @include('boxes._photo_uploader', ['id' => 'box-scanned-photos'])
                    <button class="btn btn-primary mt-3 w-100 w-sm-auto"
                        type="submit">{{ __('boxes.upload_photos') }}</button>
                </form>

                @if ($box->images->isEmpty())
                    <p class="text-muted mb-0">{{ __('boxes.no_photos') }}</p>
                @else
                    <div class="row g-3">
                        @foreach ($box->images as $image)
                            <div class="col-6 col-sm-4 col-md-3">
                                <a href="{{ asset('storage/' . $image->image_path) }}" target="_blank">
                                    <img class="img-fluid rounded border object-cover w-100" style="aspect-ratio: 1 / 1;"
                                        src="{{ asset('storage/' . $image->image_path) }}"
                                        alt="{{ __('boxes.photos') }} {{ $box->code }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white border rounded-3 p-3 p-md-4">
                <h2 class="h5 mb-3">{{ __('boxes.children_heading') }}</h2>
                @if ($box->children->isEmpty())
                    <p class="text-muted mb-0">{{ __('boxes.no_children') }}</p>
                @else
                    <div class="list-group">
                        @foreach ($box->children as $child)
                            <a class="list-group-item list-group-item-action d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2"
                                href="{{ route('boxes.qr.show', $child->qr_uuid) }}">
                                <span class="text-break">{{ $child->code }}</span>
                                <span
                                    class="badge rounded-pill text-bg-light border">{{ __('boxes.level', ['level' => $child->level]) }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
