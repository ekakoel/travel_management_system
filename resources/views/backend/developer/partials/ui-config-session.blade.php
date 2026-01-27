<div class="card-box m-b-18">
    <div class="card-box-title">
        <div class="subtitle"> <i class="icon-copy fa fa-tag" aria-hidden="true"></i>User Interface Configuration</div>
    </div>
    <div class="col-md-12">
        <form id="add-config-form">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="page-name" class="form-label">URL</label>
                        <input type="text" name="page" id="page-name" class="custom-file-input @error('page') is-invalid @enderror" placeholder="Page">
                        @error('page')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group">
                        <label for="config-name" class="form-label">Route Name</label>
                        <input type="text" name="name" id="config-name" class="custom-file-input @error('name') is-invalid @enderror" placeholder="Route name">
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="config-status" class="form-label">Status</label>
                        <select id="config-status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="config-message" class="form-label">Message</label>
                        <textarea id="config-message" name="message" style="height: 60px !important;" class="form-control border-radius-0" placeholder="Insert message">You are not permitted to access this page (您無權存取該頁面)</textarea>
                        @error('message')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-12 text-right">
        <button form="add-config-form" type="submit" class="btn btn-success"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
    </div>
    <div class="col-md-12">
        <div class="search-container m-t-18">
            <input type="text" id="search_by_url" class="form-control" placeholder="Search by URL">
            <input type="text" id="search_by_name" class="form-control" placeholder="Search by Name">
        </div>
        <table id="tbUiConfig" class="data-table table stripe hover nowrap">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Route Name</th>
                    <th>Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach($configs as $config)
                    <tr>
                        <td><input type="text" class="form-control config-page" data-name="{{ $config->name }}" value="{{ $config->page }}"></td>
                        <td><input type="text" class="form-control config-name" data-name="{{ $config->name }}" value="{{ $config->name }}"></td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="toggle-ui" data-name="{{ $config->name }}" {{ $config->status ? 'checked' : '' }}>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <textarea style="height: min-content !important;" class="form-control config-message" data-name="{{ $config->name }}">{{ $config->message }}</textarea>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        function filterTable() {
            let searchUrl = $("#search_by_url").val().toLowerCase().trim();
            let searchName = $("#search_by_name").val().toLowerCase().trim();

            $("#tbUiConfig tbody tr").each(function () {
                let url = $(this).find(".config-page").val().toLowerCase();
                let name = $(this).find(".config-name").val().toLowerCase();

                let matchUrl = searchUrl === "" || url.includes(searchUrl);
                let matchName = searchName === "" || name.includes(searchName);

                $(this).toggle(matchUrl && matchName);
            });
        }

        $("#search_by_url, #search_by_name").on("input", function () {
            filterTable();
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("add-config-form").addEventListener("submit", function (event) {
            event.preventDefault();

            let page = document.getElementById("page-name").value.trim();
            let name = document.getElementById("config-name").value.trim();
            let status = document.getElementById("config-status").value;
            let message = document.getElementById("config-message").value.trim();

            fetch("{{ route('admin.ui-config.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ page: page, name: name, status: status, message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil!",
                        text: "Konfigurasi berhasil ditambahkan.",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: data.error || "Terjadi kesalahan!",
                        confirmButtonText: "OK"
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Terjadi kesalahan pada sistem!",
                    confirmButtonText: "OK"
                });
            });
        });
    });


    $(document).ready(function () {
    function updateConfig(row, field, value) {
        let oldName = row.find(".config-name").data("name");
        let newName = row.find(".config-name").val().trim();
        let page = row.find(".config-page").val().trim();
        let status = row.find(".toggle-ui").is(":checked") ? 1 : 0;
        let message = row.find(".config-message").val().trim();

        let requestData = {
            _token: $('meta[name="csrf-token"]').attr("content"),
            old_name: oldName,
            name: newName,
            page: page,
            status: status,
            message: message
        };

        $.ajax({
            url: "/ui-config/update",
            type: "POST",
            data: requestData,
            success: function (response) {
                if (response.success) {
                    row.find(".config-name").data("name", newName);
                    row.find(".config-page").data("value", page);
                    row.find(".config-message").data("value", message);
                    row.find(".toggle-ui").data("value", status);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && field === "name") {
                    Swal.fire("Gagal!", "Nama sudah ada di database!", "error");
                }
            }
        });
    }

    $(".config-page").on("blur", function () {
        let row = $(this).closest("tr");
        let value = $(this).val().trim();
        if (value !== $(this).data("value")) {
            updateConfig(row, "page", value);
        }
    });

    $(".config-name").on("blur", function () {
        let row = $(this).closest("tr");
        let value = $(this).val().trim();
        if (value !== row.find(".config-name").data("name")) {
            updateConfig(row, "name", value);
        }
    });

    $(".config-message").on("blur", function () {
        let row = $(this).closest("tr");
        let value = $(this).val().trim();
        if (value !== $(this).data("value")) {
            updateConfig(row, "message", value);
        }
    });

    $(".toggle-ui").on("change", function () {
        let row = $(this).closest("tr");
        let value = $(this).is(":checked") ? 1 : 0;
        if (value != $(this).data("value")) {
            updateConfig(row, "status", value);
        }
    });
});




</script>