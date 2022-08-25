@extends('layouts.default')
@section('content') 
@include('layouts.alert')
 
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-menu text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Create Client') }}</h3>
    </div> 
    <div class="mt-3">
        <a href="{{ route('clients.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
  
    {!! Form::open([
        'route' => 'clients.store',
        'class' => 'ui-form',
        'id' => 'clientCreateForm'
    ]) !!}
        @include('clients.form', ['submitButtonText' => __('Create New Client')])

    {!! Form::close() !!}

    </div>
</div>

    <script>    
        $(document).ready(function () { 

            $("#clientCreateForm").validate({
                rules: {
                    name: {
                        required: true,
                    }, 
                    distributor_id: {
                        required: true,
                    },
                    email: { 
                        email: true,
                        remote: {
                            url: '{!! route('clients.checkemail') !!}',
                            type: "POST",
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: function () {
                                    return $("#id").val();
                                },
                                email: function () {
                                    return $("#email").val();
                                }, 
                                distributor_id: function () {
                                    return $("#distributor_id").val();
                                },
                            }
                        }
                    }, 
                    primary_number: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                        remote: {
                            url: '{!! route('clients.checkPrimaryNumber') !!}',
                            type: "POST",
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}",
                                number: function () {
                                    return $("#primary_number").val();
                                },
                                id: function () {
                                    return $("#id").val();
                                },
                                distributor_id: function () {
                                    return $("#distributor_id").val();
                                },
                            }
                        }
                    }, 
                    secondary_number: { 
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    }, 
                    user_id: { 
                        required: true, 
                    },  
                    state_id: {
                        required: true,
                    },
                    state_name: {
                        required: true,
                    },
                    country_id: {
                        required: true
                    },
                    client_type: {
                        required: true
                    },
                },
                messages: { 
                    name: {
                        required: "Please enter name!",
                    }, 
                    distributor_id: {
                        required: "Plese select salon!",
                    },
                    email: { 
                        email: "Please enter valid email address!",
                        remote: "Email already exist!",
                    },
                    primary_number: {
                        required: "Please enter contact number!",
                        number: "Please enter valid contact number!",
                        minlength: "Please enter valid contact number!",
                        maxlength: "Please enter valid contact number!",
                        remote: "Number already exist!"
                    }, 
                    secondary_number: { 
                        number: "Please enter valid WhatsApp number!",
                        minlength: "Please enter valid WhatsApp number!",
                        maxlength: "Please enter valid WhatsApp number!",
                    }, 
                    user_id: { 
                        required: "Please select user!", 
                    }, 
                    state_id: {
                        required: "Please select state!",
                    },
                    state_name: {
                        required: "Please enter state!",
                    },
                    country_id: {
                        required: "Please select country!",
                    },
                    client_type: {
                        required: "Please select client type!",
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
