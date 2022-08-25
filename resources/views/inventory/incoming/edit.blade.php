{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-download text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Edit stock') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('incoming_inventory.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($stock_history, [
        'method' => 'POST',
        'route' => 'incoming_inventory.updateInvoice',
        'id' => 'StockEditForm',
        'files' => true
    ]) !!}

        @include('inventory.incoming.form', ['submitButtonText' => __('Update Stock')])

        <input type="hidden" name="old_products_array" value="{{ $stock_history->products_array }}">
        <input type="hidden" name="external_id" value="{{ $stock_history->external_id }}">
        
    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

    <script> 

        $(document).ready(function () {

            $(document).on('click', '.remove-product', function (e){  

                var product_id = $(e.target).data('product-id');
                var stock_history_id = $(e.target).data('stock-history-id');
                
                if(product_id == "") {
                    $('.tooltip').tooltip().remove(); 
                    $(e.target).closest('tr').remove();
                    return false;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#ccc',
                    confirmButtonText: 'remove!'
                }).then((result) => {
                    if (result.isConfirmed) { 
                        $.ajax({
                            url: '{!! route('incoming_inventory.removeProduct') !!}',
                            type: "POST",
                            dataType: 'json',
                            cache: false,
                            data: {
                                _token: "{{ csrf_token() }}", 
                                product_id: product_id,
                                stock_history_id: stock_history_id
                            },
                            success: function (res) {
                                $('.tooltip').tooltip().remove(); 
                                $(e.target).closest('tr').remove(); 
                                Swal.fire({
                                    title: 'Removed!',
                                    text: 'Product Removed Successfully!',
                                    icon: 'success',
                                    timer: 3000
                                })
                            }
                        }) 
                    } 
                }) 
            }); 


        $.validator.addMethod("checkInvoiceValue", function(value, element) { 
            let amount_paid = $("#amount_paid").val();
            let invoice_value = $("#invoice_value").val(); 
    
            if(amount_paid > invoice_value) {
                return false;
            } else {
                return true;
            } 
        }, "Invoice amount should not be greater then invoice value!");

        // using the class name instead of field name  
        jQuery.validator.addClassRules({
            "product_name" : {
                required: true
            },
            "mrp" : {
                required: true,
                number: true,
            },
            "qty" : {
                required: true,
                number: true,
            },
            "cost_per_unit" : {
                required: true,
                number: true,
            },
            "gst" : {
                required: true,
                number: true,
            },
        }); 

            $("#StockEditForm").validate({
                rules: {
                    distributor_id: {
                        required: true,
                    },
                    branch_id: {
                        required: true,
                    },
                    name: {
                        required: true,
                    },  
                    extra_freight_charges: {
                        number: true,  
                    }, 
                    sales_price: {
                        required: true,
                        number: true,
                    },  
                    invoice_value: {
                        required: true,
                        number: true, 
                    },
                    invoice_number: {
                        required: true,  
                        remote: {
                            url: '{!! route('incoming_inventory.checkInvoice') !!}',
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
                                invoice_number: function () {
                                    return $("#invoice_number").val();
                                }, 
                            }
                        }
                    },  
                    purchase_price: {
                        checkInvoiceValue: true,
                        required: true,
                        number: true,
                    }, 
                    category_id: {
                        required: true,
                    }, 
                    unit_id: {
                        required: true,
                    }, 
                    sku_code: {
                        required: true,
                        minlength: 10,
                        maxlength: 20,
                    }, 
                    thumbnail: { 
                        extension: "jpeg|jpg|png|gif"
                    }, 
                    description: {
                        maxlength: 350,
                    }, 
                },
                messages: {
                    distributor_id: {
                        required: "Plese select salon!",
                    }, 
                    branch_id: {
                        required: "Please select branch!",
                    },
                    name: {
                        required: "Please enter product name!",
                    }, 
                    extra_freight_charges: {
                        number: "Please enter valid extra freight charges",  
                    },
                    sales_price: {
                        required: "Please enter sales price!",
                        number: "Please valid sales price!",
                    }, 
                    purchase_price: {
                        required: "Please enter purchase price!",
                        number: "Please valid purchase price!",
                    }, 
                    invoice_value: {
                        required: "Please enter invoice value!",
                        number: "Please enter valide invoice value!", 
                    },  
                    invoice_number: {
                        required: "Please enter invoice number!", 
                        remote: "Invoice number already exist!" 
                    },
                    category_id: {
                        required: "Please select product category!",
                    }, 
                    unit_id: {
                        required: "Please select unit!",
                    }, 
                    sku_code: {
                        required: "Please enter SKU Code!",
                        minlength: "SKU Code must be 10 to 20 charecter long!",
                        maxlength: "SKU Code must be 10 to 20 charecter long!",
                    }, 
                    thumbnail: { 
                        extension: "Image type not supported (supported: jpeg, png, gif, svg)!",
                        filesize: "Thumbnail size can not me more then 2 mb!"
                    }, 
                    description: {
                        maxlength: "Description can not be more then 350 characters!",
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