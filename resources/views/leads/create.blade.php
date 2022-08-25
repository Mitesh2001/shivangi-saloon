{{-- Extends layout --}}
@extends('layouts.default')
@include('layouts.alert')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-phone text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Create Inquiry') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('leads.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>  
<div class="card-body remove-padding-mobile">
    {!! Form::open([
            'route' => 'leads.store',
            'class' => 'ui-form',
            'id' => 'leadsCreateForm'
    ]) !!}
        @include('leads.form', ['submitButtonText' => __('Create New Inquiry')])
 
    {!! Form::close() !!}
</div>
</div>

@include('leads.add_client_modal')
 
<!--end::Card-->

    <script>            
        $(document).ready(function () {

            $("#leadsCreateForm").validate({
                rules: {
                    client_external_id: {
                        required: true,
                    }, 
                    distributor_id: {
                        required: true,
                    },
                    contact_number: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    }, 
                    email: { 
                        email: true,
                    }, 
                    address: {
                        required: true, 
                    }, 
                    // enquiry_for: {
                    //     required: true, 
                    // }, 
                    enquiry_type: {
                        required: true, 
                    }, 
                    enquiry_response: {
                        required: true, 
                    }, 
                    date_to_follow: {
                        required: true, 
                    }, 
                    branch_id: {
                        required: true, 
                    }, 
                    enquiry_source: {
                        required: true, 
                    }, 
                    user_assigned_id: {
                        required: true, 
                    }, 
                    status_id: {
                        required: true, 
                    }, 
                },
                messages: { 
                    client_external_id: {
                        required: "Please select client name!",
                    }, 
                    distributor_id: {
                        required: "please select salon!",
                    },
                    contact_number: {
                        required: "Please enter contact number!",
                        number: "Please enter valid contact number!",
                        minlength: "Please enter valid contact number!",
                        maxlength: "Please enter valid contact number!",
                    },
                    email: { 
                        email: "Please enter valid email address!",
                    },
                    address: { 
                        required: "Please enter address!", 
                    },
                    // enquiry_for: {
                    //     required: "Please enter inquiry for!", 
                    // },
                    enquiry_type: {
                        required: "Please enter inquiry type!", 
                    },
                    enquiry_response: {
                        required: "Please enter inquiry response!", 
                    }, 
                    date_to_follow: {
                        required: "Please select date to follow!", 
                    }, 
                    branch_id: {
                        required: "Please select branch!",
                    }, 
                    enquiry_source: {
                        required: "Please enter source of inquiry!", 
                    },
                    user_assigned_id: {
                        required: "Please select Lead Representative!", 
                    }, 
                    status_id: {
                        required: "Please select Lead Status!", 
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
                    $(element).closest('.col-lg-3').append(error);
                }
            });

        });
    </script>
@stop