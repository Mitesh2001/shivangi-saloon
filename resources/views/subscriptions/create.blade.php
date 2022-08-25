@extends('layouts.default')
@section('content')
<div class="row"> 
    @if(Session::has('success')) 
    <div class="col-lg-12">   
        <div class="alert alert-success" role="alert">
            {{Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
    @endif
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
        <h3 class="card-label">
            {{ __('Create Subscription') }}
            <span class="text-muted">(Salon Name : {{ isset($salon_data) ? $salon_data->name : null }}) </span> 
        </h3>
    </div>
    <div class="mt-3">
        <a href="{{ route('subscriptions.index', ['salon_id' => $salon_id]) }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
</div>
<div class="card-body remove-padding-mobile">
    {!! Form::open([
        'route' => 'subscriptions.store',
        'class' => 'ui-form',
        'id' => 'orderCreateForm'
    ]) !!}
     
    @include('subscriptions.form', ['submitButtonText' => __('Save Subscription')])

    {!! Form::close() !!}
    </div>
</div>
<!--end::Card--> 
 
@stop
