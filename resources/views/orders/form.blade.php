<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body"> 
			<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $client_data->distributor_id }}">

			<div class="row">
				@if(isset($is_system_user) && $is_system_user == 0 && !isset($order))
				<div class="form-group col-sm-4">
					{!! Form::label('branch_id', __('Branch'), ['class' => '']) !!}
					<span>*</span>
					{!!
					Form::select('branch_id',
						$branches,
						$selected_branch ?? null,
						['class' => 'form-control',
						'placeholder'=>'Select Branch',
						'id' => 'branch_id', 
						'required' => true])
					!!}
					<span class="form-text text-muted">Please select Branch</span>
				</div> 
				@else
				<input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}"> 
				@endif
			</div> 
            <div class="row"> 
				@if(!isset($order))
				<div class="form-group col-sm-4">
                    {{Form::hidden('client_id',$client_id)}}
                    {!! Form::label('product', __('Product/Service'), ['class' => '']) !!}
                    <span>*</span>
                    <div class="input-group">
                        {!!
                        Form::select('product',
                        [],
                        null,
                        ['class' => 'form-control product-select',
                        'id' => 'product-select'])
                        !!}
                        <div class="input-group-append">
                            <span class="input-group-text bg-primary" type="button" onClick="addProduct()"
                                data-toggle="tooltip" title="Add Product / Service">
                                <i class="fas fa-plus text-white"></i>
                            </span>
                        </div>
                    </div>
                    <span class="form-text text-muted">Please select Product</span>
                </div> 
				<div class="form-group col-sm-4 offset-sm-4">
                    {!! Form::label('order_date', __('Order Date'), ['class' => '']) !!}
                    <span>*</span>
                    {!!
						Form::date('order_date',
						isset($data['order_date']) ? $data['order_date'] : date("Y-m-d"),
						['class' => 'form-control order_date','placeholder'=>'Select Order Date', 'min' => date("Y-m-d"), 'required' => true])
                    !!}
                    <span class="form-text text-muted">Please select Order date</span>
                </div>
				@endif  
            </div>

            <div class="form-group row">
                <div class="col-sm-12">
                    		<div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div>
		<div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product/Service</th>
                                    <th width="10%">Qty</th>
                                    <!-- <th width="15%">Order Date</th> -->
                                    <th width="5%">Price/Qty</th>
                                    <th width="10%">Deal Discount</th>
                                    <th width="10%">Additional Discount %</th>
                                    <th width="15%">Total Discount Amount</th>
                                    <th width="20%">Final Amount</th>
                                </tr>
                            </thead>
                            <tbody id="products_list">
							@if(isset($client_products))
								@foreach ($client_products as $c_product)
								<tr id="products_{{$c_product->product_id}}">
									<td>  
										{{ $c_product->product->name }} 
										@php
											$description = ($c_product->product->description) ? $c_product->product->description : '';
											$plan_desc = \Illuminate\Support\Str::limit($description, 30, $end='...');
										@endphp
										<div class="word-wrap" title="{!! $c_product->product->description !!}">{!! $plan_desc !!}</div>
									</td>
									<td class="text-right">
										{{$c_product->qty}} 
									</td>
									<td class="text-right">
										{{$c_product->product_price}} 
									</td>
									<td>
										{{$c_product->product_id}}
									</td>
									<td>{{ $c_product->discount }}
									</td>
									<td class="text-right">
										{{$c_product->discount_amount}}
									</td>
									<td class="text-right">
										{{$c_product->final_amount}} 
									</td>
								</tr>
								@endforeach
							@endif 

							@if(isset($appoitnment_products))
								@foreach($appoitnment_products as $services)
								<tr id="products_{{ $services->id }}">
									<td>
										<a class="btn btn-link p-1" onClick="deleteProduct({{ $services->id }})"><i
												class="flaticon2-trash text-danger"></i></a> {{ $services->name }}
										<input type="hidden" class="product product-hidden" name="products[{{ $services->id }}]" id="products_id_{{ $services->id }}" value="{{ $services->id }}" />
										<input type="hidden" name="is_new_product[{{ $services->id }}]" id="is_new_product_{{ $services->id }}" value="1">
										<input type="hidden" class="product-igst-hidden" name="products_igst[{{ $services->id }}]"
											id="products_igst_{{ $services->id }}" value="{{ $services->igst }}" />
										<input type="hidden" class="product-sgst-hidden" name="products_sgst[{{ $services->id }}]"
											id="products_sgst_{{ $services->id }}" value="{{ $services->sgst }}" />
										<input type="hidden" class="product-cgst-hidden" name="products_cgst[{{ $services->id }}]"
											id="products_cgst_{{ $services->id }}" value="{{ $services->cgst }}" />
										<div class="word-wrap" title="${product_description}">
											{!! $services->description !!}
										</div>
									</td>
									<td class="text-right">
										<input type="number" class="form-control" style="width:80px !important" name="order_qty[{{ $services->id }}]" id="order_qty_{{ $services->id }}"
											data-addition="1" onChange="getProductQtyAmount({{ $services->id }},this.value)" step="1" value="1" min="1"
											number required />
									</td>
									<td class="text-right">
										{{ $services->sales_price }}<input type="hidden" name="products_price[{{ $services->id }}]"
											id="products_price_{{ $services->id }}" value="{{ $services->sales_price }}" />
									</td>
									<td>
										<input type="number" class="form-control deal-discount-input text-right" name="deal_discount[{{ $services->id }}]"
											min="0" max="100" id="deal_discount_{{ $services->id }}" value="0" readonly="readyonly"
											onChange="getProductDiscount({{ $services->id }})" />
									</td>
									<td>
										<input type="number" class="form-control text-right" name="products_discount[{{ $services->id }}]" min="0" max="100"
											id="products_discount_{{ $services->id }}" step=".01" onChange="getProductDiscount({{ $services->id }})" value="0" />
									</td>
									<td class="text-right">
										<span id="discount_amount_{{ $services->id }}">0.00</span><input type="hidden"
											name="products_discount_amount[{{ $services->id }}]" id="products_discount_amount_{{ $services->id }}" value="0" />
									</td>
									<td class="text-right">
										<span id="final_amount_{{ $services->id }}">{{ $services->sales_price }}</span><input type="hidden"
											name="products_final_amount[{{ $services->id }}]" id="products_final_amount_{{ $services->id }}"
											value="{{ $services->sales_price }}" />
									</td>
								</tr>
								@endforeach
							@endif

                            </tbody>
                            <tfoot>
                                <tr class="sgst d-none">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="align-middle">
                                        SGST %
                                        <input type="hidden" class="form-control" name="sgst_amount" id="sgst_amount"
                                            value="9" />
                                    </th>
                                    <th class="text-right" id="sgst_amount_html">0</th>
                                </tr>
                                <tr class="cgst d-none">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="align-middle">
                                        CGST %
                                        <input type="hidden" name="cgst_amount" id="cgst_amount" value="9" />
                                    </th>
                                    <th class="text-right" id="cgst_amount_html">0</th>
                                </tr>
                                <tr class="igst d-none">
                                    <td colspan="4"></td>
                                    <th class="align-middle">IGST %</th>
                                    <td class="align-middle">
                                        <input type="hidden" class="form-control text-right" name="igst_amount"
                                            id="igst_amount" value="0" />
                                    </td>
                                    <th class="text-right" id="igst_amount_html">0</th>
                                </tr>
                                </tr>
								@if(!isset($order))
                                <tr>
                                    <td colspan="4"></td>
                                    <th class="align-middle">Discount Code</th>
                                    <th colspan="2">
                                        <div class="input-group form-group-error">
                                            <input type="text" class="form-control" name="discount_code"
                                                id="discount_code" onChange="changeTax()" placeholder="Discount Code" />
                                            <div class="input-group-append" id="apply_code_button">
                                                <span class="input-group-text bg-primary" id="apply_code" type="button"
                                                    data-toggle="tooltip" title="Applay Code">
                                                    <i class="fas fa-check text-white"></i>
                                                </span>
                                            </div>
                                            <div class="input-group-append d-none" id="remove_code_button">
                                                <span class="input-group-text bg-danger" id="remove_code" type="button"
                                                    data-toggle="tooltip" title="Remove Discount">
                                                    <i class="far fa-window-close text-white"></i>
                                                </span>
                                            </div>
                                            <input type="hidden" name="deal_id" id="deal_id" value="0">
                                        </div>
                                    </th>
                                </tr>
								@endif
                                <tr>
                                    <td colspan="4"></td>
                                    <th colspan="2">Total Amount</th>
                                    <th class="text-right" id="final_amount">0</th>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <th colspan="2">Payment Pending</th>
                                    <th class="text-left">
                                        <div class="radio-inline">
											@if(!isset($order))
											<label class="radio radio-outline">
                                                {{ Form::radio('payment_status', 'YES', null, ['onClick'=>"paymentStatus(this.value)"]) }}
                                                <span></span>Yes</label>
                                            <label class="radio radio-outline">
                                                {{ Form::radio('payment_status', 'NO', 'checked', ['onClick'=>"paymentStatus(this.value)"]) }}
                                                <span></span>No</label>
											@else 
											<label class="radio radio-outline">
                                                {{ Form::radio('payment_status', 'YES', 'checked', ['onClick'=>"paymentStatus(this.value)"]) }}
                                                <span></span>Yes</label>
                                            <label class="radio radio-outline">
                                                {{ Form::radio('payment_status', 'NO', null, ['onClick'=>"paymentStatus(this.value)"]) }}
                                                <span></span>No</label>
											@endif
                                        </div>
                                    </th>
                                </tr>
                                <tr id="payment_mode">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="m-auto">Payment Mode <span>*</span></th>
                                    <td>{!!
                                        Form::select('payment_mode',
                                        $payment_modes,
                                        isset($data['payment_mode']) ? $data['payment_mode'] : null,
                                        ['class' => 'form-control ui search selection top right pointing
                                        payment_mode-select',
                                        'id' =>
                                        'payment_mode-select','required','onChange'=>'changePaymentMode(this.value)'])
                                        !!}</td>
                                </tr>
                                <tr id="payment_date">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="m-auto">Payment Date</th>
                                    <td>{!!
                                        Form::date('payment_date',
                                        isset($data['payment_date']) ? $data['payment_date'] : date("Y-m-d"),
                                        ['class' => 'form-control','placeholder'=>'Select Payment Date', 'min' =>
                                        date("Y-m-d")])
                                        !!}</td>
                                </tr>
                                <tr class="payment d-none">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="m-auto">Bank Name</th>
                                    <td>{!!
                                        Form::text('payment_bank_name',
                                        isset($data['payment_bank_name']) ? $data['payment_bank_name'] : null,
                                        ['class' => 'form-control','placeholder'=>'Enter Bank Name','pattern'=>'^[A-Za-z
                                        ]*$', 'max'=>'40'])
                                        !!}</td>
                                </tr>
                                <tr class="payment d-none">
                                    <td colspan="4"></td>
                                    <th colspan="2" class="m-auto">Transaction Number</th>
                                    <td>{!!
                                        Form::text('payment_number',
                                        isset($data['payment_number']) ? $data['payment_number'] : null,
                                        ['class' => 'form-control text-right','placeholder'=>'Enter Transaction Number',
                                        'pattern'=>'^[0-9 ]*$','min'=>'16'])
                                        !!}</td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-6">
						@if(!empty($order))
							{{Form::hidden('order_id',encrypt($order->id))}}
						@endif 
						{{Form::hidden('appointment_id', $appointment_id ?? 0)}} 
                        {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary  mr-2', 'id' =>
                        'submitOrder']) !!}
                        <a href="#" class="btn btn-light-primary font-weight-bold btn-cancel">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
	<div>
<!--begin::Form-->
<div> 

{{-- page scripts --}}
<script type="text/javascript">
	 
$(document).ready(function (e){

	$(document).on('click', '.btn-cancel', function (){
        location.reload();
    });

	<?php if($is_system_user): ?>
		var client_state_id = "{{$client_data->state_id}}";
		var branch_state_id = "";  
		
		setBranchState(); 
		
	<?php else: ?>
		var client_state_id = "{{$client_data->state_id}}";
		var branch_state_id = "{{$branch_data->state_id}}"; 
		toggleGSTFields(client_state_id, branch_state_id); 
	<?php endif; ?>
	changeTax();
	

	$(document).on('change', '#branch_id', function (){
		setBranchState();
	});

	function setBranchState()
	{
		let branch_id = $("#branch_id").val();

		$.ajax({
			url: '{!! route('branch.detailbyid') !!}',
			type: "POST",
			cache: false,
			data: {
				_token: "{{ csrf_token() }}",
				branch_id: branch_id
			},
			success: function (data) {
				branch_state_id = data.state_id;
				toggleGSTFields(client_state_id, branch_state_id);
			}
		});
	} 

	function toggleGSTFields(client_state_id, branch_state_id)
	{
		if(client_state_id == branch_state_id) {
			if(!jQuery('.igst').hasClass('d-none'))
				jQuery('.igst').addClass('d-none');
			jQuery('.sgst').removeClass('d-none');
			jQuery('.cgst').removeClass('d-none');
		} else {
			if(!jQuery('.sgst').hasClass('d-none'))
				jQuery('.sgst').addClass('d-none');
			if(!jQuery('.cgst').hasClass('d-none'))
				jQuery('.cgst').addClass('d-none');
			jQuery('.igst').removeClass('d-none');
		} 
	} 

	toggleDiscountCodeInput();  
  
	$('#product-select').select2({
		placeholder: "Select Product",
		allowClear: true,
		ajax: {
			url: '{!! route('product.byname') !!}',
			dataType: 'json', 
			data: function (params) { 
				ultimaConsulta = params.term;
				var distributor_id = $("#distributor_id").val();
				return {
					name: params.term, // search term
					distributor_id: distributor_id,
				};
			},
			processResults: function (data, param) {  
				return {
					results: $.map(data, function (item) { 
						return {
							text: item.name, 
							id: item.id
						}
					})
				};
			}
		}
	});
 
});

	<?php if($is_system_user): ?>
		var client_state_id = "{{$client_data->state_id}}";
		var branch_state_id = "";  
		
		setBranchState(); 
		
	<?php else: ?>
		var client_state_id = "{{$client_data->state_id}}";
		var branch_state_id = "{{$branch_data->state_id}}"; 
		toggleGSTFields(client_state_id, branch_state_id); 
	<?php endif; ?>
	changeTax();
	

	$(document).on('change', '#branch_id', function (){
		setBranchState(); 
	});

	function setBranchState()
	{
		let branch_id = $("#branch_id").val();

		$.ajax({
			url: '{!! route('branch.detailbyid') !!}',
			type: "POST",
			cache: false,
			data: {
				_token: "{{ csrf_token() }}",
				branch_id: branch_id
			},
			success: function (data) {
				branch_state_id = data.state_id;
				toggleGSTFields(client_state_id, branch_state_id);
			}
		}); 
	} 

	function toggleGSTFields(client_state_id, branch_state_id)
	{
		if(client_state_id == branch_state_id) {
			if(!jQuery('.igst').hasClass('d-none'))
				jQuery('.igst').addClass('d-none');
			jQuery('.sgst').removeClass('d-none');
			jQuery('.cgst').removeClass('d-none');
		} else {
			if(!jQuery('.sgst').hasClass('d-none'))
				jQuery('.sgst').addClass('d-none');
			if(!jQuery('.cgst').hasClass('d-none'))
				jQuery('.cgst').addClass('d-none');
			jQuery('.igst').removeClass('d-none');
		} 
		changeTax();
	} 


// Toggle discount code input
function toggleDiscountCodeInput()
{	
	if($(".product-hidden").length == 0) {
		$("#discount_code").attr('disabled', true);
		$("#apply_code").attr('disabled', true);
		$("#discount_code").val('');
	} else {
		$("#discount_code").removeAttr('disabled');
		$("#apply_code").removeAttr('disabled');
	}
}
 
var Products_final_amount = Products_net_amount = 0;

function addProduct(){
 
	var branch_id = $("#branch_id").val();
	var product_id = jQuery('#product-select').val();
	var order_date = jQuery('.order_date').val();  

	if(branch_id.length == 0) {
		alert("Please select branch first!");
		return false;
	}

	let product_count = $(`.product[value=${product_id}]`).length; 
	if(product_count > 0) {
		alert("Product is already in invoice, please update QTY if needed!");
		$("#product-select").html("");
		return false;
	} 

	if(product_id){  
		jQuery.ajax({
			url: '{!! route('orders.product.detail') !!}', 
			type: 'GET',
			data: { 
				product_id: product_id,
				branch_id: branch_id,
			},
			dataType: 'json',
			success: function(response){ 
				if(response.success){

					var product = response.product; 

					appendRow(product);  
					changeTax();
							
					jQuery('#product-select').val('');
					jQuery('#product-select').html('');
					toggleDiscountCodeInput();
					toggleBranchIntput();
				}

				if(response.success == false) {  
					Swal.fire({
						icon: 'error',
						title: 'Oops...',
						html: response.message, 
					})
				}
			}
		});
	}else 
	{ 
		return false;
	}
}

function appendRow(product)
{
	var product_description = (product.description) ? product.description : '';
	var count = 30;
	var product_desc = product_description.slice(0, count) + (product_description.length > count ? "..." : ""); 
	product.sales_price = parseFloat(product.sales_price);
  
	var html = `<tr id="products_${product.id}">
				<td>
					<a class="btn btn-link p-1" onClick="deleteProduct(${product.id})"><i
							class="flaticon2-trash text-danger"></i></a>${product.name}
					<input type="hidden" class="product product-hidden" name="products[${product.id}]" id="products_id_${product.id}" value="${product.id}" />
					<input type="hidden" name="is_new_product[${product.id}]" id="is_new_product_${product.id}" value="1">
					<input type="hidden" class="product-igst-hidden" name="products_igst[${product.id}]"
						id="products_igst_${product.id}" value="${product.igst}" />
					<input type="hidden" class="product-sgst-hidden" name="products_sgst[${product.id}]"
						id="products_sgst_${product.id}" value="${product.sgst}" />
					<input type="hidden" class="product-cgst-hidden" name="products_cgst[${product.id}]"
						id="products_cgst_${product.id}" value="${product.cgst}" />
					<div class="word-wrap" title="${product_description}">
						${product_desc}
					</div>
				</td>
				<td class="text-right">
					<input type="number" class="form-control" style="width:80px !important" name="order_qty[${product.id}]" id="order_qty_${product.id}"
						data-addition="1" onChange="getProductQtyAmount(${product.id},this.value)" step="1" value="1" min="1"
						number required />
				</td>
				<td class="text-right">
					${product.sales_price.toFixed(2)}<input type="hidden" name="products_price[${product.id}]"
						id="products_price_${product.id}" value="${product.sales_price}" />
				</td>
				<td>
					<input type="number" class="form-control deal-discount-input text-right" name="deal_discount[${product.id}]"
						min="0" max="100" id="deal_discount_${product.id}" value="0" readonly="readyonly"
						onChange="getProductDiscount(${product.id})" />
				</td>
				<td>
					<input type="number" class="form-control text-right" name="products_discount[${product.id}]" min="0" max="100"
						id="products_discount_${product.id}" step=".01" onChange="getProductDiscount(${product.id})" value="0" />
				</td>
				<td class="text-right">
					<span id="discount_amount_${product.id}">0.00</span><input type="hidden"
						name="products_discount_amount[${product.id}]" id="products_discount_amount_${product.id}" value="0" />
				</td>
				<td class="text-right">
					<span id="final_amount_${product.id}">${product.sales_price.toFixed(2)}</span><input type="hidden"
						name="products_final_amount[${product.id}]" id="products_final_amount_${product.id}"
						value="${product.sales_price.toFixed(2)}" />
				</td>
			</tr>`;

	jQuery('#products_list').append(html);
}

// Change Tax Amount
function changeTax(){  
	let total_igst = 0;
	let total_sgst = 0;
	let total_cgst = 0;
	var final_total_amount = 0; 
 
	$('.product-hidden').each(function (index, input) {
		let product_id = $(input).val();
		let product_igst = $(`#products_igst_${product_id}`).val();
		let product_sgst = $(`#products_sgst_${product_id}`).val();
		let product_cgst = $(`#products_cgst_${product_id}`).val(); 
		let product_qty  = $(`#order_qty_${product_id}`).val();
		let product_final_price = $(`#products_final_amount_${product_id}`).val();

		// console.log(product_id, product_igst, product_sgst, product_cgst, product_qty, product_final_price);

		// let sgst_percentage = "1."+product_sgst;
		// console.log(sgst_percentage); 

		if (client_state_id == branch_state_id) {
			let gstObj = getTotalGSTAmount(product_final_price, product_sgst, product_cgst, 0); 
			total_sgst += gstObj.sgst_amount;
			total_cgst += gstObj.cgst_amount;
		} else {
			let gstObj = getTotalGSTAmount(product_final_price, 0, 0, product_igst); 
			total_igst += gstObj.igst_amount; 	
		} 
 
		final_total_amount += parseFloat(product_final_price); 
	}); 

	if (client_state_id == branch_state_id) {
		$('#sgst_amount').val(total_sgst);
		$('#sgst_amount_html').html(total_sgst.toFixed(2));
		$('#cgst_amount').val(total_cgst);
		$('#cgst_amount_html').html(total_cgst.toFixed(2));
	} else {
		$('#igst_amount').val(total_igst);
		$('#igst_amount_html').html(total_igst.toFixed(2));
	} 

	// Update Final Total
	jQuery('#final_amount').html(final_total_amount.toFixed(2));
} 

function getTotalGSTAmount(amount = 0, sgst = 0, cgst = 0, igst = 0)
{
	amount = parseFloat(amount);
	let total_tax = parseFloat(sgst) + parseFloat(cgst) + parseFloat(igst);
 
	total_tax = parseInt(total_tax);
 
	if(total_tax.toString().length <= 1) {
		total_tax = '0'+total_tax;
	}
 
	let formula = "1."+total_tax; 

	let total_gst_amount = amount - (amount / formula);
 
	let sgst_amount = sgst * total_gst_amount / total_tax;
	let cgst_amount = cgst * total_gst_amount / total_tax;
	let igst_amount = igst * total_gst_amount / total_tax;

	if(total_gst_amount == 0) {
		sgst_amount = 0;
		cgst_amount = 0;
		igst_amount = 0;
	}

	return {
		'total_tax' : total_gst_amount,
		'sgst_amount' : sgst_amount,
		'cgst_amount' : cgst_amount,
		'igst_amount' : igst_amount,
	}; 
}

function getProductQtyAmount(id, qty, current_qty = false){  
 
	let form_data = $("#orderCreateForm").serialize();
	$.ajax({
		url: '{!! route('orders.manageStockBeforeSubmit') !!}', 
		type: 'post',
		data: { 
			_token: "{{ csrf_token() }}",
			form_data: form_data,
			check_qty: 1,
		},
		dataType: 'json',
		success: function(response){  
			if(response.status){ 
				changeTax();
				getProductDiscount(id);
			}

			if(response.status == false) { 
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					html: response.message, 
				})
				let old_qty = $("#order_qty_"+id).attr('data-addition');
				$("#order_qty_"+id).val(old_qty);
			}
		} 
	});
} 

function getProductDiscount(id){
	let discount_code = $("#deal_discount_"+id).val();
	let additional_discount = $("#products_discount_"+id).val();

	let discount = parseInt(discount_code) + parseInt(additional_discount);
 
	if(discount<=100 && discount>=0){ 
		var old_products_discount_amount = jQuery('#products_discount_amount_'+id).val();
		var productprice = jQuery('#products_price_'+id).val();
		
		var total_price = productprice * jQuery('#order_qty_'+id).val(); 

		var discount_amount = total_price * discount / 100;
		discount_amount = parseFloat(discount_amount);
		
		jQuery('#products_discount_amount_'+id).val(discount_amount);
		jQuery('#discount_amount_'+id).html(discount_amount.toFixed(2));
		var final_amount = total_price - discount_amount;
		final_amount = parseFloat(final_amount);
		jQuery('#products_final_amount_'+id).val(final_amount);
		jQuery('#final_amount_'+id).html(final_amount.toFixed(2));
		Products_final_amount = parseFloat(Products_final_amount) + parseFloat(old_products_discount_amount) - parseFloat(discount_amount);
		Products_final_amount = parseFloat(Products_final_amount);
		jQuery('#final_amount').html(Products_final_amount.toFixed(2));
		jQuery('#round_off_amount').html(Products_final_amount.toFixed(2));
		changeTax();
	}else{
		alert("total discount can not be more than 100%");
		$("#products_discount_"+id).val(0);
		return false;
	}
}

function changePaymentMode(mode){
	if(mode !='' && mode != 'CASH'){
		jQuery('.payment').removeClass('d-none');
	}else{
		if(!jQuery('.payment').hasClass('d-none'))
		jQuery('.payment').addClass('d-none');
	}
}

function deleteProduct(productId, entry_id = 0)
{ 
	let is_new = $(`#is_new_product_${productId}`).val();
  
	if(is_new == 1) {
		jQuery('#products_'+productId).remove(); 
		changeTax();
		toggleDiscountCodeInput();
	} else {
		Swal.fire({
			title: 'Are you sure?',
			text: "Do you want to delete product/service?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#ccc',
			confirmButtonText: 'delete!'
		}).then((result) => {
			if (result.isConfirmed) {  
				var id = $(this).data('id'); 
				var branch_id = $("#branch_id").val(); 
				window.location.href = "{{ url('admin/orders/product-delete') }}/" +entry_id+"/"+branch_id;
			}
		}) 
	} 
	toggleBranchIntput();
}

// Toggle on load
<?php if(isset($order)): ?>
	paymentStatus('YES');
<?php else: ?>
	paymentStatus('NO');
<?php endif; ?>

function paymentStatus(value){
	if(value === 'YES'){
		jQuery('#payment_mode, #payment_date').addClass('d-none');
		jQuery('#payment_mode-select').prop('required',false);
	}else{
		jQuery('#payment_mode, #payment_date').removeClass('d-none');
		jQuery('#payment_mode-select').prop('required',true);
	}
}

// Will readonly branch selection after adding product/ service on invoice
// Will remove readonly attribute if there are no products in invoice
function toggleBranchIntput()
{
	let branch_id = $("#branch_id").val();
	let product_count = $(`.product`).length; 
	if(product_count > 0) {  
		console.log(branch_id);
		$(`#branch_id option:not(option[value="${branch_id}"])`).attr('disabled', true);
	} else {
		$(`#branch_id option:not(option[value="${branch_id}"])`).removeAttr('disabled', true);
	}
}
 
</script>