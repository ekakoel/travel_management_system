@extends('layouts.head')
@section('title', __('messages.Transportation Reservation'))
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
                                    <i class="icon-copy dw dw-bus"></i> Transport Management
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Surat Perintah Kerja (SPK)</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row mb-4">
                        <div class="col-md-8 mb-4">
                            <div class="card m-b-18 h-100">
                                <div class="card-header">SPK List</div>
                                <div class="card-body">
                                    <table class="data-table table stripe nowrap dataTable no-footer dtr-inline">
                                        <thead>
                                            <tr>
                                                <th data-priority="1">#</th>
                                                <th data-priority="2">Date</th>
                                                <th data-priority="1" class="datatable-nosort">Order Number</th>
                                                <th data-priority="2">Vehicles - Driver</th>
                                                <th data-priority="2">Status</th>
                                                <th data-priority="2" class="text-right noshort">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($spks as $spk)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ date("m/d/y",strtotime($spk->spk_date)) }}
                                                    </td>
                                                    <td>
                                                        <b>{{ $spk->order_number??"-" }}</b>
                                                        <p><i>{{ $spk->spk_number }}</i></p>
                                                    </td>
                                                    <td>
                                                        {{ $spk->transport ? $spk->transport->brand." ".$spk->transport->name : 'N/A' }} - 
                                                        {{ $spk->driver ? $spk->driver->name : 'N/A' }}<br>
                                                        {{ $spk->number_of_guests }} Guests
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $statusColors[$spk->status] ?? 'bg-secondary' }}">{{ $spk->status }}</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="{{ route('view.detail-spk',$spk->id) }}">
                                                            <button class="btn btn-sm btn-light"><i class="icon-copy dw dw-eye"></i> Detail</button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No SPK Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header">Create New SPK</div>
                                <div class="card-body">
                                    <form id="createReservation" action="{{ route('spks.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <div class="alert alert-info" role="alert">
                                                    Gunakan form ini untuk membuat Surat Perintah Kerja (SPK) baru. 
                                                    Pastikan Order Number, dan Tanggal terisi dengan benar.
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="orderNumber">Order Number <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></span>
                                                        <input type="text" name="order_number" class="form-control input-icon @error('order_number') is-invalid @enderror" placeholder="Insert order number" value="{{ old('order_number') }}" required>
                                                    </div>
                                                    @error('order_number')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Transport Service <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-server" aria-hidden="true"></i></span>
                                                        <select name="type" class="custom-select input-icon form-select" required>
                                                            <option disabled selected value="">Select Service</option>
                                                            <option value="Airport Shuttle">Airport Shuttle</option>
                                                            <option value="Hotel Transfer">Hotel Transfer</option>
                                                            <option value="Tour">Tour</option>
                                                            <option value="Daily Rent">Daily Rent</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>SPK Date <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i></span>
                                                        <input readonly
                                                            class="form-control date-picker checkin input-icon @error('spk_date') is-invalid @enderror"
                                                            name="spk_date"
                                                            type="text"
                                                            value="{{ old('spk_date') }}"
                                                            placeholder="@lang('messages.Select date')" 
                                                            required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="number_of_guests">Number of Guests <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                        <input  name="number_of_guests" min="1" class="form-control input-icon @error('number_of_guests') is-invalid @enderror" type="number" value="{{ old('number_of_guest') }}" placeholder="@lang('messages.Number of guests')" required>
                                                    </div>
                                                    @error('number_of_guests')
                                                        <span class="invalid-feedback">
                                                            {{ $message }}
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Vehicle <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-car" aria-hidden="true"></i></span>
                                                        <select name="transport_id" class="custom-select form-select" required>
                                                            <option disabled selected value="">Select Vehicle</option>
                                                            @foreach ($vehicles as $vehicle)
                                                                <option value="{{ $vehicle->id }}">{{ $vehicle->brand." ".$vehicle->name }} {{ $vehicle->number_plate?" (".$vehicle->number_plate.")":"" }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Driver <span>*</span></label>
                                                    <div class="btn-icon">
                                                        <span><i class="icon-copy fa fa-user-circle-o" aria-hidden="true"></i></span>
                                                        <select name="driver_id" class="custom-select form-select" required>
                                                            <option disabled selected value="">Select Driver</option>
                                                            @foreach ($drivers as $driver)
                                                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="submit" form="createReservation" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Create SPK</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 md-4">
                            <div class="card m-b-18">
                                <div class="card-header">SPK Archived</div>
                                <div class="card-body">
                                    <!-- Filter -->
                                    <div class="row m-b-8">
                                        <div class="col-md-6">
                                            <input type="text" id="filter_order_no" class="form-control" placeholder="Search by Order No">
                                        </div>
                                    </div>
                                    <!-- Table Result -->
                                    <div id="spkArchiveResults">
                                        @include('admin.transportmanagement.partials.spk-archive',['spk_archives' => $spk_archives])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Add Reservation --}}
                

                {{-- Reservations List --}}
            </div>
        </div>
        <script>
document.addEventListener('DOMContentLoaded', function () {
    // Jika ingin menyembunyikan warning di console, bisa set ke 'none'
    // $.fn.dataTable.ext.errMode = 'none';

    let table = null;

    // Debounce helper
    function debounce(fn, delay) {
        let t;
        return function () {
            const ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(ctx, args), delay);
        };
    }

    function initSpkArchivedTable() {
        // Jika sudah ada DataTable, destroy dulu (aman)
        if ($.fn.dataTable.isDataTable('#spkArchived')) {
            try {
                $('#spkArchived').DataTable().clear().destroy();
            } catch (err) {
                console.warn('Error saat destroy DataTable (ignored):', err);
            }
        }

        // Inisialisasi ulang DataTable
        table = $('#spkArchived').DataTable({
            responsive: true,
            order: [[1, 'asc']],
            pageLength: 10,

        });

        // Hapus binding lama dan pasang binding baru (menggunakan namespace .spkFilter)
        $('#filter_order_no').off('.spkFilter').on('keyup.spkFilter', debounce(function () {
            // kolom index 2 = Order Number (sesuaikan bila kolom berbeda)
            table.column(2).search(this.value).draw();
        }, 300));

       

        // Jika ingin re-init dari luar (mis. setelah AJAX partial replace), expose function
        window.spksTable = table;
    }

    // Inisialisasi pertama
    initSpkArchivedTable();

    // Jika kamu melakukan partial reload (AJAX / Livewire), panggil:
    // window.initSpkArchivedTable();  <- kapanpun setelah tabel DOM diupdate

    // Expose fungsi agar mudah dipanggil setelah update DOM dari AJAX/Livewire
    window.initSpkArchivedTable = initSpkArchivedTable;
});
</script>

    @endcan
@endsection
