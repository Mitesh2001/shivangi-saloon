{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title remove-flex">
        <span class="card-icon">
            <i class="flaticon2-list-3 text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit Category :category' , ['category' => '(' . $category->name. ')']) }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('category.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($category, [
        'method' => 'PATCH',
        'route' => ['category.update', $category->external_id],
        'id' => 'categoryEditForm'
    ]) !!}

        @include('categories.form', ['submitButtonText' => __('Update category')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#categoryEditForm").validate({
            rules: {
                name: {
                    required: true,
                }, 
            },
            messages: { 
                name: {
                    required: "Please enter name!",
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