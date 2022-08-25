@extends('layouts.default')

@section('content')
@push('scripts')
@endpush
<?php
    $data = Session::get('data');
?>
<div class="row">
    <div class="col-lg-12">
        @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            {{Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
        @endif
    </div>
</div>
<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-crisp-icons text-primary"></i>
            </span>
            <h3 class="form_title">
				View Order 
                @if(!empty($subscription))
                <span>| {{$subscription->order_uid}}</span>
                <span class="text-muted">( Client Name : {{isset($subscription->salon->name) ? $subscription->salon->name : null}} )</span>
                @endif
            </h3> 
        </div>
        <div class="mt-3">
            <a href="{{ route('subscriptions.index').'?salon_id='.$salon_data->external_id }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                @if(!empty($SalonPlans[0]->subscription_date))
                    <h3><span class="text-muted">Subscription Date:</span> {{ date('d-m-Y', strtotime($SalonPlans[0]->subscription_date)) }}</h3>
                @endif 
            </div>
            <div class="col-md-6 text-right mb-5">
                <a href="#" data-toggle="modal" data-target="#invoice_email" class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon2-email"></i> Email
                </a>
                <a href="{{url('admin/subscriptions/'.encrypt($subscription->id).'?is_pdf=1')}}" class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon-download"></i> Download
                </a> 
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-12">
				<div class="table-responsive">
				<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th width="15%" class="d-none">Plan Date</th> 
                            <th width="5%">Amount</th> 
                            <th width="10%">Discount %</th>
                            <th width="15%">Discount Amount</th>
                            <th width="20%">Final Amount</th>
                        </tr>
                    </thead>
                    <tbody id="plans_list">
                        @if(!empty($SalonPlans)) 
                            @foreach ($SalonPlans as $splan)
                            <tr>
                                <td>{{ $splan->plan->name }}</td>
                                <td class="d-none">
                                    @if(!empty($splan->subscription_date))
                                        {{ date('d-m-Y', strtotime($splan->subscription_date)) }}
                                    @endif
                                </td> 
                                <td width="5%" class="text-right">
                                    {{ \App\Helpers\Helper::decimalNumber($splan->plan_price) }}</td>
                                <td width="10%" class="text-right">{{ $splan->discount }}</td>
                                <td width="10%" class="text-right">{{ $splan->discount_amount }}</td> 
                                <td width="20%" class="text-right">
                                    {{ \App\Helpers\Helper::decimalNumber($splan->final_amount) }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        
                        @if($subscription->deal_id != 0)
                        <tr>
                            <td colspan="3"></td>
                            <th colspan="2">Discount Code</th>
                            <th id="final_amount" class="text-right">
                                {{ $subscription->discount_code }}
                            </th>
                        </tr>
                        @endif 

                        @if(!empty($subscription->state_id)) 
                            @if($subscription->state_id == 12)
                            <tr class="sgst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">SGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($subscription->sgst_amount) }}</th>
                            </tr>
                            <tr class="cgst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">CGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($subscription->cgst_amount) }}</th>
                            </tr>
                            @else
                            <tr class="igst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">IGST</th>
                                <th class="text-right">{{ $subscription->igst_amount }}</th>
                            </tr>
                            @endif
                        @else   
                            @if($subscription->state_id == 12)
                            <tr class="sgst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">SGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($subscription->sgst_amount) }}</th>
                            </tr>
                            <tr class="cgst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">CGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($subscription->cgst_amount) }}</th>
                            </tr>
                            @else
                            <tr class="igst ">
                                <td colspan="2"></td>
                                <th colspan="2" class="align-middle">IGST</th>
                                <th class="text-right">{{ $subscription->igst_amount }}</th>
                            </tr>
                            @endif
                        @endif  
                        <tr>
                            <td colspan="2"></td>
                            <th colspan="2">Total Amount</th>
                            <th id="final_amount" class="text-right">
                                {{ \App\Helpers\Helper::decimalNumber($subscription->total_amount) }}</th>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <th colspan="2">Payment Pending</th>
                            <th class="text-right">{{ $subscription->is_payment_pending }}</th>
                        </tr>
                        @if(isset($subscription->payment_mode) && !empty($subscription->payment_mode))
                        <tr>
                            <td colspan="2"></td>
                            <th colspan="2" class="m-auto">Payment Mode </th>
                            <th class="text-right">{{ $payment_modes[$subscription->payment_mode] }}</th>
                        </tr>
                        @endif
                        @if($subscription->is_payment_pending !== "YES")
                        <tr>
                            <td colspan="2"></td>
                            <th colspan="2" class="m-auto">Payment Date</th>
                            <th class="text-right">{{ date("d/m/Y",strtotime($subscription->payment_date)) }}</th>
                        </tr>
                        @endif

                        @if($subscription->payment_mode != 'CASH' && ($subscription->is_payment_pending == 'NO' ||
                        $subscription->is_payment_pending == '') )
                        <tr class="payment ">
                            <td colspan="2"></td>
                            <th colspan="2" class="m-auto">Bank Name</th>
                            <th class="text-right">{{ $subscription->payment_bank_name }}</th>
                        </tr>
                        <tr class="payment ">
                            <td colspan="2"></td>
                            <th colspan="2" class="m-auto">Transaction Number</th>
                            <th class="text-right">{{ $subscription->payment_number }}</th>
                        </tr>
                        <!-- <tr class="payment ">
					<td colspan="3"></td>
					<th colspan="2" class="m-auto">Transaction Amount</th>
					<th class="text-right">{{ $subscription->payment_amount }}</th>
					</tr> -->
                        @endif
                    </tfoot>
                </table>
				</div>
               
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoice_email" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title remove-flex" id="exampleModalLabel">Invoice</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                {{Form::open(['url'=>'admin/subscriptions/'.encrypt($subscription->id), 'method'=>'GET'])}}
                <div class="modal-body">
                    {{Form::hidden('is_email','1')}}

                    @if($salon_id == 0)
                    {{Form::hidden('distributor', 1)}}
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            {!! Form::label('email', __('Email'), ['class' => '']) !!}
                            <span class="text-danger">*</span>
                            {{Form::text('email',!empty($subscription->salon->email) ? $subscription->salon->email : null,['class'=>'form-control','required'])}}
                            <span class="form-text text-muted">Add multiple email id by comma separated. Maximum
                                3</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Send Invoice</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
</div>

<script>
</script>
@stop