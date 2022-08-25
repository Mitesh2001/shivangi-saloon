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
                @if(!empty($order))
                <span>| {{$order->order_uid}}</span>
                <span class="text-muted">( Client Name : {{isset($order->client->name) ? $order->client->name : null}} )</span>
                @endif
            </h3>
        </div>
        <div class="card-toolbar"> 
            <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a> 
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                @if(isset($clientproducts[0]))
                <h3><span class="text-muted">Order Date:</span> {{ date('d-m-Y', strtotime($clientproducts[0]->order_date)) }}</h3>
                @endif 
            </div>
            <div class="col-md-6 text-right mb-5">
                <a href="#" data-toggle="modal" data-target="#invoice_email"
                    class="btn btn-primary font-weight-bold mr-2"><i class="flaticon2-email"></i>Email</a>
                <!-- <a href="{{url('rkadmin/subscriptions/'.encrypt($order->id).'?is_pdf=1')}}" class="btn btn-success font-weight-bold mr-2"><i class="flaticon-download"></i>Download</a> -->
                <a href="{{ route('orders.show', $order->external_id) .'?is_pdf=1' }}"
                    class="btn btn-primary font-weight-bold mr-2"><i class="flaticon-download"></i>Download</a>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-lg-12">
				<div class="table-responsive">
				<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="15%">Product/Service</th>
                            <th width="10%">Qty</th>
                            <!-- <th width="15%">Order Date</th> -->
                            <th width="15%">Amount</th>
                            <th width="15%">Deal Discount %</th>
                            <th width="10%">Discount %</th>
                            <th width="15%">Discount Amount</th>
                            <th width="20%">Final Amount</th>
                        </tr>
                    </thead>
                    <tbody id="plans_list"> 
                        @if(!empty($clientproducts)) 
                            @foreach ($clientproducts as $cplan)
                                <tr>
                                    <td>{{ $cplan->product->name }}</td>
                                    <td>
                                        @php 
                                            $product = json_decode($cplan->product);
                                            $unit_name  = $unit::find($product->unit_id); 
                                            $unit_name  = $unit_name->name ?? "QTY"; 
                                        @endphp
                                        {{ $cplan->qty ." ($unit_name)" }}
                                    </td>
                                    <!-- <td width="15%" class="text-right">{{ date('d-m-Y', strtotime($cplan->order_date)) }}</td> -->
                                    <td class="text-right">
                                        {{ \App\Helpers\Helper::decimalNumber($cplan->product_price) ."/$unit_name" }} 
                                    </td>
                                    <td class="text-right">{{ $cplan->deal_discount }}</td>
                                    <td class="text-right">{{ $cplan->discount }}</td>
                                    <td class="text-right">
                                    {{ \App\Helpers\Helper::decimalNumber($cplan->discount_amount) }}</td>
                                    <td class="text-right">
                                    {{ \App\Helpers\Helper::decimalNumber($cplan->final_amount) }}</td>
                                </tr>
                            @endforeach
                        @endif 
                    </tbody>
                    <tfoot>
                        
                        @if($order->deal_id != 0)
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2">Discount Code</th>
                            <th id="final_amount" class="text-right">
                                {{ $order->discount_code }}
                            </th>
                        </tr>
                        @endif 

                        @if(!empty($order->state_id)) 
                            @if($order->state_id == $order->branch_state_id)
                            <tr class="sgst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">SGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($order->sgst_amount) }}</th>
                            </tr>
                            <tr class="cgst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">CGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($order->cgst_amount) }}</th>
                            </tr>
                            @else
                            <tr class="igst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">IGST</th>
                                <th class="text-right">{{ $order->igst_amount }}</th>
                            </tr>
                            @endif
                        @else   
                            @if($order->state_id == $order->branch_state_id)
                            <tr class="sgst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">SGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($order->sgst_amount) }}</th>
                            </tr>
                            <tr class="cgst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">CGST</th>
                                <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($order->cgst_amount) }}</th>
                            </tr>
                            @else
                            <tr class="igst ">
                                <td colspan="4"></td>
                                <th colspan="2" class="align-middle">IGST</th>
                                <th class="text-right">{{ $order->igst_amount }}</th>
                            </tr>
                            @endif
                        @endif 
                        <!-- <tr>
                            <td colspan="4"></td>
                            <th colspan="2">Net Amount</th>
                            <th id="net_amount" class="text-right">
                                {{ \App\Helpers\Helper::decimalNumber($order->final_amount) }}</th>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2">Round off Amount</th>
                            <th class="text-right">{{ \App\Helpers\Helper::decimalNumber($order->round_off_amount) }}
                            </th>
                        </tr> -->
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2">Total Amount</th>
                            <th id="final_amount" class="text-right">
                            {{ \App\Helpers\Helper::decimalNumber($order->total_amount) }}</th>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2">Payment Pending</th>
                            <th class="text-right">{{ $order->is_payment_pending }}</th>
                        </tr>
                        @if(isset($order->payment_mode) && !empty($order->payment_mode))
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2" class="m-auto">Payment Mode </th>
                            <th class="text-right">{{ $payment_modes[$order->payment_mode] }}</th>
                        </tr>
                        @endif
                        @if($order->is_payment_pending !== "YES")
                        <tr>
                            <td colspan="4"></td>
                            <th colspan="2" class="m-auto">Payment Date</th>
                            <th class="text-right">{{ date("d/m/Y",strtotime($order->payment_date)) }}</th>
                        </tr>
                        @endif

                        @if($order->payment_mode != 'CASH' && ($order->is_payment_pending == 'NO' ||
                        $order->is_payment_pending == '') )
                        <tr class="payment ">
                            <td colspan="4"></td>
                            <th colspan="2" class="m-auto">Bank Name</th>
                            <th class="text-right">{{ $order->payment_bank_name }}</th>
                        </tr>
                        <tr class="payment ">
                            <td colspan="4"></td>
                            <th colspan="2" class="m-auto">Transaction Number</th>
                            <th class="text-right">{{ $order->payment_number }}</th>
                        </tr>
                        <!-- <tr class="payment ">
					<td colspan="4"></td>
					<th colspan="2" class="m-auto">Transaction Amount</th>
					<th class="text-right">{{ $order->payment_amount }}</th>
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
                {{Form::open(['url'=>'admin/orders/'.$order->external_id, 'method'=>'GET'])}}
                <div class="modal-body">
                    {{Form::hidden('is_email','1')}}

                    @if($distributor_id == 0)
                    {{Form::hidden('distributor', 1)}}
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            {!! Form::label('email', __('Email'), ['class' => '']) !!}
                            <span class="text-danger">*</span>
                            {{Form::text('email',!empty($order->client->email) ? $order->client->email : null,['class'=>'form-control','required'])}}
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