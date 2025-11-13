{{-- @section('title', __('messages.Transport Detail'))
@section('content')
    @extends('layouts.head')
    <div class="container">
        <h2 class="mb-4">UI Configuration Panel</h2>
    
        <!-- Form untuk menambahkan konfigurasi baru -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Tambah Konfigurasi Baru</h5>
                <form id="add-config-form">
                    <div class="mb-3">
                        <label for="config-name" class="form-label">Nama Konfigurasi</label>
                        <input type="text" class="form-control" id="config-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="config-status" class="form-label">Status</label>
                        <select class="custom-select" id="config-status" name="status" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Tambah</button>
                </form>
            </div>
        </div>
    
        <!-- Tabel untuk menampilkan daftar konfigurasi -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Daftar Konfigurasi</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="config-list">
                        @foreach($configs as $config)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $config->name)) }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-ui" data-name="{{ $config->name }}" {{ $config->status ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Style untuk toggle switch -->
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 24px;
        }
        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #28a745;
        }
        input:checked + .slider:before {
            transform: translateX(16px);
        }
    </style>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Tambah Konfigurasi Baru
            document.getElementById("add-config-form").addEventListener("submit", function (event) {
                event.preventDefault();
                
                let name = document.getElementById("config-name").value;
                let status = document.getElementById("config-status").value;
    
                fetch("{{ route('admin.ui-config.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: name, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Konfigurasi berhasil ditambahkan.");
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            });
    
            // Update Status Konfigurasi
            document.querySelectorAll('.toggle-ui').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    let name = this.getAttribute('data-name');
                    let status = this.checked ? 1 : 0;
    
                    fetch("{{ route('admin.ui-config.update') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: name, status: status })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Konfigurasi berhasil diperbarui.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
@endsection --}}

@section('title', __('messages.Transport Detail'))
@section('content')
@extends('layouts.head')
<div class="container">
    <h2 class="mb-4">UI Configuration Panel</h2>

    <!-- Form untuk menambahkan konfigurasi baru -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Tambah Konfigurasi Baru</h5>
            <form id="add-config-form">
                <div class="mb-3">
                    <label for="config-name" class="form-label">Nama Konfigurasi</label>
                    <input type="text" class="form-control" id="config-name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="config-status" class="form-label">Status</label>
                    <select class="custom-select" id="config-status" name="status" required>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="config-message" class="form-label">Pesan</label>
                    <input type="text" class="form-control" id="config-message" name="message">
                </div>
                <button type="submit" class="btn btn-success">Tambah</button>
            </form>
        </div>
    </div>

    <!-- Tabel untuk menampilkan daftar konfigurasi -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Daftar Konfigurasi</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>Status</th>
                        <th>Pesan</th>
                    </tr>
                </thead>
                <tbody id="config-list">
                    @foreach($configs as $config)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $config->name)) }}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-ui" data-name="{{ $config->name }}" {{ $config->status ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                <input type="text" class="form-control config-message" data-name="{{ $config->name }}" value="{{ $config->message }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 24px;
    }
    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #28a745;
    }
    input:checked + .slider:before {
        transform: translateX(16px);
    }

</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tambah Konfigurasi Baru
        document.getElementById("add-config-form").addEventListener("submit", function (event) {
            event.preventDefault();
            
            let name = document.getElementById("config-name").value;
            let status = document.getElementById("config-status").value;
            let message = document.getElementById("config-message").value;

            fetch("{{ route('admin.ui-config.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ name: name, status: status, message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Konfigurasi berhasil ditambahkan.");
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Update Status Konfigurasi
        document.querySelectorAll('.toggle-ui').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                let name = this.getAttribute('data-name');
                let status = this.checked ? 1 : 0;

                fetch("{{ route('admin.ui-config.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: name, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Konfigurasi berhasil diperbarui.");
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Update Pesan Konfigurasi
        document.querySelectorAll('.config-message').forEach(function (input) {
            input.addEventListener('change', function () { // Bisa diganti ke 'keyup' jika ingin update saat mengetik
                let name = this.getAttribute('data-name');
                let message = this.value;

                fetch("{{ route('admin.ui-config.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: name,
                        message: message
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Server returned an error");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Konfigurasi berhasil diperbarui.");
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

    });
</script>
@endsection
