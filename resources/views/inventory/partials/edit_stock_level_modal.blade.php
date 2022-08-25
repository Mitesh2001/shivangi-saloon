<!-- Modal-->
<div class="modal fade" id="edit-stock-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">
                    Edit Stock Level
                    @if($is_system_user)
                    <br>
                    <span class="text-muted"> (Salon : <span id="distributor-modal"></span> )</span>
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">  
                {!! Form::open([
                    'method' => 'PATCH',
                    'route' => ['incoming_inventory.update', 0],
                    'class' => 'ui-form',
                    'id' => 'EditStockLevel'
                ]) !!}

                    <div class="row">
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('Invoice Number', __('Invoice Number'). ': *', ['class' => '']) !!}
                        {!!
                            Form::select('invoice_number',
                            [],
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'invoice_number', 
                            'placeholder' => "Invoice Number", 'style' => 'width:100%'])
                        !!}
                        </div>
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('product_name', __('Product Name'). ': *', ['class' => '']) !!}
                        {!!
                            Form::text('product_name',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'product_name',
                            'placeholder' => "Product Name", 'readonly' => true])
                        !!}
                        </div>
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('qty', __('Qty'). ': *', ['class' => '']) !!}
                        {!!
                            Form::number('qty',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'qty',
                            'placeholder' => "Qty", 'number' => true, 'min' => 1])
                        !!}
                        </div> 
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('date', __('Date'). ': *', ['class' => '']) !!}
                        {!!
                            Form::date('date',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'date',
                            'placeholder' => "Date"])
                        !!}
                        </div> 
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('product_branch', __('Branch'). ': *', ['class' => '']) !!}
                        {!!
                            Form::text('product_branch',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'product_branch',
                            'placeholder' => "Branch", 'readonly' => true])
                        !!}
                        </div>
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('sku_code', __('SKU Code'). ': *', ['class' => '']) !!}
                        {!!
                            Form::text('sku_code',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'sku_code',
                            'placeholder' => "SKU Code", 'readonly' => true])
                        !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('Cost/Unit', __('Cost/Unit'). ': *', ['class' => '']) !!}
                        {!!
                            Form::number('cost_per_unit',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'cost_per_unit',
                            'placeholder' => "Cost/Unit", 'number' => true, 'min' => 1])
                        !!}
                        </div>
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('gst', __('GST').': *', ['class' => '']) !!}
                        {!!
                            Form::number('gst',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'gst',
                            'placeholder' => "GST", 'number' => true, 'min' => 0])
                        !!}
                        </div>
                        <div class="form-group col-lg-4 form-group-error">
                        {!! Form::label('mrp', __('Product MRP'). ': *', ['class' => '']) !!}
                        {!!
                            Form::number('mrp',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'mrp',
                            'placeholder' => "Product MRP", 'min' => 1, 'number' => 1])
                        !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 form-group-error">
                        {!! Form::label('Remarks', __('Remarks'). ': *', ['class' => '']) !!}
                        {!!
                            Form::textarea('remarks',
                            null,
                            ['class' => 'form-control edit-modal-input',
                            'id' => 'remarks',
                            'rows' => 3,
                            'placeholder' => "Remarks", 'required' => true])
                        !!}
                        </div>
                    </div>
 
                </div>
            <div class="modal-footer">
                <input type="hidden" name="old_qty" id="old_qty" value="">
                <input type="hidden" name="old_cost_per_unit" id="old_cost_per_unit" value="">
                <input type="hidden" name="old_gst_percent" id="old_gst_percent" value="">
                <input type="hidden" name="old_mrp" id="old_mrp" value=""> 
                <input type="hidden" name="old_date" id="old_date" value="">

                <input type="hidden" name="total_qty" id="total_qty" value="">
                <input type="hidden" name="product_id" id="product_id" value="">
                <input type="hidden" name="branch_id" id="branch_id" value="">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
            </div>
            {!! Form::close() !!}   
        </div>
    </div>
</div> 

<script> 
    $(document).ready(function () {
        $("#EditStockLevel").validate({
            rules: { 
                qty: {
                    required: true,  
                    number: true,
                },  
                invoice_number: {
                    required: true,
                }, 
                product_branch: {
                    required: true,
                },
                cost_per_unit: {
                    required: true,
                    number: true,
                },
                gst: {
                    number: true, 
                },
                mrp: {
                    required: true,
                    number: true,
                },
                remarks: {
                    required: true
                }
            },
            messages: {  
                qty: {
                    required: "Please enter qty!",  
                    number: "Please enter valid qty!",
                },  
                invoice_number: {
                    required: "Please enter invoice number!",
                }, 
                product_branch: {
                    required: "Please select product branch!",
                },
                cost_per_unit: {
                    required: "Please enter cost/unit!",
                    number: "Please enter valid cost/unit!",
                },
                gst: {
                    number: "Please enter valid gst percentage!", 
                },
                mrp: {
                    required: "Please enter mrp!",
                    number: "Please enter valid mrp!",
                },
                remarks: {
                    required: "Please enter remarks!",
                }
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