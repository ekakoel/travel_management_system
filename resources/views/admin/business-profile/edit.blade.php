@section('title', 'Company Profile')
@section('content')
@extends('layouts.head')
@php
    use Illuminate\Support\Str;

    $logoUrlFor = function (?string $logo) {
        if (!$logo) {
            return null;
        }

        return Str::startsWith($logo, ['http://', 'https://', '/'])
            ? $logo
            : asset('storage/logo/' . ltrim($logo, '/'));
    };

    $logoUrl = $logoUrlFor($businessProfile->logo ?? null);
    $logoDarkUrl = $logoUrlFor($businessProfile->logo_dark ?? null);

    $inputClass = function (string $field) use ($errors) {
        return 'form-control' . ($errors->has($field) ? ' is-invalid' : '');
    };
@endphp

<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 col-sm-12">
                        <div class="title">
                            <i class="icon-copy dw dw-building1" aria-hidden="true"></i> Company Profile
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Company Profile</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-4 col-sm-12 text-md-right">
                        <a href="{{ route('about-us') }}" target="_blank" class="btn btn-outline-primary">
                            <i class="icon-copy fa fa-external-link" aria-hidden="true"></i> Preview Public Page
                        </a>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please review the highlighted fields.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.company-profile.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="card-box mb-30">
                            <div class="pd-20 border-bottom">
                                <h4 class="text-blue h4 mb-1">Company Identity</h4>
                                <p class="mb-0 text-muted">Core company information used by public pages, invoices, and shared layouts.</p>
                            </div>
                            <div class="pd-20">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Legal Company Name <span class="text-danger">*</span></label>
                                            <input id="name" name="name" type="text" class="{{ $inputClass('name') }}" value="{{ old('name', $businessProfile->name) }}" required>
                                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nickname">Public Brand Name</label>
                                            <input id="nickname" name="nickname" type="text" class="{{ $inputClass('nickname') }}" value="{{ old('nickname', $businessProfile->nickname) }}">
                                            @error('nickname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Business Type</label>
                                            <input id="type" name="type" type="text" class="{{ $inputClass('type') }}" value="{{ old('type', $businessProfile->type) }}" placeholder="B2B Travel Agent">
                                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="caption">Short Caption</label>
                                            <input id="caption" name="caption" type="text" class="{{ $inputClass('caption') }}" value="{{ old('caption', $businessProfile->caption) }}">
                                            @error('caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="license">Business License</label>
                                            <input id="license" name="license" type="text" class="{{ $inputClass('license') }}" value="{{ old('license', $businessProfile->license) }}">
                                            @error('license')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tax_number">Tax Number</label>
                                            <input id="tax_number" name="tax_number" type="text" class="{{ $inputClass('tax_number') }}" value="{{ old('tax_number', $businessProfile->tax_number) }}">
                                            @error('tax_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="tax_id">Tax ID</label>
                                            <input id="tax_id" name="tax_id" type="text" class="{{ $inputClass('tax_id') }}" value="{{ old('tax_id', $businessProfile->tax_id) }}">
                                            @error('tax_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">Office Address</label>
                                            <textarea id="address" name="address" class="{{ $inputClass('address') }}" rows="3">{{ old('address', $businessProfile->address) }}</textarea>
                                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-box mb-30">
                            <div class="pd-20 border-bottom">
                                <h4 class="text-blue h4 mb-1">Public Content</h4>
                                <p class="mb-0 text-muted">Used on About Us, footer, and future customer-facing company sections.</p>
                            </div>
                            <div class="pd-20">
                                <div class="form-group">
                                    <label for="public_tagline">Tagline - English</label>
                                    <input id="public_tagline" name="public_tagline" type="text" class="{{ $inputClass('public_tagline') }}" value="{{ old('public_tagline', $businessProfile->public_tagline) }}">
                                    @error('public_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="public_description">Description - English</label>
                                    <textarea id="public_description" name="public_description" class="{{ $inputClass('public_description') }}" rows="5">{{ old('public_description', $businessProfile->public_description) }}</textarea>
                                    @error('public_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="public_tagline_traditional">Tagline - Chinese Traditional</label>
                                            <input id="public_tagline_traditional" name="public_tagline_traditional" type="text" class="{{ $inputClass('public_tagline_traditional') }}" value="{{ old('public_tagline_traditional', $businessProfile->public_tagline_traditional) }}">
                                            @error('public_tagline_traditional')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="public_description_traditional">Description - Chinese Traditional</label>
                                            <textarea id="public_description_traditional" name="public_description_traditional" class="{{ $inputClass('public_description_traditional') }}" rows="4">{{ old('public_description_traditional', $businessProfile->public_description_traditional) }}</textarea>
                                            @error('public_description_traditional')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="public_tagline_simplified">Tagline - Chinese Simplified</label>
                                            <input id="public_tagline_simplified" name="public_tagline_simplified" type="text" class="{{ $inputClass('public_tagline_simplified') }}" value="{{ old('public_tagline_simplified', $businessProfile->public_tagline_simplified) }}">
                                            @error('public_tagline_simplified')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="public_description_simplified">Description - Chinese Simplified</label>
                                            <textarea id="public_description_simplified" name="public_description_simplified" class="{{ $inputClass('public_description_simplified') }}" rows="4">{{ old('public_description_simplified', $businessProfile->public_description_simplified) }}</textarea>
                                            @error('public_description_simplified')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="card-box mb-30">
                            <div class="pd-20 border-bottom">
                                <h4 class="text-blue h4 mb-1">Brand Assets</h4>
                                <p class="mb-0 text-muted">Light and dark mode logos are stored in <code>storage/logo</code>.</p>
                            </div>
                            <div class="pd-20">
                                <div class="form-group">
                                    <label for="logo">Light Mode Logo</label>
                                    <div class="company-logo-preview company-logo-preview--light mb-3">
                                        @if ($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $businessProfile->name }} light mode logo">
                                        @else
                                            <span>No light mode logo uploaded</span>
                                        @endif
                                    </div>
                                    <input id="logo" name="logo" type="file" class="form-control-file @error('logo') is-invalid @enderror" accept="image/*">
                                    <small class="form-text text-muted">Recommended for bright backgrounds. PNG/WebP, max 2 MB.</small>
                                    @error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group mb-0">
                                    <label for="logo_dark">Dark Mode Logo</label>
                                    <div class="company-logo-preview company-logo-preview--dark mb-3">
                                        @if ($logoDarkUrl)
                                            <img src="{{ $logoDarkUrl }}" alt="{{ $businessProfile->name }} dark mode logo">
                                        @else
                                            <span>No dark mode logo uploaded</span>
                                        @endif
                                    </div>
                                    <input id="logo_dark" name="logo_dark" type="file" class="form-control-file @error('logo_dark') is-invalid @enderror" accept="image/*">
                                    <small class="form-text text-muted">Recommended for dark backgrounds, usually white or high-contrast. PNG/WebP, max 2 MB.</small>
                                    @error('logo_dark')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-box mb-30">
                            <div class="pd-20 border-bottom">
                                <h4 class="text-blue h4 mb-1">Contact</h4>
                                <p class="mb-0 text-muted">Shown in footer and reusable company blocks.</p>
                            </div>
                            <div class="pd-20">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="email" class="{{ $inputClass('email') }}" value="{{ old('email', $businessProfile->email) }}">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone">Primary Phone</label>
                                    <input id="phone" name="phone" type="text" class="{{ $inputClass('phone') }}" value="{{ old('phone', $businessProfile->phone) }}">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone_2">Secondary Phone</label>
                                    <input id="phone_2" name="phone_2" type="text" class="{{ $inputClass('phone_2') }}" value="{{ old('phone_2', $businessProfile->phone_2) }}">
                                    @error('phone_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone_3">Additional Phone</label>
                                    <input id="phone_3" name="phone_3" type="text" class="{{ $inputClass('phone_3') }}" value="{{ old('phone_3', $businessProfile->phone_3) }}">
                                    @error('phone_3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="whatsapp">WhatsApp</label>
                                    <input id="whatsapp" name="whatsapp" type="text" class="{{ $inputClass('whatsapp') }}" value="{{ old('whatsapp', $businessProfile->whatsapp) }}">
                                    @error('whatsapp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="map">Google Maps Embed URL</label>
                                    <input id="map" name="map" type="text" class="{{ $inputClass('map') }}" value="{{ old('map', $businessProfile->map) }}">
                                    <small class="form-text text-muted">Use a Google Maps URL that contains <code>/maps/embed</code>. Regular place/share URLs cannot be displayed inside the website map iframe.</small>
                                    @error('map')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-box mb-30">
                            <div class="pd-20 border-bottom">
                                <h4 class="text-blue h4 mb-1">Digital Channels</h4>
                                <p class="mb-0 text-muted">Use full URLs when possible for reliable public links.</p>
                            </div>
                            <div class="pd-20">
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input id="website" name="website" type="text" class="{{ $inputClass('website') }}" value="{{ old('website', $businessProfile->website) }}">
                                    @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="instagram">Instagram</label>
                                    <input id="instagram" name="instagram" type="text" class="{{ $inputClass('instagram') }}" value="{{ old('instagram', $businessProfile->instagram) }}">
                                    @error('instagram')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="facebook">Facebook</label>
                                    <input id="facebook" name="facebook" type="text" class="{{ $inputClass('facebook') }}" value="{{ old('facebook', $businessProfile->facebook) }}">
                                    @error('facebook')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="twitter">Twitter / X</label>
                                    <input id="twitter" name="twitter" type="text" class="{{ $inputClass('twitter') }}" value="{{ old('twitter', $businessProfile->twitter) }}">
                                    @error('twitter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="youtube">YouTube</label>
                                    <input id="youtube" name="youtube" type="text" class="{{ $inputClass('youtube') }}" value="{{ old('youtube', $businessProfile->youtube) }}">
                                    @error('youtube')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-group">
                                    <label for="linkedin">LinkedIn</label>
                                    <input id="linkedin" name="linkedin" type="text" class="{{ $inputClass('linkedin') }}" value="{{ old('linkedin', $businessProfile->linkedin) }}">
                                    @error('linkedin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-box mb-30 company-profile-actionbar">
                    <div>
                        <strong>Ready to update company data?</strong>
                        <p class="mb-0 text-muted">Changes will be available to public pages after saving.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-copy fa fa-check" aria-hidden="true"></i> Save Company Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .company-logo-preview {
        align-items: center;
        border: 1px solid #dbe8f6;
        border-radius: 18px;
        display: flex;
        justify-content: center;
        min-height: 160px;
        padding: 24px;
        text-align: center;
    }

    .company-logo-preview--light {
        background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
    }

    .company-logo-preview--dark {
        background: linear-gradient(135deg, #071a2d 0%, #102a43 100%);
        border-color: #24435f;
    }

    .company-logo-preview img {
        max-height: 96px;
        max-width: 100%;
        object-fit: contain;
    }

    .company-logo-preview span {
        color: #7b8aa0;
        font-weight: 600;
    }

    .company-profile-actionbar {
        align-items: center;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        padding: 20px;
        position: sticky;
        bottom: 16px;
        z-index: 20;
    }

    @media (max-width: 767px) {
        .company-profile-actionbar {
            align-items: stretch;
            flex-direction: column;
        }
    }
</style>
@endpush
@endsection
