@section('title', __('messages.User Manager'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('posDev')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="fa fa-users" aria-hidden="true"></i> User Manager
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">User Manager</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
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
                    @if (\Session::has('invalid'))
                        <div class="alert alert-danger">
                            <ul>
                                <li>{!! \Session::get('invalid') !!}</li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="row">
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy ion-alert-circled"></i> Activities</div>
                                </div>
                                <div class="banner-right">
                                    <ul class="attention">
                                        @foreach($notifications as $notification)
                                            <li>
                                                <strong>New Agent</strong><br>
                                                <p>
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                <p>
                                                    <i>at {{ dateTimeFormat($notification->created_at) }}</i>
                                                </p>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle">All User</div>
                            </div>
                            <div class="input-container">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchUserByName" type="text" onkeyup="searchUserByName()" class="form-control" name="search-user-byname" placeholder="Search by name">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchUserByGroup" type="text" onkeyup="searchUserByGroup()" class="form-control" name="search-user-group" placeholder="Search by group">
                                </div>
                            </div>
                            <div class="table-container">
                                <table id="tbUsers" class="data-table table stripe hover" >
                                    <thead>
                                        <tr>
                                            <th style="width: 10%">No</th>
                                            <th style="width: 20%">Name</th>
                                            <th style="width: 20%">Position</th>
                                            <th style="width: 10%">Status</th>
                                            <th style="width: 10%">Approval</th>
                                            <th style="width: 20%">Activity</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $no=>$user)
                                            @php
                                                $cmi = (int)$now->diffInMinutes($user->session_id);
                                                $cho = (int)$now->diffInHours($user->session_id);
                                                $cha = (int)$now->diffInDays($user->session_id);
                                                $cwe = (int)$now->diffInWeeks($user->session_id);
                                                $cmo = (int)$now->diffInMonths($user->session_id);
                                                $cye = (int)$now->diffInYears($user->session_id);
                                            @endphp
                                            @if ($user->status == "Block")
                                                <tr style="background-color: #ffd9d9;">
                                            @else
                                                <tr>
                                            @endif
                                            @if ($user->status == "Block")
                                                <td style="color: red;">{{ ++$no }}</td>
                                                <td style="color: red;">{{ $user->name }}</td>
                                                <td style="color: red;">{{ $user->position }}</td>
                                                <td style="color: red;">{{ $user->status }}</td>
                                                @if ($user->status == "Block")
                                                    <td style="color: red;"><i class="icon-copy ion-close-circled"></i> Rejected</td>
                                                @else
                                                    @if ($user->is_approved == 0)
                                                        <td style="color: #919191;"><i class="icon-copy ion-ios-clock"></i> Pending</td>
                                                    @else
                                                        <td style="color: #005900;"><i class="icon-copy ion-checkmark-circled"></i> Approved</td>
                                                    @endif
                                                @endif
                                                @if (isset($user->session_id))
                                                    @if ($cmi < 5)
                                                        <td><div class="online-status">Online</div></td>
                                                    @elseif ($cmi < 120)
                                                        <td style="color: red;">{{ $cmi }} <span>Minutes ago</span></td>
                                                    @elseif($cmi < 1440)
                                                        <td style="color: red;">{{ $cho }} <span>Hours ago</span></td>
                                                    @elseif($cmi < 10080)
                                                        <td style="color: red;">{{ $cha }} <span>Days ago</span></td>
                                                    @elseif($cmi < 43800)
                                                        <td style="color: red;">{{ $cwe }} <span>Weeks ago</span></td>
                                                    @elseif($cmi < 525600)
                                                        <td style="color: red;">{{ $cmo }} <span>Months ago</span></td>
                                                    @else
                                                        <td style="color: red;">{{ $cye }} <span>Years ago</span></td>
                                                    @endif
                                                @else
                                                    <td><div class="online-status">-</div></td>
                                                @endif
                                                
                                            @else
                                                <td>{{ ++$no }}</td>
                                                <td>{{ $user->name." (".$user->code.")" }}</td>
                                                <td>{{ $user->position }}</td>
                                                <td>@if ($user->status == "Active")
                                                        <div class="status-active"></div>
                                                    @elseif ($user->status == "Draft")
                                                        <div class="status-draft"></div>
                                                    @elseif ($user->status == "Usedup")
                                                        <div class="status-usedup"></div>
                                                    @elseif ($user->status == "Expired")
                                                        <div class="status-expired"></div>
                                                    @elseif ($user->status == "Pending")
                                                        <div class="status-pending"></div>
                                                    @elseif ($user->status == "Invalid")
                                                        <div class="status-invalid"></div>
                                                    @elseif ($user->status == "Rejected")
                                                        <div class="status-rejected"></div>
                                                    @else
                                                    @endif
                                                </td>
                                                @if ($user->status == "Block")
                                                    <td style="color: red;"><i class="icon-copy ion-close-circled"></i> Rejected</td>
                                                @else
                                                    @if ($user->is_approved == 0)
                                                        <td style="color: #919191;"><i class="icon-copy ion-ios-clock"></i> Pending</td>
                                                    @else
                                                        <td style="color: #005900;"><i class="icon-copy ion-checkmark-circled"></i> Approved</td>
                                                    @endif
                                                @endif
                                                @if (isset($user->session_id))
                                                    @if ($cmi < 5)
                                                        <td><div class="online-status"><p>Online</p></div></td>
                                                    @elseif ($cmi < 120)
                                                        <td><p>{{ $cmi }} <span>Minutes ago</span></p></td>
                                                    @elseif($cmi < 1440)
                                                        <td><p>{{ $cho }} <span>Hours ago</span></p></td>
                                                    @elseif($cmi < 10080)
                                                        <td><p>{{ $cha }} <span>Days ago</span></p></td>
                                                    @elseif($cmi < 43800)
                                                        <td><p>{{ $cwe }} <span>Weeks ago</span></p></td>
                                                    @elseif($cmi < 525600)
                                                        <td><p>{{ $cmo }} <span>Months ago</span></p></td>
                                                    @else
                                                        <td><p>{{ $cye }} <span>Years ago</span></p></td>
                                                    @endif
                                                @else
                                                    <td><p><div class="online-status">-</div></p></td>
                                                @endif
                                                
                                            @endif
                                            <form id="verified-user-{{ $user->id }}" action="/fverified-user-{{ $user->id }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                @method('put')
                                                <input type="hidden" name="verified" value="{{ $now }}">
                                            </form>
                                            <td class="text-right">
                                                <div class="table-action">
                                                    @if ($user->email_verified_at == "")
                                                        <button type="submit" form="verified-user-{{ $user->id }}" class="btn-validate"><i class="fa fa-check"></i></button>
                                                    @endif
                                                    <a href="#" data-toggle="modal" data-target="#user-view-{{ $user->id }}">
                                                        <button class="btn-view"><i class="dw dw-eye"></i></button>
                                                    </a>
                                                    <a href="#" data-toggle="modal" data-target="#user-edit-{{ $user->id }}">
                                                        <button class="btn-edit"><i class="icon-copy fa fa-edit"></i></button>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        {{-- MODAL VIEW USER DETAIL ----------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="user-view-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="subtitle"><i class="icon-copy fa fa-user" aria-hidden="true"></i> User Detail</div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-3">
                                                                    <div class="user-manager-img m-b-18">
                                                                        @if (Auth::user()->profileimg == '')
                                                                                <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="{{ Auth::user()->name }}">
                                                                        @else
                                                                                <img src="{{ asset('storage/user/profile' . '/' . Auth::user()->profileimg) }}" alt="{{ Auth::user()->name }}">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-3 col-md-2">
                                                                    <p>Name</p>
                                                                    <p>Username</p>
                                                                    <p>Email</p>
                                                                    <p>Phone</p>
                                                                    <p>Type</p>
                                                                    <p>Position</p>
                                                                    <p>Address</p>
                                                                    <p>Registered Date</p>
                                                                    <p>Activity</p>
                                                                </div>
                                                                <div class="col-9 col-md-6">
                                                                    <P>: {{ $user->name }}</P>
                                                                    <P>: {{ $user->username }}</P>
                                                                    <P>: {{ $user->email }}</P>
                                                                    <P>: {{ $user->phone }}</P>
                                                                    <P>: {{ $user->type }}</P>
                                                                    <P>: {{ $user->position }}</P>
                                                                    <P>: {{ $user->address." - ".$user->country }}</P>
                                                                    <P>: {{ dateFormat($user->created_at) }}</P>
                                                                    @if ($cmi < 5)
                                                                        <p><div class="online-status">: Online</div></p>
                                                                    @elseif ($cmi < 120)
                                                                        <p>: {{ $cmi }} <span>Minutes ago</span></p>
                                                                    @elseif($cmi < 1140)
                                                                        <p>: {{ $cho }} <span>Hours ago</span></p>
                                                                    @elseif($cmi < 10080)
                                                                        <p>: {{ $cha }} <span>Days ago</span></p>
                                                                    @elseif($cmi < 43800)
                                                                        <p>: {{ $cwe }} <span>Weeks ago</span></p>
                                                                    @elseif($cmi < 525600)
                                                                        <p>: {{ $cmo }} <span>Months ago</span></p>
                                                                    @else
                                                                        <p>: {{ $cye }} <span>Years ago</span></p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-box-footer">
                                                            @if ($user->status == "Active")
                                                                <form id="approve-user-{{ $user->id }}" action="/fapprove-user-{{ $user->id }}" method="post" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('put')
                                                                    <input type="hidden" name="note" value="Aprroved user {{ $user->id }}">
                                                                </form>
                                                                @if ($user->is_approved == 0)
                                                                    <button type="submit" form="approve-user-{{ $user->id }}" class="btn btn-primary"><i class="icon-copy ion-checkmark-circled"></i> Approve</button>
                                                                @endif
                                                            @endif
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- MODAL EDIT User ----------------------------------------------------------------------------------------------------------- --}}
                                        <div class="modal fade" id="user-edit-{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="card-box">
                                                        <div class="card-box-title">
                                                            <div class="title"><i class="fa fa-pencil"></i>Edit User</div>
                                                        </div>
                                                        <form id="update-user-{{ $user->id }}" action="/fedit-user-{{ $user->id }}" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('put')
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="type" class="form-label">Type </label>
                                                                                <select name="type" id="type" class="custom-select @error('type') is-invalid @enderror">
                                                                                    <option selected value="{{ $user->type }}"><p>{{ $user->type }}</p></option>
                                                                                    <option value="admin"><p>Admin</p></option>
                                                                                    <option value="user"><p>User</p></option>
                                                                                </select>
                                                                                @error('type')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="positionInput" class="form-label">Group </label>
                                                                                <select name="position" id="positionInput" class="custom-select @error('position') is-invalid @enderror">
                                                                                    <option selected value="{{ $user->position }}"><p>{{ $user->position }}</p></option>
                                                                                        <option {{ $user->position == "developer"?"selected":""; }} value="developer"><p>Developer</p></option>
                                                                                        <option {{ $user->position == "weddingDvl"?"selected":""; }} value="weddingDvl"><p>Wedding Developer</p></option>
                                                                                        <option {{ $user->position == "weddingRsv"?"selected":""; }}value="weddingRsv"><p>Wedding Reservation</p></option>
                                                                                        <option {{ $user->position == "weddingSls"?"selected":""; }} value="weddingSls"><p>Wedding Sales</p></option>
                                                                                        <option {{ $user->position == "weddingAuthor"?"selected":""; }} value="weddingAuthor"><p>Wedding Author</p></option>
                                                                                        <option {{ $user->position == "reservation"?"selected":""; }} value="reservation"><p>Reservation</p></option>
                                                                                        <option {{ $user->position == "staff"?"selected":""; }} value="staff"><p>Staff</p></option>
                                                                                        <option {{ $user->position == "agent"?"selected":""; }} value="agent"><p>Agent</p></option>
                                                                                </select>
                                                                                @error('position')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="statusSelect" class="form-label">Status </label>
                                                                                <select name="status" id="statusSelect" class="custom-select @error('status') is-invalid @enderror">
                                                                                    @if (isset($user->status))
                                                                                        <option selected value="{{ $user->status }}"><p>{{ $user->status }}</p></option>
                                                                                    @else
                                                                                        <option selected value="">Select status</option>
                                                                                    @endif
                                                                                    @if ($user->status == "Active")
                                                                                        <option value="Block">Block</option>
                                                                                    @elseif($user->status == "Block")
                                                                                        <option value="Active">Active</option>
                                                                                    @else
                                                                                        <option value="Block">Block</option>
                                                                                        <option value="Active">Active</option>
                                                                                    @endif
                                                                                </select>
                                                                                @error('status')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="codeInput" class="form-label">Code </label>
                                                                                <input type="text" id="codeInput" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Insert User Code" value="{{ $user->code }}">
                                                                                @error('code')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="nameInput" class="form-label">Name </label>
                                                                                <input type="text" id="nameInput" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" value="{{ $user->name }}">
                                                                                @error('name')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="userNameInput" class="form-label">Username </label>
                                                                                <input type="text" id="userNameInput" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Insert Username" value="{{ $user->username }}">
                                                                                @error('username')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="emailInput" class="form-label">Email </label>
                                                                                <input type="text" id="emailInput" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Insert Email" value="{{ $user->email }}">
                                                                                @error('email')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="phoneInput" class="form-label">Telephone </label>
                                                                                <input type="text" id="phoneInput" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert phone number" value="{{ $user->phone }}">
                                                                                @error('phone')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="officeInput" class="form-label">Office </label>
                                                                                <input type="text" id="officeInput" name="office" class="form-control @error('office') is-invalid @enderror" placeholder="Insert Office Name" value="{{ $user->office }}">
                                                                                @error('office')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="addressInput" class="form-label">Address </label>
                                                                                <input type="text" id="addressInput" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Insert Address" value="{{ $user->address }}">
                                                                                @error('address')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="countryInput" class="form-label">Country </label>
                                                                                <input type="text" id="countryInput" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Insert Country" value="{{ $user->country }}">
                                                                                @error('country')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <div class="form-group">
                                                                                <label for="commentInput" class="form-label">Comment</label>
                                                                                <input type="text" id="commentInput" name="comment" class="form-control @error('comment') is-invalid @enderror" placeholder="Insert comment" value="{{ $user->comment }}">
                                                                                @error('comment')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                            </div>
                                                        </form>
                                                        <div class="card-box-footer">
                                                            <button type="submit" form="update-user-{{ $user->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- END MODAL EDIT SERVICE ----------------------------------------------------------------------------------------------------------- --}}
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-box-footer">
                                <a href="#" data-toggle="modal" data-target="#user-add">
                                    <button class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> Add User</button>
                                </a>
                            </div>
                            {{-- MODAL ADD USER ----------------------------------------------------------------------------------------------------------- --}}
                            <div class="modal fade" id="user-add" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add New User</div>
                                            </div>
                                            <form id="add-user" method="POST" action="{{ route('create-user') }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="nameInput" class="form-label">Name </label>
                                                            <input type="text" id="nameInput" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" value="{{ old('name') }}" required>
                                                            @error('name')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="userNameInput" class="form-label">Username </label>
                                                            <input type="text" id="userNameInput" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Insert Username" value="{{ old('username') }}" required>
                                                            @error('username')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="position" class="form-label">Position <span>*</span></label>
                                                            <select name="position" id="position"  type="text" class="custom-select @error('position') is-invalid @enderror" placeholder="Select position" required>
                                                                <option selected value=""><p>Select Position</p></option>
                                                                <option value="developer"><p>Developer</p></option>
                                                                <option value="weddingDvl"><p>Wedding Developer</p></option>
                                                                <option value="weddingRsv"><p>Wedding Reservation</p></option>
                                                                <option value="weddingSls"><p>Wedding Sales</p></option>
                                                                <option value="weddingAuthor"><p>Wedding Author</p></option>
                                                                <option value="reservation"><p>Reservation</p></option>
                                                                <option value="staff"><p>Staff</p></option>
                                                                <option value="agent"><p>Agent</p></option>
                                                            </select>
                                                            @error('position')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="codeInput" class="form-label">Code </label>
                                                            <input type="text" id="codeInput" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="Insert User Code" value="{{ old('code') }}" required>
                                                            @error('code')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="emailInput" class="form-label">Email </label>
                                                            <input type="text" id="emailInput" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Insert Email" value="{{ old('email') }}" required>
                                                            @error('email')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="phoneInput" class="form-label">Telephone </label>
                                                            <input type="text" id="phoneInput" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert phone number" value="{{ old('phone') }}" required>
                                                            @error('phone')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="officeInput" class="form-label">Office </label>
                                                            <input type="text" id="officeInput" name="office" class="form-control @error('office') is-invalid @enderror" placeholder="Insert Office Name" value="{{ old('office') }}" required>
                                                            @error('office')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="addressInput" class="form-label">Address </label>
                                                            <input type="text" id="addressInput" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Insert Address" value="{{ old('address') }}" required>
                                                            @error('address')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="countryInput" class="form-label">Country </label>
                                                            <input type="text" id="countryInput" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Insert Country" value="{{ old('country') }}" required>
                                                            @error('country')
                                                                <span class="invalid-feedback">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            
                                                <hr class="form-hr">
                                                <p class="form-notif">
                                                    Make sure all the data is filled in correctly before the new user is added!.
                                                </p>
                                            </form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="add-user" class="btn btn-primary ms-auto"><i class="fa fa-check"></i> Add User</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                        <div class="col-md-12">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy ion-alert-circled"></i> Activities</div>
                                </div>
                                <div class="banner-right">
                                    <ul class="attention">
                                        @foreach($notifications as $notification)
                                            <li>
                                                <strong>New Agent</strong><br>
                                                <p>
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                <p>
                                                    <i>at {{ dateTimeFormat($notification->created_at) }}</i>
                                                </p>
                                            </li>
                                        @endforeach
                                    </ul>
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
    <script>
        function searchUserByName() {
            let input = document.getElementById("searchUserByName").value.toLowerCase();
            let table = document.getElementById("tbUsers");
            let rows = table.getElementsByTagName("tr");
            
            for (let i = 1; i < rows.length; i++) { // Mulai dari 1 agar tidak memfilter header
                let nameCell = rows[i].getElementsByTagName("td")[1]; // Kolom Name
                if (nameCell) {
                    let nameText = nameCell.textContent.toLowerCase();
                    rows[i].style.display = nameText.includes(input) ? "" : "none";
                }
            }
        }

        function searchUserByGroup() {
            let input = document.getElementById("searchUserByGroup").value.toLowerCase();
            let table = document.getElementById("tbUsers");
            let rows = table.getElementsByTagName("tr");
            
            for (let i = 1; i < rows.length; i++) { // Mulai dari 1 agar tidak memfilter header
                let positionCell = rows[i].getElementsByTagName("td")[2]; // Kolom Position (Group)
                if (positionCell) {
                    let positionText = positionCell.textContent.toLowerCase();
                    rows[i].style.display = positionText.includes(input) ? "" : "none";
                }
            }
        }

    </script>
@endsection