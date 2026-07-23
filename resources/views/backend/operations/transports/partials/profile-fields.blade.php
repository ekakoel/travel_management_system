@php
    $coverUrl = $transport?->cover
        ? asset('storage/transports/transports-cover/' . $transport->cover)
        : null;
@endphp

<section class="transport-form-cover is-wide">
    <div class="backend-section-header">
        <div>
            <span class="backend-section-header__label">Cover Image</span>
            <h3>{{ $isCreate ? 'Upload Cover' : 'Update Cover' }}</h3>
        </div>
        <p>{{ $isCreate ? 'A cover image is required for a new transport item.' : 'Leave empty to keep the current cover image.' }}</p>
    </div>

    <div class="transport-cover-preview" data-transport-cover-preview>
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="{{ $transport->name }}" loading="lazy">
        @else
            <span><i class="fa fa-picture-o"></i> No cover selected</span>
        @endif
    </div>

    <label class="backend-form-field is-wide">
        <span>Cover Image {{ $isCreate ? '*' : '' }}</span>
        <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" data-transport-cover-input accept="image/*" @required($isCreate)>
        <small class="transport-file-status" data-transport-cover-status>{{ $isCreate ? 'No cover selected' : 'Current cover will be kept unless a new file is selected' }}</small>
        @error('cover')
            <small class="backend-form-error">{{ $message }}</small>
        @enderror
    </label>
</section>

@unless ($isCreate)
    <label class="backend-form-field">
        <span>Status <b>*</b></span>
        <select name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
            @foreach (['Active', 'Draft', 'Archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $transport?->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
        @error('status')
            <small class="backend-form-error">{{ $message }}</small>
        @enderror
    </label>
@endunless

<label class="backend-form-field">
    <span>Name <b>*</b></span>
    <input type="text" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert transport name" value="{{ old('name', $transport?->name) }}" required>
    @error('name')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Brand <b>*</b></span>
    <select name="brand" class="backend-form-control @error('brand') is-invalid @enderror" required>
        <option value="">Select brand</option>
        @foreach ($brands as $transportBrand)
            <option value="{{ $transportBrand->brand }}" @selected(old('brand', $transport?->brand) === $transportBrand->brand)>{{ $transportBrand->brand }}</option>
        @endforeach
    </select>
    @error('brand')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Type <b>*</b></span>
    <select name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
        <option value="">Select type</option>
        @foreach ($types as $transportType)
            <option value="{{ $transportType->type }}" @selected(old('type', $transport?->type) === $transportType->type)>{{ $transportType->type }}</option>
        @endforeach
    </select>
    @error('type')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field">
    <span>Capacity <b>*</b></span>
    <input type="number" name="capacity" class="backend-form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ old('capacity', $transport?->capacity) }}" required>
    @error('capacity')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

@if ($isCreate)
    <input type="hidden" name="status" value="Draft">
@endif

<label class="backend-form-field is-wide">
    <span>Description <b>*</b></span>
    <textarea name="description" class="backend-form-control textarea_editor @error('description') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description" required>{{ old('description', $transport?->description) }}</textarea>
    @error('description')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field is-wide">
    <span>Include <b>*</b></span>
    <textarea name="include" class="backend-form-control textarea_editor @error('include') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert include" required>{{ old('include', $transport?->include) }}</textarea>
    @error('include')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field is-wide">
    <span>Cancellation Policy</span>
    <textarea name="cancellation_policy" class="backend-form-control textarea_editor @error('cancellation_policy') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy', $transport?->cancellation_policy) }}</textarea>
    @error('cancellation_policy')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>

<label class="backend-form-field is-wide">
    <span>Additional Information</span>
    <textarea name="additional_info" class="backend-form-control textarea_editor @error('additional_info') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info', $transport?->additional_info) }}</textarea>
    @error('additional_info')
        <small class="backend-form-error">{{ $message }}</small>
    @enderror
</label>
