@extends('layouts.default')
@section('content') 

<?php
    $data = Session::get('data');
?>
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title remove-flex">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Create Role') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('roles.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'roles.store',
        'class' => 'ui-form',
        'id' => 'roleCreateForm',
        'files' => true
    ]) !!}
    
        @include('roles.form', ['submitButtonText' => __('Create New Role')])

    {!! Form::close() !!}
    </div>
</div>
<!--end::Card-->

    <script>
        $(document).ready(function () {
            $("#roleCreateForm").validate({
                rules: {
                    name: {
                        required: true,
                    },  
                },
                messages: { 
                    name: {
                        required: "Please enter role name!",
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
                    $(element).closest('.form-group-error').append(error);
                }
            });

        });
    </script>

@stop
