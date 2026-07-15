@extends('frontend.layouts.app')

@section('title', __('messages.Manual Book'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/manual-book-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/manual-book.js') }}" defer></script>
@endpush

@section('content')
    <main class="frontend-page-shell manual-book-page" data-manual-book-page>
        <section class="container-fluid frontend-page-topband manual-book-hero py-5">
            <div class="container">
                @include('partials.breadcrumbs', [
                    'breadcrumbs' => [
                        ['url' => route('home'), 'label' => __('messages.Home')],
                        ['label' => __('messages.Manual Book')],
                    ],
                    'variant' => 'dark',
                ])

                <div class="frontend-page-intro manual-book-hero__grid">
                    <div class="frontend-page-intro__copy">
                        <span class="manual-book-eyebrow">@lang('messages.Partner Support Center')</span>
                        <h1 class="frontend-page-intro__title">@lang('messages.Manual Book')</h1>
                        <p class="frontend-page-intro__text">
                            @lang('messages.Find the latest platform guides, workflow references, and user manuals prepared for Bali Kami Tour partners.')
                        </p>
                    </div>

                    <div class="frontend-page-summary manual-book-summary">
                        @foreach ($summary as $item)
                            <div class="frontend-page-summary__item">
                                <span>{{ $item['label'] }}</span>
                                <strong>{{ $item['value'] }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="manual-book-section">
            <div class="container">
                <div class="manual-book-workspace">
                    <aside class="manual-book-filter" aria-label="@lang('messages.Manual filters')">
                        <div class="manual-book-filter__header">
                            <span>@lang('messages.Document Finder')</span>
                            <strong>@lang('messages.Filter manuals')</strong>
                        </div>

                        <div class="manual-book-field">
                            <label for="manualBookSearch">@lang('messages.Search manual')</label>
                            <div class="manual-book-search">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <input
                                    id="manualBookSearch"
                                    type="search"
                                    data-manual-search
                                    placeholder="@lang('messages.Search by manual name or language')"
                                    autocomplete="off"
                                >
                            </div>
                        </div>

                        <div class="manual-book-field">
                            <label for="manualBookLanguage">@lang('messages.Language')</label>
                            <select id="manualBookLanguage" data-manual-language>
                                <option value="all">@lang('messages.All Languages')</option>
                                @foreach ($languageOptions as $language => $label)
                                    <option value="{{ $language }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p class="manual-book-filter__note">
                            @lang('messages.Use preview for quick reading or open the PDF in a new tab for a full screen view.')
                        </p>
                    </aside>

                    <div class="manual-book-content">
                        <div class="manual-book-content__header">
                            <div>
                                <span>@lang('messages.Available Documents')</span>
                                <h2>@lang('messages.Platform guides and manuals')</h2>
                            </div>
                            <p>
                                @lang('messages.Choose the guide that matches your preferred language and continue your booking workflow with clearer instructions.')
                            </p>
                        </div>

                        @if ($manualBooks->isNotEmpty())
                            <div class="manual-book-grid" data-manual-list>
                                @foreach ($manualBooks as $manualBook)
                                    <article
                                        class="manual-book-card"
                                        data-manual-item
                                        data-manual-language-value="{{ $manualBook['language'] }}"
                                        data-manual-search-value="{{ $manualBook['search_text'] }}"
                                    >
                                        <div class="manual-book-card__visual">
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>{{ $manualBook['extension'] }}</span>
                                        </div>

                                        <div class="manual-book-card__body">
                                            <div class="manual-book-card__meta">
                                                <span class="manual-book-language manual-book-language--{{ $manualBook['language_tone'] }}">
                                                    {{ $manualBook['language_label'] }}
                                                </span>
                                                <span>{{ $manualBook['created_label'] }}</span>
                                            </div>

                                            <h3>{{ $manualBook['name'] }}</h3>
                                            <p>@lang('messages.Reference document for partner platform usage and operational booking guidance.')</p>
                                        </div>

                                        <div class="manual-book-card__actions">
                                            <button
                                                type="button"
                                                class="manual-book-action manual-book-action--primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#manualBookPreview{{ $manualBook['id'] }}"
                                            >
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                @lang('messages.Preview')
                                            </button>
                                            <a class="manual-book-action" href="{{ $manualBook['document_url'] }}" target="_blank" rel="noopener noreferrer">
                                                <i class="fa fa-external-link" aria-hidden="true"></i>
                                                @lang('messages.Open PDF')
                                            </a>
                                            <a class="manual-book-action" href="{{ $manualBook['document_url'] }}" download>
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                                @lang('messages.Download PDF')
                                            </a>
                                        </div>
                                    </article>

                                    <div class="modal fade manual-book-modal" id="manualBookPreview{{ $manualBook['id'] }}" tabindex="-1" aria-labelledby="manualBookPreview{{ $manualBook['id'] }}Label" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div>
                                                        <span>{{ $manualBook['language_label'] }}</span>
                                                        <h5 class="modal-title" id="manualBookPreview{{ $manualBook['id'] }}Label">{{ $manualBook['name'] }}</h5>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('messages.Close')"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe src="{{ $manualBook['document_url'] }}" title="{{ $manualBook['name'] }}" loading="lazy"></iframe>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="manual-book-action manual-book-action--primary" href="{{ $manualBook['document_url'] }}" target="_blank" rel="noopener noreferrer">
                                                        <i class="fa fa-external-link" aria-hidden="true"></i>
                                                        @lang('messages.Open PDF')
                                                    </a>
                                                    <button type="button" class="manual-book-action" data-bs-dismiss="modal">@lang('messages.Close')</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="manual-book-empty d-none" data-manual-empty>
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <h3>@lang('messages.No manuals found')</h3>
                                <p>@lang('messages.Try another keyword or select all languages to see every available guide.')</p>
                            </div>
                        @else
                            <div class="manual-book-empty">
                                <i class="fa fa-book" aria-hidden="true"></i>
                                <h3>@lang('messages.No manuals available')</h3>
                                <p>@lang('messages.Our team has not published manual documents yet. Please check again later.')</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
