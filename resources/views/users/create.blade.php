@extends('layouts.default')
@section('heading')
    {{ __('Create user') }}
@stop
@section('styles')
    <link href="{{asset('css/gsdk-bootstrap-wizard.css')}}" rel="stylesheet" />
    <style>
        .employee-tab{
            height: 55px !important;
            text-align: center !important;
            padding: 10px !important;
        }
        .moving-tab {
            height: 55px !important;
        }
        .week-off-th{
            max-width: 140px !important;
        }
        .week-off-year{
            max-width: 80px !important;
        }
    </style>
@endsection
@section('content') 
@include('layouts.alert')
    <div class="row">
        <div class="col-sm-12 col-sm-offset-2">
            <!--      Wizard container        -->
            <div class="wizard-container p-0">
                <div class="card wizard-card" data-color="blue" id="wizardProfile">
                    <!--         You can switch ' data-color="orange" '  with one of the next bright colors: "blue", "green", "orange", "red"          -->
                    {!! Form::open([
                        'route' => 'users.store',
                        'class' => 'ui-form',
                        'id' => 'usersCreateForm',
                        'files' => true
                    ]) !!}
                        
                        @include('users.form', ['submitButtonText' => __('')])
                        
                    {!! Form::close() !!} 
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
                    
    <script>
    $(document).ready(function () {

        $.validator.addMethod('maxfilesize', function(value, element, param) {

            var length = ( element.files.length );
            var fileSize = 0;

            if (length > 0) {
                for (var i = 0; i < length; i++) {
                    fileSize = element.files[i].size; // get file size
                        // console.log("if" +length);
                            fileSize = fileSize / 1024; //file size in Kb
                            fileSize = fileSize / 1024; //file size in Mb
                        return this.optional( element ) || fileSize <= param;
                }
            }
            else
            {
                return this.optional( element ) || fileSize <= param;
            }
        }); 

        $("#usersCreateForm").validate({
            rules: {
                first_name: {
                    required: true,  
                },   
                last_name: {
                    required: true,  
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
                branch_id: {
                    required: true,
                },
                role: {
                    required: true,
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 12,
                },
                date_of_joining: {
                    required: true,
                },
                salary: {
                    required: true,
                },
                working_hours: {
                    required: true,
                },
                "certification_attachment[]": {
                    extension: 'jpg|png|jpeg|gif|pdf',
                    maxfilesize: 2,
                }, 
                bank_attachment: {
                    extension: 'jpg|png|jpeg|gif|pdf',
                },
                plan_commission: {
                    required: true,
                },
                product_commission: {
                    required: true,
                },
                service_commission: {
                    required: true,
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
                    remote: "Email already exist!",
                },    
                primary_number: {
                    required: "Please enter primary number!",
                    number: "Please enter valid primary number!",
                    minlength: "Please enter valid primary number!", 
                    maxlength: "Please enter valid primary number!",
                    remote: "Primary number already exist!",
                },  
                secondary_number: { 
                    number: "Please enter valid secondary number!",
                    minlength: "Please enter valid secondary number!",  
                    maxlength: "Please enter valid secondary number!",
                },   
                branch_id: {
                    required: "Please select branch!",
                },
                role: {
                    required: "Plese select role!",
                },
                password: {
                    required: "Please enter password!",
                    minlength: "Password must be 6 to 12 character long!",
                    maxlength: "Password must be 6 to 12 character long!",
                },
                date_of_joining: {
                    required: "Please select date of joining!",
                },
                salary: {
                    required: "Please enter salary!",
                },
                working_hours: {
                    required: "Please enter working hours!",
                },
                "certification_attachment[]": {
                    extension: 'Invalid file format! (supported types: jpg, png, jpeg, gif, pdf)',
                    maxfilesize: "File size must be less than 2 mb!",
                },   
                bank_attachment: {
                    extension: 'nvalid file format! (supported types: jpg, png, jpeg, gif, pdf)',
                }, 
                plan_commission: {
                    required: "Please enter plan commission",
                }, 
                product_commission: {
                    required: "Please enter product commission",
                },
                service_commission: {
                    required: "Please enter service commission",
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

    });
    </script>

    <script src="{{asset('js/wizard/jquery.bootstrap.wizard.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/wizard/gsdk-bootstrap-wizard.js')}}"></script>
    <script src="{{asset('js/wizard/jquery.validate.min.js')}}"></script>
    <script src="{{asset('/plugins/custom/jquery_validate/additional-methods.min.js')}}"></script> 
    <script src="{{asset('js/employee.js')}}"></script>
    <script>
        var monthsData = @json($months);
    </script>
@endsection