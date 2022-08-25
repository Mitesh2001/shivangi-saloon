@extends('layouts.default')
@section('content')
<div class="row">  
@include('layouts.alert') 
</div>
<?php
    $data = Session::get('data');
?>
<div class="card card-custom">
<div class="card-header d-flex justify-content-between">
    <div class="card-title">
        <span class="card-icon">
            <i class="flaticon2-supermarket text-primary"></i>
        </span>
        <h3 class="card-label">{{ __('Update Subscription') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('subscriptions.index', ['salon_id' => $salon_data->external_id]) }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
    {!! Form::model($subscription, [
        'method' => 'PATCH',
        'route' => ['subscriptions.update', $subscription->id], 
        'id' => 'orderSubscriptionForm'
    ]) !!}  
    
        @include('subscriptions.form', ['submitButtonText' => __('Update Subscription')])

    {!! Form::close() !!}
    </div>
</div>
<!--end::Card--> 
@stop
