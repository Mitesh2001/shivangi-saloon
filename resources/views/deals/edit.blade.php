{{-- Extends layout --}}
@extends('layouts.default')

@section('content')
@include('layouts.alert')
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-percentage text-primary"></i>
        </span>
        <h3 class="card-label">
            {{ __('Edit Deal :deal' , ['deal' => '(' . $deal->deal_name. ')']) }}
            @if($is_system_user == 0)
                <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
            @endif 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('deals.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">

    {!! Form::model($deal, [
        'method' => 'PATCH',
        'route' => ['deals.update', $deal->external_id],
        'id' => 'dealEditForm'
    ]) !!}

        @include('deals.form', ['submitButtonText' => __('Update Deal')])

    {!! Form::close() !!}
</div>
</div>
<!--end::Card-->

<script>
    $(document).ready(function () {

        $.validator.addMethod("alphaNumeric", function(value, element) {
            return this.optional(element) || /^[a-z 0-9\\]+$/i.test(value);
        }, "Deal code must contain only letters and numbers!");

        $.validator.addMethod("checkInvoiceMinMax", function(value, element) {
            let min_value = $("#invoice_min_amount").val();
            let max_value = $("#invoice_max_amount").val();
            if(min_value != "") {
                if(parseInt(min_value) > parseInt(max_value)) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }, "Invoice min ammount cannot greater than invoice max amount!");

        $.validator.addMethod("checkTime", function(value, element) { 
            let start_time = $("#start_at").val();
            let end_time = $("#end_at").val(); 

            start_arr = start_time.split(":");   
            end_arr = end_time.split(":");  

            console.log(start_arr);
            console.log(end_arr);   

            start_time = new Date().setHours(start_arr[0], start_arr[1], 00);
            end_time = new Date().setHours(end_arr[0], end_arr[1], 00);

            if(start_time >= end_time) { 
                return false;
            }  else {
                return true;
            }
        }, "End time should be greater then start time!");

        $("#dealEditForm").validate({
            rules: {
                segament_id: {
                    required: true,  
                },  
                deal_name: {
                    required: true,  
                },  
                deal_code: {
                    required: true,
                    alphaNumeric: true,  
                    maxlength: 25,
                    remote: {
                        url: '{!! route('deals.checkCode') !!}',
                        type: "POST",
                        cache: false,
                        data: {
                            _token: "{{ csrf_token() }}",
                            deal_code: function () {
                                return $("#deal_code").val();
                            },
                            id: function () {
                                return $("#id").val();
                            }
                        }
                    }
                },   
                deal_description: {
                     maxlength: 150,
                },
                validity: {
                    required: true,  
                },   
                start_at: {
                    // required: true,  
                    checkTime: true, 
                },   
                end_at: {
                    // required: true,  
                    checkTime: true, 
                },   
                applicable_on_weekends: {
                    required: true,  
                },   
                applicable_on_holidays: {
                    required: true,  
                },   
                applicable_on_bday_anniv: {
                    required: true,  
                },   
                week_days: {
                    required: true,  
                },   
                benefit_type: {
                    required: true,  
                },   
                invoice_min_amount: {
                    // required: true,  
                    number: true,
                    checkInvoiceMinMax: true,
                },   
                invoice_max_amount: {
                    // required: true,  
                    number: true,
                    checkInvoiceMinMax: true,
                },   
                redemptions_max: {
                    required: true,  
                    number: true,
                },   
                discount: {
                    required: true,  
                    number: true,
                },   
                'products[]' : {
                    required: function (element) {
                        if($("#apply_on_bill_total").prop("checked") == false){
                            return true;                          
                        } else {
                            return false;
                        }  
                    },
                },
            },
            messages: { 
                segament_id: {
                    required: "Please enter select customer segament!",  
                },     
                deal_name: {
                    required: "Please enter deal name!",  
                },  
                deal_code: {
                    required: "Please enter deal code!",  
                    maxlength: "Deal code may not be greater than 25 characters!", 
                    remote: "Deal code already exist!"
                },   
                deal_description: {
                     maxlength: "Deal code may not be greater than 150 characters!",
                },
                validity: {
                    required: "Please select deal validity date!",  
                },   
                start_at: {
                    // required: "Please select deal start time!",  
                },   
                end_at: {
                    // required: "Please select deal end time!",  
                },   
                applicable_on_weekends: {
                    required: "Please select applicable on weekends!",  
                },   
                applicable_on_holidays: {
                    required: "Please select applicable on holidays!",  
                },   
                applicable_on_bday_anniv: {
                    required: "Please select applicable on bday/anniv!",  
                },   
                week_days: {
                    required: "Please select week days!",  
                },   
                benefit_type: {
                    required: "Please select benefit type!",  
                },   
                invoice_min_amount: {
                    required: "Please enter min invoice amount!",  
                    number: "Please enter valid min invoice amount!",
                    checkInvoiceMinMax: "Invoice min ammount cannot greater than invoice max amount!",
                },   
                invoice_max_amount: {
                    // required: "Please enter max invoice amount!",  
                    number: "Please enter valid max invoie amount!",
                    checkInvoiceMinMax: "Invoice max ammount cannot less than invoice min amount!",
                },   
                redemptions_max: {
                    required: "Please enter redemptions max!",  
                    number: "Please enter valid redemptions max!",
                },   
                discount: {
                    required: "Please enter discount!",  
                    number: "Please enter valid discount!",
                }, 
                'products[]': {
                    required: "Please either select products or apply discount on bill amount!",
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