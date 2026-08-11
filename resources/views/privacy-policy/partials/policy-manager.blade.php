@extends('layouts.head')
@section('title', __('messages.Term and Condition'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="info-action">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="card-box mb-30">
                    <div class="card-box-title flex-between">
                        <div>
                            <div class="title">@lang('messages.Term and Condition')</div>
                            <p class="text-muted m-b-0">Manage public Terms, Privacy Policy, and FAQ content from one place.</p>
                        </div>
                        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#add-new-policy">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add New Policy
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($policySections as $section)
                                @php
                                    $activeCount = $section['items']->where('status', 'Active')->count();
                                    $draftCount = $section['items']->where('status', 'Draft')->count();
                                @endphp
                                <div class="col-md-12 mb-4">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3">
                                        <div>
                                            <h4 class="mb-1">{{ $section['title'] }}</h4>
                                            <span class="badge badge-success">{{ $activeCount }} Active</span>
                                            <span class="badge badge-secondary">{{ $draftCount }} Draft</span>
                                        </div>
                                        <small class="text-muted">Type: {{ $section['type'] }}</small>
                                    </div>

                                    @forelse ($section['items'] as $policy)
                                        <div class="border rounded p-3 mb-3 {{ $policy->status === 'Draft' ? 'bg-light' : '' }}">
                                            <div class="d-flex align-items-start justify-content-between flex-wrap">
                                                <div>
                                                    <h5 class="mb-1">{{ $policy->name_en }}</h5>
                                                    <span class="badge {{ $policy->status === 'Active' ? 'badge-success' : 'badge-secondary' }}">{{ $policy->status }}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <a href="#" class="btn btn-sm btn-outline-primary mr-2" data-toggle="modal" data-target="#edit-policy-{{ $policy->id }}">
                                                        <i class="fa fa-pencil-alt" aria-hidden="true"></i> Edit
                                                    </a>
                                                    <form action="{{ url('fdestroy-policy/'.$policy->id) }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" class="btn btn-sm btn-outline-danger">
                                                            <i class="fa fa-trash-alt" aria-hidden="true"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-muted">
                                                {!! \Illuminate\Support\Str::limit(strip_tags($policy->policy_en), 240) !!}
                                            </div>
                                        </div>

                                        @include('privacy-policy.partials.policy-modal', [
                                            'modalId' => 'edit-policy-'.$policy->id,
                                            'formId' => 'edit-policy-form-'.$policy->id,
                                            'action' => url('fupdate-policy/'.$policy->id),
                                            'method' => 'put',
                                            'title' => 'Update '.$section['title'],
                                            'policy' => $policy,
                                            'policyTypes' => $policyTypes,
                                        ])
                                    @empty
                                        <div class="alert alert-light border">
                                            No {{ $section['title'] }} content yet. Use Add New Policy and select type {{ $section['type'] }}.
                                        </div>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @include('privacy-policy.partials.policy-modal', [
                    'modalId' => 'add-new-policy',
                    'formId' => 'add-term-and-condition',
                    'action' => url('fadd-policy'),
                    'method' => 'put',
                    'title' => 'Add New Policy',
                    'policy' => null,
                    'policyTypes' => $policyTypes,
                ])

                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
