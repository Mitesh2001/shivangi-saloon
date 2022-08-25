@extends('layouts.default')

@section('content') 

    <div class="container remove-padding-mobile">
        <!--begin::Profile Personal Information-->
        <div class="d-flex flex-row">
 
            @include('users.partials.profile_aside')

            <!--begin::Content-->
            <div class="flex-row-fluid ml-lg-8">

            <div class="mobile-profile-menu d-none">
                <div class="row">
                    <div class="col-lg-4 mb-2">
                        @if($is_system_user)
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.show', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-info btn-block">Personal Details</a>
                            @else  
                                <a href="{{ route('users.show', $user->external_id) }}" class="btn btn-info btn-block">Personal Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.show', $user->external_id) }}" class="btn btn-info btn-block">Personal Details</a>
                        @endif  
                    </div>
                    <div class="col-lg-4 mb-2">
                        @if($is_system_user)
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-white btn-block">Professional Details</a>
                            @else
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-white btn-block">Professional Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-white btn-block">Professional Details</a>
                        @endif  
                    </div>
                    <div class="col-lg-4 mb-2">
                        @if($is_system_user)
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-info btn-block">Other Details</a>
                            @else  
                                <a href="{{ route('users.showOtherDetails', $user->external_id) }}" class="btn btn-info btn-block">Other Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.showOtherDetails', $user->external_id) }}" class="btn btn-info btn-block">Other Details</a>
                        @endif   
                    </div>
                </div>
            </div>

            @if($user->user_type != 0)
                <!--begin::Card-->
                <div class="card card-custom mb-6">
                    <!--begin::Header-->
                    <div class="card-header py-3">
                        <div class="card-title remove-flex align-items-start flex-column">
                            <h3 class="card-label font-weight-bolder text-dark">Commission</h3>
                            <span class="text-muted font-weight-bold font-size-sm mt-1">Employee Commission</span>
                        </div>
                        <div class="card-toolbar">
                            @if(!$is_profile)
                            <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
                            @endif
                        </div>  
                    </div>
                    <!--end::Header-->
                    <!--begin::Form-->
                    <form class="form">
                        <!--begin::Body--> 
                        <div class="card-body">  
                            @if($user->user_type == 1) 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Commission Settings</label>
                                <div class="col-lg-9 col-xl-6">
                                    <a href="{{ route('product.viewCommission', $user->external_id) . '?profile=true' }}" class="btn btn-primary">View Details</a>
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Product Commission </label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" readonly="true" type="text" value="{{ $user->product_commission }}%">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Service Commission </label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" readonly="true" type="text" value="{{ $user->service_commission }}%">
                                </div>
                            </div> 
                            @else
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Plan Commission </label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" readonly="true" type="text" value="{{ $user->plan_commission }}%">
                                </div>
                            </div>  
                            @endif
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Unpaid Commission</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" type="text" value="{{ $unpaid_commission }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Total Paid Commission</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" type="text" value="{{ $paid_commission }}">
                                </div>
                            </div> 
                        </div>
                        
                        <!--end::Body-->
                    </form>
                    <!--end::Form-->
                </div>  
                @endif 

                <!--begin::Card-->
                <div class="card card-custom mb-6">
                    <!--begin::Header-->
                    <div class="card-header py-3">
                        <div class="card-title remove-flex align-items-start flex-column">
                            <h3 class="card-label font-weight-bolder text-dark">Professional Details</h3>
                            <span class="text-muted font-weight-bold font-size-sm mt-1">Employee Professional Details</span>
                        </div>
                        <div class="card-toolbar">
                            @if(!$is_profile)
                            <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
                            @endif
                        </div>  
                    </div>
                    <!--end::Header-->
                    <!--begin::Form-->
                    <form class="form">
                        <!--begin::Body-->
                        <div class="card-body">  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Total Experience</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="total_experience"  type="text" value="{{ $user->total_experience }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Salary</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="salary" type="text" value="{{ $user->salary }}">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Basic</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="basic" type="text" value="{{ $user->basic }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">PF</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="ph"  type="text" value="{{ $user->pf }}">
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Gratutiy</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="gratutity"  type="text" value="{{ $user->gratutity }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Others</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="others"  type="text" value="{{ $user->others }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">PT</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="pt"  type="text" value="{{ $user->pt }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Income Tax</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="income_tax"  type="text" value="{{ $user->income_tax }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Working Hours</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="working_hours"  type="text" value="{{ $user->working_hours }}">
                                </div>
                            </div>   
                        </div>
                        <!--end::Body-->
                    </form>
                    <!--end::Form-->
                </div>  

                <div class="card card-custom gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title remove-flex align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Services</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Services that employee can provide</span>
                        </h3>
                        <div class="card-toolbar"> 
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">
                        <ul class="list-group">
                            @if(!empty($employee_services))
                                @foreach($employee_services as $service)
                                <li class="list-group-item">{{ $service->name }}</li>
                                @endforeach 
                            @else 
                                <li class="list-group-item">No Services</li>
                            @endif
                        </ul> 
                    </div>
                    <!--end::Body-->
                </div> 

                <div class="card card-custom gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title remove-flex align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Week Off</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Employee Week Off Details</span>
                        </h3>
                        <div class="card-toolbar"> 
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table table-head-custom table-head-bg table-vertical-center">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th style="min-width: 100px" class="pl-7">
                                            <span class="text-dark-75">Year</span>
                                        </th>
                                        <th style="min-width: 100px" >Month</th>
                                        <th style="min-width: 120px" >Date</th> 
                                        <th style="min-width: 120px" >Date</th> 
                                        <th style="min-width: 120px" >Date</th> 
                                        <th style="min-width: 120px" >Date</th> 
                                        <th style="min-width: 120px" >Date</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($week_offs))
                                    @foreach($week_offs as $week_off) 
                                        @if($week_off->year !== "")
                                            <tr>
                                                <td>{{ $week_off->year }}</td>
                                                <td>
                                                    @if($week_off->month !== "")
                                                        {{ date('F', mktime(0, 0, 0, $week_off->month, 10))  }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($week_off->date_1 !== "") 
                                                        {{ date('d-m-Y', strtotime($week_off->date_1)) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($week_off->date_2 !== "") 
                                                        {{ date('d-m-Y', strtotime($week_off->date_2)) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($week_off->date_3 !== "") 
                                                        {{ date('d-m-Y', strtotime($week_off->date_3)) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($week_off->date_4 !== "") 
                                                        {{ date('d-m-Y', strtotime($week_off->date_4)) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($week_off->date_5 !== "") 
                                                        {{ date('d-m-Y', strtotime($week_off->date_5)) }}
                                                    @endif
                                                </td>  
                                            </tr>
                                        @endif 
                                    @endforeach 
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                    </div>
                    <!--end::Body-->
                </div> 
 
                <div class="card card-custom gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title remove-flex align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Employers</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Employee's Employers Details</span>
                        </h3>
                        <div class="card-toolbar"> 
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body pt-0 pb-3">
                        <!--begin::Table-->
                        <div class="table-responsive">
                            <table class="table table-head-custom table-head-bg table-vertical-center">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th style="min-width: 100px" class="pl-7">
                                            <span class="text-dark-75">Employer</span>
                                        </th>
                                        <th style="min-width: 120px">From (Date)</th>
                                        <th style="min-width: 120px">To (Date)</th>  
                                    </tr>
                                </thead>
                                <tbody> 
                                    @if(!empty($employeers))
                                    @foreach($employeers as $company) 
                                        <tr>
                                            <td>{{ $company->employeer }}</td>
                                            <td>
                                                @if($company->from !== "") 
                                                    {{ date('d-m-Y', strtotime($company->from)) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($company->to !== "") 
                                                    {{ date('d-m-Y', strtotime($company->to)) }}
                                                @endif
                                            </td>  
                                        </tr>
                                    @endforeach 
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <!--end::Table-->
                    </div>
                    <!--end::Body-->
                </div> 

            </div>
            <!--end::Content-->
        </div>
        <!--end::Profile Personal Information-->
    </div>

    <script>
        $(document).ready(function () {

            $("input").attr('disabled', true);
            $("select").attr('disabled', true);
            $("textarea").attr('disabled', true);
             
        })
    </script> 
@stop 
 


