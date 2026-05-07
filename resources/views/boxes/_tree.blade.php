<ul class="box-tree">
    @foreach ($boxes as $box)
        <li class="mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge text-bg-dark">{{ $box->code }}</span>
                                <span
                                    class="badge text-bg-light border">{{ __('boxes.level', ['level' => $box->level]) }}</span>
                                <span class="badge text-bg-secondary">{{ $box->status_label }}</span>
                            </div>
                            <a class="h5 d-block text-decoration-none mb-1"
                                href="{{ route('boxes.show', $box) }}">{{ $box->code }}</a>
                            @if ($box->description)
                                <p class="text-muted mb-0">{{ Illuminate\Support\Str::limit($box->description, 140) }}
                                </p>
                            @endif
                        </div>
                        <div class="d-flex justify-content-end">
                            <a class="btn btn-sm btn-outline-primary ms-auto"
                                href="{{ route('boxes.create', ['parent_id' => $box->id]) }}">{{ __('boxes.add_child') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($box->descendants->isNotEmpty())
                @include('boxes._tree', ['boxes' => $box->descendants])
            @endif
        </li>
    @endforeach
</ul>
