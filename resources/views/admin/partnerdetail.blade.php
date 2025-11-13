@section('title', __('messages.Partner Detail'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>
	@can('isAdmin')
	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-8 col-sm-12">
							<div class="title">
								<h4>Detail Partner - {{ $dpartners["name"] }}</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
									<li class="breadcrumb-item"><a href="partners">Partner</a></li>
									<li class="breadcrumb-item active" aria-current="page">Details</li>
								</ol>
							</nav>
						</div>
                        <div class="col-md-2 col-sm-12">
							<div class="title text-right" style="height: 100%">
                                <h6>Status : </h6>
                                {{ $dpartners["status"] }} 
                            </div>
						</div>
                        <div class="col-md-2 col-sm-12 text-right">
							<div class="title">
								<a href="partner-update" class="bg-light-blue btn text-blue weight-500"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Partner</a>
                                
                            </div>
						</div>
					</div>
				</div>
				<div class="product-wrap">
					<div class="product-detail-wrap mb-30">
						<div class="row">
							<div class="col-lg-4 col-md-12 col-sm-12">
								<div class="product-slider slider-arrow">
									<div class="product-slide">
										<img src="images/activity/product-img1.jpg" alt="">
									</div>
									<div class="product-slide">
										<img src="images/activity/product-img2.jpg" alt="">
									</div>
									<div class="product-slide">
										<img src="images/activity/product-img3.jpg" alt="">
									</div>
									<div class="product-slide">
										<img src="images/activity/product-img4.jpg" alt="">
									</div>
								</div>
								<div class="product-slider-nav">
									<div class="product-slide-nav">
										<img src="images/activity/product-img1.jpg" alt="">
									</div>
									<div class="product-slide-nav">
										<img src="images/activity/product-img2.jpg" alt="">
									</div>
									<div class="product-slide-nav">
										<img src="images/activity/product-img3.jpg" alt="">
									</div>
									<div class="product-slide-nav">
										<img src="images/activity/product-img4.jpg" alt="">
									</div>
								</div>
							</div>
							<div class="col-lg-8 col-md-12 col-sm-12">
								<div class="product-detail-desc pd-20 card-box height-100-p">

									<h4 class="mb-10">{{ $dpartners["name"] }}</h4>
                                    {{-- <div class="price">
                                        <i class="icon-copy fa fa-envelope-o" style="color: rgb(95, 95, 95)" aria-hidden="true"></i> &nbsp; {{ $dpartners["email"] }}<br>
                                        <i class="icon-copy fa fa-map-marker" style="color: red" aria-hidden="true"></i> &nbsp; {{ $dpartners["address"] }} 
									</div> --}}
									<hr>
                                    <p>Status : {{ $dpartners["status"] }}</p>
                                    <p>Email : {{ $dpartners["email"] }}</p>
                                    <p>Phone : {{ $dpartners["phone"] }}</p>
                                    <p>Location : {{ $dpartners["address"] }}</p>
                                    <p>Created At : {{ date('d M Y', strtotime($dpartners->created_at)) }}</p>
                                    <p>Update At : {{ date('d M Y', strtotime($dpartners->updated_at)) }}</p>
									<hr>
									<div class="row">
										
										<div class="col-md-12 col-12">
											<a href="partners" class="btn btn-outline-primary btn-block">Back to all Partner</a>
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