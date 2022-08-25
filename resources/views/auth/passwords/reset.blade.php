

@extends('layouts.app')

@section('content')
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
	<!--begin::Login-->
	<div class="login login-1 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
		<!--begin::Aside-->
		<div class="login-aside d-flex flex-column flex-row-auto"> 
			<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url( {{ asset('storage/assets/site_identity/login-bg.png') }} );background-position:center;background-size:cover"></div> 
		</div>
		<!--begin::Aside-->
		<!--begin::Content-->
		<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-lg-auto">
			<!--begin::Content body-->
			<div class="d-flex flex-column-fluid flex-center">
				<!--begin::Signin-->
				<div class="login-form login-signin">
					<!--begin::Form-->
					<form id="reset_password_form" class="form-horizontal" role="form" method="POST" action="{{ url('admin/password/reset') }}">
					{!! csrf_field() !!}

					<input type="hidden" name="token" value="{{ $token }}">
						<!--begin::Title-->
						<div class="pb-13 pt-lg-0 pt-5">
							{{-- <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Welcome to Shivangi Beauty Parlor</h3> --}}
							<img src="{{ asset('storage/assets/site_identity/logo.png') }}" class="max-h-70px" alt="" />
						</div>
						<!--begin::Title-->
						<!--begin::Form group-->
						<div class="form-group ({ $errors->has('email') ? ' has-error' : '' }}">
							<label class="font-size-h6 font-weight-bolder text-dark">Email</label> 
							<input type="email" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" style="border-radius: 4px; box-shadow:0px 2px 4px rgba(0,0,0,0.18); padding-right:40px;"  name="email" value="{{ $email ?: old('email') }}" autocomplete="off" pattern="^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" title="Please eter valid email address!">
							@if ($errors->has('email'))
								<span class="help-block">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
							@endif
						</div>
						<!--end::Form group-->
						<!--begin::Form group-->
						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<div class="d-flex justify-content-between mt-n5">
								<label class="font-size-h6 font-weight-bolder text-dark pt-5">New Password</label> 
							</div> 
							<input type="password"class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" style="border-radius: 4px; box-shadow:0px 2px 4px rgba(0,0,0,0.18); padding-right:40px;" type="password" name="password" autocomplete="off" placeholder="New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$" title = 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.'>
							
						@if ($errors->has('password'))
							<span class="help-block">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
						</div>
						<!--end::Form group-->
						<!--begin::Form group-->
						<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
							<div class="d-flex justify-content-between mt-n5">
								<label class="font-size-h6 font-weight-bolder text-dark pt-5">Confirm Password</label> 
							</div> 
							<input type="password" class="form-control form-control-solid h-auto py-6 px-6 rounded-lg" style="border-radius: 4px; box-shadow:0px 2px 4px rgba(0,0,0,0.18); padding-right:40px;" placeholder="Confirm password" name="password_confirmation" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$" title = 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.'>
							
						@if ($errors->has('password'))
								<span class="help-block">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
						</div>
						<!--end::Form group-->
						<!--begin::Action-->
						<div class="pb-lg-0 pb-5">
							<button type="submit" value="submit" id="kt_login_signin_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Reset Password</button> 
						</div>
						<!--end::Action-->
					</form>
					<!--end::Form-->
				</div>
				<!--end::Signin-->
			</div>
			<!--end::Content body-->
			<!--begin::Content footer-->
			<div class="text-center">
				<div class="text-dark-50 font-size-lg font-weight-bolder mr-10">
					<span class="mr-1">{{ date("Y") }} &copy;</span>
					<a href="#" target="_blank" class="text-dark-75 text-hover-primary">{{ config('app.name') }}</a>
				</div>
				
			</div>
			<!--end::Content footer-->
		</div>
		<!--end::Content-->
	</div>
	<!--end::Login-->
</div>

<script>
	$(document).ready(function () {

		$("#reset_password_form").validate();

	});
</script>
@endsection
