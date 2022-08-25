{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Inquiry Type :enquiry_type' , ['enquiry_type' => '(' . $enquiry_type->name. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('enquirytype.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($enquiry_type, [
        'method' => 'PATCH',
        'route' => ['enquirytype.update', $enquiry_type->external_id],
        'id' => 'enquirytypeEditForm'
    ]) !!}

        @include('enquirytype.form', ['submitButtonText' => __('Update Enquiry Type')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
$(document).ready(function () { 
    $("#enquirytypeEditForm").validate({
        rules: {
            name: {
                required: true,
                remote: {
                    url: '{!! route('enquirytype.checkname') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: function () {
                            return $("#name").val();
                        },
                        id: function () {
                            return $("#id").val();
                        }
                    }
                }
            }, 
        },
        messages: { 
            name: {
                required: "Please enter enquiry name!", 
                remote: "Name already exist!"
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
            $(element).closest('.col-lg-6').append(error);
        }
    }); 
});
</script> 

@stop