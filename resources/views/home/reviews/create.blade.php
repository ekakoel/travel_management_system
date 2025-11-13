@section('title',__('Create Review'))
@extends('home.reviews.layouts.app')
@section('meta')
    <meta property="og:title" content="Customer Reviews - Bali Kami Tour" />
    <meta property="og:description" content="See what our customers say about their travel experience with Bali Kami Tour." />
    <meta property="og:image" content="{{ asset('images/property/reviews.webp') }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Bali Kami Tour" />
@endsection
@section('content')
    <div class="form-container">
        <div class="container py-4">
            
            
            <div class="card shadow-sm rounded-4">
                <div class="card-header">
                    <h2 class="my-3 text-center text-primary">📝 @lang('messages.Customer Review Form')</h2>
                </div>
                <div class="card-body">
                    <div class="btn-container">
                        <div class="nav-item dropdown text-center">
                            <b>@lang('messages.Select Language')</b>
                            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-language" aria-hidden="true"></i>
                                @if (app()->getLocale() == 'en')
                                    English
                                @elseif (app()->getLocale() == 'zh')
                                    繁體中文
                                @else
                                    简体中文
                                @endif
                            </a>
                            <ul class="dropdown-menu shadow-sm m-0" aria-labelledby="langDropdown">
                                <li><a class="dropdown-item" href="{{ url('lang/en') }}"><i class="fa fa-language"></i> English</a></li>
                                <li><a class="dropdown-item" href="{{ url('lang/zh') }}"><i class="fa fa-language"></i> 繁體中文 (Chinese Traditional)</a></li>
                                <li><a class="dropdown-item" href="{{ url('lang/zh-CN') }}"><i class="fa fa-language"></i> 简体中文 (Chinese Simplified)</a></li>
                            </ul>
                        </div>
                    </div>
                    <p class="mb-3">
                        <i style="font-size: 14px; color: #555;">
                            <ul>
                                <li>@lang("messages.Thank you for travelling with us in Bali, in order to 'BE BETTER', We must also continuously improve the service quality, please sincerely need your kind advise on the following questionnaire.")</li>
                                <li>@lang('messages.Please fill out this review form to help us evaluate and improve our services. Your feedback on our guide, driver, and facilities is essential to ensure the best experience for all future guests.')</li>
                                <li>@lang('messages.All required fields are marked with a *. Thank you for your time and support!')</li>
                            </ul>
                            
                        </i>
                    </p>
                    <hr class="my-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>@lang('messages.Error'):</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">
                            @foreach((array) session('success') as $msg)
                                <p class="mb-1">{{ $msg }}</p>
                            @endforeach
                        </div>
                    @endif
    
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
    
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required-label">@lang('messages.Travel Agent')</label>
                                <input type="text" name="travel_agent" class="form-control" required>
                            </div>
    
                            <div class="col-md-3">
                                <label class="form-label required-label">@lang('messages.Arrival Date')</label>
                                <input type="date" name="arrival_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label required-label">@lang('messages.Departure Date')</label>
                                <input type="date" name="departure_date" class="form-control" required>
                            </div>
    
                            <div class="col-md-6">
                                <hr class="my-4">
                                <legend>@lang('messages.Tour Guide')</legend>
                                <label class="form-label">@lang('messages.Select Tour Guide')</label>
                                <select name="guide_id" class="form-select">
                                    <option value="">-- @lang('messages.Tour Guide') --</option>
                                    @foreach($guides as $guide)
                                        <option value="{{ $guide->id }}">{{ $guide->name }}</option>
                                    @endforeach
                                </select>
                                <div style="height: 18px !important;"></div>
                                <fieldset>
                                    @foreach($guide_questions as $field => $label)
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold d-block required-label">{{ __('messages.'.$label) }}</label>
                                            <div class="star-rating">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="{{ $field }}_{{ $i }}" name="{{ $field }}" value="{{ $i }}">
                                                    <label for="{{ $field }}_{{ $i }}" title="{{ $options[$i] }}">
                                                        ★
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                    @endforeach
                                </fieldset>
                            </div>                        
                            <div class="col-md-6">
                                <hr class="my-4">
                                <legend>@lang('messages.Transport')</legend>
                                <label class="form-label">@lang('messages.Select Driver')</label>
                                <select name="driver_id" class="form-select">
                                    <option value="">-- @lang('messages.Driver') --</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <div style="height: 18px !important;"></div>
                                <fieldset>
                                    @foreach($driver_questions as $field => $label)
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold d-block">{{ __('messages.'.$label) }}</label>
                                            <div class="star-rating">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="{{ $field }}_{{ $i }}" name="{{ $field }}" value="{{ $i }}">
                                                    <label for="{{ $field }}_{{ $i }}" title="{{ $options[$i] }}">
                                                        ★
                                                    </label>
                                                @endfor
                                            </div>
                                        </div>
                                    @endforeach
                                </fieldset>
                            </div>                        
                        </div>
    
                        <hr class="my-4">
    
                        
                        
                        <fieldset>
                            <legend>@lang('messages.Services Evaluation')</legend>
                            @foreach($service_questions as $field => $label)
                                <div class="mb-4">
                                    <label class="form-label fw-semibold d-block required-label">{{ __('messages.'.$label) }}</label>
                                    <div class="star-rating">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="{{ $field }}_{{ $i }}" name="{{ $field }}" value="{{ $i }}" required>
                                            <label for="{{ $field }}_{{ $i }}" title="{{ $options[$i] }}">
                                                ★
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </fieldset>
                        <div class="mb-4 mt-4">
                            <label class="form-label d-block fw-semibold required-label">@lang('messages.Your Traveling Mood')</label>
                            <div class="mood-options d-flex gap-3 flex-wrap">
                                @foreach($moods as $value => $label)
                                    <input type="radio" class="btn-check" name="travel_mood" id="mood_{{ $value }}" value="{{ $label }}" required>
                                    <label class="btn btn-outline-secondary px-4 py-2 rounded-pill" for="mood_{{ $value }}">
                                        {{ __('messages.'.$label) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">@lang('messages.Your Full Review')</label>
                            <textarea name="customer_review" rows="5" class="form-control"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label required-label">@lang('messages.Your Name') (@lang('messages.Signature'))</label>
                            <input type="text" name="signature" class="form-control">
                        </div>
                        <div class="form-check my-3">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label" for="agree">
                                @lang('messages.I agree to the')
                                <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">@lang('messages.Terms and Conditions')</a> @lang('messages.and') 
                                <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">@lang('messages.Privacy Policy')</a>
                                <span class="text-danger">*</span>
                            </label>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-lg btn-success px-5">
                                <i class="bi bi-send-fill"></i> @lang('messages.Submit Review')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @php
    use Carbon\Carbon;
        $years = Carbon::now();
    @endphp
    <div class="footer text-center d-print-none">
        <p>©@lang('messages.Copyright'), {{ date('Y',strtotime($years))." ".config('app.business') }}</p>
    </div>
    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="termsModalLabel">@lang('messages.Terms and Conditions')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p>@lang('messages.By submitting this review, you agree to the following terms and conditions'):</p>
            <ol>
                <li>@lang('messages.Your feedback is based on a real experience.')</li>
                <li>@lang('messages.We may use your input for service improvements.')</li>
                <li>@lang('messages.Personal data is handled confidentially.')</li>
                <li>@lang('messages.You agree that no compensation was promised for your review.')</li>
                <li>@lang('messages.We may edit or reject inappropriate content.')</li>
            </ol>
            <p>@lang('messages.By continuing, you accept these terms.')</p>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="privacyModalLabel">@lang('messages.Privacy Policy')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>@lang('messages.We collect your review information to improve our services. Data collected includes'):</p>
                    <ul>
                        <li>@lang('messages.Your name, travel details, and feedback')</li>
                        <li>@lang('messages.Your selected guide and driver')</li>
                    </ul>
                    <p>@lang('messages.All data is confidential, not shared with third parties, and may be retained unless deletion is requested.')</p>
                    <p>@lang('messages.By submitting, you agree to this policy.')</p>
                </div>
            </div>
        </div>
    </div>
@endsection
