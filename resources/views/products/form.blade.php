<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form"> 
		<div class="card-body">
			<div class="row"> 
				@if($is_system_user == 0 && !isset($product))  
					<div class="col-lg-3 form-group  form-group-error"> 
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
				@else 
					<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $product->distributor_id ?? 0 }}"> 
				@endif 
			</div>
			<div class="row">
				<div class="col-lg-3 form-group form-group-error"> 
				{!! Form::label('name', __('Type'). ': *', ['class' => '']) !!}
				
					<select name="type" id="type" class="type form-control">
						<option value="">Select Type</option>

						<?php 
							$product_types = [
								0 => "Product",
								1 => "Service",
								2 => "Package",
							]; 
						?>
						
						@foreach($product_types as $key => $value)

							@if(isset($product->type)) 
								@if($product->type == $key)
									<option value="{{ $key }}" selected>{{ $value }}</option>
								@else 
									<option value="{{ $key }}" disabled>{{ $value }}</option>
								@endif
							@else 
							<option value="{{ $key }}">{{ $value }}</option>
							@endif 
						@endforeach 
					</select>
					@if ($errors->has('type'))  
						<span class="form-text text-danger">{{ $errors->first('type') }}</span>
					@endif 
				</div> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('name', __('Product/Service Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						$data['name'] ?? old('name'), 
						['class' => 'form-control',
						'placeholder' => "Product Name"]) 
					!!}
					@if ($errors->has('name'))  
                        <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('categories', __('Category'). ':', ['class' => '']) !!} 
					{!! Form::select('categories[]',
						$selected_categories ?? [],
						old('categories'),	
						['class' => 'form-control', 'multiple' => 'multiple', 'id' => 'categories'])
					!!}
					@if ($errors->has('categories'))  
						<span class="form-text text-danger">{{ $errors->first('categories') }}</span>
					@endif 
				</div> 
				<div class="col-lg-3 form-group form-group-error sku_code_group">
					{!! Form::label('sku_code', __('SKU Code'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('sku_code',  
						old('sku_code') ?? null, 
						['class' => 'form-control',
						'placeholder' => "SKU Code"]) 
					!!}
					@if ($errors->has('sku_code'))  
						<span class="form-text text-danger">{{ $errors->first('sku_code') }}</span>
					@endif  
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 form-group form-group-error">  
					{!! Form::label('unit_id', __('Unit'). ': *', ['class' => 'unit-label']) !!} 
					<select name="unit_id" id="unit_id" class="form-control searchable-select">
						<option value="" data-name="QTY"> Select Unit </option>
						@if(isset($product))
							@foreach($units as $unit)  
								@if($product->unit_id == $unit['id']) 
									<option value="{{ $unit['id'] }}" data-name="{{ $unit['name'] }}" selected>{{ $unit['name'] }}</option>
								@else 
									<option value="{{ $unit['id'] }}" data-name="{{ $unit['name'] }}">{{ $unit['name'] }}</option>
								@endif
							@endforeach
						@else
							@foreach($units as $unit) 
								<option value="{{ $unit['id'] }}" data-name="{{ $unit['name'] }}">{{ $unit['name'] }}</option>
							@endforeach
						@endif
					</select>

					@if ($errors->has('unit_id'))  
						<span class="form-text text-danger">{{ $errors->first('unit_id') }}</span>
					@endif  
				</div> 
				<div class="col-md-3 form-group form-group-error">
					{!! Form::label('purchase_price', __('Purchase Price'). ': *', ['class' => '']) !!}
					<div class="input-group">
						<div class="input-group-prepend"><span class="input-group-text unit-prepend">QTY</span></div>
						{!! 
							Form::number('purchase_price',  
							$data['purchase_price'] ?? old('purchase_price'), 
							['class' => 'form-control',
							'placeholder' => "Purchase Price", 'min' => 0, 'number' => true]) 
						!!}
					</div> 
					@if ($errors->has('price'))  
						<span class="form-text text-danger">{{ $errors->first('price') }}</span>
					@endif
				</div> 
				<div class="col-md-3 form-group form-group-error">
					{!! Form::label('sales_price', __('Sales Price'). ': *', ['class' => '']) !!}
					<div class="input-group">
						<div class="input-group-prepend"><span class="input-group-text unit-prepend">QTY</span></div>
						{!! 
							Form::number('sales_price',  
							$data['sales_price'] ?? old('sales_price'), 
							['class' => 'form-control',
							'placeholder' => "Sales Price", 'min' => 0, 'number' => true]) 
						!!}
					</div> 
					@if ($errors->has('price'))  
						<span class="form-text text-danger">{{ $errors->first('price') }}</span>
					@endif
					<span class="text-muted">Inclusive GST</span>
				</div> 
				<div class="form-group col-md-3">
					<div class="form-group-error"> 
						{!! Form::label('expiry_reminder', __('Stock Remider'). ': *', ['class' => '']) !!}
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text unit-prepend">QTY</span></div>
							{!! 
								Form::number('expiry_reminder',  
								$data['expiry_reminder'] ?? old('expiry_reminder'), 
								['class' => 'form-control',
								'placeholder' => "Stock Remider",
								'min' => 0,
								'step' => 1]) 
							!!}
						</div> 
						@if ($errors->has('expiry_reminder'))  
							<span class="form-text text-danger">{{ $errors->first('expiry_reminder') }}</span>
						@else 
							<span class="text-muted">Stock remider at specific QTY</span>
						@endif
					</div>  
				</div> 
				<div class="form-group col-md-3">
					<div class="form-group-error"> 
						{!! Form::label('reorder_qty', __('Reorder Qty'). ': *', ['class' => '']) !!}
							<div class="input-group">
								<div class="input-group-prepend"><span class="input-group-text unit-prepend">QTY</span></div>
								{!! 
									Form::number('reorder_qty',  
									$data['reorder_qty'] ?? old('reorder_qty'), 
									['class' => 'form-control',
									'placeholder' => "Reorder Qty",
									'min' => 0,
									'step' => 1]) 
								!!}
							</div> 
						@if ($errors->has('reorder_qty'))  
							<span class="form-text text-danger">{{ $errors->first('reorder_qty') }}</span>
						@endif
					</div>  
				</div> 
				<div class="col-md-3 form-group form-group-error">
					{!! Form::label('sgst', __('SGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('sgst',
						$data['sgst'] ?? old('sgst'),  
						['class' => 'form-control',
						'placeholder' => "SGST", 
						'min' => 0, 'max' => 99.99, 
						'number' => true, 
						'step' => "0.01"]) 
					!!}
					@if ($errors->has('sgst'))  
						<span class="form-text text-danger">{{ $errors->first('sgst') }}</span>
					@endif
					<span class="text-muted">SGST in %</span>
				</div> 
				<div class="col-md-3 form-group form-group-error">
					{!! Form::label('cgst', __('CGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('cgst',  
						$data['cgst'] ?? old('cgst'),
						['class' => 'form-control',
						'placeholder' => "CGST", 
						'min' => 0, 'max' => 99.99,
						'number' => true, 
						'step' => "0.01"]) 
					!!}
					@if ($errors->has('cgst'))  
						<span class="form-text text-danger">{{ $errors->first('cgst') }}</span>
					@endif
					<span class="text-muted">CGST in %</span>
				</div> 
				<div class="col-md-3 form-group form-group-error">
					{!! Form::label('igst', __('IGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('igst',  
						$data['igst'] ?? old('igst'), 
						['class' => 'form-control',
						'placeholder' => "IGST", 
						'min' => 0, 'max' => 99.99, 
						'number' => true, 
						'step' => "0.01"]) 
					!!}
					@if ($errors->has('price'))  
						<span class="form-text text-danger">{{ $errors->first('price') }}</span>
					@endif
					<span class="text-muted">IGST in %</span>
				</div> 
				<!-- <div class="col-md-3 form-group form-group-error">  
					{!! Form::label('package_id', __('Package'). ':', ['class' => '']) !!} 
					{!! Form::select('package_id',
						['' => 'Select Package'] + $packages,
						old('package_id'),	
						['class' => 'form-control searchable-select'])
					!!}
					@if ($errors->has('package_id'))  
						<span class="form-text text-danger">{{ $errors->first('package_id') }}</span>
					@endif   
				</div> --> 
			</div> 
			<div class="row">
				<div class="col-lg-6">   
					<div class="row mt-mb-5">  
						<div class="form-group col-md-6">
							<div class="form-group-error custom-file mt-6">
								{!! 
									Form::file('thumbnail', 	
									['class' => 'custom-file-input',
									'id' => 'thumbnail']) 
									!!}
								{!! Form::label('thumbnail', __('Image'). ':', ['class' => 'custom-file-label']) !!}
								@if ($errors->has('thumbnail'))  
									<span class="form-text text-danger">{{ $errors->first('thumbnail') }}</span>
								@endif 
								<br><br>
								{!! Form::hidden('old_thumbnail', $product->thumbnail ?? NULL) !!}
								@if(isset($product->thumbnail))
									<img src="{{ asset($product->thumbnail) }}" id="product-thumbnail" alt="" height="150px">
								@else 
									<img src="{{ asset('storage/assets/no_image.png') }}" id="product-thumbnail" alt="" height="150px">
								@endif 
							</div> 
						</div>   
						<div class="form-group col-md-6">
							<div class="form-group-error custom-file mt-6"> 
								{!! 
									Form::file('other_document', 
									['class' => 'custom-file-input']) 
									!!}
								{!! Form::label('other_document', __('Other Document'). ':', ['class' => 'custom-file-label']) !!}
								@if ($errors->has('other_document'))  
									<span class="form-text text-danger">{{ $errors->first('other_document') }}</span>
								@endif 
								{!! Form::hidden('old_other_document', $product->other_document ?? NULL) !!} 
							</div> 
						</div>   
					</div> 
				</div> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('description', __('Description'). ':', ['class' => '']) !!}
					{!! 
						Form::textarea('description',  
						old('description'), 
						['rows' => 5,'class' => 'form-control',
						'placeholder' => "Description"]) 
					!!}
					@if ($errors->has('description'))  
						<span class="form-text text-danger">{{ $errors->first('description') }}</span>
					@endif
				</div> 
			</div> 
			<div class="row">
				<div class="col-lg-6 form-group form-group-error checkbox-inline is_default">
					<label class="checkbox">
						@if(isset($product)) 
							@if($product->is_default == 1) 
							<input type="checkbox" checked="checked" name="is_default_service" id="is_default_service"/>
							@else 
								<input type="checkbox" name="is_default_service" id="is_default_service"/>
							@endif 
						@else 
							<input type="checkbox" name="is_default_service" id="is_default_service"/>
						@endif 
						<span></span>
						Default Service  
					</label>
					@if ($errors->has('is_default_service')) 
						<span class="form-text text-danger">{{ $errors->first('is_default_service') }}</span>
					@endif  
				</div>
			</div> 

			<div class="package-products-card">
				<hr>
				<h5>Add Product / Service to package</h5>
				<hr>

				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th width="70%">Product/Service</th> 
								<th width="25%">Qty</th> 
								<th width="5%"> </th>
							</tr>
						</thead>
						<tbody class="table-products"> 
							@if(isset($product))
								<?php $i = 1; ?>
								@foreach($package_products as $p_product)
									<tr class="product-row" data-row="{{ $i }}">
										<td class="form-group-error form-group"> 
											<select name="product_data[{{ $i }}][product_id]" id="product_id_{{ $i }}" class="form-control product_id dynamic-input requied_field" required>
												<option value="{{ $p_product->pivot->product_id }}" selected>{{ $p_product->name }}</option>
											</select> 
										</td>  
										<td class="form-group-error form-group">
											<input type="number" min="1" name="product_data[{{ $i }}][qty]" id="qty_{{ $i }}" class="form-control qty dynamic-input requied_field number-input" placeholder="QTY" value="{{ $p_product->pivot->qty }}" required number> 
										</td> 
										<td class="form-group-error form-group">
											<a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product/Service">
												<i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="" ></i>
											</a>
										</td>
									</tr>  
									<?php $i++; ?>
								@endforeach
							@else 
								<tr class="product-row" data-row="1">
									<td class="form-group-error form-group"> 
										<select name="product_data[1][product_id]" id="product_id_1" class="form-control product_id dynamic-input requied_field" required></select> 
									</td>  
									<td class="form-group-error form-group">
										<input type="number" min="1" name="product_data[1][qty]" id="qty_1" class="form-control qty dynamic-input requied_field number-input" placeholder="QTY" required number> 
									</td> 
									<td class="form-group-error form-group">
										<a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product/Service">
											<i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="" ></i>
										</a>
									</td>
								</tr>  
							@endif 
						</tbody>
					</table>
				</div>
	
				<div class="row">
					<div class="col-lg-6">
						<a href="javascript:void(0)" class="btn btn-primary add-product" data-toggle="tooltip"
							title="Add Product">
							<i class="flaticon-plus icon-lg"></i>
							Add
						</a> 
					</div>
				</div>
			</div> 
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('is_popup', $_GET['is_popup'] ?? null, ['id' => 'is_popup']) !!}
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitClient']) !!}
				</div> 
			</div>
		</div>
	</duv>
	<!--end::Form-->
</div>
<!--end::Card-->

<script>
$(document).ready(function () {
	
   
	// Check duplicate product/service in package
    $(document).on('change', '.product_id', function (e) {
        let target_input = $(e.target);
        let id = $(e.target).val() 
        let attr_id = $(e.target).attr('id');
 
        $(".product_id").each(function (index, input){ 
            let current_attr_id = $(input).attr('id');  

			if($(input).val() !== null) {
				if(id == $(input).val() && current_attr_id != attr_id) { 
					target_input.val(null).trigger('change');
					alert("Can not add same product/service!");
					return false;
				} 
			}  
        });
    });
        
	// Remove Product TR 
	$(document).on('click', '.remove-product', function (e){  
		$('.tooltip').tooltip().remove(); 
		$(e.target).closest('tr').remove(); 
	}); 
	
    // Add more products tr
    $(document).on('click', '.add-product', function () { 
		
		let dynamic_id = $('.table-products tr:last').data('row') + 1;

		if(isNaN(dynamic_id)) {
			dynamic_id = 1;
		}

		let product_row = `
			<tr class="product-row" data-row="${dynamic_id}">
				<td class="form-group-error form-group" style="width:200px"> 
					<select name="product_data[${dynamic_id}][product_id]" id="product_id_${dynamic_id}" class="form-control product_id dynamic-input requied_field" required></select> 
				</td>  
				<td class="form-group-error form-group">
					<input type="number" min="1" name="product_data[${dynamic_id}][qty]" id="qty_${dynamic_id}" class="form-control qty dynamic-input requied_field" placeholder="QTY" required number> 
				</td> 
				<td class="form-group-error form-group">
					<a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product/Service">
						<i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="" ></i>
					</a>
				</td>
			</tr>
		`;  

		$(".table-products").append(product_row); 
		init_product_select2(`#product_id_${dynamic_id}`);
		$('[data-toggle="tooltip"]').tooltip();
	}); 

    let length = $(".table-products .product-row").length; 
    for(let x = 1; x <= length; x++) {
        init_product_select2(`#product_id_${x}`);
    }
 
    // Init Select2 (Product Field)
    function init_product_select2(element_id) { 
        $(element_id).select2({
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
						package: 1,
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

 
	ClassicEditor.create(document.querySelector('#description'));

	toggleSKUCode();
	toggleDefault();
	toggleUnit();
	togglePackageTable();
	compulsoryUnitInput();

	$(document).on('change', '#type', function (e) {
		toggleSKUCode();
		toggleDefault();
		togglePackageTable();
		compulsoryUnitInput();
	});

	$(document).on('change', '#unit_id', function (e) {
		toggleUnit();
	});
	
	function compulsoryUnitInput() {
		var type = $("#type").val();
		if(type == 0) { 
			return $(".unit-label").html("Unit: *");
		} else {
			return $(".unit-label").html("Unit:");
		}
	}

	function toggleUnit() {
		let unit = $("#unit_id option:selected").attr('data-name');
		$(".unit-prepend").html(unit);
	}

	function togglePackageTable()
	{
		let product_type = $("#type").val();
		
		if(product_type != "2") {
			$(".package-products-card").addClass('d-none');
		} else {
			$(".package-products-card").removeClass('d-none');
		}
	}

	function toggleSKUCode()
	{
		let product_type = $("#type").val();
  
		if(product_type == "1") {
			$(".sku_code_group").addClass('d-none');
		} else {
			$(".sku_code_group").removeClass('d-none');
		}
	}

	function toggleDefault()
	{
		let product_type = $("#type").val();
  
		if(product_type == "0") {
			$(".is_default").addClass('d-none');
		} else {
			$(".is_default").removeClass('d-none');
		}
	}

	<?php if($is_system_user == 0 && !isset($product)): ?>
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

		$(document).on('change', '#distributor_id', function (e) {
			$("#client_external_id").val("").trigger('change');
			$("#branch_id").val("").trigger('change');
			$("#user_assigned_id").val("").trigger('change');
		});

	<?php endif; ?>

	$(document).on('change', '#thumbnail', function (event){
		$('#product-thumbnail').attr('src', URL.createObjectURL(event.target.files[0]));
	});

	$('#categories').select2({
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
});
</script>