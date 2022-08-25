<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body"> 
		<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $order->distributor_id }}">
		<input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}">

		@if($order->is_payment_pending == "YES") 		
			<div class="row">
				<div class="form-group col-sm-4">
					{{Form::hidden('client_id',$client_id)}}
					{!! Form::label('plan', __('Product/Service'), ['class' => '']) !!}
					<span>*</span>
					<div class="input-group"> 
						{!!
							Form::select('plan',
							[],
							null, 
							['class' => 'form-control ui searchpicker selection top right pointing product-select',
							'id' => 'product-select'])
						!!} 
						<div class="input-group-append">
							<span class="input-group-text bg-primary" type="button" onClick="addPlan()" data-toggle="tooltip" title="Add Product / Service">
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
						['class' => 'form-control order_date','placeholder'=>'Select Order Date', 'min' => date("Y-m-d")])
					!!}
					<span class="form-text text-muted">Please select Order date</span>
				</div>
			</div>
		@endif
			<div class="form-group row">
				<div class="col-lg-12">
					<table class="table table-bordered">
					<thead>
					<tr>
					<th>Product/Service</th>
					<th width="10%">Qty</th>
					<!-- <th width="15%">order Date</th> -->
					<th width="5%">Amount</th>
					<th width="15%">Deal Discount %</th>
					<th width="10%">Discount %</th>
					<th width="15%">Discount Amount</th>
					<th width="20%">Final Amount</th>
					</tr>
					</thead>
					<tbody id="plans_list">
					@foreach ($clientproducts as $cplan)
					<tr id="plans_{{$cplan->id}}">
					<input type="hidden" name="product_update_data[{{$cplan->id}}]" value="{{$cplan->id}}" />
					<input type="hidden" name="product_update[{{$cplan->product_id}}]" class="plan plan-hidden" id="plans_id_{{$cplan->product_id}}" value="{{$cplan->product_id}}" />
					<input type="hidden" name="plans_price[{{$cplan->product_id}}]" id="plans_price_{{$cplan->product_id}}" value="{{$cplan->product_price}}" />

						<td>
							@if($order->is_payment_pending == "YES")
								<a href="javascript:;" class="btn btn-link delete_product p-1" data-id="{{$cplan->id}}"><i class="flaticon2-trash text-danger"></i></a>
							@endif
							{{ $cplan->product->name }}
							@php
								$description = ($cplan->product->description) ? $cplan->product->description : '';
								$plan_desc = \Illuminate\Support\Str::limit($description, 30, $end='...');
							@endphp

						<input type="hidden" class="plan-igst-hidden" name="plans_igst[{{$cplan->product_id}}]" id="plans_igst_{{$cplan->product_id}}" value="{{$cplan->igst}}" />  <input type="hidden" class="plan-sgst-hidden" name="plans_sgst[{{$cplan->product_id}}]" id="plans_sgst_{{$cplan->product_id}}" value="{{$cplan->sgst}}" />  <input type="hidden" class="plan-cgst-hidden" name="plans_cgst[{{$cplan->product_id}}]" id="plans_cgst_{{$cplan->product_id}}" value="{{$cplan->cgst}}" />

						<div class="word-wrap" title="{!! $cplan->product->description !!}">{!! $plan_desc !!}  </div>
						</td>
						<td class="text-right"> 
							<input type="number" class="form-control text-right" name="order_qty[{{$cplan->product_id}}]" min="1" max="100" id="order_qty_{{$cplan->product_id}}" data-addition="{{ $cplan->qty }}" step="1" onChange="getPlanQtyAmount({{$cplan->product_id}}, this.value, {{$cplan->qty}})" value="{{ $cplan->qty }}" /> 
						</td>
						<!-- <td class="text-right">{{ $cplan->order_date }}</td> -->
						<td class="text-right">{{ \App\Helpers\Helper::decimalNumber($cplan->product_price) }}</td>
						<td class="text-right"> 
							<input type="number" class="form-control text-right" id="deal_discount_{{$cplan->product_id}}" value="{{ $cplan->deal_discount }}" readonly="true" />
						</td>
						<td class="text-right">
						@if($order->is_payment_pending == "YES")
							<input type="number" class="form-control text-right" name="plans_discount[{{$cplan->product_id}}]" min="0" max="100" id="plans_discount_{{$cplan->product_id}}" step=".01" onChange="getPlanDiscount({{$cplan->product_id}})" value="{{ $cplan->discount }}" />
						@else
							<input type="number" class="form-control text-right" name="plans_discount[{{$cplan->product_id}}]" min="0" max="100" id="plans_discount_{{$cplan->product_id}}" step=".01" onChange="getPlanDiscount({{$cplan->product_id}})" value="{{ $cplan->discount }}" disabled="disabled" />
						@endif

						</td>
						<td class="text-right"><span id="discount_amount_{{$cplan->product_id}}">{{ \App\Helpers\Helper::decimalNumber($cplan->discount_amount * $cplan->qty) }}</span>
							<input type="hidden" name="plans_discount_amount[{{$cplan->product_id}}]" id="plans_discount_amount_{{$cplan->product_id}}" value="{{ $cplan->discount_amount * $cplan->qty }}"></td>
						<td class="text-right"><span id="final_amount_{{$cplan->product_id}}">{{ \App\Helpers\Helper::decimalNumber($cplan->final_amount * $cplan->qty) }}</span>
						<input type="hidden" name="plans_final_amount[{{$cplan->product_id}}]" id="plans_final_amount_{{$cplan->product_id}}" value="{{ $cplan->final_amount * $cplan->qty }}"></td>
					</tr>
					@endforeach
					</tbody>
					<tfoot> 
					<tr class="sgst d-none">
					<td colspan="4"></td>
					<th colspan="2"  class="align-middle">
						SGST %
						<input type="hidden" class="form-control" name="sgst_amount" id="sgst_amount" value="{{ $order->sgst_amount }}" />
					</th> 
					<th class="text-right" id="sgst_amount_html">{{ \App\Helpers\Helper::decimalNumber($order->sgst_amount) }}</th>
					</tr>
					<tr class="cgst d-none">
					<td colspan="4"></td>
					<th colspan="2"  class="align-middle">
						CGST %
						<input type="hidden"  name="cgst_amount" id="cgst_amount" value="{{ $order->cgst_amount }}" />
					</th> 
					<th class="text-right" id="cgst_amount_html">{{ \App\Helpers\Helper::decimalNumber($order->cgst_amount) }}</th>
					</tr>
					<tr class="igst d-none">
					<td colspan="4"></td>
					<th colspan="2" class="align-middle">
						IGST %
						<input type="hidden" class="form-control text-right" name="igst_amount" id="igst_amount" value="{{ $order->igst_amount }}" />
					</th> 
					<th class="text-right" id="igst_amount_html">{{ \App\Helpers\Helper::decimalNumber($order->igst_amount) }}</th>
					</tr>
					<tr>
					<td colspan="4"></td>
					<th colspan="2">Total Amount</th>
					<th class="text-right" id="final_amount">{{ \App\Helpers\Helper::decimalNumber($order->total_amount) }}</th>
					</tr>
					<!-- <tr>
					<td colspan="4"></td>
					<th colspan="2">Net Amount</th>
					<th class="text-right" id="net_amount">{{ \App\Helpers\Helper::decimalNumber($order->final_amount) }}</th>
					</tr>
					<tr>
					<td colspan="4"></td>
					<th colspan="2">Round off Amount</th>
					<th class="text-right" id="round_off_amount">{{ \App\Helpers\Helper::decimalNumber($order->round_off_amount) }}</th>
					</tr> -->
					<tr>
					<td colspan="4"></td>
					<th colspan="2">Payment Pending</th>
					<th class="text-left">
						<div class="radio-inline">
							<label class="radio radio-outline">
							{{ Form::radio('payment_status', 'YES', $order->is_payment_pending == "YES" ? 'checked' : '', ['onClick'=>"paymentStatus(this.value)"]) }}
							<span></span>Yes</label>
							<label class="radio radio-outline">
							{{ Form::radio('payment_status', 'NO', $order->is_payment_pending == "NO" ? 'checked' : '', ['onClick'=>"paymentStatus(this.value)"]) }}
							<span></span>No</label>
						</div>
					</th>
					</tr>
					<tr id="payment_mode">
					<td colspan="4"></td>
					<th colspan="2" class="m-auto">Payment Mode <span class="text-danger">*</span></th>
					<td>{!!
						Form::select('payment_mode',
						$payment_modes,
						isset($data['payment_mode']) ? $data['payment_mode'] : null, 
						['class' => 'form-control ui search selection top right pointing payment_mode-select',
						'id' => 'payment_mode-select','required','onChange'=>'changePaymentMode(this.value)'])
					!!}</td>
					</tr>
					<tr id="payment_date">
					<td colspan="4"></td>
					<th colspan="2" class="m-auto">Payment Date</th>
					<td>{!!
							Form::date('payment_date',
							isset($data['payment_date']) ? $data['payment_date'] : $order->payment_date,
							['class' => 'form-control','placeholder'=>'Select Payment Date', 'min' => $order->payment_date])
						!!}</td>
					</tr>
					<tr class="payment d-none">
					<td colspan="4"></td>
					<th colspan="2" class="m-auto">Bank Name</th>
					<td>{!!
							Form::text('payment_bank_name',
							isset($data['payment_bank_name']) ? $data['payment_bank_name'] : null,
							['class' => 'form-control','placeholder'=>'Enter Bank Name','pattern'=>'^[A-Za-z ]*$', 'max'=>'40'])
						!!}</td>
					</tr>
					<tr class="payment d-none">
					<td colspan="4"></td>
					<th colspan="2" class="m-auto">Transaction Number</th>
					<td>{!!
							Form::text('payment_number',
							isset($data['payment_number']) ? $data['payment_number'] : null,
							['class' => 'form-control','placeholder'=>'Enter Transaction Number', 'pattern'=>'^[0-9]*$','min'=>'16'])
						!!}</td>
					</tr>
					
					</tfoot>
					</table>
				</div>        
			</div>
			<div class="form-group row">
				<div class="col-lg-12">
				</div>        
			</div>

			<div class="card-footer">
				<div class="row">
					<div class="col-lg-6">
					@if(!empty($order))
						{{Form::hidden('order_id',encrypt($order->id))}}
					@endif
						{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitpermission']) !!}
						<a href="#" class="btn btn-light-primary font-weight-bold btn-cancel">Cancel</a>
					</div>
				</div>
			</div>

		<div> 
	<div>
<div>

@include('layouts.modal',['modalId'=>'plan-delete','content'=>'Are you sure you want to delete plan ?','title'=>'Delete'])
{{-- page scripts --}}
<script type="text/javascript">

	$(document).ready(function () {
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

var state_id = country_id = 0;
function getCompanyDetails(client_id){
	if(!client_id){
		$( "#orderUpdateForm" ).before('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Please select company!</strong></div>');
		return false;
	}
	jQuery.ajax({
		url: '{!! route('orders.company.detail') !!}', //this is your uri
		type: 'GET',
		data: { client_id:client_id },
		dataType: 'json',
		success: function(response){ 
			if(response.success){
				var company = response.company;
				state_id = company.state_id;
				country_id = company.country_id;
					
					if(state_id == 12){
						if(!jQuery('.igst').hasClass('d-none'))
							jQuery('.igst').addClass('d-none');
						jQuery('.sgst').removeClass('d-none');
						jQuery('.cgst').removeClass('d-none');
					}else{
						if(!jQuery('.sgst').hasClass('d-none'))
							jQuery('.sgst').addClass('d-none');
						if(!jQuery('.cgst').hasClass('d-none'))
							jQuery('.cgst').addClass('d-none');
						jQuery('.igst').removeClass('d-none');
					}
				
			}
		}
	});
}
var Plans_final_amount =  parseFloat({{ $order->total_amount }});
var Plans_net_amount =  parseFloat({{ $order->final_amount }});

function addPlan(){
  
	var product_id = jQuery('#product-select').val();
	var order_date = jQuery('.order_date').val();
	jQuery('.product-select option[value="'+product_id+'"]').prop('disabled',true)

	let product_count = $(`.plan[value=${product_id}]`).length; 
	if(product_count > 0) {
		alert("Can not add same product/service!");
		$("#product-select").html("");
		return false;
	}

	if(product_id){  
		jQuery.ajax({
			url: '{!! route('orders.product.detail') !!}', //this is your uri
			type: 'GET',
			data: { 
				product_id: product_id, 
				branch_id: $("#branch_id").val()
			},
			dataType: 'json',
			success: function(response){ 
				if(response.success){
					var plan = response.plan;
					var plan_description = (plan.description) ? plan.description : '';
					var count = 30;
					var plan_desc = plan_description.slice(0, count) + (plan_description.length > count ? "..." : ""); 
					plan.sales_price = parseInt(plan.sales_price); 

					jQuery('#plans_list').append('<tr id="plans_'+plan.id+'"><td><a class="btn btn-link p-1" onClick="deletePlan('+plan.id+')"><i class="flaticon2-trash text-danger"></i></a>'+plan.name+'<input type="hidden" class="plan plan-hidden" name="product_add_id['+plan.id+']" id="plans_id_'+plan.id+'" value="'+plan.id+'" />   <input type="hidden" class="plan-igst-hidden" name="plans_igst['+plan.id+']" id="plans_igst_'+plan.id+'" value="'+plan.igst+'" />  <input type="hidden" class="plan-sgst-hidden" name="plans_sgst['+plan.id+']" id="plans_sgst_'+plan.id+'" value="'+plan.sgst+'" />  <input type="hidden" class="plan-cgst-hidden" name="plans_cgst['+plan.id+']" id="plans_cgst_'+plan.id+'" value="'+plan.cgst+'" /> <div class="word-wrap" title="'+plan_description+'">'+plan_desc+'</div></td><td class="text-right"><input type="number" class="form-control text-right" name="order_qty['+plan.id+']" id="order_qty_'+plan.id+'" data-addition="1" onChange="getPlanQtyAmount('+plan.id+',this.value)" step="1" value="1" min="1" number required/></td><td class="text-right">'+plan.sales_price.toFixed(2)+'<input type="hidden" name="plans_add_price['+plan.id+']" id="plans_price_'+plan.id+'" value="'+plan.sales_price+'" /></td><td ><input type="number" class="form-control deal-discount-input text-right" name="deal_discount['+plan.id+']" min="0" max="100" id="deal_discount_'+plan.id+'" value="0" readonly="readyonly" onChange="getPlanDiscount('+plan.id+')" /></td><td ><input type="number" class="form-control text-right" name="plans_add_discount['+plan.id+']" min="0" max="100" id="plans_discount_'+plan.id+'" step=".01" onChange="getPlanDiscount('+plan.id+')" value="0" /></td><td class="text-right"><span id="discount_amount_'+plan.id+'">0.00</span><input type="hidden" name="plans_discount_amount['+plan.id+']" id="plans_discount_amount_'+plan.id+'" value="0" /></td><td class="text-right"><span id="final_amount_'+plan.id+'">'+plan.sales_price.toFixed(2)+'</span><input type="hidden" name="plans_final_amount['+plan.id+']" id="plans_final_amount_'+plan.id+'" value="'+plan.sales_price+'" /></td></tr>');
					Plans_final_amount += parseFloat(plan.sales_price);
					Plans_final_amount = parseFloat(Plans_final_amount)
					jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
					changeTax();		
					jQuery('#product-select').val('');
					jQuery('#product-select').html(''); 
				}

				if(response.success == false) {
					$('#product-select').val('');
					$('#product-select').html('');
					alert(response.message);
				}
			}
		});
	}else 
	{
		// $("#kt_header").before('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button><strong>Please select plan!</strong></div>'); 
		return false;
	}
}


function getPlanQtyAmount(id, qty, current_qty = false){  
 
	$.ajax({
		url: '{!! route('orders.product.stock') !!}', //this is your uri
		type: 'post',
		data: { 
			_token: "{{ csrf_token() }}",
			branch_id: $("#branch_id").val(),
			product_id: id,
			qty: qty,
			current_qty: current_qty,
		},
		dataType: 'json',
		success: function(response){  
			if(response.success){

				var old_value_plan = parseInt($("#plans_final_amount_"+id).val());
				var plans_discount_amount = parseInt(jQuery('#plans_discount_amount_'+id).val() * 2);
				var planprice = parseInt(jQuery('#plans_price_'+id).val());
				var planprice_total = parseInt(planprice * qty); 
			
				let addtion_remove = $("#order_qty_"+id).attr('data-addition'); 
				addition_remove_total = addtion_remove * planprice;
				Plans_final_amount -= parseInt(addition_remove_total);
				Plans_final_amount += planprice_total;
				$("#order_qty_"+id).attr('data-addition', qty); 
			
				jQuery('#plans_final_amount_'+id).val(planprice_total);
				jQuery('#final_amount_'+id).html(planprice_total.toFixed(2)); 
				jQuery('#final_amount').html(planprice_total.toFixed(2));
			
				var final_amount = planprice_total - plans_discount_amount;
				final_amount = parseFloat(final_amount);
			
				jQuery('#final_amount').html(planprice_total.toFixed(2));
				jQuery('#round_off_amount').html(Math.round(planprice_total).toFixed(2));

				let discount = $("#plans_discount_"+id).val();
				getPlanDiscount(id, discount);  
			}

			if(response.success == false) {
				alert(response.message);
				let old_qty = $("#order_qty_"+id).attr('data-addition');
				$("#order_qty_"+id).val(old_qty);
			}
		} 
	});
}

function getPlanDiscount(id){
	let discount_code = $("#deal_discount_"+id).val();
	let additional_discount = $("#plans_discount_"+id).val(); 

	let discount = parseInt(discount_code) + parseInt(additional_discount);
 
	if(discount<=100 && discount>=0){ 
		var old_plans_discount_amount = jQuery('#plans_discount_amount_'+id).val();
		var planprice = jQuery('#plans_price_'+id).val();
		// console.log(planprice)
		var total_price = planprice * jQuery('#order_qty_'+id).val(); 

		var discount_amount = total_price * discount / 100;
		discount_amount = parseFloat(discount_amount);
		jQuery('#plans_discount_amount_'+id).val(discount_amount);
		jQuery('#discount_amount_'+id).html(discount_amount.toFixed(2));
		var final_amount = total_price - discount_amount;
		final_amount = parseFloat(final_amount);
		jQuery('#plans_final_amount_'+id).val(final_amount);
		jQuery('#final_amount_'+id).html(final_amount.toFixed(2));
		Plans_final_amount = parseFloat(Plans_final_amount) + parseFloat(old_plans_discount_amount) - parseFloat(discount_amount);
		Plans_final_amount = parseFloat(Plans_final_amount);
		jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
		jQuery('#round_off_amount').html(Math.round(Plans_final_amount).toFixed(2));
		changeTax();
	}else{
		alert("total discount can not be more than 100%");
		$("#plans_discount_"+id).val(0);
		return false;
	}
}


function changeTax(){  
	let total_igst = 0;
	let total_sgst = 0;
	let total_cgst = 0;

	$('.plan-hidden').each(function (index, input) {
		let product_id = $(input).val();
		let product_igst = $(`#plans_igst_${product_id}`).val();
		let product_sgst = $(`#plans_sgst_${product_id}`).val();
		let product_cgst = $(`#plans_cgst_${product_id}`).val(); 
		let product_qty  = $(`#order_qty_${product_id}`).val();
		let product_final_price = $(`#plans_final_amount_${product_id}`).val();
		
		// console.log(product_id, product_igst, product_sgst, product_cgst, product_qty, product_final_price);

		if(state_id == 12){ 
			total_sgst += parseFloat(product_final_price) * product_sgst / 100;
			total_cgst += parseFloat(product_final_price) * product_cgst / 100;
		}else{ 
			total_igst += parseFloat(product_final_price) * product_igst / 100;
		}	
	}); 

	if(state_id == 12){ 
		$('#sgst_amount').val(total_sgst);
		$('#sgst_amount_html').html(total_sgst.toFixed(2));
		$('#cgst_amount').val(total_cgst);
		$('#cgst_amount_html').html(total_cgst.toFixed(2));
	}else{ 
		$('#igst_amount').val(total_igst);
		$('#igst_amount_html').html(total_igst.toFixed(2));
	}	
}

// function changeTax(){
// 	var sgst = jQuery('#sgst').val();
// 	var cgst = jQuery('#cgst').val();
// 	var igst = jQuery('#igst').val();
	
// 	if(sgst>=0 && sgst<=100 && cgst>=0 && cgst<=100 && igst>=0 && igst<=100){
// 		if(state_id == 12){
// 			var sgstamt = parseFloat(Plans_final_amount) * parseFloat(sgst) / 100;
// 			var cgstamt = parseFloat(Plans_final_amount) * parseFloat(cgst) / 100;
// 			sgstamt = parseFloat(sgstamt);
// 			jQuery('#sgst_amount').val(sgstamt);
// 			jQuery('#sgst_amount_html').html(sgstamt.toFixed(2));
// 			cgstamt = parseFloat(cgstamt);
// 			jQuery('#cgst_amount').val(cgstamt);
// 			jQuery('#cgst_amount_html').html(cgstamt.toFixed(2));
// 			Plans_net_amount = parseFloat(Plans_final_amount) + sgstamt + cgstamt;
// 		}else{
// 			var igstamt = parseFloat(Plans_final_amount) * parseFloat(igst) / 100;
// 			igstamt = parseFloat(igstamt);
// 			jQuery('#igst_amount').val(igstamt);
// 			jQuery('#igst_amount_html').html(igstamt.toFixed(2));
// 			Plans_net_amount = parseFloat(Plans_final_amount) + igstamt;
// 		}	
	
// 	Plans_net_amount = parseFloat(Plans_net_amount);
// 	jQuery('#net_amount').html(Plans_net_amount.toFixed(2));
// 	jQuery('#round_off_amount').html(Math.round(Plans_net_amount).toFixed(2));
// 	}
// }

function changePaymentMode(mode){
	if(mode !='' && mode != 'CASH'){
		jQuery('.payment').removeClass('d-none');
	}else{
		if(!jQuery('.payment').hasClass('d-none'))
		jQuery('.payment').addClass('d-none');
	}
}

function deletePlan(planId){

	console.log(planId);

	var plans_final_amount = jQuery('#plans_final_amount_'+planId).val();	
	var final_amount = jQuery('#final_amount').html()
	var net_amount = jQuery('#net_amount').html()
	
	Plans_final_amount = parseFloat(final_amount) - parseFloat(plans_final_amount)
	Plans_net_amount = parseFloat(net_amount) - parseFloat(plans_final_amount)

	jQuery('#net_amount').html(Plans_net_amount.toFixed(2));
	jQuery('#final_amount').html(Plans_final_amount.toFixed(2));
	jQuery('#plans_'+planId).remove();
	jQuery('.plan-select option[value="'+planId+'"]').prop('disabled',false)
	changeTax();
}

function paymentStatus(value){
	if(value === 'YES'){
		jQuery('#payment_mode, #payment_date').addClass('d-none');
		jQuery('#payment_mode-select').prop('required',false);
	}else{
		jQuery('#payment_mode, #payment_date').removeClass('d-none');
		jQuery('#payment_mode-select').prop('required',true);
	}
}
</script>
@section('scripts')
<script>
jQuery(function() {
	getCompanyDetails('{{$client_id}}')
	paymentStatus('{{$order->is_payment_pending}}')
	@foreach ($clientproducts as $cplan)
    	$('.product-select option[value="'+{{$cplan->product_id}}+'"]').prop('disabled',true)
	@endForeach			
	
	$(document).on('click','.delete_product',function(){ 

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
				window.location.href = "{{URL::to('/admin/orders/product-delete')}}/"+id+"/"+branch_id;
			}
		})  
	});
	// $(document).on('click','.delete-record',function(){
	// 	var id = $(this).data('id');
	// 	window.location.href = "{{URL::to('/admin/orders/product-delete')}}/"+id;
	// });
});
</script>
@stop