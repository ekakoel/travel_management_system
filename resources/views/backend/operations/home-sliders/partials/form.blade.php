<div class="backend-form">

    <div class="backend-form-grid">

        <div class="backend-form-field">
            <label for="title">Title</label>
            <input
                type="text"
                name="title"
                id="title"
                class="backend-form-control"
                value="{{ old('title', $slider->title ?? '') }}"
            >
        </div>

        <div class="backend-form-field">
            <label for="sort_order">Sort Order</label>
            <input
                type="number"
                name="sort_order"
                id="sort_order"
                min="0"
                class="backend-form-control"
                value="{{ old('sort_order', $slider->sort_order ?? 0) }}"
            >
        </div>

    </div>

    <div class="backend-form-field">
        <label for="description">Description</label>
        <textarea
            name="description"
            id="description"
            rows="4"
            class="backend-form-control"
        >{{ old('description', $slider->description ?? '') }}</textarea>
    </div>

    <div class="backend-form-grid">

        <div class="backend-form-field">
            <label for="image">Desktop Image</label>

            @if(!empty($slider?->image))
                <div class="mb-2">
                    <img
                        src="{{ asset('storage/' . $slider->image) }}"
                        alt="{{ $slider->title }}"
                        style="width:180px; height:70px; object-fit:cover;"
                    >
                </div>
            @endif

            <input
                type="file"
                name="image"
                id="image"
                class="backend-form-control"
                accept=".jpg,.jpeg,.png,.webp"
            >
        </div>

        <div class="backend-form-field">
            <label for="mobile_image">Mobile Image</label>

            @if(!empty($slider?->mobile_image))
                <div class="mb-2">
                    <img
                        src="{{ asset('storage/' . $slider->mobile_image) }}"
                        alt="{{ $slider->title }}"
                        style="width:100px; height:125px; object-fit:cover;"
                    >
                </div>
            @endif

            <input
                type="file"
                name="mobile_image"
                id="mobile_image"
                class="backend-form-control"
                accept=".jpg,.jpeg,.png,.webp"
            >
        </div>

    </div>

    <div class="backend-form-grid">

        <div class="backend-form-field">
            <label for="button_text">Button Text</label>
            <input
                type="text"
                name="button_text"
                id="button_text"
                class="backend-form-control"
                value="{{ old('button_text', $slider->button_text ?? '') }}"
            >
        </div>

        <div class="backend-form-field">
            <label for="button_url">Button URL</label>
            <input
                type="text"
                name="button_url"
                id="button_url"
                class="backend-form-control"
                value="{{ old('button_url', $slider->button_url ?? '') }}"
            >
        </div>

    </div>

    <div class="backend-form-grid">

        <div class="backend-form-field">
            <label for="start_at">Start At</label>
            <input
                type="datetime-local"
                name="start_at"
                id="start_at"
                class="backend-form-control"
                value="{{ old('start_at', isset($slider?->start_at) ? $slider->start_at->format('Y-m-d\TH:i') : '') }}"
            >
        </div>

        <div class="backend-form-field">
            <label for="end_at">End At</label>
            <input
                type="datetime-local"
                name="end_at"
                id="end_at"
                class="backend-form-control"
                value="{{ old('end_at', isset($slider?->end_at) ? $slider->end_at->format('Y-m-d\TH:i') : '') }}"
            >
        </div>

    </div>

    <div class="backend-form-field">
        <label>
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', $slider->is_active ?? true) ? 'checked' : '' }}
            >
            Active
        </label>
    </div>

</div>