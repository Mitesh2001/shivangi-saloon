@extends('layouts.default')
@section('content')
@push('scripts')
    <script>
        $(document).ready(function () { 
            
        });
    </script>
@endpush

<div class="row">
	@if(Session::has('success')) 
	<div class="col-lg-12">   
		<div class="alert alert-success" role="alert">
			{{Session::get('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="ki ki-close"></i></span>
			</button>
		</div>
	</div>
	@endif
    @if($errors->any())
        {!! implode('', $errors->all('<div>:message</div>')) !!}
    @endif
</div>
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon-list-2 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Create Plan') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('plans.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'plans.store',
        'class' => 'ui-form',
        'id' => 'planCreateForm',
        'files' => true
    ]) !!}
    
        @include('plans.form', ['submitButtonText' => __('Create New Plan')])

    {!! Form::close() !!}
    </div>
</div>
<!--end::Card-->

    <script>
        $(document).ready(function () {
            $("#planCreateForm").validate({
                rules: { 
                    name: {
                        required: true,
                    },   
                    price: {
                        required: true,
                        number: true,
                    }, 
                    no_of_branches: {
                        required: true,
                        number: true,
                    },  
                    no_of_users: {
                        required: true,
                        number: true,
                    },    
                    no_of_email: {
                        required: true,
                        number: true,
                    },    
                    no_of_sms: {
                        required: true,
                        number: true,
                    },    
                    duration_months: {
                        required: true,
                        number: true,
                    }, 
                    sgst: {
                        required: true,
                        number: true,
                    },
                    cgst: {
                        required: true,
                        number: true,
                    },
                    igst: {
                        required: true,
                        number: true,
                    },   
                },
                messages: {   
                    name: {
                        required: "Please enter product name!", 
                    },   
                    price: {
                        required: "Please enter price!",
                        number: "Please enter vaild price!",
                    },  
                    no_of_branches: {
                        required: "Please enter number of branches!",
                        number: "Please enter vaild number of branches!",
                    },  
                    no_of_users: {
                        required: "Please enter number of users!",
                        number: "Please enter vaild number of users!",
                    },    
                    no_of_email: {
                        required: "Please enter number of email!",
                        number: "Please enter vaild number of email!",
                    },    
                    no_of_sms: {
                        required: "Please enter number of sms!",
                        number: "Please enter vaild number of sms!",
                    },    
                    duration_months: {
                        required: "Please enter duration in months!",
                        number: "Please enter vaild duration in months!",
                    }, 
                    sgst: {
                        required: "Please enter sgst!",
                        number: "Please enter sgst in range of 1 to 100!",
                    },
                    cgst: {
                        required: "Please enter cgst!",
                        number: "Please enter cgst in range of 1 to 100!",
                    },
                    igst: {
                        required: "Please enter igst!",
                        number: "Please enter igst in range of 1 to 100!",
                    },
                },
                normalizer: function( value ) { 
                    return $.trim( value );
                },
                errorElement: "span",
                errorClass: "form-text text-danger",
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    $(element).closest('.form-group-error').append(error);
                }
            });

        });
    </script>

@stop
