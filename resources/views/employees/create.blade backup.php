@extends('layouts.default')
@section('heading')
    {{ __('Create user') }}
@stop
@section('styles')
    <link href="{{asset('css/gsdk-bootstrap-wizard.css')}}" rel="stylesheet" />
    <style>
        .employee-tab{
            height: 38px !important;
            text-align: center !important;
            padding: 10px !important;
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
    <div class="row">
        <div class="col-sm-12 col-sm-offset-2">
            <!--      Wizard container        -->
            <div class="wizard-container p-0">
                <div class="card wizard-card" data-color="orange" id="wizardProfile">
                    <form action="" method="">
                        <!--         You can switch ' data-color="orange" '  with one of the next bright colors: "blue", "green", "orange", "red"          -->

                        <div class="wizard-header">
                            <h3>
                                <b>Employee</b> PROFILE <br>
                            </h3>
                        </div>

                        <div class="wizard-navigation">
                            <ul class="employee-tab">
                                <li><a href="#personal_details" data-toggle="tab">Personal Details</a></li>
                                <li><a href="#professional_details" data-toggle="tab">Professional Details</a></li>
                                <li><a href="#other_details" data-toggle="tab">Other Details</a></li>
                            </ul>

                        </div>

                        <div class="tab-content">
                            {{-- personal details --}}
                            <div class="tab-pane" id="personal_details">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('First Name')}}<small>(required)</small>
                                            {{Form::text('first_name','',['class'=>'form-control','placeholder'=>'First Name'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Last Name')}}<small>(required)</small>
                                            {{Form::text('last_name','',['class'=>'form-control','placeholder'=>'Last Name'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Nick Name')}}
                                            {{Form::text('nick_name','',['class'=>'form-control','placeholder'=>'Nick Name'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Email')}}<small>(required)</small>
                                            {{Form::email('email','',['class'=>'form-control','placeholder'=>'Email'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Mobile Number')}}<small>(required)</small>
                                            {{Form::text('mobile_number','',['class'=>'form-control valid-number','placeholder'=>'Mobile Number'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Outlet')}}
                                            {{Form::select('outlet',['1'=>'Salon First','2'=>'Tamarind Cafe'],'',['class'=>'form-control','placeholder'=>'Select Outlet'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Expertise')}}
                                            {{Form::text('expertise','',['class'=>'form-control','placeholder'=>'Expertise'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Role')}}<small>(required)</small>
                                            {{Form::select('role',['1'=>'Cashier','2'=>'Expert'],'',['class'=>'form-control','placeholder'=>'Select Role'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Password')}}<small>(required)</small>
                                            {{Form::password('password', [
                                                'class'=>'form-control','placeholder'=>'Password',
                                                'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$',
                                                'title' => 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.'
                                            ])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Date of Joining')}}
                                            {{Form::date('date','',['class'=>'form-control'])}}
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            {{Form::label('Address')}}  
                                            {{Form::textarea('address','',['class'=>'form-control','placeholder'=>'Address','rows'=>4,'cols'=>5])}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- professional details --}}
                            <div class="tab-pane" id="professional_details">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{Form::label('Salary')}}
                                            {{Form::text('salary','',['class'=>'form-control valid-number','placeholder'=>'Salary'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{Form::label('Basic')}}
                                            {{Form::text('basic','',['class'=>'form-control valid-number','placeholder'=>'Basic'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            {{Form::label('PF')}}
                                            {{Form::text('pf','',['class'=>'form-control valid-number','placeholder'=>'PF'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{Form::label('Gratuity')}}
                                            {{Form::text('gratuity','',['class'=>'form-control valid-number','placeholder'=>'Gratuity'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{Form::label('Others')}}
                                            {{Form::text('others','',['class'=>'form-control valid-number','placeholder'=>'Others'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            {{Form::label('PT')}}
                                            {{Form::text('pt','',['class'=>'form-control valid-number','placeholder'=>'PT'])}}
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            {{Form::label('Income Tax')}}
                                            {{Form::text('income_tax','',['class'=>'form-control valid-number','placeholder'=>'Income Tax'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('Over Time P/H')}}
                                            {{Form::text('over_time_p_h','',['class'=>'form-control valid-number','placeholder'=>'Over Time P/H'])}}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{Form::label('Working Hours')}}
                                            {{Form::text('income_tax','',['class'=>'form-control','placeholder'=>'Working Hours'])}}
                                        </div>
                                    </div>
                                </div>
                                <h5>Week-Off</h5>
                                <table class="week-off-table table">
                                    <tbody>
                                        @for($i=1; $i<=3; $i++)
                                            <tr class="{{$i == 3 ? "week-tr week-off-data" : null}}">
                                                <td class="week-off-year">
                                                    {{Form::select('week_off['.$i.'][year]',['2021'=>'2021','2022'=>'2022'],'',['class'=>'form-control','placeholder'=>'Year'])}}
                                                </td>
                                                <td class="week-off-year">
                                                    {{Form::select('week_off['.$i.'][month]',$months,'',['class'=>'form-control','placeholder'=>'Month'])}}
                                                </td>
                                                <td class="week-off-th">
                                                    {{Form::date('week_off['.$i.'][date_1]','',['class'=>'form-control'])}}
                                                </td>
                                                <td class="week-off-th">
                                                    {{Form::date('week_off['.$i.'][date_2]','',['class'=>'form-control'])}}
                                                </td>
                                                <td class="week-off-th">
                                                    {{Form::date('week_off['.$i.'][date_3]','',['class'=>'form-control'])}}
                                                </td>
                                                <td class="week-off-th">
                                                    {{Form::date('week_off['.$i.'][date_4]','',['class'=>'form-control'])}}
                                                </td>
                                                <td class="week-off-th">
                                                    {{Form::date('week_off['.$i.'][date_5]','',['class'=>'form-control'])}}
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                                <a href="javascript:void(0)" class="btn btn-info font-weight-bolder font-size-sm mr-3 add-week-off" data-id="4">Add</a>
                                <br>
                                <br>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            {{Form::label('Total Experience')}}
                                            {{Form::text('total_experience','',['class'=>'form-control valid-number','placeholder'=>'Total Experience'])}}
                                        </div>
                                    </div>
                                </div>
                                <table class="employeer-table table">
                                    <thead>
                                        <th>Employeer</th>        
                                        <th>From</th>        
                                        <th>To</th>        
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{Form::text('employer_data[1][employeer]','',['class'=>'form-control','placeholder'=>'Employeer'])}}
                                            </td>
                                            <td>
                                                {{Form::date('employer_data[1][from]','',['class'=>'form-control'])}}
                                            </td>
                                            <td>
                                                {{Form::date('employer_data[1][to]','',['class'=>'form-control'])}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="javascript:void(0)" class="btn btn-info font-weight-bolder font-size-sm mr-3 add-employeer" data-id="2">Add</a>
                            </div>
                            
                            <div class="tab-pane" id="other_details">
                                <table class="employeer-table table">
                                    <thead>
                                        <th>Certificate Name</th>        
                                        <th>From</th>        
                                        <th>To</th>        
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                {{Form::text('certification_data[1][name]','',['class'=>'form-control','placeholder'=>'Certificate Name'])}}
                                            </td>
                                            <td>
                                                {{Form::date('certification_data[1][from]','',['class'=>'form-control'])}}
                                            </td>
                                            <td>
                                                {{Form::date('certification_data[1][to]','',['class'=>'form-control'])}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <a href="javascript:void(0)" class="btn btn-info font-weight-bolder font-size-sm mr-3 add-certificate" data-id="2">Add</a>
                            </div>
                        </div>
                        <div class="wizard-footer height-wizard">
                            <div class="pull-right">
                                <input type='button' class='btn btn-next btn-fill btn-warning btn-wd btn-sm' name='next' value='Next' />
                                <input type='button' class='btn btn-finish btn-fill btn-warning btn-wd btn-sm' name='finish' value='Finish' />

                            </div>

                            <div class="pull-left">
                                <input type='button' class='btn btn-previous btn-fill btn-default btn-wd btn-sm' name='previous' value='Previous' />
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{asset('js/wizard/jquery.bootstrap.wizard.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/wizard/gsdk-bootstrap-wizard.js')}}"></script>
    <script src="{{asset('js/wizard/jquery.validate.min.js')}}"></script>
    <!-- <script src="{{asset('js/employee.js')}}"></script> -->
    <script>
        var monthsData = @json($months);
    </script>
@endsection