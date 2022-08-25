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
            <h3 class="card-label">{{ __('Create Status') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('status.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'status.store',
        'class' => 'ui-form',
        'id' => 'statusCreateForm'
    ]) !!}
            
        @include('status.form', ['submitButtonText' => __('Create Status')])

    {!! Form::close() !!}  
    </div>
</div>
 
    <script>
        $(document).ready(function () {

            $("#statusCreateForm").validate({
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
