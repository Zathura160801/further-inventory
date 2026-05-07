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
            <a class="navbar-brand fw-semibold" href="{{ route('boxes.index') }}">{{ __('boxes.app_name') }}</a>
            <div class="d-flex flex-wrap gap-2 ms-auto">
                <form action="{{ route('locale.update') }}" method="post">
                    @csrf
                    <label class="visually-hidden" for="locale">{{ __('boxes.language') }}</label>
                    <select class="form-select" id="locale" name="locale" onchange="this.form.submit()">
                        <option value="en" @selected(app()->getLocale() === 'en')>{{ __('boxes.english') }}</option>
                        <option value="zh_TW" @selected(app()->getLocale() === 'zh_TW')>{{ __('boxes.traditional_chinese') }}
                        </option>
                    </select>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('boxes.index') }}">{{ __('boxes.home') }}</a></li>
                    @yield('breadcrumbs')
                </ol>
            </nav>
            @hasSection('backUrl')
                <a class="btn btn-sm btn-outline-secondary align-self-start" href="@yield('backUrl')">
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
