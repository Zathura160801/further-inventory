<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('boxes.app_name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7f9;
        }

        .box-tree {
            list-style: none;
            padding-left: 0;
        }

        .box-tree .box-tree {
            border-left: 2px solid #dee2e6;
            margin-left: 1rem;
            padding-left: 1rem;
        }

        .object-cover {
            object-fit: cover;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ route('home') }}">{{ __('boxes.app_name') }}</a>
            <div class="d-flex flex-wrap justify-content-end gap-2 ms-auto">
                @if (request()->routeIs('boxes.scan', 'boxes.qr.show'))
                    <a class="btn btn-sm btn-outline-primary"
                        href="{{ route('boxes.index') }}">{{ __('boxes.manage_items') }}</a>
                @elseif (request()->routeIs('boxes.*'))
                    <a class="btn btn-sm btn-outline-secondary"
                        href="{{ route('boxes.scan') }}">{{ __('boxes.scan_qr') }}</a>
                @endif
                <form action="{{ route('locale.update') }}" method="post">
                    @csrf
                    <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('boxes.language') }}">
                        <button
                            class="btn @if (app()->getLocale() === 'en') btn-primary @else btn-outline-secondary @endif"
                            type="submit" name="locale" value="en">EN</button>
                        <button
                            class="btn @if (app()->getLocale() === 'zh_TW') btn-primary @else btn-outline-secondary @endif"
                            type="submit" name="locale" value="zh_TW">繁</button>
                    </div>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
            <nav aria-label="breadcrumb" class="flex-grow-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('boxes.home') }}</a></li>
                    @yield('breadcrumbs')
                </ol>
            </nav>
            @hasSection('backUrl')
                <a class="btn btn-sm btn-outline-secondary align-self-end ms-auto text-nowrap" href="@yield('backUrl')">
                    {{ __('boxes.back') }}
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">{{ __('boxes.validation_summary') }}</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
