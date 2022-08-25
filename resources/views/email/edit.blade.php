{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon-shopping-basket text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit Email Template :email' , ['email' => '(' . $email->name. ')']) }}
            @if($is_system_user == 0)
                @if($email->default_template == 1)
                    <span class="text-muted">Default Template</span>
                @else 
                    <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
                @endif
            @endif 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('emails.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($email, [
        'method' => 'PATCH',
        'route' => ['emails.update', encrypt($email->email_template_id)],
        'id' => 'EmailEditForm'
    ]) !!}

        @include('email.add_form', ['submitButtonText' => __('Update Vendor')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
$(document).ready(function (){
    $("#SMSEditForm").validate({
        rules: {
            name: {
                required: true,  
            },  
            message: {
                required: true,  
            },   
            event_date: {
                required: true,
            },  
            before_days: {
                required: true,   
            },  
            distributor_id: {
                required: true,
            }, 
        },
        messages: { 
            name: {
                required: "Please enter template name!",    
            },  
            message: {
                required: "please select salon!",
            },     
            event_date: {
                required:  "Please select event date!",
            },  
            before_days: {
                required:  "Please enter event (days)!",
                number: "Please enter valid number!"  
            },  
            distributor_id: {
                required: "please select salon!",
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
})

</script>

@stop