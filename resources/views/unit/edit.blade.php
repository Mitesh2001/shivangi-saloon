{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Unit :unit' , ['unit' => '(' . $unit->name. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('unit.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($unit, [
        'method' => 'PATCH',
        'route' => ['unit.update', $unit->external_id],
        'id' => 'unitEditForm'
    ]) !!}

        @include('unit.form', ['submitButtonText' => __('Update Unit')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#unitEditForm").validate({
            rules: {
                name: {
                    required: true, 
                    remote: {
                        url: '{!! route('unit.checkname') !!}',
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
                        },
                    },
                },  
            },
            messages: { 
                name: {
                    required: "Please enter unit name!", 
                    remote: "Unit already exist!",
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