@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="parent_id">{{ __('boxes.parent_box') }}</label>
        <select class="form-select" id="parent_id" name="parent_id">
            <option value="">{{ __('boxes.no_parent') }}</option>
            @foreach ($boxes as $parentOption)
                <option value="{{ $parentOption->id }}" @selected(old('parent_id', $box->parent_id ?? ($parent?->id ?? '')) == $parentOption->id)>
                    {!! str_repeat('&mdash; ', max(0, $parentOption->level - 1)) !!}{{ $parentOption->code }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('boxes.parent_help') }}</div>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="code">{{ __('boxes.box_code') }}</label>
        <input class="form-control text-uppercase" id="code" name="code"
            value="{{ old('code', $box->code ?? ($suggestedCode ?? '')) }}" required>
    </div>

    <div class="col-md-4">
        <label class="form-label" for="status">{{ __('boxes.status') }}</label>
        <select class="form-select" id="status" name="status" required>
            @foreach (['packed' => __('boxes.packed'), 'unpacked' => __('boxes.unpacked')] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $box->status ?? 'packed') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label" for="description">{{ __('boxes.contents') }}</label>
        <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $box->description ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label" for="images">{{ __('boxes.photos') }}</label>
        @include('boxes._photo_uploader', ['id' => 'box-form-photos'])
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button class="btn btn-primary" type="submit">{{ __('boxes.save') }}</button>
    <a class="btn btn-outline-secondary"
        href="{{ isset($box) ? route('boxes.show', $box) : route('boxes.index') }}">{{ __('boxes.cancel') }}</a>
</div>
