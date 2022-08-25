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
        <h3 class="card-label">{{ __('Update Payment Status') }}</h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('orders.index', ['client_id' => encrypt($client_id)]) }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
    {!! Form::model($order, [
        'method' => 'PATCH',
        'route' => ['orders.update', $order->id], 
        'id' => 'orderUpdateForm'
    ]) !!}  
    
        @include('orders.form', ['submitButtonText' => __('Update Payment Status')])

    {!! Form::close() !!}
    </div>
</div>
<!--end::Card--> 

<script> 
@stop
