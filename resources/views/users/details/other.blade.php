@extends('layouts.default')

@section('content') 

    <div class="container remove-padding-mobile">
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
        </div>
    </div>
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
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-info btn-block">Professional Details</a>
                            @else
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-info btn-block">Professional Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-info btn-block">Professional Details</a>
                        @endif  
                    </div>
                    <div class="col-lg-4 mb-2">
                        @if($is_system_user)
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-white btn-block">Other Details</a>
                            @else  
                                <a href="{{ route('users.showOtherDetails', $user->external_id) }}" class="btn btn-white btn-block">Other Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.showOtherDetails', $user->external_id) }}" class="btn btn-white btn-block">Other Details</a>
                        @endif   
                    </div>
                </div>
            </div>

                <!--begin::Card-->
                <div class="card card-custom mb-6">
                    <!--begin::Header-->
                    <div class="card-header py-3">
                        <div class="card-title remove-flex align-items-start flex-column">
                            <h3 class="card-label font-weight-bolder text-dark">Bank Details</h3>
                            <span class="text-muted font-weight-bold font-size-sm mt-1">Employee Bank Details</span>
                        </div>
                        <div class="card-toolbar">
                            @if(!$is_profile)
                            <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Form-->
                    {!! Form::open([
                        'route' => 'users.updateOther',
                        'class' => 'ui-form',
                        'id' => 'update_profile_other',
                        'files' => true
                    ]) !!}
                        <!--begin::Body-->
                        <div class="card-body">  
  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Account Number</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="account_number"  type="text" value="{{ $user->account_number }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Holder Name</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="holder_name" type="text" value="{{ $user->holder_name }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Bank Name</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="bank_name" type="text" value="{{ $user->bank_name }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">ISFC Code</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input class="form-control form-control-lg form-control-solid" name="isfc_code"  type="text" value="{{ $user->isfc_code }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Attachment</label>
                                <div class="col-lg-9 col-xl-6">
                                    @if($user->bank_attachment !== "") 
                                        <a href="{{ asset($user->bank_attachment) }}" class="" target="_blank" data-toggle="tooltip" title="View Attachment">
                                            <i class="flaticon-eye icon-lg text-primary"></i>
                                        </a>
                                    @else 
                                        <span class="text-muted">No Attachment</span>
                                    @endif
                                </div>
                            </div>  
                            @if($is_profile == true)
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Update Bank Attachment</label>
                                <div class="col-lg-9 col-xl-6">
                                    <input type="file" class="form-control form-control-lg form-control-solid" name="bank_attachment" id="bank_attachment">  
                                    <input type="hidden" name="old_bank_attachment" value="{{ $user->bank_attachment }}">
                                </div>
                            </div>
                            @endif
                            @if($is_profile == true && !$allow_view_only)
                            <div class="row">
                                <div class="col-lg-6">
                                    {!! Form::submit("Update Personal Details", ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
                                    <button type="button" class="btn btn-light-primary" onClick="window.location.reload(true);">Cancel</button> 
                                </div> 
                            </div>
                            @endif
                        </div>
                        <!--end::Body-->
                    {!! Form::close() !!}
                    <!--end::Form-->
                </div>   
                <div class="card card-custom gutter-b">
                    <!--begin::Header-->
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title remove-flex align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Certificates</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Employee Certificates Details</span>
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
                                            <span class="text-dark-75">Certificate</span>
                                        </th>
                                        <th style="min-width: 120px">From (Date)</th>
                                        <th style="min-width: 120px">To (Date)</th>
                                        <th style="min-width: 120px">Attachemnt</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($certificates))
                                    @foreach($certificates as $certificate)  
                                        <tr>
                                            <td>{{ $certificate->name ?? "" }}</td>
                                            <td>
                                                @if($certificate->from !== "")
                                                    {{ date('d-m-Y', strtotime($certificate->from))  }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($certificate->to !== "")
                                                    {{ date('d-m-Y', strtotime($certificate->to))  }}
                                                @endif  
                                            </td>  
                                            <td> 
                                                @if($certificate->attachment !== "")
                                                    <a href="{{ asset($certificate->attachment) }}" target="_blank" data-toggle="tooltip" title="View Attachment">
                                                        <i class="flaticon-eye icon-lg text-primary"></i>
                                                    </a>
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
            <?php if($is_profile == false): ?>

                $(document).on('change', '#profile_pic', function(e){
                    alert("Helloworld");
                    $('.profile_pic').attr('src', URL.createObjectURL(e.target.files[0]));
                });

                $("input").attr('disabled', true);
                $("select").attr('disabled', true);
                $("textarea").attr('disabled', true); 

            <?php endif; ?> 
        })
    </script> 
@stop 
 


