@section('title', __('messages.Term and Condition'))
@section('content')
    @extends('layouts.head')
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
                <div class="row">
                    <div id="term-and-condition" class="col-md-12">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">Term and Condition</div>
                            </div>
                            <div class="row">
                                <div  class="col-md-12 m-b-18 text-center">
                                    <div class="title">Term And Condition</div>
                                    <p><i>https://online.balikamitour.com</i></p>
                                </div>
                                <div  class="col-md-12 text-left">
                                    <div class="isi-term">
                                            <p>For the smoothness and comfort of Bali Kami Tour's tourism data information system users, every user is expected to understand the term and condition.</p>
                                    </div>
                                </div>
                                <div  class="col-md-12 text-left">
                                    {{-- USER TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">User Policy</div>
                                    <div class="isi-term">
                                        @foreach ($tandcs as $user_term)
                                            <div class="term-subtitle {{ $user_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $user_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-user-{{ $user_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $user_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $user_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $user_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT POLICY USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-user-{{ $user_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $user_term->type }} Term & Condition</div>
                                                            </div>
                                                       
                                                            <form id="edit-user-policy-{{ $user_term->id }}" action="/fupdate-policy/{{ $user_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $user_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $user_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $user_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $user_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $user_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $user_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option selected value="{{ $user_term->status }}">{{ $user_term->status }}</option>
                                                                                <option {{ $user_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $user_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $user_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $user_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $user_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $user_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $user_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $user_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-user-policy-{{ $user_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- SYSTEM TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">System Policy</div>
                                    <div class="isi-term">
                                        @foreach ($system_term as $sys_term)
                                            <div class="term-subtitle {{ $sys_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $sys_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-system-{{ $sys_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $sys_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $sys_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $sys_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT SYSTEM USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-system-{{ $sys_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $sys_term->type }} Term & Condition</div>
                                                            </div>
                                                            <form id="edit-system-policy-{{ $sys_term->id }}" action="/fupdate-policy/{{ $sys_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $sys_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $sys_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $sys_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $sys_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $sys_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $sys_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option {{ $sys_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $sys_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $sys_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $sys_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $sys_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $sys_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $sys_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $sys_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-system-policy-{{ $sys_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- ADMINISTRATOR TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">Administrator Policy</div>
                                    <div class="isi-term">
                                        @foreach ($admin_term as $adm_term)
                                            <div class="term-subtitle {{ $adm_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $adm_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-admin-{{ $adm_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $adm_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $adm_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $adm_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT SYSTEM USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-admin-{{ $adm_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $adm_term->type }} Term & Condition</div>
                                                            </div>
                                                            <form id="edit-admin-policy-{{ $adm_term->id }}" action="/fupdate-policy/{{ $adm_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $adm_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $adm_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $adm_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $adm_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $adm_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $adm_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option {{ $adm_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $adm_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $adm_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $adm_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $adm_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $adm_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $adm_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $adm_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-admin-policy-{{ $adm_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- PRICE TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">Price Policy</div>
                                    <div class="isi-term">
                                        @foreach ($price_term as $prs_term)
                                            <div class="term-subtitle {{ $prs_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $prs_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-price-{{ $prs_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $prs_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $prs_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $prs_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT SYSTEM USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-price-{{ $prs_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $prs_term->type }} Term & Condition</div>
                                                            </div>
                                                            <form id="edit-price-policy-{{ $prs_term->id }}" action="/fupdate-policy/{{ $prs_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $prs_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $prs_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $prs_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $prs_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $prs_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $prs_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option {{ $prs_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $prs_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $prs_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $prs_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $prs_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $prs_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $prs_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $prs_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-price-policy-{{ $prs_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- CURRENCY TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">Currency Policy</div>
                                    <div class="isi-term">
                                        @foreach ($currency_term as $curr_term)
                                            <div class="term-subtitle {{ $curr_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $curr_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-currency-{{ $curr_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $curr_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $curr_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $curr_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT SYSTEM USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-currency-{{ $curr_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $curr_term->type }} Term & Condition</div>
                                                            </div>
                                                            <form id="edit-currency-policy-{{ $curr_term->id }}" action="/fupdate-policy/{{ $curr_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $curr_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $curr_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $curr_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $curr_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $curr_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $curr_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option {{ $curr_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $curr_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $curr_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $curr_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $curr_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $curr_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $curr_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $curr_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-currency-policy-{{ $curr_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- PROMOTION TERM ----------------------------------------------------------------------------------------------------------- --}}
                                    <div class="heading-policy">Promotion Policy</div>
                                    <div class="isi-term">
                                        @foreach ($promotion_term as $promo_term)
                                            <div class="term-subtitle {{ $promo_term->status == "Draft"?"text-grey":""; }}">
                                                {{ $promo_term->name_en }}
                                                <div class="act-btn">
                                                    <a href="#" data-toggle="modal" data-target="#edit-promotion-{{ $promo_term->id }}"><i class="fa fa-pencil" data-toggle="tooltip" data-placement="top" title="Edit" ></i></a>
                                                    <form action="fdestroy-policy/{{ $promo_term->id }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        <button type="submit" onclick="return confirm('Are you sure?');" data-toggle="tooltip" data-placement="top" title="Delete" class="btn-destroy"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="term-value {{ $promo_term->status == "Draft"?"text-grey":""; }}">
                                                {!! $promo_term->policy_en !!}
                                            </div>
                                            <div class="term-closing-line"><hr class="form-hr"></div>
                                            {{-- MODAL EDIT SYSTEM USER ----------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="edit-promotion-{{ $promo_term->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Update {{ $promo_term->type }} Term & Condition</div>
                                                            </div>
                                                            <form id="edit-promotion-policy-{{ $promo_term->id }}" action="/fupdate-policy/{{ $promo_term->id }}" method="post" enctype="multipart/form-data">
                                                                @method('put')
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="type" class="form-label col-form-label">Policy Type</label>
                                                                            <select name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option {{ $promo_term->type == "User"?"Selected":""; }} value="User">User</option>
                                                                                <option {{ $promo_term->type == "System"?"Selected":""; }} value="System">System</option>
                                                                                <option {{ $promo_term->type == "Administrator"?"Selected":""; }} value="Administrator">Administrator</option>
                                                                                <option {{ $promo_term->type == "Price"?"Selected":""; }} value="Price">Price</option>
                                                                                <option {{ $promo_term->type == "Promotion"?"Selected":""; }} value="Promotion">Promotion</option>
                                                                                <option {{ $promo_term->type == "Currency"?"Selected":""; }} value="Currency">Currency</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="form-group">
                                                                            <label for="status" class="form-label col-form-label">Policy Status</label>
                                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                                <option {{ $promo_term->status == "Draft"?"Selected":""; }} value="Draft">Draft</option>
                                                                                <option {{ $promo_term->status == "Active"?"Selected":""; }} value="Active">Active</option>
                                                                            </select>
                                                                            @error('status')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_id" class="form-label">User Policy</label>
                                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Indonesia policy title" value="{{ $promo_term->name_id }}" required>
                                                                            @error('name_id')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Insert policy" required>{!! $promo_term->policy_id !!}</textarea>
                                                                            @error('policy_id')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_en" class="form-label">User Policy EN</label>
                                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{{ $promo_term->name_en }}" required>
                                                                            @error('name_en')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy" required>{!! $promo_term->policy_en !!}</textarea>
                                                                            @error('policy_en')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="name_zh" class="form-label">User Policy ZH</label>
                                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{{ $promo_term->name_zh }}" required>
                                                                            @error('name_zh')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                            <textarea name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy" required>{!! $promo_term->policy_zh !!}</textarea>
                                                                            @error('policy_zh')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12 text-left">
                                                                        <p class="form-text text-muted">
                                                                            Make sure all data is filled in correctly!
                                                                        </p>
                                                                    </div>
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                </div>
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="edit-promotion-policy-{{ $promo_term->id }}" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-18">
                                <div class="col-md-12">
                                    <div class="isi-term">
                                        <p>The terms and conditions are subject to change at any time without further notice in accordance with the provisions of the prevailing Information and Electronic Transactions Law.</p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="isi-term">
                                        <p>Denpasar, 01 January 2023</p>
                                        <p><b>{{ $business->name }}</b></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-box-footer">
                                <a href="#" data-toggle="modal" data-target="#add-new-policy"><button type="submit" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add New Policy</button></a>
                            </div>
                            {{-- MODAL ADD NEW POLICY ----------------------------------------------------------------------------------------------------------- --}}
                            <div class="modal fade" id="add-new-policy" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add New Policy</div>
                                            </div>
                                            <form id="add-term-and-condition" action="/fadd-policy" method="post" enctype="multipart/form-data">
                                                @method('put')
                                                {{ csrf_field() }}
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="type">Policy Type</label>
                                                            <select name="type" class="custom-select @error('type') is-invalid @enderror" required>
                                                                <option selected value="">Select Policy type</option>
                                                                <option value="User">User</option>
                                                                <option value="System">System</option>
                                                                <option value="Administrator">Administrator</option>
                                                                <option value="Price">Price</option>
                                                                <option value="Promotion">Promotion</option>
                                                                <option value="Currency">Currency</option>
                                                            </select>
                                                            @error('status')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="status">Policy Status</label>
                                                            <select name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                                <option selected value="">Select Status</option>
                                                                <option value="Draft">Draft</option>
                                                                <option value="Active">Active</option>
                                                            </select>
                                                            @error('status')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="name_id">Policy Title ID </label>
                                                            <input type="text" name="name_id" class="form-control @error('name_id') is-invalid @enderror" placeholder="Judul Kebijakan" value="{!! old('name_id') !!}" required>
                                                            @error('name_id')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="add_policy_id">Policy ID </label>
                                                            <textarea id="add_policy_id" name="policy_id" class="textarea_editor form-control border-radius-0 @error('policy_id') is-invalid @enderror" placeholder="Isi Kebijakan" required>{!! old('policy_id') !!}</textarea>
                                                            @error('policy_id')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="name_en">Policy Title EN </label>
                                                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" placeholder="English policy title" value="{!! old('name_en') !!}" required>
                                                            @error('name_en')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                            
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="policy_en">Policy EN </label>
                                                            <textarea id="add_policy_en" name="policy_en" class="textarea_editor form-control border-radius-0 @error('policy_en') is-invalid @enderror" placeholder="Insert policy in English" required>{!! old('policy_en') !!}</textarea>
                                                            @error('policy_en')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="add_policy_zh">Policy Title ZH </label>
                                                            <input type="text" name="name_zh" class="form-control @error('name_zh') is-invalid @enderror" placeholder="Chinese policy title" value="{!! old('name_zh') !!}" required>
                                                            @error('name_zh')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="policy_zh">Policy ZH </label>
                                                            <textarea id="add_policy_zh" name="policy_zh" class="textarea_editor form-control border-radius-0 @error('policy_zh') is-invalid @enderror" placeholder="Insert policy in Chinese" required>{!! old('policy_zh') !!}</textarea>
                                                            @error('policy_zh')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 text-left">
                                                        <p class="form-text text-muted">
                                                            Make sure all data is filled in correctly!
                                                        </p>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="add-term-and-condition" class="btn btn-primary ms-auto"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                            </div>
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
    @endcan
@endsection