<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body">  
            <div class="row">
                <div class="form-group col-sm-4">
                    {{Form::hidden('salon_id',$salon_id)}}
                    {!! Form::label('plan', __('Plan'), ['class' => '']) !!}
                    <span>*</span>
                    <div class="input-group">
                        {!!
                        Form::select('plan',
                        [],
                        null,
                        ['class' => 'form-control plan-select',
                        'id' => 'plan-select'])
                        !!}
                        <div class="input-group-append">
                            <span class="input-group-text bg-primary" type="button" onClick="addPlan()"
                                data-toggle="tooltip" title="Add Plan / Service">
                                <i class="fas fa-plus text-white"></i>
                            </span>
                        </div>
                    </div>
                    <span class="form-text text-muted">Please select Plan</span>
                </div>
                <div class="form-group col-sm-4 offset-sm-4 d-none">
                    {!! Form::label('plan_date', __('Plan Date'), ['class' => '']) !!}
                    <span>*</span>
                    {!!
                    Form::date('plan_date',
                    isset($data['plan_date']) ? $data['plan_date'] : date("Y-m-d"),
                    ['class' => 'form-control plan_date','placeholder'=>'Select Plan Date', 'min' => date("Y-m-d")])
                    !!}
                    <span class="form-text text-muted">Please select plan date</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>	
                                    <th>Plan</th> 
                                    <th width="15%" class="d-none">Plan Date</th>
                                    <th width="5%">Amount</th> 
                                    <th width="10%">Discount %</th>
                                    <th width="15%">Total Discount Amount</th>
                                    <th width="20%" class="text-right">Final Amount</th>
                                </tr>
                            </thead>
                            <tbody id="plan_list">
							@if(isset($salon_plan))
								@foreach ($salon_plan as $c_plan)
								<tr id="plans_{{$c_plan->plan_id}}">
									<td>
										<a class="btn btn-link p-1" onClick="deletePlan({{$c_plan->plan_id}}, {{$c_plan->id}})">
											<i class="flaticon2-trash text-danger"></i></a>
											{{ $c_plan->plan->name }} <br>
											<div class="word-wrap" title="{{ $c_plan->description }}">
												{{$c_plan->plan->description}}
											</div> <br>
											Users : {{ $c_plan->no_of_users }} , Branches : {{ $c_plan->no_of_branches }} <br>
											SMS : {{ $c_plan->no_of_sms }} , Emails : {{ $c_plan->no_of_email }}
										<input type="hidden" class="plan plan-hidden" name="plans[{{$c_plan->plan_id}}]" id="plans_id_{{$c_plan->plan_id}}" value="{{$c_plan->plan_id}}" />
										<input type="hidden" class="salon-plan salon-plan-hidden" name="salon_plans[{{$c_plan->plan_id}}]" id="salon_plans_id_{{$c_plan->plan_id}}" value="{{$c_plan->id}}" />
										<input type="hidden" name="is_new_plan[{{$c_plan->plan_id}}]" id="is_new_plan_{{$c_plan->plan_id}}" value="0">
										<input type="hidden" class="plan-igst-hidden" name="plans_igst[{{$c_plan->plan_id}}]"
											id="plans_igst_{{$c_plan->plan_id}}" value="{{ $c_plan->igst }}" />
										<input type="hidden" class="plan-sgst-hidden" name="plans_sgst[{{$c_plan->plan_id}}]"
											id="plans_sgst_{{$c_plan->plan_id}}" value="{{ $c_plan->sgst }}" />
										<input type="hidden" class="plan-cgst-hidden" name="plans_cgst[{{$c_plan->plan_id}}]"
											id="plans_cgst_{{$c_plan->plan_id}}" value="{{ $c_plan->cgst }}" />
										<input type="hidden" name="plans_price[{{$c_plan->plan_id}}]"
											id="plans_price_{{$c_plan->plan_id}}" value="{{ $c_plan->plan_price }}" />
										
									</td> 
									<td class="text-right d-none">
										{{ $c_plan->subscription_date }}
										<input type="hidden" class="subscription-date-hidden" name="subscription_date[{{$c_plan->plan_id}}]"
											id="subscription_date_{{$c_plan->plan_id}}" value="{{ $c_plan->subscription_date }}" />
									</td> 
									<td class="text-right">
										{{ $c_plan->plan_price }}
									</td> 
									<td>
										<input type="number" class="form-control text-right" name="plans_discount[{{$c_plan->plan_id}}]" min="0" max="100"
											id="plans_discount_{{$c_plan->plan_id}}" step=".01" onChange="getPlanDiscount({{$c_plan->plan_id}})" value="{{ $c_plan->discount }}" />
									</td>
									<td class="text-right">
										<span id="discount_amount_{{$c_plan->plan_id}}">{{ $c_plan->discount_amount }}</span>
										<input type="hidden" name="plans_discount_amount[{{$c_plan->plan_id}}]" id="plans_discount_amount_{{$c_plan->plan_id}}" value="{{ $c_plan->discount_amount }}" />
									</td>
									<td class="text-right">
										<span id="final_amount_{{$c_plan->plan_id}}">{{ $c_plan->final_amount }}</span><input type="hidden"
											name="plans_final_amount[{{$c_plan->plan_id}}]" id="plans_final_amount_{{$c_plan->plan_id}}"
											value="{{ $c_plan->final_amount }}" />
									</td>
								</tr>
								@endforeach
							@endif 
                            </tbody>
                            <tfoot>
                                <tr class="sgst d-none">
                                    <td colspan="2	"></td>
                                    <th colspan="2" class="align-middle">
                                        SGST %
                                        <input type="hidden" class="form-control" name="sgst_amount" id="sgst_amount"
                                            value="9" />
                                    </th>
                                    <th class="text-right" id="sgst_amount_html">0</th>
                                </tr>
                                <tr class="cgst d-none">
                                    <td colspan="2	"></td>
                                    <th colspan="2" class="align-middle">
                                        CGST %
                                        <input type="hidden" name="cgst_amount" id="cgst_amount" value="9" />
                                    </th>
                                    <th class="text-right" id="cgst_amount_html">0</th>
                                </tr>
                                <tr class="igst d-none">
                                    <td colspan="2	"></td>
                                    <th class="align-middle">IGST %</th>
                                    <td class="align-middle">
                                        <input type="hidden" class="form-control text-right" name="igst_amount"
                                            id="igst_amount" value="0" />
                                    </td>
                                    <th class="text-right" id="igst_amount_html">0</th>
                                </tr> 
                                <tr>
                                    <td colspan="2	"></td>
                                    <th colspan="2">Total Amount</th>
                                    <th class="text-right" id="final_amount">0</th>
                                </tr>
                                <tr>
                                    <td colspan="2	"></td>
                                    <th colspan="2">Payment Pending</th>
                                    <th class="text-left">
                                        <div class="radio-inline">
											@if(!isset($subscription))
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
                                    <td colspan="2	"></td>
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
                                    <td colspan="2	"></td>
                                    <th colspan="2" class="m-auto">Payment Date</th>
                                    <td>{!!
                                        Form::date('payment_date',
                                        isset($data['payment_date']) ? $data['payment_date'] : date("Y-m-d"),
                                        ['class' => 'form-control','placeholder'=>'Select Payment Date', 'min' =>
                                        date("Y-m-d")])
                                        !!}</td>
                                </tr>
                                <tr class="payment d-none">
                                    <td colspan="2	"></td>
                                    <th colspan="2" class="m-auto">Bank Name</th>
                                    <td>{!!
											Form::text('payment_bank_name',
											isset($data['payment_bank_name']) ? $data['payment_bank_name'] : null,
											['class' => 'form-control', 'id' => 'payment_bank_name','placeholder'=>'Enter Bank Name','pattern'=>'^[A-Za-z
											]*$', 'max'=>'40'])
                                        !!}
									</td>
                                </tr>
                                <tr class="payment d-none">
                                    <td colspan="2	"></td>
                                    <th colspan="2" class="m-auto">Transaction Number</th>
                                    <td>{!!
											Form::text('payment_number',
											isset($data['payment_number']) ? $data['payment_number'] : null,
											['class' => 'form-control text-right',
											 'id' => 'payment_number', 
											'placeholder'=>'Enter Transaction Number'])
                                        !!}
									</td>
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
                        {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' =>
                        'submitOrder']) !!}
                        <a href="#" class="btn btn-light-primary font-weight-bold ml-2 btn-cancel">Cancel</a>
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

	changeTax()

	// Check for GST Fields	
	<?php if($salon_data->state_id == 12): ?>
		if(!jQuery('.igst').hasClass('d-none'))
			jQuery('.igst').addClass('d-none');
		jQuery('.sgst').removeClass('d-none');
		jQuery('.cgst').removeClass('d-none');
	<?php else: ?>
		if(!jQuery('.sgst').hasClass('d-none'))
			jQuery('.sgst').addClass('d-none');
		if(!jQuery('.cgst').hasClass('d-none'))
			jQuery('.cgst').addClass('d-none');
		jQuery('.igst').removeClass('d-none');
	<?php endif; ?>  
  
	$('#plan-select').select2({
		placeholder: "Select plan",
		allowClear: true,
		ajax: {
			url: '{!! route('plans.byname') !!}',
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

  
var plans_final_amount = plans_net_amount = 0;

function addPlan(){
  
	var plan_id = jQuery('#plan-select').val();
	var plan_date = jQuery('.plan_date').val();  

	let plan_count = $(`.plan[value=${plan_id}]`).length; 
	if(plan_count > 0) {
		alert("Plan is already in invoice!");
		$("#plan-select").html("");
		return false;
	} 

	if(plan_id){  
		jQuery.ajax({
			url: '{!! route('subscriptions.plan.detail') !!}', //this is your uri
			type: 'GET',
			data: { 
				plan_id: plan_id, 
			},
			dataType: 'json',
			success: function(response){ 
				if(response.success){

					var plan = response.plan; 

					appendRow(plan, plan_date);  
					changeTax();
							
					jQuery('#plan-select').val('');
					jQuery('#plan-select').html(''); 
				}

				if(response.success == false) {
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

function appendRow(plan, plan_date)
{
	var plan_description = (plan.description) ? plan.description : '';
	var count = 30;
	var plan_desc = plan_description.slice(0, count) + (plan_description.length > count ? "..." : ""); 
	plan.price = parseFloat(plan.price);
  
	var html = `<tr id="plans_${plan.id}">
				<td>
					<a class="btn btn-link p-1" onClick="deletePlan(${plan.id})"><i
							class="flaticon2-trash text-danger"></i></a>${plan.name}
					<input type="hidden" class="plan plan-hidden" name="plans[${plan.id}]" id="plans_id_${plan.id}" value="${plan.id}" />
					<input type="hidden" name="is_new_plan[${plan.id}]" id="is_new_plan_${plan.id}" value="1">
					<input type="hidden" class="plan-igst-hidden" name="plans_igst[${plan.id}]"
						id="plans_igst_${plan.id}" value="${plan.igst}" />
					<input type="hidden" class="plan-sgst-hidden" name="plans_sgst[${plan.id}]"
						id="plans_sgst_${plan.id}" value="${plan.sgst}" />
					<input type="hidden" class="plan-cgst-hidden" name="plans_cgst[${plan.id}]"
						id="plans_cgst_${plan.id}" value="${plan.cgst}" />
					<input type="hidden" name="plans_price[${plan.id}]"
						id="plans_price_${plan.id}" value="${plan.price}" />
					<div class="word-wrap" title="${plan_description}">
						${plan_desc}
					</div> <br>
					Users : ${ plan.no_of_users} , Branches : ${ plan.no_of_branches} <br>
					SMS : ${ plan.no_of_sms} , Emails : ${ plan.no_of_email}
				</td> 
				<td class="text-right d-none">
					${plan_date}
					<input type="hidden" class="subscription-date-hidden" name="subscription_date[${plan.id}]"
						id="subscription_date_${plan.id}" value="${plan_date}" />
				</td> 
				<td class="text-right">
					${plan.price.toFixed(2)}
				</td> 
				<td>
					<input type="number" class="form-control text-right" name="plans_discount[${plan.id}]" min="0" max="100"
						id="plans_discount_${plan.id}" step=".01" onChange="getPlanDiscount(${plan.id})" value="0" />
				</td>
				<td class="text-right">
					<span id="discount_amount_${plan.id}">0.00</span><input type="hidden"
						name="plans_discount_amount[${plan.id}]" id="plans_discount_amount_${plan.id}" value="0" />
				</td>
				<td class="text-right">
					<span id="final_amount_${plan.id}">${plan.price.toFixed(2)}</span><input type="hidden"
						name="plans_final_amount[${plan.id}]" id="plans_final_amount_${plan.id}"
						value="${plan.price.toFixed(2)}" />
				</td>
			</tr>`;

	jQuery('#plan_list').append(html);
}

// Change Tax Amount
function changeTax(){  
	let total_igst = 0;
	let total_sgst = 0;
	let total_cgst = 0;
	var final_total_amount = 0; 


	$('.plan-hidden').each(function (index, input) {
		let plan_id = $(input).val();
		let plan_igst = $(`#plans_igst_${plan_id}`).val();
		let plan_sgst = $(`#plans_sgst_${plan_id}`).val();
		let plan_cgst = $(`#plans_cgst_${plan_id}`).val();  
		let plan_final_price = $(`#plans_final_amount_${plan_id}`).val(); 
   
		<?php if($salon_data->state_id == 12): ?>
			let gstObj = getTotalGSTAmount(plan_final_price, plan_sgst, plan_cgst, 0); 
			total_sgst += gstObj.sgst_amount;
			total_cgst += gstObj.cgst_amount;
		<?php else: ?> 
			let gstObj = getTotalGSTAmount(plan_final_price, 0, 0, plan_igst); 
			total_igst += gstObj.igst_amount; 
		<?php endif; ?>	

		console.log(gstObj)

		// Calculate Final Total
		final_total_amount += parseFloat(plan_final_price); 
	}); 

	<?php if($salon_data->state_id == 12): ?>
		$('#sgst_amount').val(total_sgst);
		$('#sgst_amount_html').html(total_sgst.toFixed(2));
		$('#cgst_amount').val(total_cgst);
		$('#cgst_amount_html').html(total_cgst.toFixed(2));
	<?php else: ?>
		$('#igst_amount').val(total_igst);
		$('#igst_amount_html').html(total_igst.toFixed(2));
	<?php endif; ?>	

	// Update Final Total
	jQuery('#final_amount').html(final_total_amount.toFixed(2));
}  

function getTotalGSTAmount(amount = 0, sgst = 0, cgst = 0, igst = 0)
{
	amount = parseFloat(amount);
	let total_tax = parseFloat(sgst) + parseFloat(cgst) + parseFloat(igst);

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
  
function getPlanDiscount(id){
	let discount = $("#plans_discount_"+id).val(); 

	if(discount<=100 && discount>=0){ 
		var old_plans_discount_amount = jQuery('#plans_discount_amount_'+id).val();
		var total_price = jQuery('#plans_price_'+id).val();
		  
		var discount_amount = total_price * discount / 100;
		discount_amount = parseFloat(discount_amount);
		
		jQuery('#plans_discount_amount_'+id).val(discount_amount);
		jQuery('#discount_amount_'+id).html(discount_amount.toFixed(2));
		var final_amount = total_price - discount_amount;
		final_amount = parseFloat(final_amount);
		jQuery('#plans_final_amount_'+id).val(final_amount);
		jQuery('#final_amount_'+id).html(final_amount.toFixed(2));
		plans_final_amount = parseFloat(plans_final_amount) + parseFloat(old_plans_discount_amount) - parseFloat(discount_amount);
		plans_final_amount = parseFloat(plans_final_amount);
		jQuery('#final_amount').html(plans_final_amount.toFixed(2));
		jQuery('#round_off_amount').html(plans_final_amount.toFixed(2));
		changeTax();
	}else{
		alert("total discount can not be more than 100%");
		$("#plans_discount_"+id).val(0);
		return false;
	}
}

function changePaymentMode(mode){
	if(mode != '' && mode != 'CASH'){
		jQuery('.payment').removeClass('d-none');
 
		$("#payment_bank_name").attr('required', true);
		$("#payment_number").attr('required', true);

		$("#payment_bank_name").val('');
		$("#payment_number").val('');
	}else{
		if(!jQuery('.payment').hasClass('d-none'))
		jQuery('.payment').addClass('d-none');

		$("#payment_bank_name").removeAttr('required');
		$("#payment_number").removeAttr('required');
	
		$("#payment_bank_name").val('');
		$("#payment_number").val('');
	}
}

function deletePlan(planId, entry_id = 0)
{   
	let is_new = $(`#is_new_plan_${planId}`).val();
  
	if(is_new == 1) {
		jQuery('#plans_'+planId).remove(); 
		changeTax(); 
	} else {
		Swal.fire({
			title: 'Are you sure?',
			text: "Do you want to delete plan/service?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#ccc',
			confirmButtonText: 'delete!'
		}).then((result) => {
			if (result.isConfirmed) {     
				window.location.href = "{{ url('admin/subscriptions/plan-delete') }}/" +entry_id;
			}
		}) 
	} 
}

// Toggle on load
<?php if(isset($subscription)): ?>
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
 
</script>