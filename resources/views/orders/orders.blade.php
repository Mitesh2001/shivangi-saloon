@extends('layouts.default')

@section('content')
    @push('scripts') 
    @endpush 
    <?php
        $data = Session::get('data');
    ?>
<div class="row">
	<div class="col-lg-12">   
		@if(Session::has('success')) 
			<div class="alert alert-success" role="alert">
				{{Session::get('success') }}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="ki ki-close"></i></span>
				</button>
			</div>
		@endif
        @include('layouts.alert')
	</div>
</div>
<div class="card card-custom">
    <div class="card-header justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-list-3 text-primary"></i>
            </span>
            <h3 class="card-label">{{ __(!empty($order) ? 'Update Order' : 'Create Order') }}</h3>

            @if(!empty($client_data))
				<span class="text-muted"> ( Client Name : {{isset($client_data->name) ? $client_data->name : null}} )</span>
			@endif
        </div>
        <div class="mt-3">
        <a href="{{ route('orders.index', ['client_id' => encrypt($client_id)]) }}" class="btn btn-light-primary font-weight-bold">Back</a> 
        </div>
    </div>
    <div class="card-body remove-padding-mobile">  
		@if(isset($order))
			{!! Form::model($order, [
            'method' => 'PATCH',
            'route' => ['orders.update', $order->id],
			'enctype' => 'multipart/form-data',
            'id' => 'orderUpdateForm'
            ]) !!}
		@else
        {!! Form::open([
                'route' => 'orders.store',
                'class' => 'ui-form',
                'id' => 'orderCreateForm'
                ]) !!}
		@endif

        @if(isset($order))
            @include('orders.update_form', ['submitButtonText' => __(!empty($order) ? 'Update Order' : 'Save Order')])
        @else
            @include('orders.form', ['submitButtonText' => __(!empty($order) ? 'Update Order' : 'Save Order')])
        @endif
                 
        {!! Form::close() !!}
    </div>
</div>
 
<script>
    jQuery(function() {
        getCompanyDetails('<?php echo encrypt($client_id)?>');
    });
    $(document).ready(function () {

        $(document).on('click', '.btn-cancel', function (){
            location.reload();
        });

        function toggleSubmitButton()
        {
            let deal_id = $("#deal_id").val();
            let discount_code = $("#discount_code").val();

            if(deal_id == 0 && discount_code.length > 0) {
                $("#submitOrder").attr('disabled', true);
            }
            if(discount_code.length == 0) {
                $("#submitOrder").removeAttr('disabled');
                $("#deal_id").val("");
            }
        }

        $(document).on('click', '#remove_code', function (e) {
            $("#discount_code").val('');
            $("#discount_code").removeAttr('readonly');
            $("#deal_id").val(0);

            $("#apply_code_button").removeClass('d-none');
            $("#remove_code_button").addClass('d-none');

            $('.deal-discount-input').each(function (index, input) { 
                $(input).val(0).trigger('change'); 
            }); 

            $("#submitOrder").removeAttr('disabled');
        });

        $(document).on('change', "#discount_code", function (e) {

            let discount_code = e.target.value;
            let deal_id = $("#deal_id").val();

            if(discount_code.length > 0 && deal_id == 0) { 
                $("#submitOrder").attr('disabled', true);
            }
            if(discount_code.length == 0) {
                $("#submitOrder").removeAttr('disabled');
                $("#deal_id").val("");
            }
            
        });

        $(document).on('click', '#apply_code', function () {
            let order_date = $("#order_date").val(); 
            let discount_code = $("#discount_code").val();

            if(discount_code.length == 0) {
                alert("Please enter discount code!");
                return false;
            }
            if(order_date.length == 0) {
                alert("Please select order date first!");
                return false;
            } 
            
            let form_data = $("#orderCreateForm").serialize();

            $.ajax({
                url: '{!! route('orders.applyCode') !!}',
                type: "POST",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    form_data: form_data
                },
                success: function (res) {
                    console.log(res);
                    if(res.status == false) {
                        if(res.description == false) {
                            Swal.fire({         
                                icon: 'error',
                                title: "invalid code!",  
                                text: res.message,
                            }); 
                        } else {
                            Swal.fire({ 
                                icon: 'error',
                                title: "invalid code!", 
                                html: res.description,
                            }); 
                        }  
                        return false;
                    }

                    if(res.status == true) {

                        $("#discount_code").attr('readonly', true);
                        $("#apply_code_button").addClass('d-none');
                        $("#remove_code_button").removeClass('d-none');

                        var deal_id = res.deal_id;
                        var discount = res.discount; 

                        $("#deal_id").val(deal_id);
                        
                        if(res.applicable_on == "products"){ 
                            
                            res.products.forEach(function (id) { 
                                $("#deal_discount_"+id).val(discount).trigger('change'); 
                            }); 
                            Swal.fire({ 
                                icon: 'success',
                                title: "Success!", 
                                html: "Discount code successfully applied!",
                            });  
                        } else { 

                            $('.deal-discount-input').each(function (index, input) { 
                                $(input).val(discount).trigger('change'); 
                            }); 
                            Swal.fire({ 
                                icon: 'success',
                                title: "Success!", 
                                html: "Discount code successfully applied!",
                            }); 
                            $("#submitOrder").removeAttr('disabled');
                        } 
                    } 
                }
            });
        });
 

    });
</script>
@stop
