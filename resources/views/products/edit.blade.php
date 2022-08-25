{{-- Extends layout --}}
@extends('layouts.default')
  
@section('content')
@include('layouts.alert')
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-supermarket text-primary"></i>
            </span>
            <h3 class="card-label">
                {{ __('Edit product / Service :product' , ['product' => '(' . $product->name. ')']) }}
                @if($is_system_user == 0)
                    <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
                @endif 
            </h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('product.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">

        {!! Form::model($product, [
            'method' => 'PATCH',
            'route' => ['product.update', $product->external_id],
            'id' => 'productEditForm',
            'files' => true
        ]) !!}

            @include('products.form', ['submitButtonText' => __('Update Product/Service')])
    
        {!! Form::close() !!}
    </div>
    </div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $("#productEditForm").validate({
            rules: {
                name: {
                    required: true,
                },  
                sales_price: {
                    required: true,
                    number: true,
                }, 
                purchase_price: {
                    required: true,
                    number: true,
                }, 
                category_id: {
                    required: true,
                },  
                type: {
                    required: true,
                }, 
                sku_code: {
                    required: true,
                    remote: {
                        url: '{!! route('product.checkSKUCode') !!}',
                        type: "POST",
                        cache: false,
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: function () {
                                return $("#id").val();
                            },
                            salon_id: function () {
                                return $("#distributor_id").val();
                            }, 
                            sku_code: function () {
                                return $("#sku_code").val();
                            }, 
                        }
                    } 
                }, 
                thumbnail: { 
                    extension: "jpeg|jpg|png|gif"
                }, 
                description: {
                    maxlength: 350,
                }, 
                sgst: {
                    number: true,
                    required: true,
                }, 
                cgst: {
                    number: true,
                    required: true,
                }, 
                igst: {
                    number: true,
                    required: true,
                }, 
                expiry_reminder: {
                    number: true,
                    required: true,
                }, 
                reorder_qty: {
                    number: true,
                    required: true,
                }, 
                unit_id: {
                    required: function(element) {
                        var type = $("#type").val();
                        if(type == 0) { 
                            return true;
                        } else {
                            return false;
                        }
                    }
                }, 
            },
            messages: { 
                name: {
                    required: "Please enter product name!",
                }, 
                sales_price: {
                    required: "Please enter sales price!",
                    number: "Please valid sales price!",
                }, 
                purchase_price: {
                    required: "Please enter purchase price!",
                    number: "Please valid purchase price!",
                }, 
                category_id: {
                    required: "Please select product category!",
                },   
                type: {
                    required: "Please select type!",
                }, 
                sku_code: {
                    required: "Please enter SKU Code!", 
                    remote: "SKU code already exists!"
                }, 
                thumbnail: { 
                    extension: "Image type not supported (supported: jpeg, png, gif, svg)!",
                    filesize: "Thumbnail size can not me more then 2 mb!"
                }, 
                description: {
                    maxlength: "Description can not be more then 350 characters!",
                },  
                sgst: {
                    number: "Please enter valid sgst!",
                    required: "Please enter sgst!",
                }, 
                cgst: {
                    number: "Please enter valid cgst!",
                    required: "Please enter cgst!",
                }, 
                igst: {
                    number: "Please enter valid igst!",
                    required: "Please enter igst!",
                }, 
                expiry_reminder: {
                    number: "Please enter valid stock reminder days!",
                    required: "Please enter expiry reminder!",
                }, 
                reorder_qty: {
                    number: "Please enter valid reorder qty!",
                    required: "Please enter reorder qty!",
                },   
                unit_id: {
                    required: "Please select unit!",
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