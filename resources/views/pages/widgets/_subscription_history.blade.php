{{-- Advance Table Widget 2 --}}

<div class="card card-custom {{ @$class }}"> 
    {{-- Header --}}
    <div class="card-header border-0"> 
        <h3 class="card-title">
            <span class="card-label font-weight-bolder text-dark">Subscription History</span> 
        </h3>
        <div class="card-toolbar">
            <span class="label label-danger label-inline label-xl font-weight-boldest label-rounded mr-2">subscription Expiry :- {{ $subscription_expiry }}</span> 
        </div>
    </div>
    {{-- Body --}}
    <div class="card-body detail-parent">
        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-vertical-center">
                <thead>
                    <tr>
                        <th>Subscription ID</th>
                        <th>Mode of Payment</th>
                        <th>Amount</th>  
                        <th>Payment Pending</th>
                        <th>Plan Expiry</th>  
                        <th>Action</th>  
                    </tr>
                </thead>
                <tbody>
                    @foreach($subscriptions as $subscription)
                    <tr>
                        <td>{{ $subscription->subscription_id }}</td>
                        <td>{{ $subscription->payment_mode }}</td>
                        <td>{{ $subscription->final_amount }}</td>
                        <td>{{ $subscription->is_payment_pending }}</td>
                        <td>
                            @if(!empty($subscription->subscription_expiry_date))
                                {{ date('d-m-Y', strtotime($subscription->subscription_expiry_date)) }}
                            @endif
                        </td>
                        <td>
                            <a href="{{url('admin/subscriptions/'.encrypt($subscription->id).'?is_pdf=1')}}" class="btn btn-sm btn-primary btn-icon font-weight-bold mr-2"><i class="flaticon-download"></i></a> 
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
