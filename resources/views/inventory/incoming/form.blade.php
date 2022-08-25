<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body"> 
            <div class="row"> 
				@if($is_system_user == 0 && !isset($stock_history))  
					<div class="col-lg-5 form-group-error form-group"> 
						{!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
						<select name="distributor_id" id="distributor_id" class="form-control" style="width:100%">  
						</select>
						@if ($errors->has('distributor_id'))  
							<span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
						@endif
					</div>  
                    <div class="col-lg-5 form-group-error form-group offset-lg-2">
                        {!! Form::label('branch_id', __('Branch Id'). ': *', ['class' => '']) !!}  
                        {!! Form::select('branch_id',
                            [],
                            null,	
                            ['class' => 'form-control','id' => 'branch_id', 'style' => "width:100%"])
                        !!}
                        @if ($errors->has('invoice_value'))
                            <span class="form-text text-danger">{{ $errors->first('invoice_value') }}</span>
                        @endif
                    </div>
				@else 
					<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $stock_history->distributor_id ?? 0 }}"> 
				@endif  
			</div>
            <div class="row">
                <div class="col-lg-5 form-group-error form-group">
                    {!! Form::label('date', __('Date'). ': *', ['class' => '']) !!}
                    {!!
                    Form::date('date',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Date", 'max' => date('Y-m-d')])
                    !!}
                    @if ($errors->has('date'))
                    <span class="form-text text-danger">{{ $errors->first('date') }}</span>
                    @endif
                </div>
                <div class="col-lg-5 form-group-error form-group offset-lg-2">
                    {!! Form::label('invoice_number', __('Invoice Number'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('invoice_number',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Invoice Number",
                    'required'])
                    !!}
                    @if ($errors->has('invoice_number'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_number') }}</span>
                    @endif
                </div> 
            </div>
            <div class="row">
                <div class="col-lg-5 form-group-error form-group">
                    {!! Form::label('extra_freight_charges', __('Extra Freight Charges'). ':', ['class' => '']) !!}
                    {!!
                    Form::number('extra_freight_charges',
                    $data['extra_freight_charges'] ?? 0,
                    ['class' => 'form-control number-input',
                    'placeholder' => "Extra Freigh Charges", 
                    "number" => "true", 'min' => 0])
                    !!}
                    @if ($errors->has('extra_freight_charges'))
                    <span class="form-text text-danger">{{ $errors->first('extra_freight_charges') }}</span>
                    @endif
                </div>
                <div class="col-lg-5 form-group-error form-group offset-lg-2">
                    {!! Form::label('source_type', __('Source Type'). ': *', ['class' => '']) !!}
                    {!! Form::select('source_type',
                    ['' => "Select Source Type",
                    'vendor' => 'vendor'],
                    old('source_type'),
                    ['class' => 'form-control', 'style' => 'width:100%'])
                    !!}
                    @if ($errors->has('source_type'))
                    <span class="form-text text-danger">{{ $errors->first('source_type') }}</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5 form-group-error form-group">
                    {!! Form::label('invoice_type', __('Invoice Type'). ':', ['class' => '']) !!}
                    {!! Form::select('invoice_type',
                    ['' => "Select Invoice Type",
                    'tax invoice' => 'Tax Invoice'],
                    old('invoice_type'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('invoice_type'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_type') }}</span>
                    @endif
                </div>
                <!-- vendor id -->
                <div class="col-lg-5 form-group-error form-group offset-lg-2">
                    {!! Form::label('source_id', __('Source'). ': *', ['class' => '']) !!}
                    {!! Form::select('source_id',
                    $source_id ?? [],
                    old('source_id'),
                    ['class' => 'form-control', 'id' => 'source_id'])
                    !!}
                    @if ($errors->has('source_id'))
                        <span class="form-text text-danger">{{ $errors->first('source_id') }}</span>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
            <table class="table table-products">
                <thead>
                    <tr>
                        <th>Product Name *</th> 
                        <th>SKU Code</th>
                        <th>MRP * <br> (inclusive tax)</th>
                        <th>QTY *</th>
                        <th>Cost/Unit * <br> (before tax)</th>
                        <th>GST * <br> (in %)</th>
                        <th>Total Cost</th>
                        <th>Expiry</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody> 

                    @if(isset($products_array))  
                        <?php $i = 1; ?>
                        @foreach($products_array as $product)
                            <tr class="product-row" data-row="{{ $i }}">
                                <td class="form-group-error form-group" style="width:200px"> 
                                    <select name="product_data[{{ $i }}][product_id]" id="product_id_{{ $i }}" class="form-control product_id dynamic-input requied_field" required>
                                        <option value="{{ $product->product_id }}" selected>{{ $product->product_name }}</option>
                                    </select>
                                    <input type="hidden" name="product_data[{{ $i }}][product_name]" id="product_name_{{ $i }}" class="product_name" value="{{ $product->product_name }}"> 
                                </td> 
                                <td class="form-group-error form-group">
                                    <input type="text" name="product_data[{{ $i }}][sku_code]" id="sku_code_{{ $i }}" class="form-control sku_code dynamic-input requied_field" placeholder="SKU Code" value="{{ $product->sku_code }}" readonly> 
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="number" min="0" name="product_data[{{ $i }}][mrp]" id="mrp_{{ $i }}" class="form-control mrp dynamic-input requied_field number-input" placeholder="MRP" value="{{ $product->mrp }}" required number> 
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="number" min="0" name="product_data[{{ $i }}][qty]" id="qty_{{ $i }}" class="form-control qty dynamic-input requied_field number-input" placeholder="QTY" value="{{ $product->qty }}" required number> 
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="number" min="0" name="product_data[{{ $i }}][cost_per_unit]" id="cost_per_unit_{{ $i }}" class="form-control cost_per_unit dynamic-input requied_field number-input" placeholder="Cost/Unit" value="{{ $product->cost_per_unit }}" required number>
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="number" min="0" name="product_data[{{ $i }}][gst]" id="gst_{{ $i }}" class="form-control gst dynamic-input required number-input" placeholder="GST" value="{{ $product->gst }}" required number> 
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="text" name="product_data[{{ $i }}][total_cost]" id="total_cost_{{ $i }}" class="form-control total_cost dynamic-input requied_field number-input" placeholder="Total Cost" value="{{ $product->total_cost }}" readonly number>  
                                </td>
                                <td class="form-group-error form-group">
                                    <input type="date" name="product_data[{{ $i }}][expiry]" id="expiry_{{ $i }}" class="form-control expiry dynamic-input" placeholder="Expiry" value="{{ $product->expiry }}" min="{{ date('Y-m-d') }}">   
                                </td>
                                <td class="form-group-error form-group">   
                                    <a href="javascript:void(0)" class="remove-product" data-product-id="{{ $product->product_id }}" data-stock-history-id="{{ $stock_history->id }}" data-toggle="tooltip" title="Remove Product">
                                        <i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="{{ $product->product_id }}" data-stock-history-id="{{ $stock_history->id }}"></i>
                                    </a>
                                </td>
                            </tr> 
                                <?php $i++; ?>
                        @endforeach
                    @else
                        <tr class="product-row" data-row="1">

                            @if(isset($selected_product))
                                <td class="form-group-error form-group" style="width:200px"> 
                                    <select name="product_data[1][product_id]" id="product_id_1" class="form-control product_id dynamic-input requied_field" required>
                                        <option value="{{ $selected_product->id }}" selected>{{ $selected_product->name }}</option>
                                    </select>
                                    <input type="hidden" name="product_data[1][product_name]" id="product_name_1" class="product_name" value=""> 
                                </td> 
                                <td class="form-group-error form-group">
                                    <input type="text" name="product_data[1][sku_code]" id="sku_code_1" class="form-control sku_code dynamic-input requied_field" placeholder="SKU Code" value="{{ $selected_product->sku_code }}" readonly> 
                                </td>
                            @else 
                                <td class="form-group-error form-group" style="width:200px"> 
                                    <select name="product_data[1][product_id]" id="product_id_1" class="form-control product_id dynamic-input requied_field" required></select>
                                    <input type="hidden" name="product_data[1][product_name]" id="product_name_1" class="product_name" value=""> 
                                </td> 
                                <td class="form-group-error form-group">
                                    <input type="text" name="product_data[1][sku_code]" id="sku_code_1" class="form-control sku_code dynamic-input requied_field" placeholder="SKU Code" readonly> 
                                </td>
                            @endif 
                            
                            <td class="form-group-error form-group">
                                <input type="number" min="0" name="product_data[1][mrp]" id="mrp_1" class="form-control mrp dynamic-input requied_field number-input" placeholder="MRP" required number> 
                            </td>
                            <td class="form-group-error form-group">
                                <input type="number" min="0" name="product_data[1][qty]" id="qty_1" class="form-control qty dynamic-input requied_field number-input" placeholder="QTY" required number> 
                            </td>
                            <td class="form-group-error form-group">
                                <input type="number" min="0" name="product_data[1][cost_per_unit]" id="cost_per_unit_1" class="form-control cost_per_unit dynamic-input requied_field number-input" placeholder="Cost/Unit" required number>
                            </td>
                            <td class="form-group-error form-group">
                                <input type="number" min="0" name="product_data[1][gst]" id="gst[1]" class="form-control gst dynamic-input required number-input" placeholder="GST" value="0" required number> 
                            </td>
                            <td class="form-group-error form-group">
                                <input type="text" name="product_data[1][total_cost]" id="total_cost_1" class="form-control total_cost dynamic-input requied_field" placeholder="Total Cost" readonly number>  
                            </td>
                            <td class="form-group-error form-group">
                                <input type="date" name="product_data[1][expiry]" id="expiry_1" class="form-control expiry dynamic-input" placeholder="Expiry" min="{{ date('Y-m-d') }}">   
                            </td>
                            <td class="form-group-error form-group">
                                <a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product">
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
                    <a href="javascript:void(0)" class="btn btn-primary new-product" data-toggle="tooltip"
                        title="New Product">
                        <i class="flaticon-plus icon-lg"></i>
                        New Product
                    </a>
                </div>
            </div>
			<br><hr><br>
			<div class="row">  
                <div class="col-lg-3 form-group-error form-group">
                    {!! Form::label('payment_type', __('Payment Type'). ': *', ['class' => '']) !!}
                    {!! Form::select('payment_type',
                    [
                        '' => "Select Payment Type",
                        'cash' => 'cash',
                        'credit card' => 'Credit Card',
                        'debit card' => 'Debit Card',
                        'phonepe' => 'Phonepe',
                        'google pay' => 'Google Pay',
                        'paytm' => 'paytm',
                        'bank a' => 'bank_a'
                    ],
                    old('payment_type'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('payment_type'))
                    <span class="form-text text-danger">{{ $errors->first('payment_type') }}</span>
                    @endif 
                </div>
                <div class="col-lg-3 form-group-error form-group">
                    {!! Form::label('amount_paid', __('Amount Paid'). ': *', ['class' => '']) !!}
                    {!!
						Form::number('amount_paid',
						null,
						['class' => 'form-control number-input',
						'placeholder' => "Amount Paid", "number" => "true", 'min' => 0])
					!!}
                    @if ($errors->has('source_id'))
                    <span class="form-text text-danger">{{ $errors->first('source_id') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error form-group">
                    {!! Form::label('invoice_value', __('Invoice Value'). ': *', ['class' => '']) !!}
                    {!!
                    Form::number('invoice_value',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Invoice Value", 'number' => true, 'min' => 0, 'readonly' => 1])
                    !!}
                    @if ($errors->has('invoice_value'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_value') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error form-group"> 
                    {!! Form::label('payment_status', __('Payment Status'). ': ', ['class' => '']) !!}
                    {!!
						Form::select('payment_status',
						['Paid' => 'Paid',
                        'Partialy Paid' => 'Partialy Paid'],
                        null,
						['class' => 'form-control'])
					!!}
                    @if ($errors->has('payment_status'))
                    <span class="form-text text-danger">{{ $errors->first('payment_status') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error form-group">
                    {!! Form::label('notes', __('Notes'). ':', ['class' => '']) !!}
                    {!! Form::textarea('notes',
						null,
						['class'=>'form-control','placeholder'=>'Notes','rows'=>3])
                    !!}
                    @if ($errors->has('notes'))
                    <span class="form-text text-danger">{{ $errors->first('notes') }}</span>
                    @endif
                </div>   
            </div> 
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    {!! Form::hidden('id', null, ['id' => 'id']) !!}
                    {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient'])
                    !!}
                    {!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitClient']) !!}
                </div>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<!--end::Card-->


<script>
	
    function show_product_form() {
         
        // open the page as popup //
        var page = "{{ route('product.create') . '?is_popup=1' }}";
        var myWindow = window.open(page, "_blank", "scrollbars=yes,width=600,height=500,top=200,left=500,menubar=no");
        
        // focus on the popup //
        myWindow.focus(); 
    }

$(document).ready(function() {

    $(document).on('click', '.new-product', function () {
        show_product_form()
    });

 
    init_vendor_select2("#source_id"); 

    $('.number-input').on('input', function(event){
        event.target.value = event.target.value.replace(/\+|-/ig, 0);
    });

    $(document).on('change', '.qty', function (e) {
        updateInvoiceValue();
    }); 
    $(document).on('change', '.cost_per_unit', function (e) {
        updateInvoiceValue();
    }); 
    $(document).on('change', '.gst', function (e) {
        updateInvoiceValue();
    }); 
    $(document).on('change', '#extra_freight_charges', function (e) {
        updateInvoiceValue();
    }); 

    function updateInvoiceValue()
    {
        let total_invoice_value = 0; 

        $('.total_cost').each(function (index, input) {
            let value = $(input).val();  
            if(value.length > 0) {
                total_invoice_value += parseInt(value);
            } 
        }) 
        let extra_freight_charges = $('#extra_freight_charges').val(); 
        
        if(extra_freight_charges.length > 0) {
            total_invoice_value += parseInt(extra_freight_charges);
        } 
        console.log(extra_freight_charges.length);
        $("#invoice_value").val(total_invoice_value);
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
			$("#branch_id").val("").trigger('change'); 
			$("#source_id").val("").trigger('change'); 
		});

        $('#branch_id').select2({
            placeholder: "Select Branch",
            allowClear: true,
            ajax: {
                url: '{!! route('branch.getBranchByName') !!}',
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
	<?php endif; ?>

    let length = $(".table-products .product-row").length; 
    for(let x = 1; x <= length; x++) {
        init_product_select2(`#product_id_${x}`);
    }
   
    $(document).on('change', '.product_id', function (e) {
        let target_input = $(e.target);
        let id = $(e.target).val() 
        let attr_id = $(e.target).attr('id');
        $(".product_id").each(function (index, input){
            let current_attr_id = $(input).attr('id');  
            if(id == $(input).val() && current_attr_id != attr_id) { 
                target_input.val(null).trigger('change');
                alert("Can not add same product!");
                return false;
            }
        });
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
                    <input type="hidden" name="product_data[${dynamic_id}][product_name]" id="product_name_${dynamic_id}" class="product_name" value=""> 
                </td> 
                <td class="form-group-error form-group">
                    <input type="text" name="product_data[${dynamic_id}][sku_code]" id="sku_code_${dynamic_id}" class="form-control sku_code dynamic-input requied_field" placeholder="SKU Code" readonly> 
                </td>
                <td class="form-group-error form-group">
                    <input type="number" min="0" name="product_data[${dynamic_id}][mrp]" id="mrp_${dynamic_id}" class="form-control mrp dynamic-input requied_field" placeholder="MRP" required number> 
                </td>
                <td class="form-group-error form-group">
                    <input type="number" min="0" name="product_data[${dynamic_id}][qty]" id="qty_${dynamic_id}" class="form-control qty dynamic-input requied_field" placeholder="QTY" required number> 
                </td>
                <td class="form-group-error form-group">
                    <input type="number" min="0" name="product_data[${dynamic_id}][cost_per_unit]" id="cost_per_unit_${dynamic_id}" class="form-control cost_per_unit dynamic-input requied_field" placeholder="Cost/Unit" required number>
                </td>
                <td class="form-group-error form-group">
                    <input type="number" min="0" name="product_data[${dynamic_id}][gst]" id="gst_${dynamic_id}" class="form-control gst dynamic-input requied_field" placeholder="GST" value="0" required number> 
                </td>
                <td class="form-group-error form-group">
                    <input type="text" name="product_data[${dynamic_id}][total_cost]" id="total_cost_${dynamic_id}" class="form-control total_cost dynamic-input requied_field" placeholder="Total Cost" readonly number>  
                </td>
                <td class="form-group-error form-group">
                    <input type="date" name="product_data[${dynamic_id}][expiry]" id="expiry_${dynamic_id}" class="form-control expiry dynamic-input" placeholder="Expiry" min="{{ date('Y-m-d') }}">   
                    <input type="hidden" name="product_data[${dynamic_id}][new_added]" value="1">
                </td>
                <td class="form-group-error form-group">
                    <a href="javascript:void(0)" class="remove-product"  data-product-id=""  data-toggle="tooltip" title="Remove Product">
                        <i class="flaticon2-rubbish-bin icon-lg text-danger" data-product-id="" ></i>
                    </a>
                </td>
            </tr>
        `;  
  
		$(".table-products").append(product_row); 
        init_product_select2(`#product_id_${dynamic_id}`);
		$('[data-toggle="tooltip"]').tooltip();
	}); 
    

    $(document).on('input', '.qty', function (e) {
        get_calculated_total_cost(e);
    });
    $(document).on('input', '.cost_per_unit', function (e) {
        get_calculated_total_cost(e);
    });
    $(document).on('input', '.gst', function (e) {
        get_calculated_total_cost(e);
    });

    function get_calculated_total_cost(e)
    { 
        let cpu = $(e.target).closest('tr').find('.cost_per_unit').val();
        let qty = $(e.target).closest('tr').find('.qty').val();
        let gst = $(e.target).closest('tr').find('.gst').val();

        let total = (cpu * qty);
        
        gst = total / 100 * gst;
        let final_total = total + gst;
        $(e.target).closest('tr').find('.total_cost').val(final_total);
    }
  
 
    // Init Select2 (Product Field)
    function init_product_select2(element_id) { 
        $(element_id).select2({
            placeholder: "Select Product",
            allowClear: true,
            ajax: {
                url: '{!! route('product.typebyname') !!}',
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
    }

    // Init Select2 (Vendor Field)
    function init_vendor_select2(element_id) {
        $(element_id).select2({
            placeholder: "Select Vendor",
            allowClear: true,
            ajax: {
                url: '{!! route('vendor.byname') !!}',
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
    }
     
  
    $(document).on('change', '.product_id', function (e) {
        let id = $(this).val();
        
        $.ajax({
			url: '{!! route('product.byid') !!}',
			type: "POST",
			cache: false,
			data: {
				_token: "{{ csrf_token() }}",
				id: id
			},
			success: function (data) {
                $(e.target).closest('tr').find('.sku_code').val(data.sku_code);
                $(e.target).closest('tr').find('.product_name').val(data.name); 
			}
		})
    })
  
});
</script>