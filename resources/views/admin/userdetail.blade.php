@section('title', __('messages.User Detail'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-8 col-sm-12">
							<div class="title">
								<h4>Detail User - {{ $dusers["name"] }}</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="users">User</a></li>
									<li class="breadcrumb-item active" aria-current="page">Detail</li>
								</ol>
							</nav>
						</div>
                        <div class="col-md-4 col-sm-12">
							<div class="title text-right" style="height: 100%">
                                <h6>Role : </h6>
                                {{ $dusers["type"] }} 
                            </div>
						</div>
					</div>
				</div>
				<div class="product-wrap">
					<div class="product-detail-wrap mb-30">
						<div class="row">
							
							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="product-detail-desc pd-20 card-box height-100-p">

									<h4 class="mb-10">{{ $dusers["name"] }}</h4>
									<hr>
                                    <p>Email : {{ $dusers["email"] }}</p>
                                    <p>Created At : {{ date('d M Y', strtotime($dusers->created_at)) }}</p>
                                    <p>Update At : {{ date('d M Y', strtotime($dusers->updated_at)) }}</p>
									<hr>
									<div class="row">
										
										<div class="col-md-12 col-12">
											<a href="users" class="btn btn-outline-primary btn-block">Back to all User</a>
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
	@endsection