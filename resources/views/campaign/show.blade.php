@extends('layouts.default')
@section('content')
@push('scripts')
<script>
$(document).ready(function() {

});
</script>
@endpush

<div class="card card-custom mb-3">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-percentage text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('View Deal') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('deals.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="card card-custom gutter-b example example-compact">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Segament</th>
                                <td>
                                    @if($deal-> customer_segment_client)
                                    {{ $deal->customer_segment_client }}
                                    @else
                                    {{ $deal->getTag->name }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Deal Name</th>
                                <td>
                                    {{ $deal->deal_name }}
                                </td>
                            </tr>
                            <tr>
                                <th>Deal Code</th>
                                <td>
                                    {{ $deal->deal_code }}
                                </td>
                            </tr>
                            <tr>
                                <th>Validity</th>
                                <td>
                                    {{ date('d-m-Y',strtotime($deal->validity)) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Deal Start Time</th>
                                <td>
                                    {{ date('h:i a',strtotime($deal->start_time)) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Deal End Time</th>
                                <td>
                                    {{ date('h:i a',strtotime($deal->end_time)) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Applicable on Weekends</th>
                                <td>
                                    @if($deal->applicable_on_weekends == 1)
                                    Yes
                                    @else
                                    No
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Applicable on Holiday</th>
                                <td>
                                    @if($deal->applicable_on_holidays == 1)
                                    Yes
                                    @else
                                    No
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Applicable on B'day/Anniv</th>
                                <td>
                                    @if($deal->applicable_on_bday_anniv == 1)
                                    Yes
                                    @else
                                    No
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Week Days</th>
                                <td>
                                    {{ $deal->week_days }}
                                </td>
                            </tr>
                            <tr>
                                <th>Benefit Type</th>
                                <td>
                                    {{ $deal->benifit_type }}
                                </td>
                            </tr>
                            <tr>
                                <th>Invoice Min Amount</th>
                                <td>
                                    {{ $deal->invoice_min_amount }}
                                </td>
                            </tr>
                            <tr>
                                <th>Invoice Max Amount</th>
                                <td>
                                    {{ $deal->invoice_max_amount }}
                                </td>
                            </tr>
                            <tr>
                                <th>Redemptions Max</th>
                                <td>
                                    {{ $deal->redemptions_max }}
                                </td>
                            </tr>
                            <tr>
                                <th>Discount (in %)</th>
                                <td>
                                    {{ $deal->discount . "%" }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-custom gutter-b">
    <div class="card-header border-0 py-5">
        <h3 class="card-title remove-flex align-items-start flex-column">
            <span class="card-label font-weight-bolder text-dark">Product/Service</span> 
        </h3>
        <div class="card-toolbar">
        </div>
    </div>
    <div class="card-body pt-0 pb-3">
        <!--begin::Table--> 
            <table class="table table-head-custom table-head-bg table-vertical-center">
                <thead>
                    <tr class="bg-gray-100 text-left"> 
                        <th>Type</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Product/Service</th>
                        <th>Product Min Price</th>
                        <th>Product Max Price</th>
                    </tr>
                </thead>
                <tbody>  
                    @if(!empty($deal_products))
                        @foreach($deal_products as $deal_product) 
                        <tr>
							<td>  
                                @if($deal_product->product_type == 0)
                                    Product
                                @else  
                                    Service
                                @endif  
							</td>
							<td> 
                                @if(!empty($deal_product->category_id))
                                {{ $deal_product->category->name }}
                                @endif 
							</td>
							<td> 
                                @if(!empty($deal_product->sub_category_id))
                                    {{ $deal_product->sub_category->name }}
                                @endif 
							</td>
							<td> 
                                @if(!empty($deal_product->product_id))
                                {{ $deal_product->product->name }}
                                @endif 
							</td>
							<td> 
								{{ $deal_product->product_min_price }} 
							</td>
							<td> 
                            {{ $deal_product->product_max_price }}
							</td> 
						</tr> 
                        @endforeach
                    @else 
                        <tr class="text-center bg-gray-100">
                            <td colspan="6">No Product/Service</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!--end::Table-->
    </div>
</div>


@stop