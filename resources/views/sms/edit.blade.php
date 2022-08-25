{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon-chat-1 text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit SMS Template :sms' , ['sms' => '(' . $sms->name. ')']) }}
            @if($is_system_user == 0)
                <span class="text-muted">( Salon : {{ $distributor->name ?? "Default Template" }} )</span>
            @endif 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('sms.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($sms, [
        'method' => 'PATCH',
        'route' => ['sms.update', $sms->external_id],
        'id' => 'SMSEditForm'
    ]) !!}

        @include('sms.form', ['submitButtonText' => __('Update SMS Template')])

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
                remote: {
                    url: '{!! route('sms.checkname') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: function () {
                            return $("#name").val();
                        },
                        id: function () {
                            return $("#id").val();
                        },
                        distributor_id: function () {
                            return $("#distributor_id").val();
                        }
                    },
                },
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
                remote: "SMS Template already exist with this name!",   
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