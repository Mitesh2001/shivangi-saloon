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
                    <div class="col-lg-4 mb-3">
                        @if($is_system_user || isset($distributor_id))
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.show', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-white btn-block">Personal Details</a>
                            @else  
                                <a href="{{ route('users.show', $user->external_id) }}" class="btn btn-white btn-block">Personal Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.show', $user->external_id) }}" class="btn btn-white btn-block">Personal Details</a>
                        @endif  
                    </div>
                    <div class="col-lg-4 mb-3">
                        @if($is_system_user || isset($distributor_id))
                            @if(isset($distributor_id)) 
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) . '?distributor='. $distributor_id }}" class="btn btn-info btn-block">Professional Details</a>
                            @else
                                <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-info btn-block">Professional Details</a>
                            @endif 
                        @else  
                            <a href="{{ route('users.showProfessionalDetails', $user->external_id) }}" class="btn btn-info btn-block">Professional Details</a>
                        @endif  
                    </div>
                    <div class="col-lg-4 mb-3">
                        @if($is_system_user || isset($distributor_id))
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

                <!--begin::Card-->
                <div class="card card-custom">
                    <!--begin::Header-->
                    <div class="card-header py-3">
                        <div class="card-title remove-flex align-items-start flex-column">
                            <h3 class="card-label font-weight-bolder text-dark">Personal Details</h3>
                            <span class="text-muted font-weight-bold font-size-sm mt-1">Employee Personal Details</span>
                        </div>
                        <div class="card-toolbar">
                            @if($is_profile == false)
                                <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Form--> 
                    {!! Form::open([
                        'route' => 'users.updatePersonal',
                        'class' => 'ui-form',
                        'id' => 'updatePersonalForm',
                        'files' => true
                    ]) !!}
                        <!--begin::Body-->
                        <div class="card-body"> 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Profile Pic</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    @if(!empty($user->profile_pic))
                                        <img src="{{ asset($user->profile_pic) }}" class="rounded shadow profile_pic" alt="Profile Pic" height="80">
                                    @else
                                        <img src="{{ asset('storage/assets/no_image.png') }}" class="rounded shadow profile_pic" alt="Profile Pic" height="80">
                                    @endif  
                                </div> 
                            </div>
                            @if($is_profile == true)
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Update Profile</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input type="file" class="form-control form-control-lg form-control-solid" name="profile_pic" id="profile_pic">  
                                    <input type="hidden" name="old_profile_pic" value="{{ $user->profile_pic }}"> 
                                </div>
                            </div>
                            @endif

                            @if(isset($user->getDistibutor->name))
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label">Salon</label>
                                    <div class="col-lg-9 col-xl-6 form-group-error">
                                        <input class="form-control form-control-lg form-control-solid" name="nick_name" type="text" value="{{ $user->getDistibutor->name }}" disabled>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Nick Name</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input class="form-control form-control-lg form-control-solid" name="nick_name" type="text" value="{{ $user->nick_name }}">
                                </div>
                            </div> -->
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">First Name</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input class="form-control form-control-lg form-control-solid" name="first_name" type="text" value="{{ $user->first_name }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Last Name</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input class="form-control form-control-lg form-control-solid" name="last_name"  type="text" value="{{ $user->last_name }}" required>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Expertise</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input class="form-control form-control-lg form-control-solid" name="expertise"  type="text" value="{{ $user->expertise }}">
                                </div>
                            </div>  
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Role</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <input class="form-control form-control-lg form-control-solid" name="roles"  type="text" value="{{ $user->roles->pluck('name')[0] }}" disabled>
                                </div>
                            </div>  
                            @if(!in_array($user->user_type, [0, 1]))
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Branch</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <select name="roles" id="" class="form-control form-control-lg form-control-solid" disabled>
                                        @foreach($branches as $key => $branch)
                                            <option {{ isset($user) && optional($user->getBranch)->branch_id === $key ? "selected" : "" }} value="{{$key}}">{{$branch}}</option>
                                        @endforeach
                                    </select> 
                                </div>
                            </div> 
                            @endif 
                            <div class="row">
                                <label class="col-xl-3"></label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <h5 class="font-weight-bold mt-10 mb-6">Contact Info</h5>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Contact Number</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <div class="input-group input-group-lg input-group-solid">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-phone"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg form-control-solid" name="primary_number" id="primary_number" value="{{ $user->primary_number }}" placeholder="Contact Number" required>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">WhatsApp Number</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <div class="input-group input-group-lg input-group-solid">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-phone"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg form-control-solid" name="secondary_number" id="secondary_number" value="{{ $user->secondary_number }}" placeholder="WhatsApp Number">
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Email Address</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <div class="input-group input-group-lg input-group-solid">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-at"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-lg form-control-solid" name="email" id="email" value="{{ $user->email }}" placeholder="Email" required>
                                    </div>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label">Address</label>
                                <div class="col-lg-9 col-xl-6 form-group-error">
                                    <div class="input-group input-group-lg input-group-solid">
                                        <textarea name="address" id="address" class="form-control form-control-lg form-control-solid"  rows="5">{{ $user->address }}</textarea>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="la la-map-pin"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            @if($is_profile == true && !$allow_view_only)
                            <div class="row">
                                <div class="col-lg-6">
                                    {!! Form::submit("Update Personal Details", ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
                                    <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                                    <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $user->distributor_id }}">
                                    <button type="button" class="btn btn-light-primary" onClick="window.location.reload(true);">Cancel</button> 
                                </div> 
                            </div>
                            @endif
                        </div>
                        <!--end::Body--> 
                    {!! Form::close() !!}
                    <!--end::Form-->   
                </div>  
            </div>
            <!--end::Content-->
        </div>
        <!--end::Profile Personal Information--> 
    </div>



    <script>
        $(document).ready(function () { 
            <?php if($is_profile == false): ?>


                $("input").attr('disabled', true);
                $("select").attr('disabled', true);
                $("textarea").attr('disabled', true); 


            <?php else: ?> 

                $(document).on('change', '#profile_pic', function(e){ 
                    $('.profile_pic').attr('src', URL.createObjectURL(e.target.files[0]));
                });
                
                $("#updatePersonalForm").validate({
                    rules: {
                        first_name: {
                            required: true,
                        },
                        last_name: {
                            required: true,
                        },   
                        primary_number: { 
                            required: true,
                            number: true,
                            minlength: 10,  
                            maxlength: 10,
                            remote: {
                                url: '{!! route('users.checkPrimaryNumber') !!}',
                                type: "POST",
                                cache: false,
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    number: function () {
                                        return $("#primary_number").val();
                                    },
                                    id: function () {
                                        return $("#id").val();
                                    },
                                    distributor_id: function () {
                                        return $("#distributor_id").val();
                                    }
                                }
                            }
                        }, 
                        secondary_number: {  
                            number: true,
                            minlength: 10,
                            maxlength: 10,
                        }, 
                        email: {  
                            required: true,
                            email: true, 
                            remote: {
                                url: '{!! route('users.checkemail') !!}',
                                type: "POST",
                                cache: false,
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    email: function () {
                                        return $("#email").val();
                                    },
                                    id: function () {
                                        return $("#id").val();
                                    },
                                    distributor_id: function () {
                                        return $("#distributor_id").val();
                                    }
                                }
                            }
                        }, 
                    },
                    messages: { 
                        first_name: {
                            required: "Please enter first name!",  
                        },   
                        last_name: {
                            required: "Please enter last name!",  
                        },   
                        email: {
                            required: "Please enter email address!",  
                            email: "Please enter valid email address!",  
                            remote: "Email already in use!",
                        },    
                        primary_number: {
                            required: "Please enter contact number!",
                            number: "Please enter valid contact number!",
                            minlength: "Please enter valid contact number!", 
                            maxlength: "Please enter valid contact number!",
                            remote: "contact number already exist!",
                        },  
                        secondary_number: { 
                            number: "Please enter valid whatsapp number!",
                            minlength: "Please enter valid whatsapp number!",  
                            maxlength: "Please enter valid whatsapp number!",
                        },  
                    },
                    normalizer: function( value ) { 
                        return $.trim( value );
                    },
                    errorElement: "span",
                    errorClass: "form-text text-danger",
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    errorPlacement: function(error, element) {
                        $(element).closest('.form-group-error').append(error);
                    }
                });

            <?php endif; ?> 
        })
    </script> 
@stop 
 



