@section('title', __('messages.Users'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
        
		<div class="pd-ltr-20">
			<div class="row">
                <div class="col-xl-3 mb-30">
                    <a href="users">
                        <div class="card-box height-100-p widget-style1">
                            <div class="d-flex flex-wrap align-items-center"style="height: 100%;">
                                
                                <div class="chart-icon" style="font-size:53px; padding:3px;">
                                    <i class="icon-copy fa fa-cubes" aria-hidden="true"></i>
                                </div>
                                <div class="widget-data">
                                    <div class="h4 mb-0">{{ $adminusers->count() }} User</div>
                                    <div class="weight-600 font-14">As Admin</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-xl-3 mb-30">
                    <a href="users">
                        <div class="card-box height-100-p widget-style1">
                            <div class="d-flex flex-wrap align-items-center"style="height: 100%;">
                                
                                <div class="chart-icon" style="font-size:53px; padding:3px;">
                                    <i class="icon-copy fa fa-cubes" aria-hidden="true"></i>
                                </div>
                                <div class="widget-data">
                                    <div class="h4 mb-0">{{ $userusers->count() }} User</div>
                                    <div class="weight-600 font-14">As User</div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                

                
                
            </div>
            <div class="card-box mb-30">
                <div class="row">
                    <div class="col-md-5 col-sm-5">
                        <h2 class="h4 pd-20" style="line-height: 2.5;"><img src="images/icons/Partner.png" width="50" alt="" >&nbsp; As Admin</h2>
                    </div>
                    <div class="col-md-7 col-sm-7 text-right" style="padding-right: 5%; padding-bottom: 2%">
                        <a href="user-add" style="margin-top: 20px;" class="bg-light-blue btn text-blue weight-500"><i class="ion-plus-round"></i> Add Users</a>
                    </div>
                </div>
                <table class="data-table table nowrap" style="padding: 0 20px;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Profile Image</th>
                            <th style="width: 20%;">Name</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($adminusers as $user)
                        <tr>
                            <td>
                                <div class="user-img">
                                    <img src="{{ $user["profile_img"] }}" alt="">
                                </div>
                            </td>
                            <td>
                                <h5 class="font-16">{{ $user["name"] }}</h5>
                                <i class="icon-copy fa fa-envelope-o" style="color: rgb(143, 143, 143);" aria-hidden="true"></i>&nbsp; {{ $user["email"] }}<br>
                                
                            </td>


                            <td>
                                {{ $user["type"] }}
                            </td>
                            <td>
                                <a href="user-detail-{{ $user["id"] }}" data-toggle="tooltip" data-original-title="View Detail user"><i class="dw dw-eye"></i></a> &nbsp; &nbsp;
                                <a href="user-edit {{ $user["id"] }}"data-toggle="tooltip" data-original-title="Edit data user"><i class="icon-copy fa fa-edit" aria-hidden="true"></i></a>&nbsp; &nbsp;
                                <a href="user-delete {{ $user["id"] }}"data-toggle="tooltip" data-original-title="Delete data Tour"> <i class="icon-copy fa fa-trash" aria-hidden="true"></i></a>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
           
                <div class="row">
                    <div class="col-md-5 col-sm-5">
                        <h2 class="h4 pd-20" style="line-height: 2.5;"><img src="images/icons/Partner.png" width="50" alt="" >&nbsp; As User</h2>
                    </div>
                    
                </div>
                <table class="data-table table nowrap" style="padding: 0 20px;">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Profile Image</th>
                            <th style="width: 20%;">Name</th>
                            <th style="width: 10%;">Location</th>
                            <th style="width: 10%;">Status</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userusers as $user)
                        <tr>
                            <td>
                                <div class="user-img">
                                    <img src="{{ $user["profile_img"] }}" alt="">
                                </div>
                            </td>
                            <td>
                                <h5 class="font-16">{{ $user["name"] }}</h5>
                                <i class="icon-copy fa fa-envelope-o" style="color: rgb(143, 143, 143);" aria-hidden="true"></i>&nbsp; {{ $user["email"] }}<br>
                                <i class="icon-copy fa fa-whatsapp" style="color: rgb(143, 143, 143);" aria-hidden="true"></i>&nbsp; {{ $user["phone"] }}<br>
                               
                            </td>

                            <td>
                                {{ $user["address"] }}
                            </td>
                            <td>
                                {{ $user["status"] }}
                            </td>
                            <td>
                                <a href="user-package-{{ $user["id"] }}" data-toggle="tooltip" data-original-title="View Detail user"><i class="dw dw-eye"></i></a> &nbsp; &nbsp;
                                <a href="user-edit {{ $user["id"] }}"data-toggle="tooltip" data-original-title="Edit data user"><i class="icon-copy fa fa-edit" aria-hidden="true"></i></a>&nbsp; &nbsp;
                                <a href="user-delete {{ $user["id"] }}"data-toggle="tooltip" data-original-title="Delete data Tour"> <i class="icon-copy fa fa-trash" aria-hidden="true"></i></a>
                                
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            </div>
            </div>
			
			{{-- End Tour Package on booking period ================================================================================ --}}

			@include('layouts.footer')
		</div>
	</div>
	@endsection