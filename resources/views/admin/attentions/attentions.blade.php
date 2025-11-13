@section('title', __('messages.Attentions'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy fa fa-exclamation-circle"></i> Attentions
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active" aria-current="page">Manage Attention</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (\Session::has('error'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('error') !!}</li>
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
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                    <input id="searchAttentionByPage" type="text" onkeyup="searchAttentionByPage()" class="form-control" name="search-wedding-location" placeholder="Search by Page...">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table id="tbAttentions" class="data-table table break-spaces dataTable" >
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Page</th>
                                            <th>name</th>
                                            <th>EN</th>
                                            <th>ZH</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attentions as $no=>$attention)
                                            <tr>
                                                <td>{{ ++$no }}</td>
                                                <td>{{ $attention->page }}</td>
                                                <td>{{ $attention->name }}</td>
                                                <td>{{ $attention->attention_en }}</td>
                                                <td>{{ $attention->attention_zh }}</td>
                                                <td class="table-action">
                                                    <a href="#" data-target="#detailAttention-{{ $attention->id }}" data-toggle="modal">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="#" data-target="#modal-update-attention-{{ $attention->id }}" data-toggle="modal">
                                                        <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                    </a>
                                                    <form id="removeAttention-{{ $attention->id }}" action="/fremove-attention/{{ $attention->id }}" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        @method("DELETE")
                                                        <button type="submit" class="btn-delete" form="removeAttention-{{ $attention->id }}" onclick="return confirm('Are you sure?');"  data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                    </form>
                                                    
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="detailAttention-{{ $attention->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i> Attentions for page "{{ $attention->page }}"</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <b>Name: </b><br>
                                                                    {{ $attention->name }}
                                                                </div>
                                                                <div class="col-6">
                                                                    <b>Page: </b><br>
                                                                    {{ $attention->page }}
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr class="form-hr">
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <b>Attentions English: </b><br>
                                                                    {{ $attention->attention_en }}
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <hr class="form-hr">
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <b>Attentions Chinese: </b><br>
                                                                    {{ $attention->attention_zh }}
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <div class="form-group">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="modal-update-attention-{{ $attention->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i> Attentions for page "{{ $attention->page }}"</div>
                                                            </div>
                                                            <form id="faddUpdateAttention-{{ $attention->id }}" action="{{ route('func.update-attention',$attention->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="name" class="form-label col-form-label">Name</label>
                                                                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Name" value="{{ $attention->name }}" required>
                                                                            @error('name')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="page" class="form-label col-form-label">Page</label>
                                                                            <input type="text" name="page" class="form-control @error('page') is-invalid @enderror" placeholder="Insert URL" value="{{ $attention->page }}" required>
                                                                            @error('page')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="attention_en">Attention English <span>*</span></label>
                                                                            <textarea name="attention_en" class="textarea_editor form-control @error('attention_en') is-invalid @enderror" placeholder="Attention English" type="text">{{ $attention->attention_en }}</textarea>
                                                                            @error('attention_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="attention_zh">Attention Chinese <span>*</span></label>
                                                                            <textarea name="attention_zh" class="textarea_editor form-control @error('attention_zh') is-invalid @enderror" placeholder="Attention Chinese" type="text">{{ $attention->attention_zh }}</textarea>
                                                                            @error('attention_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <div class="form-group">
                                                                    <button type="submit" form="faddUpdateAttention-{{ $attention->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="card-box-footer">
                                    <a href="#" data-target="#addAttentionModal" data-toggle="modal">
                                        <button type="button" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Add Attention
                                        </button>
                                    </a>
                                </div>
                                <!-- Modal Add Attention -->
                                <div class="modal fade" id="addAttentionModal" tabindex="-1" aria-labelledby="addAttentionLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="fa fa fa-exclamation-circle" aria-hidden="true"></i>Create Attentions</div>
                                                </div>
                                                <form action="{{ url('/fstore-attention') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addAttentionLabel"><i class="fa fa-exclamation-circle"></i> Add Attention</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="name" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Page</label>
                                                            <input type="text" name="page" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Attention English</label>
                                                            <textarea name="attention_en" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Attention Chinese</label>
                                                            <textarea name="attention_zh" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchAttentionByPage() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchAttentionByPage");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbAttentions");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
