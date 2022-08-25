{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-location text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit Branch :branch' , ['branch' => '(' . $branch->name. ')']) }}
            @if($is_system_user && $distributor_title)
                <span class='text-muted'>( {!! "Salon : ". $selected_distributor->name ?? "" !!} )</span>
            @endif
        </h3>
    </div>
    <div class="mt-3"> 
        <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a> 
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($branch, [
        'method' => 'PATCH',
        'route' => ['branch.update', $branch->external_id],
        'id' => 'branchEditForm'
    ]) !!}

        @include('branch.form', ['submitButtonText' => __('Update Branch')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

    <script>
        $(document).ready(function () { 
            $("#branchEditForm").validate({
                rules: {
                    name: {
                        required: true, 
                    },   
                    // primary_contact_person: {
                    //     required: true, 
                    // }, 
                    primary_contact_number: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                        remote: {
                            url: '{!! route('branch.checkPrimaryNumber') !!}',
                            type: "POST",
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}",
                                number: function () {
                                    return $("#primary_contact_number").val();
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
                    secondary_contact_number: { 
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    },
                    primary_email: { 
                        email: true,
                        remote: {
                            url: '{!! route('branch.checkPrimaryEmail') !!}',
                            type: "POST",
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}",
                                email: function () {
                                    return $("#primary_email").val();
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
                    secondary_email: { 
                        email: true,
                    }, 
                    country_id: { 
                        required: true,
                    }, 
                    state_id: { 
                        required: true,
                    }, 
                    state_name: { 
                        required: true,
                    }, 
                    city: { 
                        required: true,
                    }, 
                    zipcode: { 
                        required: true,
                    }, 
                },
                messages: { 
                    name: {
                        required: "Please enter branch name!", 
                    },   
                    // primary_contact_person: {
                    //     required: "Please select primary contact person!", 
                    // }, 
                    primary_contact_number: {
                        required: "Please enter primary contact number!",
                        number: "Please enter valid primary contact number!",
                        minlength: "Please enter valid primary contact number!",
                        maxlength: "Please enter valid primary contact number!",
                        remote: "Primary number already exist!"
                    }, 
                    secondary_contact_number: { 
                        number: "Please enter valid secondary contact number!",
                        minlength: "Please enter valid secondary contact number!",
                        maxlength: "Please enter valid secondary contact number!",
                    },
                    primary_email: { 
                        email: "Please enter valid primary email!",
                        remote: "Primary email already exist!",
                    }, 
                    secondary_email: { 
                        email: "Please enter valid secondary email!",
                    }, 
                    country_id: { 
                        required: "Please select country!",
                    }, 
                    state_id: { 
                        required: "Please select state!",
                    }, 
                    state_name: { 
                        required: "Please enter state name!",
                    }, 
                    city: { 
                        required: "Please enter city!",
                    }, 
                    zipcode: { 
                        required: "Please enter zip code!",
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