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
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-percentage text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('View Deal') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('deals.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body detail-parent">
        <div class="card card rounded gradient-detail-card text-white">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table table-bordered text-white font-size-14"> 
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
                    <div class="col-lg-4">
                        <h3>Applicable clients</h3> 
                        <ul class="list-group text-dark">
                            @if($clients_count > 0)
                                @foreach($clients as $client)
                                <li class="list-group-item">{{ $client->name }}</li>
                                @endforeach 
                            @else 
                                <li class="list-group-item">Applicable for all clients</li>
                            @endif
                        </ul> 
                        <br><br>
                        <h3>Selected Products</h3>
                        <ul class="list-group text-dark">
                            @if(!empty($products))
                                @foreach($products as $product)
                                <li class="list-group-item">{{ $product->name }}</li>
                                @endforeach 
                            @else 
                                <li>Applicable on bill amount</li>
                            @endif
                        </ul> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 

@stop