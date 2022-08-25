<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-5 form-group-error">
                    {!! Form::label('date', __('Date'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('date',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Date"])
                    !!}
                    @if ($errors->has('date'))
                    <span class="form-text text-danger">{{ $errors->first('date') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-5 form-group-error">
                    {!! Form::label('invoice_number', __('Invoice Number'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('invoice_number',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Invoice Number"])
                    !!}
                    @if ($errors->has('invoice_number'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_number') }}</span>
                    @endif
                </div>
                <div class="col-lg-5 form-group-error offset-lg-2">
                    {!! Form::label('invoice_value', __('Invoice Value'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('invoice_value',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Invoice Value"])
                    !!}
                    @if ($errors->has('invoice_value'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_value') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-5 form-group-error">
                    {!! Form::label('extra_freight_charges', __('Extra Freigh Charges'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('extra_freight_charges',
                    null,
                    ['class' => 'form-control',
                    'placeholder' => "Extra Freigh Charges"])
                    !!}
                    @if ($errors->has('extra_freight_charges'))
                    <span class="form-text text-danger">{{ $errors->first('extra_freight_charges') }}</span>
                    @endif
                </div>
                <div class="col-lg-5 form-group-error offset-lg-2">
                    {!! Form::label('source_type', __('Source Type'). ': *', ['class' => '']) !!}
                    {!! Form::select('source_type',
                    [],
                    old('source_type'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('source_type'))
                    <span class="form-text text-danger">{{ $errors->first('source_type') }}</span>
                    @endif
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-5 form-group-error">
                    {!! Form::label('invoice_type', __('Invoice Type'). ': *', ['class' => '']) !!}
                    {!! Form::select('invoice_type',
                    [],
                    old('invoice_type'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('invoice_type'))
                    <span class="form-text text-danger">{{ $errors->first('invoice_type') }}</span>
                    @endif
                </div>
                <div class="col-lg-5 form-group-error offset-lg-2">
                    {!! Form::label('source_id', __('Source'). ': *', ['class' => '']) !!}
                    {!! Form::select('source_id',
                    [],
                    old('source_id'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('source_id'))
                    <span class="form-text text-danger">{{ $errors->first('source_id') }}</span>
                    @endif
                </div>
            </div>
            <table class="table table-hover table-products">
                <thead>
                    <tr>
                        <th>Product Name</th> 
                        <th>SKU Code</th>
                        <th>MRP</th>
                        <th>QTY</th>
                        <th>Cost/Unit <br> (before tex)</th>
                        <th>GST</th>
                        <th>Total Cost</th>
                        <th>Expiry</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="product-row">
                        <td style="width:200px">
						{!! Form::select('product_name[1]', 
							[],
							null,
							['class' => 'form-control product_name dynamic-input']) 
						!!}
						    <input type="hidden" name="product_id[1]" class="product_id" value=""> 
                        </td> 
                        <td>
                            {!!
                            Form::text('sku_code[1]',
                            null,
                            ['class' => 'form-control dynamic-input sku_code',
                            'placeholder' => "SKU Code"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::text('mrp[1]',
                            null,
                            ['class' => 'form-control dynamic-input mrp',
                            'placeholder' => "MRP"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::text('qty[1]',
                            null,
                            ['class' => 'form-control dynamic-input qty',
                            'placeholder' => "QTY"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::text('cost_per_unit[1]',
                            null,
                            ['class' => 'form-control dynamic-input cost_per_unit',
                            'placeholder' => "Cost/Unit"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::text('gst[1]',
                            null,
                            ['class' => 'form-control dynamic-input gst',
                            'placeholder' => "Invoice Number"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::text('total_cost[1]',
                            null,
                            ['class' => 'form-control dynamic-input total_cost',
                            'placeholder' => "Total Cost"])
                            !!}
                        </td>
                        <td>
                            {!!
                            Form::date('expiry[1]',
                            null,
                            ['class' => 'form-control dynamic-input expiry',
                            'placeholder' => "Expiry"])
                            !!}
                        </td>
                        <td>
							<a href="javascript:void(0)" class="remove-product" data-toggle="tooltip" title="Remove Product">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    <tr class="product-row">
                        <td style="width:200px"> 
                            <input type="text" name="product_name[1]" id="product_name[1]" class="form-control product_name dynamic-input" placeholder="Product Name"> 
						    <input type="hidden" name="product_id[1]" class="product_id" value=""> 
                        </td> 
                        <td>
                            <input type="text" name="sku_code[1]" id="sku_code[1]" class="form-control sku_code dynamic-input" placeholder="SKU Code"> 
                        </td>
                        <td>
                            <input type="text" name="mrp[1]" id="mrp[1]" class="form-control mrp dynamic-input" placeholder="MRP"> 
                        </td>
                        <td>
                            <input type="text" name="qty[1]" id="qty[1]" class="form-control qty dynamic-input" placeholder="QTY"> 
                        </td>
                        <td>
                            <input type="text" name="cost_per_unit[1]" id="cost_per_unit[1]" class="form-control cost_per_unit dynamic-input" placeholder="Cost/Unit">
                        </td>
                        <td>
                            <input type="text" name="gst[1]" id="gst[1]" class="form-control gst dynamic-input" placeholder="GST"> 
                        </td>
                        <td>
                            <input type="text" name="total_cost[1]" id="total_cost[1]" class="form-control total_cost dynamic-input" placeholder="Total Cost">  
                        </td>
                        <td>
                            <input type="date" name="expiry[1]" id="expiry[1]" class="form-control expiry dynamic-input" placeholder="Expiry">   
                        </td>
                        <td>
							<a href="javascript:void(0)" class="remove-product" data-toggle="tooltip" title="Remove Product">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-lg-6">
                    <a href="javascript:void(0)" class="btn btn-primary add-product" data-toggle="tooltip"
                        title="Add Product">
                        Add &nbsp; &nbsp;
                        <i class="flaticon-plus icon-lg"></i>
                    </a>
                </div>
            </div>
			<br><hr><br>
			<div class="form-group row">
                <div class="col-lg-3 form-group-error">
                    {!! Form::label('notes', __('Notes'). ': *', ['class' => '']) !!}
                    {!! Form::textarea('notes',
						null,
						['class'=>'form-control','placeholder'=>'Notes','rows'=>3])
                    !!}
                    @if ($errors->has('notes'))
                    <span class="form-text text-danger">{{ $errors->first('notes') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error">
                    {!! Form::label('amount_paid', __('Amount Paid'). ': *', ['class' => '']) !!}
                    {!!
						Form::text('amount_paid',
						null,
						['class' => 'form-control',
						'placeholder' => "amount_paid"])
					!!}
                    @if ($errors->has('source_id'))
                    <span class="form-text text-danger">{{ $errors->first('source_id') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error">
                    {!! Form::label('payment_type', __('Payment Type'). ': *', ['class' => '']) !!}
                    {!! Form::select('payment_type',
                    [],
                    old('payment_type'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('payment_type'))
                    <span class="form-text text-danger">{{ $errors->first('payment_type') }}</span>
                    @endif
                </div>
                <div class="col-lg-3 form-group-error">
                    {!! Form::label('payment_status', __('Payment Status'). ': *', ['class' => '']) !!}
                    {!! Form::select('payment_status',
                    [],
                    old('payment_status'),
                    ['class' => 'form-control'])
                    !!}
                    @if ($errors->has('payment_status'))
                    <span class="form-text text-danger">{{ $errors->first('payment_status') }}</span>
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
                    {!! Form::reset("Cancel", ['class' => 'btn btn-md btn-secondary', 'id' => 'submitClient']) !!}
                </div>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<!--end::Card-->


<script>
	
$(document).ready(function() {

	init_select2();
    $('.sku_code').attr('disabled', true);
    $('.total_cost').attr('disabled', true);

    // Add more week off tr
    $(document).on('click', '.add-product', function () { 

        let product_row `
            <tr class="product-row">
                <td style="width:200px"> 
                    <input type="text" name="product_name[1]" id="product_name[1]" class="form-control product_name dynamic-input" placeholder="Product Name"> 
                    <input type="hidden" name="product_id[1]" class="product_id" value=""> 
                </td> 
                <td>
                    <input type="text" name="sku_code[1]" id="sku_code[1]" class="form-control sku_code dynamic-input" placeholder="SKU Code"> 
                </td>
                <td>
                    <input type="text" name="mrp[1]" id="mrp[1]" class="form-control mrp dynamic-input" placeholder="MRP"> 
                </td>
                <td>
                    <input type="text" name="qty[1]" id="qty[1]" class="form-control qty dynamic-input" placeholder="QTY"> 
                </td>
                <td>
                    <input type="text" name="cost_per_unit[1]" id="cost_per_unit[1]" class="form-control cost_per_unit dynamic-input" placeholder="Cost/Unit">
                </td>
                <td>
                    <input type="text" name="gst[1]" id="gst[1]" class="form-control gst dynamic-input" placeholder="GST"> 
                </td>
                <td>
                    <input type="text" name="total_cost[1]" id="total_cost[1]" class="form-control total_cost dynamic-input" placeholder="Total Cost">  
                </td>
                <td>
                    <input type="date" name="expiry[1]" id="expiry[1]" class="form-control expiry dynamic-input" placeholder="Expiry">   
                </td>
                <td>
                    <a href="javascript:void(0)" class="remove-product" data-toggle="tooltip" title="Remove Product">
                        <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                    </a>
                </td>
            </tr>
        `;

		let table_length = $('.product-row').length + 1;

        $(".product_name").select2('destroy'); 

		let clone = $(".product-row:first").clone();
		$(clone).find('.dynamic-input').val("");

		$(clone).find('.product_name').attr('name', `product_name[${table_length}]`); 
		$(clone).find('.product_name').attr('id', `product-id-${table_length}`); 
		$(clone).find('.product_id').attr('name', `product_id[${table_length}]`); 
		$(clone).find('.sku_code').attr('name', `sku_code[${table_length}]`); 
		$(clone).find('.mrp').attr('name', `mrp[${table_length}]`); 
		$(clone).find('.qty').attr('name', `qty[${table_length}]`); 
		$(clone).find('.cost_per_unit').attr('name', `cost_per_unit[${table_length}]`); 
		$(clone).find('.gst').attr('name', `gst[${table_length}]`); 
		$(clone).find('.total_cost').attr('name', `total_cost[${table_length}]`); 
		$(clone).find('.expiry').attr('name', `expiry[${table_length}]`); 
 
		$(".table-products").append(clone);

        init_select2();
   
		$('[data-toggle="tooltip"]').tooltip();
	}); 

    // Remove Product TR 
    $(document).on('click', '.remove-product', function (e){ 
        
        let table_length = $(".product-row").length; 

        if(table_length <= 1) {
            alert("Sorry, Can't remove first row!"); 
        } else {
            $(e.target).closest('tr').remove();
        }  
    });

    function init_select2()
    {
        $('.product_name').select2({
            placeholder: "Select Product",
            allowClear: true,
            ajax: {
                url: '{!! route('product.byname') !!}',
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

    $(document).on('change', '.product_name', function (e) {
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
                $(e.target).closest('tr').find('.product_id').val(data.id); 
			}
		})
    })
  
});
</script>