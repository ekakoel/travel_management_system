@extends('frontend.layouts.app')

@section('title', __('agent-registration.title'))

@section('content')
    <main class="container py-5">
        <header class="mb-4">
            <h1>@lang('agent-registration.title')</h1>
            <p>@lang('agent-registration.introduction')</p>
        </header>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('partner.application.submit') }}" enctype="multipart/form-data">
            @csrf
            @include('partials.form-submission-token')

            <fieldset class="mb-4">
                <legend>@lang('agent-registration.sections.company')</legend>
                <div class="mb-3">
                    <label class="form-label" for="company_name">@lang('agent-registration.fields.company_name')</label>
                    <input class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                    @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="company_type">@lang('agent-registration.fields.company_type')</label>
                    <select class="form-select @error('company_type') is-invalid @enderror" id="company_type" name="company_type" required>
                        <option value="">@lang('agent-registration.select')</option>
                        @foreach (['travel_agency', 'tour_operator', 'wholesaler', 'corporate_travel'] as $type)
                            <option value="{{ $type }}" @selected(old('company_type') === $type)>@lang('agent-registration.company_types.'.$type)</option>
                        @endforeach
                    </select>
                    @error('company_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="country">@lang('agent-registration.fields.country')</label>
                    <input class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country') }}" required>
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="company_address">@lang('agent-registration.fields.company_address')</label>
                    <textarea class="form-control @error('company_address') is-invalid @enderror" id="company_address" name="company_address" rows="3" required>{{ old('company_address') }}</textarea>
                    @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="website">@lang('agent-registration.fields.website')</label>
                    <input class="form-control @error('website') is-invalid @enderror" id="website" type="url" name="website" value="{{ old('website') }}">
                    @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="business_license_number">@lang('agent-registration.fields.business_license_number')</label>
                    <input class="form-control @error('business_license_number') is-invalid @enderror" id="business_license_number" name="business_license_number" value="{{ old('business_license_number') }}">
                    @error('business_license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </fieldset>

            <fieldset class="mb-4">
                <legend>@lang('agent-registration.sections.contact')</legend>
                @foreach (['contact_name' => 'text', 'contact_email' => 'email', 'phone' => 'text', 'position' => 'text', 'preferred_contact' => 'text'] as $field => $inputType)
                    <div class="mb-3">
                        <label class="form-label" for="{{ $field }}">@lang('agent-registration.fields.'.$field)</label>
                        <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" type="{{ $inputType }}" name="{{ $field }}" value="{{ old($field) }}" @if (in_array($field, ['contact_name', 'contact_email', 'phone'], true)) required @endif>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
            </fieldset>

            <fieldset class="mb-4">
                <legend>@lang('agent-registration.sections.business')</legend>
                <div class="mb-3">
                    <label class="form-label" for="main_market">@lang('agent-registration.fields.main_market')</label>
                    <input class="form-control @error('main_market') is-invalid @enderror" id="main_market" name="main_market" value="{{ old('main_market') }}">
                    @error('main_market')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="monthly_bali_clients">@lang('agent-registration.fields.monthly_bali_clients')</label>
                    <input class="form-control @error('monthly_bali_clients') is-invalid @enderror" id="monthly_bali_clients" type="number" min="0" name="monthly_bali_clients" value="{{ old('monthly_bali_clients') }}">
                    @error('monthly_bali_clients')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <span class="form-label d-block">@lang('agent-registration.fields.interested_services')</span>
                    @foreach (['accommodation', 'transport', 'tour_packages', 'activities'] as $service)
                        <div class="form-check">
                            <input class="form-check-input" id="service_{{ $service }}" type="checkbox" name="interested_services[]" value="{{ $service }}" @checked(in_array($service, old('interested_services', []), true))>
                            <label class="form-check-label" for="service_{{ $service }}">@lang('agent-registration.services.'.$service)</label>
                        </div>
                    @endforeach
                    @error('interested_services')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
            </fieldset>

            <fieldset class="mb-4">
                <legend>@lang('agent-registration.sections.documents')</legend>
                @foreach (['business_license' => true, 'company_letter' => true, 'tax_document' => false] as $field => $required)
                    <div class="mb-3">
                        <label class="form-label" for="{{ $field }}">@lang('agent-registration.fields.'.$field)</label>
                        <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" type="file" name="{{ $field }}" accept=".pdf,.jpg,.jpeg,.png" @if ($required) required @endif>
                        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <div class="mb-3">
                    <label class="form-label" for="supporting_documents">@lang('agent-registration.fields.supporting_documents')</label>
                    <input class="form-control @error('supporting_documents') is-invalid @enderror" id="supporting_documents" type="file" name="supporting_documents[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    @error('supporting_documents')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </fieldset>

            <fieldset class="mb-4">
                <legend>@lang('agent-registration.sections.agreement')</legend>
                <div class="form-check mb-2">
                    <input class="form-check-input @error('agree_to_terms') is-invalid @enderror" id="agree_to_terms" type="checkbox" name="agree_to_terms" value="1" @checked(old('agree_to_terms')) required>
                    <label class="form-check-label" for="agree_to_terms">@lang('agent-registration.fields.agree_to_terms')</label>
                    @error('agree_to_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check">
                    <input class="form-check-input @error('agree_to_contact') is-invalid @enderror" id="agree_to_contact" type="checkbox" name="agree_to_contact" value="1" @checked(old('agree_to_contact'))>
                    <label class="form-check-label" for="agree_to_contact">@lang('agent-registration.fields.agree_to_contact')</label>
                    @error('agree_to_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </fieldset>

            <button class="btn btn-primary" type="submit">@lang('agent-registration.submit')</button>
        </form>
    </main>
@endsection

{{-- @extends('frontend.layouts.app')
@section('title', __('messages.Become Partner'))
@section('content') 
    <div class="partner-page"><section class="partner-hero">
            <div class="container">
                <div class="partner-hero-content"> <span class="partner-eyebrow"> {{ __('partner.become_partner') }} </span>
                    <h1> {{ __('partner.hero_title') }} </h1>
                    <p> {{ __('partner.hero_description') }} </p>
                </div>
            </div>
        </section><section class="partner-benefits">
            <div class="container">
                <div class="partner-section-heading">
                    <h2>{{ __('partner.why_partner') }}</h2>
                    <p>{{ __('partner.why_partner_description') }}</p>
                </div>
                <div class="partner-benefits-grid">
                    <div class="partner-benefit">
                        <h3>{{ __('partner.benefit_local_expertise') }}</h3>
                        <p>{{ __('partner.benefit_local_expertise_desc') }}</p>
                    </div>
                    <div class="partner-benefit">
                        <h3>{{ __('partner.benefit_premium_services') }}</h3>
                        <p>{{ __('partner.benefit_premium_services_desc') }}</p>
                    </div>
                    <div class="partner-benefit">
                        <h3>{{ __('partner.benefit_support') }}</h3>
                        <p>{{ __('partner.benefit_support_desc') }}</p>
                    </div>
                    <div class="partner-benefit">
                        <h3>{{ __('partner.benefit_b2b') }}</h3>
                        <p>{{ __('partner.benefit_b2b_desc') }}</p>
                    </div>
                </div>
            </div>
        </section><section class="partner-application" id="partner-application">
            <div class="container">
                <div class="partner-section-heading">
                    <h2>{{ __('partner.application_title') }}</h2>
                    <p>{{ __('partner.application_description') }}</p>
                </div>
                @if ($errors->any())
                    <div class="partner-alert partner-alert-error">
                        <strong>{{ __('partner.validation_error_title') }}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif @if (session('error'))
                        <div class="partner-alert partner-alert-error"> {{ session('error') }} </div>
                    @endif
                    <form id="partnerApplicationForm" method="POST" action="{{ route('partner.application.submit') }}"
                        enctype="multipart/form-data" novalidate> @csrf <input type="hidden" name="submission_token"
                            value="{{ old('submission_token', (string) \Illuminate\Support\Str::uuid()) }}">
                        <div class="partner-steps"
                            aria-label="{{ __('partner.application_steps') }}"> <button type="button"
                                class="partner-step active" data-step-target="1"> <span>1</span>
                                <strong>{{ __('partner.step_partnership') }}</strong> </button> <button type="button"
                                class="partner-step" data-step-target="2"> <span>2</span>
                                <strong>{{ __('partner.step_company') }}</strong> </button> <button type="button"
                                class="partner-step" data-step-target="3"> <span>3</span>
                                <strong>{{ __('partner.step_contact') }}</strong> </button> <button type="button"
                                class="partner-step" data-step-target="4"> <span>4</span>
                                <strong>{{ __('partner.step_documents') }}</strong> </button> <button type="button"
                                class="partner-step" data-step-target="5"> <span>5</span>
                                <strong>{{ __('partner.step_review') }}</strong> </button> </div>
                        <div class="partner-form-step active" data-step="1">
                            <div class="partner-form-heading"> <span>01</span>
                                <h3>{{ __('partner.step_partnership_title') }}</h3>
                                <p>{{ __('partner.step_partnership_description') }}</p>
                            </div>
                            <div class="partner-field-grid">
                                <div class="partner-field"> <label for="company_type"> {{ __('partner.company_type') }}
                                        <span>*</span> </label> <select id="company_type" name="company_type" required>
                                        <option value=""> {{ __('partner.select_company_type') }} </option>
                                        <option value="travel_agency" @selected(old('company_type') === 'travel_agency')>
                                            {{ __('partner.company_type_travel_agency') }} </option>
                                        <option value="tour_operator" @selected(old('company_type') === 'tour_operator')>
                                            {{ __('partner.company_type_tour_operator') }} </option>
                                        <option value="event_organizer" @selected(old('company_type') === 'event_organizer')>
                                            {{ __('partner.company_type_event_organizer') }} </option>
                                        <option value="wedding_planner" @selected(old('company_type') === 'wedding_planner')>
                                            {{ __('partner.company_type_wedding_planner') }} </option>
                                        <option value="corporate" @selected(old('company_type') === 'corporate')>
                                            {{ __('partner.company_type_corporate') }} </option>
                                        <option value="other" @selected(old('company_type') === 'other')>
                                            {{ __('partner.company_type_other') }} </option>
                                    </select> @error('company_type')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="country"> {{ __('partner.country') }}
                                        <span>*</span> </label> <input type="text" id="country" name="country"
                                        value="{{ old('country') }}" required> @error('country')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div><div class="partner-form-step" data-step="2">
                            <div class="partner-form-heading"> <span>02</span>
                                <h3>{{ __('partner.step_company_title') }}</h3>
                                <p>{{ __('partner.step_company_description') }}</p>
                            </div>
                            <div class="partner-field-grid">
                                <div class="partner-field partner-field-full"> <label for="company_name">
                                        {{ __('partner.company_name') }} <span>*</span> </label> <input type="text"
                                        id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field partner-field-full"> <label for="company_address">
                                        {{ __('partner.company_address') }} <span>*</span> </label>
                                    <textarea id="company_address" name="company_address" rows="4" required>{{ old('company_address') }}</textarea> @error('company_address')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="website"> {{ __('partner.website') }} </label>
                                    <input type="url" id="website" name="website" value="{{ old('website') }}"
                                        placeholder="https://"> @error('website')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="business_license_number">
                                        {{ __('partner.business_license_number') }} </label> <input type="text"
                                        id="business_license_number" name="business_license_number"
                                        value="{{ old('business_license_number') }}"> @error('business_license_number')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div><div class="partner-form-step" data-step="3">
                            <div class="partner-form-heading"> <span>03</span>
                                <h3>{{ __('partner.step_contact_title') }}</h3>
                                <p>{{ __('partner.step_contact_description') }}</p>
                            </div>
                            <div class="partner-field-grid">
                                <div class="partner-field"> <label for="contact_name"> {{ __('partner.contact_name') }}
                                        <span>*</span> </label> <input type="text" id="contact_name"
                                        name="contact_name" value="{{ old('contact_name') }}" required>
                                    @error('contact_name')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="contact_email"> {{ __('partner.contact_email') }}
                                        <span>*</span> </label> <input type="email" id="contact_email"
                                        name="contact_email" value="{{ old('contact_email') }}" required>
                                    @error('contact_email')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="phone"> {{ __('partner.phone') }}
                                        <span>*</span> </label> <input type="tel" id="phone" name="phone"
                                        value="{{ old('phone') }}" required> @error('phone')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="position"> {{ __('partner.position') }} </label>
                                    <input type="text" id="position" name="position" value="{{ old('position') }}">
                                    @error('position')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="preferred_contact">
                                        {{ __('partner.preferred_contact') }} </label> <select id="preferred_contact"
                                        name="preferred_contact">
                                        <option value=""> {{ __('partner.select_preferred_contact') }} </option>
                                        <option value="email" @selected(old('preferred_contact') === 'email')>
                                            {{ __('partner.contact_email_option') }} </option>
                                        <option value="whatsapp" @selected(old('preferred_contact') === 'whatsapp')> WhatsApp </option>
                                        <option value="phone" @selected(old('preferred_contact') === 'phone')>
                                            {{ __('partner.contact_phone_option') }} </option>
                                    </select> @error('preferred_contact')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field"> <label for="main_market"> {{ __('partner.main_market') }}
                                    </label> <input type="text" id="main_market" name="main_market"
                                        value="{{ old('main_market') }}"> @error('main_market')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-field partner-field-full"> <label for="monthly_bali_clients">
                                        {{ __('partner.monthly_bali_clients') }} </label> <input type="number"
                                        id="monthly_bali_clients" name="monthly_bali_clients"
                                        value="{{ old('monthly_bali_clients') }}" min="0">
                                    @error('monthly_bali_clients')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="partner-field partner-services"> <label> {{ __('partner.interested_services') }}
                                </label>
                                <div class="partner-checkbox-grid">
                                    @foreach (['private_tours' => __('partner.service_private_tours'), 'transportation' => __('partner.service_transportation'), 'activities' => __('partner.service_activities'), 'accommodation' => __('partner.service_accommodation'), 'mice_events' => __('partner.service_mice_events'), 'weddings' => __('partner.service_weddings')] as $value => $label)
                                        <label class="partner-checkbox"> <input type="checkbox"
                                                name="interested_services[]" value="{{ $value }}"
                                                @checked(in_array($value, old('interested_services', []), true))> <span>{{ $label }}</span> </label>
                                    @endforeach
                                </div> @error('interested_services')
                                    <small class="partner-field-error">{{ $message }}</small>
                                    @enderror @error('interested_services.*')
                                    <small class="partner-field-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div><div class="partner-form-step" data-step="4">
                            <div class="partner-form-heading"> <span>04</span>
                                <h3>{{ __('partner.step_documents_title') }}</h3>
                                <p>{{ __('partner.step_documents_description') }}</p>
                            </div>
                            <div class="partner-document-notice"> {{ __('partner.document_security_notice') }} </div>
                            <div class="partner-document-grid">
                                <div class="partner-document-field"> <label for="business_license">
                                        {{ __('partner.business_license') }} <span>*</span> </label> <input type="file"
                                        id="business_license" name="business_license" accept=".pdf,.jpg,.jpeg,.png"
                                        required> <small> {{ __('partner.document_requirement_required') }} </small>
                                    @error('business_license')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-document-field"> <label for="company_letter">
                                        {{ __('partner.company_letter') }} <span>*</span> </label> <input type="file"
                                        id="company_letter" name="company_letter" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <small> {{ __('partner.document_requirement_required') }} </small>
                                    @error('company_letter')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-document-field"> <label for="tax_document">
                                        {{ __('partner.tax_document') }} </label> <input type="file" id="tax_document"
                                        name="tax_document" accept=".pdf,.jpg,.jpeg,.png"> <small>
                                        {{ __('partner.document_requirement_optional') }} </small> @error('tax_document')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="partner-document-field"> <label for="supporting_documents">
                                        {{ __('partner.supporting_documents') }} </label> <input type="file"
                                        id="supporting_documents" name="supporting_documents[]"
                                        accept=".pdf,.jpg,.jpeg,.png" multiple> <small>
                                        {{ __('partner.document_requirement_optional') }} </small>
                                    @error('supporting_documents')
                                        <small class="partner-field-error">{{ $message }}</small>
                                        @enderror @error('supporting_documents.*')
                                        <small class="partner-field-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <p class="partner-file-note"> {{ __('partner.document_file_note') }} </p>
                        </div><div class="partner-form-step" data-step="5">
                            <div class="partner-form-heading"> <span>05</span>
                                <h3>{{ __('partner.step_review_title') }}</h3>
                                <p>{{ __('partner.step_review_description') }}</p>
                            </div>
                            <div id="partnerReview" class="partner-review">
                                <div class="partner-review-section">
                                    <h4>{{ __('partner.review_company') }}</h4>
                                    <div class="partner-review-grid">
                                        <div> <span>{{ __('partner.company_name') }}</span> <strong
                                                data-review="company_name">—</strong> </div>
                                        <div> <span>{{ __('partner.company_type') }}</span> <strong
                                                data-review="company_type">—</strong> </div>
                                        <div> <span>{{ __('partner.country') }}</span> <strong
                                                data-review="country">—</strong> </div>
                                        <div> <span>{{ __('partner.website') }}</span> <strong
                                                data-review="website">—</strong> </div>
                                    </div>
                                </div>
                                <div class="partner-review-section">
                                    <h4>{{ __('partner.review_contact') }}</h4>
                                    <div class="partner-review-grid">
                                        <div> <span>{{ __('partner.contact_name') }}</span> <strong
                                                data-review="contact_name">—</strong> </div>
                                        <div> <span>{{ __('partner.contact_email') }}</span> <strong
                                                data-review="contact_email">—</strong> </div>
                                        <div> <span>{{ __('partner.phone') }}</span> <strong
                                                data-review="phone">—</strong> </div>
                                        <div> <span>{{ __('partner.position') }}</span> <strong
                                                data-review="position">—</strong> </div>
                                    </div>
                                </div>
                                <div class="partner-review-section">
                                    <h4>{{ __('partner.review_business') }}</h4>
                                    <div class="partner-review-grid">
                                        <div> <span>{{ __('partner.main_market') }}</span> <strong
                                                data-review="main_market">—</strong> </div>
                                        <div> <span>{{ __('partner.monthly_bali_clients') }}</span> <strong
                                                data-review="monthly_bali_clients">—</strong> </div>
                                    </div>
                                </div>
                                <div class="partner-review-section">
                                    <h4>{{ __('partner.review_documents') }}</h4>
                                    <ul id="partnerReviewDocuments"></ul>
                                </div>
                            </div>
                            <div class="partner-agreements"> <label class="partner-checkbox partner-checkbox-required">
                                    <input type="checkbox" name="agree_to_terms" value="1" required
                                        @checked(old('agree_to_terms'))> <span> {{ __('partner.agree_terms') }}
                                        <strong>*</strong> </span> </label> @error('agree_to_terms')
                                    <small class="partner-field-error">{{ $message }}</small>
                                @enderror <label class="partner-checkbox"> <input type="checkbox" name="agree_to_contact"
                                        value="1" @checked(old('agree_to_contact'))> <span>
                                        {{ __('partner.agree_contact') }} </span> </label> @error('agree_to_contact')
                                    <small class="partner-field-error">{{ $message }}</small>
                                @enderror
                            </div>
                        </div><div class="partner-form-navigation"> <button type="button"
                                class="ui-btn partner-btn-secondary" id="partnerBack" hidden> {{ __('partner.back') }}
                            </button> <button type="button" class="ui-btn partner-btn-primary" id="partnerNext">
                                {{ __('partner.continue') }} </button> <button type="submit"
                                class="ui-btn partner-btn-primary" id="partnerSubmit" hidden> <span
                                    class="partner-submit-text"> {{ __('partner.submit_application') }} </span> <span
                                    class="partner-submit-loading" hidden> {{ __('partner.submitting') }} </span>
                            </button> </div>
                    </form>
            </div>
        </section>
    </div> @endsection @push('styles')
    <style>
        .partner-page {
            --partner-blue: #0f5fa8;
            --partner-teal: #0d7b8f;
            --partner-green: #21a179;
        }

        .partner-hero {
            padding: 90px 0 70px;
            background: linear-gradient(135deg, var(--partner-blue), var(--partner-teal));
            color: #fff;
        }

        .partner-hero-content {
            max-width: 850px;
        }

        .partner-eyebrow {
            display: inline-block;
            margin-bottom: 15px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .partner-hero h1 {
            margin: 0 0 20px;
            font-size: clamp(36px, 5vw, 60px);
            line-height: 1.1;
        }

        .partner-hero p {
            max-width: 760px;
            margin: 0;
            font-size: 18px;
            line-height: 1.7;
        }

        .partner-benefits,
        .partner-application {
            padding: 70px 0;
        }

        .partner-section-heading {
            max-width: 800px;
            margin-bottom: 40px;
        }

        .partner-section-heading h2 {
            margin-bottom: 12px;
        }

        .partner-benefits-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .partner-benefit {
            padding: 0;
        }

        .partner-benefit h3 {
            margin-bottom: 10px;
        }

        .partner-benefit p {
            margin: 0;
            line-height: 1.7;
        }

        .partner-application {
            background: #f7f9fb;
        }

        .partner-alert {
            padding: 18px 20px;
            margin-bottom: 30px;
        }

        .partner-alert-error {
            background: #fff1f1;
            border-left: 4px solid #c62828;
        }

        .partner-alert ul {
            margin: 10px 0 0;
        }

        .partner-steps {
            display: flex;
            gap: 10px;
            margin-bottom: 40px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        .partner-step {
            flex: 1;
            min-width: 130px;
            border: 0;
            background: transparent;
            padding: 10px;
            text-align: left;
            cursor: pointer;
        }

        .partner-step span {
            display: inline-flex;
            width: 34px;
            height: 34px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #dfe7ed;
            margin-right: 8px;
        }

        .partner-step.active span,
        .partner-step.completed span {
            background: var(--partner-blue);
            color: #fff;
        }

        .partner-form-step {
            display: none;
        }

        .partner-form-step.active {
            display: block;
        }

        .partner-form-heading {
            margin-bottom: 35px;
        }

        .partner-form-heading>span {
            color: var(--partner-blue);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .1em;
        }

        .partner-form-heading h3 {
            margin: 8px 0;
        }

        .partner-form-heading p {
            margin: 0;
            max-width: 700px;
        }

        .partner-field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .partner-field-full {
            grid-column: 1 / -1;
        }

        .partner-field label,
        .partner-document-field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .partner-field label span,
        .partner-document-field label span {
            color: #c62828;
        }

        .partner-field input,
        .partner-field select,
        .partner-field textarea,
        .partner-document-field input {
            width: 100%;
            box-sizing: border-box;
        }

        .partner-field textarea {
            resize: vertical;
        }

        .partner-field-error {
            display: block;
            margin-top: 7px;
            color: #c62828;
        }

        .partner-services {
            margin-top: 30px;
        }

        .partner-checkbox-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 12px;
        }

        .partner-checkbox {
            display: flex !important;
            gap: 10px;
            align-items: flex-start;
            cursor: pointer;
        }

        .partner-checkbox input {
            width: auto !important;
            margin-top: 4px;
        }

        .partner-document-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .partner-document-field {
            padding: 24px;
            border: 1px solid #dfe7ed;
        }

        .partner-document-field small {
            display: block;
            margin-top: 8px;
        }

        .partner-document-notice {
            margin-bottom: 30px;
            padding: 18px;
            background: #eef7f7;
            border-left: 4px solid var(--partner-teal);
        }

        .partner-file-note {
            margin-top: 20px;
            font-size: 14px;
        }

        .partner-review {
            border-top: 1px solid #dfe7ed;
        }

        .partner-review-section {
            padding: 25px 0;
            border-bottom: 1px solid #dfe7ed;
        }

        .partner-review-section h4 {
            margin-bottom: 20px;
        }

        .partner-review-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .partner-review-grid span {
            display: block;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .partner-review-grid strong {
            display: block;
        }

        .partner-review-section ul {
            margin: 0;
            padding-left: 20px;
        }

        .partner-agreements {
            margin-top: 30px;
        }

        .partner-agreements .partner-checkbox {
            margin-bottom: 15px;
        }

        .partner-form-navigation {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 45px;
        }

        .partner-submit-loading {
            opacity: .75;
        }

        @media (max-width: 900px) {
            .partner-benefits-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .partner-checkbox-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .partner-hero {
                padding: 60px 0 50px;
            }

            .partner-benefits,
            .partner-application {
                padding: 50px 0;
            }

            .partner-field-grid,
            .partner-document-grid,
            .partner-review-grid,
            .partner-checkbox-grid,
            .partner-benefits-grid {
                grid-template-columns: 1fr;
            }

            .partner-field-full {
                grid-column: auto;
            }

            .partner-form-navigation {
                flex-direction: column-reverse;
            }

            .partner-form-navigation .ui-btn {
                width: 100%;
            }
        }
    </style>
    @endpush @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('partnerApplicationForm');
            if (!form) {
                return;
            }
            const steps = Array.from(form.querySelectorAll('.partner-form-step'));
            const indicators = Array.from(form.querySelectorAll('.partner-step'));
            const nextButton = document.getElementById('partnerNext');
            const backButton = document.getElementById('partnerBack');
            const submitButton = document.getElementById('partnerSubmit');
            let currentStep = 1;

            function showStep(step) {
                currentStep = step;
                steps.forEach(function(element) {
                    element.classList.toggle('active', Number(element.dataset.step) === step);
                });
                indicators.forEach(function(element) {
                    const target = Number(element.dataset.stepTarget);
                    element.classList.toggle('active', target === step);
                    element.classList.toggle('completed', target < step);
                });
                backButton.hidden = step === 1;
                nextButton.hidden = step === steps.length;
                submitButton.hidden = step !== steps.length;
                if (step === steps.length) {
                    updateReview();
                }
                const application = document.getElementById('partner-application');
                if (application) {
                    application.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }

            function validateCurrentStep() {
                const current = steps.find(function(element) {
                    return Number(element.dataset.step) === currentStep;
                });
                if (!current) {
                    return true;
                }
                const fields = current.querySelectorAll('input, select, textarea');
                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        return false;
                    }
                }
                return true;
            }

            function getValue(name) {
                const field = form.querySelector('[name="' + name + '"]');
                if (!field) {
                    return '';
                }
                return field.value || '';
            }

            function updateReview() {
                const values = ['company_name', 'company_type', 'country', 'website', 'contact_name',
                    'contact_email', 'phone', 'position', 'main_market', 'monthly_bali_clients'
                ];
                values.forEach(function(name) {
                    const target = form.querySelector('[data-review="' + name + '"]');
                    if (target) {
                        target.textContent = getValue(name) || '—';
                    }
                });
                const documents = document.getElementById('partnerReviewDocuments');
                if (!documents) {
                    return;
                }
                documents.innerHTML = '';
                ['business_license', 'company_letter', 'tax_document', 'supporting_documents'].forEach(function(
                    name) {
                    const input = document.getElementById(name);
                    if (!input || !input.files.length) {
                        return;
                    }
                    Array.from(input.files).forEach(function(file) {
                        const item = document.createElement('li');
                        item.textContent = input.files.length > 1 ? file.name : file.name;
                        documents.appendChild(item);
                    });
                });
                if (!documents.children.length) {
                    const item = document.createElement('li');
                    item.textContent = '—';
                    documents.appendChild(item);
                }
            }
            nextButton.addEventListener('click', function() {
                if (!validateCurrentStep()) {
                    return;
                }
                if (currentStep < steps.length) {
                    showStep(currentStep + 1);
                }
            });
            backButton.addEventListener('click', function() {
                if (currentStep > 1) {
                    showStep(currentStep - 1);
                }
            });
            indicators.forEach(function(indicator) {
                indicator.addEventListener('click', function() {
                    const target = Number(indicator.dataset.stepTarget);
                    if (target < currentStep) {
                        showStep(target);
                    }
                });
            });
            form.addEventListener('submit', function(event) {
                if (!validateCurrentStep()) {
                    event.preventDefault();
                    return;
                }
                submitButton.disabled = true;
                nextButton.disabled = true;
                backButton.disabled = true;
                const submitText = submitButton.querySelector('.partner-submit-text');
                const loadingText = submitButton.querySelector('.partner-submit-loading');
                if (submitText) {
                    submitText.hidden = true;
                }
                if (loadingText) {
                    loadingText.hidden = false;
                }
            });
            showStep(1);
        });
    </script>
@endpush --}}
