{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title remove-flex">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Status :status' , ['status' => '(' . $status->title. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('status.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($status, [
        'method' => 'PATCH',
        'route' => ['status.update', $status->external_id],
        'id' => 'statusEditForm'
    ]) !!}

        @include('status.form', ['submitButtonText' => __('Update Status')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
        $(document).ready(function () {

            $("#statusEditForm").validate({
                rules: {
                    title: {
                        required: true,
                        remote: {
                            url: '{!! route('status.checkname') !!}',
                            type: "POST",
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}",
                                title: function () {
                                    return $("#title").val();
                                },
                                id: function () {
                                    return $("#id").val();
                                }
                            }
                        }
                    }, 
                    status: {
                        required: true,
                    }
                },
                messages: { 
                    title: {
                        required: "Please enter enquiry title!", 
                        remote: "Title already exist!"
                    },  
                    status: {
                        required: "Please select color!",
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
                    $(element).closest('.col-lg-6').append(error);
                }
            });

        });
    </script>

@stop