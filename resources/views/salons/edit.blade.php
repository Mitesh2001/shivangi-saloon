{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Salon :salon' , ['salon' => '(' . $distributor->name. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('salons.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($distributor, [
        'method' => 'PATCH',
        'route' => ['salons.update', $distributor->external_id],
        'id' => 'salonCreate',
        'files' => true,
    ]) !!}

        @include('salons.form', ['submitButtonText' => __('Update Salon')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#salonCreate").validate({
            rules: {
                name: {
                    required: true,  
                },  
                gst_number: {
                    minlength: 15,  
                    maxlength: 15,
                },  
                primary_number: {
                    required: true,
                    number: true,
                    minlength: 10,  
                    maxlength: 10,
                    remote: {
                        url: '{!! route('salons.checkPrimaryNumber') !!}',
                        type: "POST",
                        cache: false,
                        data: {
                            _token: "{{ csrf_token() }}",
                            number: function () {
                                return $("#primary_number").val();
                            },
                            id: function () {
                                return $("#id").val();
                            }
                        }
                    }
                },  
                secondary_number: { 
                    number: true,
                    minlength: 10,  
                    maxlength: 10,
                },  
                primary_email: { 
                    required: true,
                    email: true,
                    remote: {
                        url: '{!! route('salons.checkPrimaryEmail') !!}',
                        type: "POST",
                        cache: false,
                        data: {
                            _token: "{{ csrf_token() }}",
                            email: function () {
                                return $("#primary_email").val();
                            },
                            id: function () {
                                return $("#id").val();
                            }
                        }
                    }
                },  
                secondary_email: { 
                    email: true,
                },   
                contact_person: { 
                    required: true,
                },  
                contact_person_number: { 
                    required: true,
                    number: true,
                    minlength: 10,  
                    maxlength: 10,
                },  
                contact_person_email: { 
                    required: true,
                    email: true,
                },
                city: { 
                    required: true,
                },
                sender_id: { 
                    required: true,
                },
                from_email: { 
                    required: true,
                    email: true,
                },
                from_name: { 
                    required: true,  
                },
            },
            messages: { 
                name: {
                    required: "Please enter package name!",  
                },    
                gst_number: {
                    minlength: "Please enter valid gst number!",  
                    maxlength: "Please enter valid gst number!",  
                },    
                primary_number: { 
                    required: "Please enter primary number!",
                    number: "Please enter valid primary number!",  
                    minlength: "Please enter valid primary number!",  
                    maxlength: "Please enter valid primary number!",
                    remote: "Primary number already exist!"
                },
                secondary_number: { 
                    number: "Please enter valid secondary number!",  
                    minlength: "Please enter valid secondary number!",  
                    maxlength: "Please enter valid secondary number!",
                }, 
                primary_email: { 
                    required: "Please enter primary email!",
                    email: "Please enter valid primary email!",
                    remote: "Primary email already exist!"
                }, 
                secondary_email: { 
                    email: "Please enter valid secondary email!",
                },
                contact_person: { 
                    required: "Please enter contact person name!",
                }, 
                contact_person_number: { 
                    required: "Please enter contact person number!",
                    number: "Please enter valid contact person number!",  
                    minlength: "Please enter valid contact person number!",  
                    maxlength: "Please enter valid contact person number!",
                },
                contact_person_email: { 
                    required: "Please enter contact person email!",
                    email: "Please enter valid contact person email!",
                },
                city: { 
                    required: "Please enter city!",
                },
                sender_id: { 
                    required: "Please enter sender id!",
                },
                from_email: { 
                    required: "Please enter from email",
                    email: "Please enter valid email address!",
                },
                from_name: { 
                    required: "Please enter from name!",  
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