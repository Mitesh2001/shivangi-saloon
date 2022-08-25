{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title remove-flex">
        <span class="card-icon">
            <i class="flaticon2-percentage text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Deal :deal' , ['deal' => '(' . $deal->deal_name. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('campaigns.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body">

    {!! Form::model($deal, [
        'method' => 'PATCH',
        'route' => ['deals.update', $deal->external_id],
        'id' => 'dealEditForm'
    ]) !!}

        @include('deals.form', ['submitButtonText' => __('Update Deal')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $.validator.addMethod("alphaNumeric", function(value, element) {
            return this.optional(element) || /^[a-z 0-9\\]+$/i.test(value);
        }, "Deal code must contain only letters and numbers!");

        $("#dealEditForm").validate({
            rules: {
                name: {
                    required: true,  
                    alphaNumeric: true, 
                },  
                subject: {
                    required: true,
                    alphaNumeric: true
                }, 
                message: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter campaign name",  
                    alphaNumeric: "Campaign must contain only letters and numbers!", 
                },  
                subject: {
                    required: "Please enter subject!",
                    alphaNumeric: "Subject must contain only letters and numbers!"
                }, 
                message: {
                    required: "Please Enter Message!",
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