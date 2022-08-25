@extends('layouts.default')

@section('content')
    @push('scripts') 
    @endpush 
    <?php
        $data = Session::get('data');
    ?>

<div class="card card-custom">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-list-3 text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Create Inquiry Type') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('enquirytype.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'enquirytype.store',
        'class' => 'ui-form',
        'id' => 'enquirytypeCreateForm'
    ]) !!}
            
        @include('enquirytype.form', ['submitButtonText' => __('Create New Enquiry Type')])

    {!! Form::close() !!}  
    </div>
</div>
 
    <script>
        $(document).ready(function () {

            $("#enquirytypeCreateForm").validate({
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
