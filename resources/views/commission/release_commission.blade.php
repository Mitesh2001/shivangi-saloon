<!-- Modal-->
<div class="modal fade" id="release-payment" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modalsm" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">
                    Release Commssion 
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
            {!! Form::open([
                'route' => 'commissions.release',
                'class' => 'ui-form',
                'id' => 'release-commission-form'
            ]) !!}
                 
                <div class="form-group row">
                    <div class="col-lg-12">
                        {!! Form::label('payment_type', __('Payment Type'). ': *', ['class' => '']) !!}
                        {!! Form::select('payment_type',
                            [
                                '' => "Select Payment Type",
                                'cash' => 'cash', 
                                'phonepe' => 'Phonepe',
                                'google pay' => 'Google Pay',
                                'paytm' => 'paytm', 
                            ],
                            old('payment_type'),
                            ['class' => 'form-control', 'required' => true])
                        !!} 
                    </div> 
                </div>   
                <input type="hidden" name="user_id" id="user_id" value="0">
            </div>
            <div class="modal-footer">
                <input type="hidden" name="appointment_id" id="appointment_id" value="">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancle</button>
                <button type="submit" class="btn btn-primary font-weight-bold submit-appointment">Update</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>