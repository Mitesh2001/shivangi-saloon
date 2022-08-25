{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon-grid-menu text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Create Appointment') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ $back }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>  
<div class="card-body remove-padding-mobile">
    {!! Form::open([
            'route' => 'appointments.store',
            'class' => 'ui-form',
            'id' => 'AppointmentCreateForm'
    ]) !!}
        <input type="hidden" name="index_url" value="{{ $back }}">
        @include('appointments.form', ['submitButtonText' => __('Create New Appointment')])
 
    {!! Form::close() !!}
</div>
</div>

@include('leads.add_client_modal')
 
<!--end::Card-->

    <script>            
        $(document).ready(function () {
            
            $('#AppointmentCreateForm').submit(function (e) {

                e.preventDefault();

                if(is_valid_time_entry() === true) {
                    $("#AppointmentCreateForm").unbind('submit').submit();
                } else {
                    alert("End time should be greater then start time!");
                    return false;
                }

            });

            function is_valid_time_entry() 
            {
                let start_time = $("#start_at").val();
		        let end_time = $("#end_at").val(); 
  
                start_arr = start_time.split(":");   
                end_arr = end_time.split(":");  

                console.log(start_arr);
                console.log(end_arr);   

                start_time = new Date().setHours(start_arr[0], start_arr[1], 00);
                end_time = new Date().setHours(end_arr[0], end_arr[1], 00);

                if(start_time >= end_time) { 
                    return false;
                }  else {
                    return true;
                }
            }
 
            // $.validator.addMethod("checkTime", function(value, element) { 
            //     let start_time = $("#start_at").val();
		    //     let end_time = $("#end_at").val(); 
  
            //     start_arr = start_time.split(":");   
            //     end_arr = end_time.split(":");  

            //     console.log(start_arr);
            //     console.log(end_arr);   

            //     start_time = new Date().setHours(start_arr[0], start_arr[1], 00);
            //     end_time = new Date().setHours(end_arr[0], end_arr[1], 00);

            //     if(start_time >= end_time) { 
            //         return false;
            //     }  else {
            //         return true;
            //     }
            // }, "End time should be greater then start time!");
  
 
            $("#AppointmentCreateForm").validate({
                rules: {
                    client_external_id: {
                        required: true, 
                    }, 
                    distributor_id: {
                        required: true,
                    },
                    branch_id: {
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
                    user_assigned_id: {
                        required: true,
                    },
                    status_id: {
                        required: true,
                    },
                    'appointment_for[]': {
                        required: true,
                    },
                    date: {
                        required: true,
                    },
                    start_at: {
                        required: true,
                        // checkTime: true,
                    },
                    end_at: {
                        required: true,
                        // checkTime: true,
                    }
                },
                messages: { 
                    client_external_id: {
                        required: "Please select client name!",
                    }, 
                    distributor_id: {
                        required: "Plese select salon!",
                    },
                    branch_id: {
                        required: "Plese select branch!",
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
                    user_assigned_id: {
                        required: "Please select representative!",
                    },
                    status_id: {
                        required: "Please select appointment status!",
                    },
                    appointment_for: {
                        required: "Please enter appointment for!",
                    },
                    date: {
                        required: "Please enter date of appointment!",
                    },
                    start_at: {
                        required: "Please select appointment start time!",
                    },
                    end_at: {
                        required: "Please select appointment end time!",
                    }
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