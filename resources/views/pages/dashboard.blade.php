{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')
@include('layouts.alert')
    {{-- Dashboard 1 --}}

    <!-- Dashboard for salon login  -->
    @if($authUser->user_type == 1) 
        <div class="row">  
            <div class="col-lg-12">
                @include('pages.widgets._appointments', ['class' => 'card-stretch gutter-b', 'is_system_user' => $authUser->distributor_id])
            </div> 
            <div class="col-lg-12">
                @include('pages.widgets._revenue_report', ['class' => 'card-stretch gutter-b', 'salon_statistics' => $salonStatistics])
            </div> 
            <div class="col-lg-12">
                @include('pages.widgets._email_sms_report', ['class' => 'card-stretch gutter-b', 'salon_statistics' => $salonStatistics])
            </div>  
            <div class="col-lg-6">
                @include('pages.widgets._client_birthday', ['class' => 'card-stretch gutter-b'])
            </div>  
            <div class="col-lg-6">
                @include('pages.widgets._subscription_history', ['class' => 'card-stretch gutter-b', 'subscriptions' => $subscriptions, 'subscription_expiry' => $salonStatistics['subscription_expiry'] ])
            </div>  
            <div class="col-12"> 
                @include('pages.widgets._stock_reminder', ['class' => 'card-stretch gutter-b', 'stockremiders' => $stockremiders])
            </div> 
        </div>
    @endif

    <!-- dashboard for super admin  -->
    @if($authUser->user_type == 0) 
        @include('pages.widgets._super_admin_dashboard', ['adminStatistics' => $adminStatistics])
    @endif

    @if($authUser->user_type == 2)  
        @include('pages.widgets._distributor_dashboard', ['adminStatistics' => $adminStatistics])
    @endif
  
@endsection

{{-- Scripts Section --}}
@section('scripts') 

{{-- vendors --}}
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

@endsection
