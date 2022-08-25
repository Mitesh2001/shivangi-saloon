@extends('layouts.default')

@section('content')
    @push('scripts') 
    @endpush 
    <?php
        $data = Session::get('data');
    ?>

<div class="card card-custom">
    <div class="card-header justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-list-3 text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Create Branch') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('branch.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body">
    {!! Form::open([
        'route' => 'branch.store',
        'class' => 'ui-form',
        'id' => 'branchCreateForm'
    ]) !!}
            
        @include('branch.form', ['submitButtonText' => __('Create Branch')])

    {!! Form::close() !!}  
    </div>
</div>
 
    <script>
        $(document).ready(function () {

            $("#branchCreateForm").validate({
                rules: {
                    name: {
                        required: true, 
                    },   
                    primary_contact_person: {
                        required: true, 
                    }, 
                    primary_contact_number: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    }, 
                    secondary_contact_number: { 
                        number: true,
                        minlength: 10,
                        maxlength: 10,
                    },
                    primary_email: { 
                        email: true,
                    }, 
                    secondary_email: { 
                        email: true,
                    }, 
                },
                messages: { 
                    name: {
                        required: "Please enter branch name!", 
                    },   
                    primary_contact_person: {
                        required: "Please select primary contact person!", 
                    }, 
                    primary_contact_number: {
                        required: "Please enter primary contact number!",
                        number: "Please enter valid primary contact number!",
                        minlength: "Please enter valid primary contact number!",
                        maxlength: "Please enter valid primary contact number!",
                    }, 
                    secondary_contact_number: { 
                        number: "Please enter valid secondary contact number!",
                        minlength: "Please enter valid secondary contact number!",
                        maxlength: "Please enter valid secondary contact number!",
                    },
                    primary_email: { 
                        email: "Please enter valid primary email!",
                    }, 
                    secondary_email: { 
                        email: "Please enter valid secondary email!",
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
