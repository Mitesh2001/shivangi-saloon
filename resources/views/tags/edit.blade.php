{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title remove-flex">
        <span class="card-icon">
            <i class="flaticon2-tag text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit Tag :tag' , ['tag' => '(' . $tag->name. ')']) }}
            @if($is_system_user == 0)
                <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
            @endif 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('tags.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body">

    {!! Form::model($tag, [
        'method' => 'PATCH',
        'route' => ['tags.update', $tag->external_id],
        'id' => 'tagEditForm'
    ]) !!}

        @include('tags.form', ['submitButtonText' => __('Update Tag')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#tagEditForm").validate({
            rules: {
                name: {
                    required: true, 
                    remote: {
                        url: '{!! route('tags.checkname') !!}',
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
                            distributor_id : function () {
                                return $("#distributor_id").val();
                            },
                        },
                    },
                },   
            },
            messages: { 
                name: {
                    required: "Please enter tag name!", 
                    remote: "Tag name already exist!",
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