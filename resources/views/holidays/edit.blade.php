{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit Holiday :holiday' , ['holiday' => '(' . $holiday->name. ')']) }}
            @if($is_system_user == 0)
                <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
            @endif 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('holidays.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($holiday, [
        'method' => 'PATCH',
        'route' => ['holidays.update', $holiday->external_id],
        'id' => 'holidayEditForm'
    ]) !!}

        @include('holidays.form', ['submitButtonText' => __('Update Holday')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#holidayEditForm").validate({
            rules: {
                name: {
                    required: true, 
                    remote: {
                        url: '{!! route('holidays.checkname') !!}',
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
                date: {
                    required: true,
                },
            },
            messages: { 
                name: {
                    required: "Please enter holiday name!", 
                    remote: "Holiday name already exist!",
                }, 
                date: {
                    required: "Please select holiday date!",
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