@extends('layouts.default')

@section('content')
@include('layouts.alert')
    <?php
        $data = Session::get('data');
    ?>

<div class="card card-custom">
    <div class="card-header justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-download text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Add Stock') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('incoming_inventory.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'incoming_inventory.store',
        'class' => 'ui-form',
        'id' => 'AddStockForm'
    ]) !!}
            
        @include('inventory.incoming.form', ['submitButtonText' => __('Add Stock')])

    {!! Form::close() !!}  
    </div>
</div>
 
<script>
    $(document).ready(function () {
        
        // Remove Product TR 
        $(document).on('click', '.remove-product', function (e){  
            $('.tooltip').tooltip().remove(); 
            $(e.target).closest('tr').remove(); 
        }); 

        $.validator.addMethod("checkInvoiceValue", function(value, element) { 
            let amount_paid = parseInt($("#amount_paid").val());
            let invoice_value = parseInt($("#invoice_value").val()); 

            if(amount_paid >= invoice_value) {
                $("#payment_status").val("Paid");
                $("#payment_status").attr("Disabled", true);
            } else {
                $("#payment_status").val("Paid");
                $("#payment_status").attr("Disabled", false);
            }
            
            if(amount_paid > invoice_value) {
                console.log(amount_paid, invoice_value);
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

        $("#AddStockForm").validate({
            rules: {
                branch_id: {
                    required: true,
                },
                distributor_id: {
                    required: true,
                },
                date: {
                    required: true,  
                },  
                extra_freight_charges: {
                    number: true,  
                }, 
                invoice_value: {
                    required: true,
                    number: true, 
                    checkInvoiceValue: true,
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
                            }
                        }
                    }
                },  
                source_type: {
                    required: true,  
                },  
                source_id: {
                    required: true,  
                },   
                amount_paid: {
                    checkInvoiceValue: true,
                    required: true,
                    number: true,  
                },  
                payment_type: {
                    required: true,  
                },  
            },
            messages: { 
                distributor_id: {
                    required: "Plese select salon!",
                },
                branch_id: {
                    required: "Please select branch!",
                },
                date: {
                    required: "Please select date!",  
                }, 
                extra_freight_charges: {
                    number: "Please enter valid extra freight charges",  
                }, 
                invoice_value: {
                    required: "Please enter invoice value!",
                    number: "Please enter valide invoice value!", 
                },  
                invoice_number: {
                    required: "Please enter invoice number!", 
                    remote: "Invoice number already exist!" 
                },  
                source_type: {
                    required: "Please select source type!",  
                },  
                source_id: {
                    required: "Please select source!",  
                },   
                amount_paid: {
                    required: "Please enter amount paid!", 
                    number: "please enter valida amount paid!",  
                },  
                payment_type: {
                    required: "Please select payment type!",  
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
