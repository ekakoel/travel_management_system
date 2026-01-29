@extends('layouts.head')
@section('title', 'Detail Itineraries')
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-map-6" aria-hidden="true"></i> Itineraries
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">Data</li>
                                        <li class="breadcrumb-item"><a href="{{ route('itineraries.index') }}">Itineraries</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Detail Itinerary</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    @include('partials.alerts')
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Detail Itinerary</div>
                                </div>
                                <div class="mb-3">
                                    <ul class="nav nav-tabs" id="itineraryTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="english-tab" data-toggle="tab" href="#english" role="tab" aria-controls="english" aria-selected="true">English</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="traditional-tab" data-toggle="tab" href="#traditional" role="tab" aria-controls="traditional" aria-selected="false">Chinese (Traditional)</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="simplified-tab" data-toggle="tab" href="#simplified" role="tab" aria-controls="simplified" aria-selected="false">Chinese (Simplified)</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content" id="itineraryTabContent">
                                    <div class="tab-pane fade show active" id="english" role="tabpanel" aria-labelledby="english-tab">
                                        <div class="copy-icon">
                                            <i class="dw dw-copy" onclick="copyDivContent('english')" title="Copy Itinerary"></i>
                                        </div>
                                        <h4>{{ $itinerary->title }}</h4>
                                        <p>{{ $itinerary->code }}</p>
                                        <div class="m-b-18">
                                            {!! $itinerary->itinerary !!}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="traditional" role="tabpanel" aria-labelledby="traditional-tab">
                                        <div class="copy-icon">
                                            <i class="dw dw-copy" onclick="copyDivContent('traditional')" title="Copy Itinerary"></i>
                                        </div>
                                        <h4>
                                            {{ $itinerary->title_traditional ?? '-' }}
                                        </h4>
                                        <p>{{ $itinerary->code }}</p>
                                        <div class="m-b-18">
                                            {!! $itinerary->itinerary_traditional ? $itinerary->itinerary_traditional : '-' !!}
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="simplified" role="tabpanel" aria-labelledby="simplified-tab">
                                        <div class="copy-icon">
                                            <i class="dw dw-copy" onclick="copyDivContent('simplified')" title="Copy Itinerary"></i>
                                        </div>
                                        <h4>
                                            {{ $itinerary->title_simplified ?? '-' }}
                                        </h4>
                                        <p>{{ $itinerary->code }}</p>
                                        <div class="m-b-18">
                                            {!! $itinerary->itinerary_simplified ? $itinerary->itinerary_simplified : '-' !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    <a href="{{ route('itineraries.index') }}">
                                        <button class="btn btn-secondary" data-toggle="tooltip" data-placement="top"
                                            title="Back">Back</button>
                                    </a>
                                    <a href="{{ route('itineraries.edit', $itinerary->id) }}">
                                        <button class="btn btn-primary" data-toggle="tooltip" data-placement="top"
                                            title="Edit">Edit</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
    <script>
        function copyDivContent(divId) {
            const div = document.getElementById(divId);
            if (!div) return;

            const textarea = document.createElement('textarea');
            textarea.style.position = 'absolute';
            textarea.style.left = '-9999px';
            textarea.value = div.innerText.trim();
            document.body.appendChild(textarea);
            textarea.select();

            try {
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Itinerary copied to clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            } catch (err) {
                console.error('Failed to copy text: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to copy itinerary.',
                });
            }

            document.body.removeChild(textarea);
        }
    </script>
@endsection