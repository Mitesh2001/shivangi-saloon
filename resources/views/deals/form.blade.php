<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row"> 
				@if($is_system_user == 0)
					@if(isset($deal))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $deal->distributor_id }}">
					@else
						<div class="col-lg-3 form-group-error"> 
							{!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
							<select name="distributor_id" id="distributor_id" class="form-control">
								@if(isset($selected_distributor))
									<option value="{{ $selected_distributor->id }}">{{ $selected_distributor->name }}</option>
								@endif
							</select>
							@if ($errors->has('distributor_id'))  
								<span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
							@endif
						</div> 
					@endif
				@endif 
				<div class="col-lg-3 form-group form-group-error"> 
					{!! Form::label('clients', __('clients'). ':', ['class' => '']) !!}
					{!!
						Form::select('clients[]',
						$selected_clients ?? [],
						null, 
						['class' => 'form-control',
						'id' => 'clients',
						'multiple' => 'multiple'])
					!!} 
					
					@if ($errors->has('clients'))  
						<span class="form-text text-danger">{{ $errors->first('clients') }}</span>
					@else 
						<span class="text-muted">Leave blank for all clients</span>
					@endif
				</div>
			</div> 
			<div class="form-group row"> 
				<!-- <div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('segament_id', __('Segaments'). ':', ['class' => '']) !!} 
					{!! Form::select('segament_id',
						$selected_segament ?? [],
						old('segament_id'),	
						['class' => 'form-control searchable-select'])
					!!}
					@if ($errors->has('segament_id'))  
						<span class="form-text text-danger">{{ $errors->first('segament_id') }}</span>
					@endif  
				</div>   -->
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('deal_name', __('Deal Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('deal_name',  
						$data['deal_name'] ?? old('deal_name'), 
						['class' => 'form-control',
						'placeholder' => "Deal Name"]) 
					!!}
					@if ($errors->has('deal_name'))  
                        <span class="form-text text-danger">{{ $errors->first('deal_name') }}</span>
                    @endif  
				</div>  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('deal_code', __('Deal Code'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('deal_code',  
						$data['deal_code'] ?? old('deal_code'), 
						['class' => 'form-control',
						'placeholder' => "Deal Code"]) 
					!!}
					@if ($errors->has('deal_code'))  
                        <span class="form-text text-danger">{{ $errors->first('deal_code') }}</span>
                    @endif  
				</div>  
				<!-- <div class="col-lg-3 form-group form-group-error">
					{!! Form::label('deal_description', __('Deal Description'). ':', ['class' => '']) !!}
					{!! 
						Form::text('deal_description',  
						$data['deal_description'] ?? old('deal_description'), 
						['class' => 'form-control',
						'placeholder' => "Deal Description"]) 
					!!}
					@if ($errors->has('deal_description'))  
                        <span class="form-text text-danger">{{ $errors->first('deal_description') }}</span>
                    @endif  
				</div>   -->
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('validity', __('validity'). ': *', ['class' => '']) !!}
					{!! 
						Form::date('validity',  
						$data['validity'] ?? old('validity'), 
						['class' => 'form-control',
						'placeholder' => "validity", 'min' => date('Y-m-d') ]) 
					!!}
					@if ($errors->has('validity'))  
                        <span class="form-text text-danger">{{ $errors->first('validity') }}</span>
                    @endif  
				</div>  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('redemptions_max', __('Redemptions Max'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('redemptions_max',  
						$data['redemptions_max'] ?? old('redemptions_max'), 
						['class' => 'form-control',
						'placeholder' => "Redemptions Max", "number" => "true", 'min' => 0]) 
					!!}
					@if ($errors->has('redemptions_max'))  
                        <span class="form-text text-danger">{{ $errors->first('redemptions_max') }}</span>
                    @endif  
				</div>
			</div>  
			<div class="form-group row">   
				<!-- <div class="col-lg-3 form-group form-group-error">
					{!! Form::label('start_at', __('Deal Start Time'). ':', ['class' => '']) !!}
					{!! 
						Form::text('start_at',  
						isset($deal->start_at) ? date('h:i a', strtotime($deal->start_at)) : "", 
						['class' => 'form-control',
						'placeholder' => "Deal Time From"]) 
					!!}
					@if ($errors->has('start_at')) 
					<span class="form-text text-danger">{{ $errors->first('start_at') }}</span>
                    @else 
					<span class="text-muted">leave blank if 24 hrs</span>
					@endif
				</div>  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('end_at', __('Deal End Time'). ':', ['class' => '']) !!}
					{!! 
						Form::text('end_at',  
						isset($deal->end_at) ? date('h:i a', strtotime($deal->end_at)) : "", 
						['class' => 'form-control',
						'placeholder' => "Deal Time to"]) 
					!!} 
					@if ($errors->has('end_at'))   
                        <span class="form-text text-danger">{{ $errors->first('end_at') }}</span>
                    @else
						<span class="text-muted">leave blank if 24 hrs</span>
					@endif
				</div>   -->
				<div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('applicable_on_weekends', __('Applicable on Weekends'). ': *', ['class' => '']) !!} 
					{!! Form::select('applicable_on_weekends',
						['0' => 'No',
						'1' => 'Yes'],
						old('applicable_on_weekends'),	
						['class' => 'form-control searchable-select'])
					!!}
					@if ($errors->has('applicable_on_weekends'))  
						<span class="form-text text-danger">{{ $errors->first('applicable_on_weekends') }}</span>
					@endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('applicable_on_holidays', __('Applicable on Holidays'). ': *', ['class' => '']) !!} 
					{!! Form::select('applicable_on_holidays',
						['0' => 'No',
						'1' => 'Yes'],
						old('applicable_on_holidays'),	
						['class' => 'form-control'])
					!!}
					@if ($errors->has('applicable_on_holidays'))  
						<span class="form-text text-danger">{{ $errors->first('applicable_on_holidays') }}</span>
					@endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('applicable_on_bday_anniv', __("Applicable on B'day/Anniv"). ': *', ['class' => '']) !!} 
					{!! Form::select('applicable_on_bday_anniv',
						['0' => 'No',
						'1' => 'Yes'],
						old('applicable_on_bday_anniv'),	
						['class' => 'form-control'])
					!!}
					@if ($errors->has('applicable_on_bday_anniv'))  
						<span class="form-text text-danger">{{ $errors->first('applicable_on_bday_anniv') }}</span>
					@endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('week_days', __('Select Week Days'). ': *', ['class' => '']) !!} 
					{!! Form::select('week_days',
						[
							'All' => 'All Days',
							'monday' => 'Monday',
							'tuesday' => 'Tuesday',
							'wednesday' => 'Wednesday',
							'thirsday' => 'Thirsday',
							'friday' => 'Friday',
							'saturday' => 'Saturday',
							'Sunday' => 'Sunday',
						],
						old('week_days'),	
						['class' => 'form-control'])
					!!}
					@if ($errors->has('week_days'))  
						<span class="form-text text-danger">{{ $errors->first('week_days') }}</span>
					@endif  
				</div> 
				<!-- <div class="col-lg-3 form-group form-group-error">  
					{!! Form::label('benefit_type', __("Benefit Type"). ': *', ['class' => '']) !!} 
					{!! Form::select('benefit_type',
						['Discount' => 'Discount'],
						old('benefit_type'),	
						['class' => 'form-control'])
					!!}
					@if ($errors->has('benefit_type'))  
						<span class="form-text text-danger">{{ $errors->first('benefit_type') }}</span>
					@endif  
				</div>  -->
			</div>  
			<div class="form-group row">   
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('invoice_min_amount', __('Invoice Min Amount'). ':', ['class' => '']) !!}
					{!! 
						Form::number('invoice_min_amount',  
						$data['invoice_min_amount'] ?? old('invoice_min_amount'), 
						['class' => 'form-control',
						'placeholder' => "Invoice Min Amount", "number" => "true", 'min' => 0]) 
					!!}
					@if ($errors->has('invoice_min_amount'))  
                        <span class="form-text text-danger">{{ $errors->first('invoice_min_amount') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('invoice_max_amount', __('Invoice Max Amount'). ':', ['class' => '']) !!}
					{!! 
						Form::number('invoice_max_amount',  
						$data['invoice_max_amount'] ?? old('invoice_max_amount'), 
						['class' => 'form-control',
						'placeholder' => "Invoice Max Amount", "number" => "true", 'min' => 0]) 
					!!}
					@if ($errors->has('invoice_max_amount'))  
                        <span class="form-text text-danger">{{ $errors->first('invoice_max_amount') }}</span>
                    @endif  
				</div>  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('discount', __('Discount (in %)'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('discount',  
						$data['discount'] ?? old('discount'), 
						['class' => 'form-control',
						'placeholder' => "Discount", "number" => "true", 'min' => 0]) 
					!!}
					@if ($errors->has('discount'))  
                        <span class="form-text text-danger">{{ $errors->first('discount') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-3 form-group-error pt-5">
					<span class="switch form-group switch-primary" data-toggle="tooltip" title="Active/Inactive deal">
						Is Active &nbsp; &nbsp;
						<label> 
						@if(\Entrust::can('deal-toggle'))
							@if(isset($deal))
								@if($deal->is_active == 0)
								<input type="checkbox" name="is_active">
								@else 
								<input type="checkbox" name="is_active" checked>
								@endif
							@else 
								<input type="checkbox" name="is_active" checked>
							@endif
							<span></span>                
						@else 
							@if(isset($deal))
								@if($deal->is_active == 0)
								<input type="checkbox" name="is_active" disabled>
								@else 
								<input type="checkbox" name="is_active" checked disabled>
								@endif
							@else 
								<input type="checkbox" name="is_active">
							@endif
							<span></span> 
						@endif
							
						</label>
					</span>
				</div>  
			</div>   
			<div class="row"> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('Apply on products', __('Apply on Product/service'). ':', ['class' => '']) !!}
					{!!
						Form::select('products[]',
						$selected_products ?? [],
						null, 
						['class' => 'form-control',
						'id' => 'products',
						'multiple' => 'multiple'])
					!!} 
					@if ($errors->has('deal_description'))  
                        <span class="form-text text-danger">{{ $errors->first('products') }}</span>
                    @endif  
					<span class="text-muted">Please select products or check apply on bill amount</span>
				</div>
				<div class="col-lg-3 form-group mt-6 form-group-error">
					<label class="checkbox">
						@if(isset($deal->apply_on_bill_total))
							@if($deal->apply_on_bill_total != 1)
							<input type="checkbox" id="apply_on_bill_total" name="apply_on_bill_total"/>
							@else 
							<input type="checkbox" checked="true" id="apply_on_bill_total" name="apply_on_bill_total"/>
							@endif
						@else
							<input type="checkbox" id="apply_on_bill_total" name="apply_on_bill_total"/>
						@endif
						<span></span>
						&nbsp; &nbsp;
						Apply on bill amount
					</label>
				</div>
			</div>
			<div class="form-group row">    
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('deal_description', __('Deal Description'). ':', ['class' => '']) !!}
					{!! 
						Form::textarea('deal_description',  
						$data['deal_description'] ?? old('deal_description'), 
						['class' => 'form-control',
						'rows' => 2,
						'placeholder' => "Deal Description"]) 
					!!}
					@if ($errors->has('deal_description'))  
                        <span class="form-text text-danger">{{ $errors->first('deal_description') }}</span>
                    @endif  
				</div>   
			</div>   	
			 
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
					{!! Form::button("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'cancleDeal']) !!}
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->  

<script>
$(document).ready(function () { 

	ClassicEditor.create(document.querySelector('#deal_description'));
	toggleProductsInput();

	$(document).on('click', '#cancleDeal', function () {
		location.reload();
	});
	
	$(document).on("change", "#apply_on_bill_total", function (e){
		toggleProductsInput();
	});

	function toggleProductsInput()
	{
		if($("#apply_on_bill_total").prop('checked') == true) {
			$('#products').val("").trigger('change');
			$('#products').attr('disabled', true);
		} else {
			$('#products').removeAttr('disabled');
		}
	}

	<?php if(!isset($deal)): ?>
		$('#distributor_id').select2({
			placeholder: "Select Salon",
			allowClear: true,
			ajax: {
				url: '{!! route('salons.byname') !!}',
				dataType: 'json', 
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
		})
		$(document).on('change', '#distributor_id', function (){
			$("#clients").html("");
			$("#products").html("");
		})
	<?php endif; ?>

	$('#products').select2({
		placeholder: "Select Products",
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
	 
	$('#clients').select2({
		placeholder: "Select Client",
		allowClear: true,
		ajax: {
			url: '{!! route('leads.clientsbyname') !!}',
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

	$('#segament_id').select2({
		placeholder: "Select Segament",
		allowClear: true,
		ajax: {
			url: '{!! route('deals.segamentsByName') !!}',
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
  

	$(document).on('change', '.min-price', function (e){
		$(e.target).closest('tr').find('.max-price').attr('min', e.target.value);
	});

	$(document).on('change', '.max-price', function (e){
		$(e.target).closest('tr').find('.min-price').attr('max', e.target.value);
	}); 

	let start_time = $('#start_at').timepicker({
		showMeridian: false,
		defaultTime: false,
	});
	let end_time = $('#end_at').timepicker({
		showMeridian: false,
		defaultTime: false,
	}); 
	init_category_select2(`#category_1`);
	  

	$(document).on('change', '.sub_category', function (e){
		let category = $(e.target).closest('tr').find('.category').val();
		if(category == "") {
			alert("Please select category first");
			$(e.target).val(null).trigger('change');
		}
	});

	$(document).on('change', '.product-id', function (e){
		let category = $(e.target).closest('tr').find('.category').val(); 
		if(category == "" || category == null) {
			alert("Please select category first");
			$(e.target).html("");
			return false;
		}

		if(e.target.value.length > 0) {
			$(this).closest('tr').find('.min-price').attr('disabled', true);
			$(this).closest('tr').find('.min-price').val("");
			$(this).closest('tr').find('.max-price').attr('disabled', true);
			$(this).closest('tr').find('.max-price').val("");
		} else {
			$(this).closest('tr').find('.min-price').removeAttr('disabled', true); 
			$(this).closest('tr').find('.max-price').removeAttr('disabled', true); 
		}
	});

	var length = $(".deal-products-table .product-row").length; 
    for(var x = 1; x <= length; x++) { 
		init_category_select2(`#category_${x}`);
		init_sub_category_select2(`#category_${x}`, `#sub_category_${x}`);
		init_product_select2(`#product_${x}`, `#category_${x}`, `#sub_category_${x}`);
    }

	$(document).on('change', '.category', function (e) {
		let x = $(e.target).closest('.product-row').data('row');  
		$(`#product_${x}`).val("");
		$(`#sub_category_${x}`).val(""); 
		init_sub_category_select2(`#category_${x}`, `#sub_category_${x}`);
		init_product_select2(`#product_${x}`, `#category_${x}`, `#sub_category_${x}`); 
	});

	$(document).on('change', '.sub_category', function (e) {
		let x = $(e.target).closest('.product-row').data('row');   
		$(`#product_${x}`).val("");  
		init_product_select2(`#product_${x}`, `#category_${x}`, `#sub_category_${x}`); 
	});
	
    // Add more week off tr
    $(document).on('click', '.add-product', function () { 

		var dynamic_id = $('.deal-products-table tr:last').data('row');

		if(typeof dynamic_id == 'undefined') {
			dynamic_id = 1;
		} else {
			dynamic_id++;
		}
  
		var product_row = `
			<tr class="product-row" data-row="${dynamic_id}">
				<td class="form-group-error"> 
					<select name="deal_array[${dynamic_id}][type]" class="form-control type" id="type_${dynamic_id}" required>
						<option value="">Select Type</option>
						<option value="0">Product</option>
						<option value="1">Service</option>
					</select>
				</td>
				<td class="form-group-error"> 
					<select name="deal_array[${dynamic_id}][category]" class="form-control category" id="category_${dynamic_id}">
						<option value="">Select Category</option> 
					</select>
				</td>
				<td class="form-group-error"> 
					<select name="deal_array[${dynamic_id}][sub_category]" class="form-control sub_category" id="sub_category_${dynamic_id}">
						<option value="">Select Sub Category</option> 
					</select>
				</td>
				<td class="form-group-error"> 
					<select name="deal_array[${dynamic_id}][product]" class="form-control product-id" id="product_${dynamic_id}">
						<option va	lue="">Select Product</option> 
					</select>
				</td>
				<td class="form-group-error"> 
					<input type="number" name="deal_array[${dynamic_id}][min_price]" class="form-control min-price" placeholder="Min Price" min="0" id="min_price_${dynamic_id}" required nuber>
				</td>
				<td class="form-group-error"> 
					<input type="number" name="deal_array[${dynamic_id}][max_price]" class="form-control max-price" placeholder="Max Price" min="0" id="max_price_${dynamic_id}" required number>
				</td>
				<td class="form-group-error"> 
					<a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product/Service">
						<i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="" ></i>
					</a>
				</td>
			</tr>
		`;
  
		$(".deal-products-table").append(product_row);  
		init_category_select2(`#category_${dynamic_id}`);
		$('[data-toggle="tooltip"]').tooltip();
	}); 
 
	// Remove Product TR 
	$(document).on('click', '.remove-product', function (e){  

		var product_id = $(e.target).data('product-id'); 
  
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
					url: '{!! route('deals.removeProduct') !!}',
					type: "POST",
					dataType: 'json',
					cache: false,
					data: {
						_token: "{{ csrf_token() }}", 
						product_id: product_id, 
					},
					success: function (res) {
						$('.tooltip').tooltip().remove(); 
						$(e.target).closest('tr').remove();
						Swal.fire({
							title: 'Removed!',
							text: 'Product/service Removed Successfully!',
							icon: 'success',
							timer: 3000
						}); 
					}
				}) 
			} 
		})
 
	}); 

 
    // Init Select2 (Product Field)
    function init_category_select2(element_id) { 
		$(element_id).select2({
			placeholder: "Select Category",
			allowClear: true,
			ajax: {
				url: '{!! route('category.categorybyname') !!}',
				dataType: 'json', 
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
    }

	$(document).on('change', '.category', function (e) {
		var category_element_id = $(e.target).attr('id'); 
		var sub_category_element_id = $(e.target).closest('tr').find('.sub_category').attr('id');
		init_sub_category_select2("#"+category_element_id, "#"+sub_category_element_id); 

		var product_element_id = "#"+$(e.target).closest('tr').find('.product-id').attr('id');
		var category_element_id = "#"+ $(e.target).attr('id');
		var sub_category_element_id = "#"+ $(e.target).closest('tr').find('.sub_category').attr('id');

		init_product_select2(product_element_id, category_element_id, sub_category_element_id);
	})

    // Init Select2 (Product Field)
    function init_sub_category_select2(category_element_id, element_id) {  
		$(element_id).select2({
			placeholder: "Select Category",
			allowClear: true,
			ajax: {
				url: '{!! route('category.subCategoryByName') !!}',
				dataType: 'json', 
				data: function (params) { 
					ultimaConsulta = params.term;
					var category_id = $(category_element_id).val();
					return {
						name: params.term, // search term
						category_id: category_id,
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
    }
 
    // Init Select2 (Product Field)
    function init_product_select2(product_element_id, category_element_id, sub_category_element_id) {
  
        $(product_element_id).select2({
            placeholder: "Select Product",
            allowClear: true,
            ajax: {
                url: '{!! route('product.byCategory') !!}',
                dataType: 'json', 
				data: function (params) { 
					ultimaConsulta = params.term;
					var category_id = $(category_element_id).val();
					var sub_category_id = $(sub_category_element_id).val();
					var distributor_id = $("#distributor_id").val();
					return {
						name: params.term, // search term
						category_id: category_id,
						sub_category_id: sub_category_id,
						distributor_id : distributor_id,
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
    }
})
</script>