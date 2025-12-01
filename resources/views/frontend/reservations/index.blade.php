@extends('frontend.layouts.header')
@section('title', __('messages.Hotels'))
@section('content')
    <div class="body-container">
        <section id="reservations">

            <h2 class="mb-4">My Reservations</h2>

            <!-- Stats Section (Optional) -->
            <div class="d-flex mb-3 gap-3">
                <div class="badge bg-success p-2">Active: {{ $activeReservations->count() }}</div>
                <div class="badge bg-secondary p-2">Past: {{ $pastReservations->count() }}</div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="reservationTabs">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#active">Active</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#past">Past / Expired</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3">

                <!-- Active Reservations -->
                <div class="tab-pane fade show active" id="active">
                    @forelse ($activeReservations as $res)
                        @include('frontend.reservations.partials.card', ['res' => $res])
                    @empty
                        <p class="text-muted">No active reservations.</p>
                    @endforelse

                    {{ $activeReservations->links() }}
                </div>

                <!-- Past Reservations -->
                <div class="tab-pane fade" id="past">
                    @forelse ($pastReservations as $res)
                        @include('frontend.reservations.partials.card', ['res' => $res])
                    @empty
                        <p class="text-muted">No past reservations.</p>
                    @endforelse

                    {{ $pastReservations->links() }}
                </div>

            </div>
        </section>
    <div>
@endsection
