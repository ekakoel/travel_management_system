@section('title', __('messages.User Detail'))
@section('content')
    @extends('layouts.head')
	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
								<x-backend.page-hero>
				    <x-slot name="heading">
				        Detail User - {{ $dusers["name"] }}
				    </x-slot>
				</x-backend.page-hero>
				<div class="product-wrap">
					<div class="product-detail-wrap mb-30">
						<div class="row">

							<div class="col-lg-12 col-md-12 col-sm-12">
								<div class="backend-panel product-detail-desc pd-20 height-100-p">

									<h4 class="mb-10">{{ $dusers["name"] }}</h4>
									<hr>
                                    <p>Email : {{ $dusers["email"] }}</p>
                                    <p>Created At : {{ date('d M Y', strtotime($dusers->created_at)) }}</p>
                                    <p>Update At : {{ date('d M Y', strtotime($dusers->updated_at)) }}</p>
									<hr>
									<div class="row">

										<div class="col-md-12 col-12">
											<a href="{{ route('user-manager') }}" class="backend-button backend-button-secondary">Back to all User</a>
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
