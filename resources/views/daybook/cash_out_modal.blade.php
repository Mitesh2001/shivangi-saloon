<!-- Modal-->
<div class="modal fade" id="cash-out-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">Cash Out</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            {!! Form::open([
                'route' => 'daybook.storeCashOut',
                'class' => 'ui-form',
                'id' => 'cashOutForm'
            ]) !!}  
            <div class="modal-body">  
                @if($is_system_user == 0)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group form-group-error"> 
                                {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
                                <select name="distributor_id" id="distributor_id_cashout" class="form-control" style="width:100%">
                                </select>
                                @if ($errors->has('distributor_id'))  
                                    <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                                @endif
                            </div>  
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group form-group-error">
                                {{Form::label('Branch')}} : *
                                {{Form::select('branch_id', $selected_branch ?? [], null,['class'=>'form-control', 'id' => 'branch_id_cashout', 'style' => 'width:100%'])}}
                                @if ($errors->has('branch_id')) 
                                    <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                                @endif
                            </div> 
                        </div>
                    </div> 
                @endif  
                <div class="row"> 
                    <div class="form-group col-lg-12 form-group-error">
                        {!! Form::label('amount', __('Amount'). ': *', ['class' => '']) !!}
                        {!! 
                            Form::number('amount',  
                            null, 
                            ['class' => 'form-control',
                            'placeholder' => 'Amount', 'number' => true, 'min' => 1]) 
                        !!}
                        @if ($errors->has('amount'))  
                            <span class="form-text text-danger">{{ $errors->first('amount') }}</span>
                        @endif
                    </div> 
                    <div class="form-group col-lg-12 form-group-error">
                        {!! Form::label('payment_method', __('Payment Mode'). ':', ['class' => '']) !!}
						{!!
							Form::select('payment_method',
							[
                                'Cash' => 'Cash',
                                'Google Pay' => 'Google Pay',
                                'Paytm' => 'Paytm',
                                'Debit Card' => 'Debit Card',
                                'credit Card' => 'credit Card',
                                'Phonepe' => 'Phonepe',
                                'Cashback Wallet' => 'Cashback Wallet', 
                            ],
							old('payment_method'),
							['class' => 'form-control',
							'id' => 'payment_method'])
						!!}
                        @if ($errors->has('payment_method'))  
                            <span class="form-text text-danger">{{ $errors->first('payment_method') }}</span>
                        @endif
                    </div>  
                    <div class="form-group col-lg-12 form-group-error">
                        {!! Form::label('description', __('Description'). ': *', ['class' => '']) !!}
                        {!! 
                            Form::textarea('description',  
                            null, 
                            ['class' => 'form-control',
                            'placeholder' => 'Description']) 
                        !!}
                        @if ($errors->has('description'))  
                            <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                        @endif 
                    </div>  
                </div> 
                @if($is_system_user != 0)
                    <span class="text-muted">You can only cash out for your branch.</span>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
                <button type="submit" class="btn btn-primary font-weight-bold">Submit</button> 
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 
 