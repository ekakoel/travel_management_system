<div class="backend-form">
    <div class="backend-form-grid m-b-18">
        <div class="backend-form-field">
            <label for="sort_order">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" min="0" class="backend-form-control"
                value="{{ old('sort_order', $slider->sort_order ?? 0) }}">
        </div>
    </div>
    <div class="backend-form-grid-2 m-b-18">
        <div class="backend-form-field">
            <label for="image">Desktop Image</label>

            @if (!empty($slider?->image))
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}"
                        style="width:180px; height:70px; object-fit:cover;">
                </div>
            @endif

            <input type="file" name="image" id="image" class="backend-form-control"
                accept=".jpg,.jpeg,.png,.webp">
        </div>

        <div class="backend-form-field">
            <label for="mobile_image">Mobile Image</label>

            @if (!empty($slider?->mobile_image))
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $slider->mobile_image) }}" alt="{{ $slider->title }}"
                        style="width:100px; height:125px; object-fit:cover;">
                </div>
            @endif

            <input type="file" name="mobile_image" id="mobile_image" class="backend-form-control"
                accept=".jpg,.jpeg,.png,.webp">
        </div>

    </div>
    <div class="backend-form-grid m-b-18">
        <div class="backend-form-field">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="backend-form-control"
                value="{{ old('title', $slider->title ?? '') }}">
        </div>
        <div class="backend-form-field">
            <label for="title_traditional">Title Traditional</label>
            <input type="text" name="title_traditional" id="title_traditional" class="backend-form-control"
                value="{{ old('title_traditional', $slider->title_traditional ?? '') }}">
        </div>
        <div class="backend-form-field">
            <label for="title_simplified">Title Simplified</label>
            <input type="text" name="title_simplified" id="title_simplified" class="backend-form-control"
                value="{{ old('title_simplified', $slider->title_simplified ?? '') }}">
        </div>
    </div>
    <div class="backend-form-grid m-b-18">
        <div class="backend-form-field">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" class="backend-form-control">{{ old('description', $slider->description ?? '') }}</textarea>
        </div>
        <div class="backend-form-field">
            <label for="description_traditional">Description Traditiona</label>
            <textarea name="description_traditional" id="description_traditional" rows="4" class="backend-form-control">{{ old('description_traditional', $slider->description_traditional ?? '') }}</textarea>
        </div>
        <div class="backend-form-field">
            <label for="description_simplified">Description Simplified</label>
            <textarea name="description_simplified" id="description_simplified" rows="4" class="backend-form-control">{{ old('description_simplified', $slider->description_simplified ?? '') }}</textarea>
        </div>
    </div>


    <div class="backend-form-grid m-b-18">

        <div class="backend-form-field">
            <label for="button_text">Button Text</label>
            <input type="text" name="button_text" id="button_text" class="backend-form-control"
                value="{{ old('button_text', $slider->button_text ?? '') }}">
        </div>
    </div>

    <div class="backend-form-grid m-b-18">
        <div class="backend-form-field">
            <label for="button_url">Button URL</label>
            <input type="text" name="button_url" id="button_url" class="backend-form-control"
                value="{{ old('button_url', $slider->button_url ?? '') }}">
        </div>

        <div class="backend-form-field">
            <label for="start_at">Start At</label>
            <input type="datetime-local" name="start_at" id="start_at" class="backend-form-control"
                value="{{ old('start_at', isset($slider?->start_at) ? $slider->start_at->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="backend-form-field">
            <label for="end_at">End At</label>
            <input type="datetime-local" name="end_at" id="end_at" class="backend-form-control"
                value="{{ old('end_at', isset($slider?->end_at) ? $slider->end_at->format('Y-m-d\TH:i') : '') }}">
        </div>
    </div>
     {{-- <div class="backend-form-field">
        <button type="button" class="backend-status-toggle {{ $slider->is_active ?? true ? 'is-active' : '' }}"
            title="{{ $slider->is_active ?? true ? 'Active' : 'Draft' }}" data-backend-status-toggle>
            <span class="backend-status-toggle__track" aria-hidden="true">
                <span class="backend-status-toggle__knob"></span>
            </span>

            <span class="backend-status-toggle__label" data-backend-status-toggle-label>
                {{ $slider->is_active ?? true ? 'Active' : 'Draft' }}
            </span>
        </button>
        <input type="hidden" name="is_active" id="is_active" value="{{ $slider->is_active ?? true ? 1 : 0 }}">
    </div> --}}
</div>
