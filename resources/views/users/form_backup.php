<div class="wizard-header d-flex justify-content-between align-items-center" style="width: 100%">
    <h4 class="pl-3"> 
        @if(isset($user))
            Update <b>Employee</b>
            @if($is_system_user && $selected_distributor)
                <br>
                <span class='text-muted'>( {!! "Salon : ". $selected_distributor->name ?? "" !!} )</span>
            @endif
        @else
            Create <b>Employee</b> 
            @if($is_system_user && $distributor_title)
                <br>
                <span class='text-muted'>( {!! "Salon : $distributor_title" !!} )</span>
            @endif
        @endif
    </h4>
    <div class="back-button pr-3">
        <a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
    </div>
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
        @if(!isset($user))
            @if($is_system_user && $distributor == false)
                <div class="col-sm-4">
                    <div class="form-group form-group-error">
                        {!! Form::label('user_type', __('Type'). ': *', ['class' => '']) !!}  
                        {!! Form::select('user_type',
                            [
                                '0' => "Normal User",
                                '1' => "Salon Employee",
                                '2' => "Distributor",
                            ],
                            $user_type ?? 0,	
                            ['class' => 'form-control', 'id' => 'user_type'])
                        !!}
                        @if ($errors->has('user_type'))  
                            <span class="form-text text-danger">{{ $errors->first('user_type') }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4 distributor-container">
                    <div class="form-group form-group-error">
                        {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!} 
                        <select name="distributor_id" id="distributor_id" class="form-control" style="width:100%">
                            @if(isset($selected_distributor))
                                <option value="{{ $selected_distributor->id }}">{{ $selected_distributor->name }}</option>
                            @endif
                        </select> 
                        @if ($errors->has('distributor_id'))  
                            <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                        @endif
                    </div>
                </div>
            @else 
                <input type="hidden" name="user_type" id="user_type" value="1">
                <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $distributor_id }}">
            @endif
        @else 
            <input type="hidden" name="user_type" id="user_type" value="{{ $user->user_type }}">
            <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $user->distributor_id }}">
        @endif
                             
            <div class="col-sm-4 branch-container">
                <div class="form-group form-group-error">
                    {{Form::label('Branch')}} *
                    <select name="branch_id" id="branch_id" class="form-control" style="width:100%">
                        @if(isset($selected_branch))
                            <option value="{{ $selected_branch->id }}">{{ $selected_branch->name }}</option>
                        @endif
                    </select> 

                    @if ($errors->has('branch_id')) 
                        <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                    @endif
                </div> 
            </div> 
           
            <div class="col-sm-4 plan-commission-container">
                <div class="form-group form-group-error">
                    {{Form::label('Plan Commission')}}% *
                    {{Form::number('plan_commission', null,['class'=>'form-control','placeholder'=>'Plan Commission'])}}
                    <span class="text-muted">Plan commission in percentage</span>
                    @if ($errors->has('plan_commission')) 
                        <span class="form-text text-danger">{{ $errors->first('plan_commission') }}</span>
                    @endif
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('First Name')}} *
                    {{Form::text('first_name', null,['class'=>'form-control','placeholder'=>'First Name'])}}
                    
                    @if ($errors->has('first_name')) 
                        <span class="form-text text-danger">{{ $errors->first('first_name') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Last Name')}} *
                    {{Form::text('last_name', null,['class'=>'form-control','placeholder'=>'Last Name'])}}

                    @if ($errors->has('last_name')) 
                        <span class="form-text text-danger">{{ $errors->first('last_name') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Nick Name')}}
                    {{Form::text('nick_name', null,['class'=>'form-control','placeholder'=>'Nick Name'])}}

                    @if ($errors->has('nick_name')) 
                        <span class="form-text text-danger">{{ $errors->first('nick_name') }}</span>
                    @endif
                </div>
               
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Email')}} *
                    {{Form::email('email', null,[
                        'class'=>'form-control',
                        'id' => 'email', 
                        'placeholder'=>'Email',
                        'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!'
                    ])}}

                    @if ($errors->has('email')) 
                        <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Primary Number')}} *
                    {{Form::text('primary_number', null,['class'=>'form-control valid-number','placeholder'=>'Primary Number', 'id'=> 'primary_number'])}}

                    @if ($errors->has('primary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_number') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Secondary Number')}}<small> </small>
                    {{Form::text('secondary_number', null,['class'=>'form-control valid-number','placeholder'=>'Secondary Number'])}}

                    @if ($errors->has('secondary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_number') }}</span>
                    @endif
                </div>
                
            </div> 
        </div>
        <div class="row"> 
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Expertise')}}
                    {{Form::text('expertise', null,['class'=>'form-control','placeholder'=>'Expertise'])}}
                    
                    @if ($errors->has('expertise')) 
                        <span class="form-text text-danger">{{ $errors->first('expertise') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Role')}} *
                    @if(isset($user)) 
                        {{Form::select('role',$roles, $user->roles->first()->id ?? NULL,['class'=>'form-control','placeholder'=>'Select Role', 'id' => 'role'])}}
                    @else
                        {{Form::select('role',$roles, NULL,['class'=>'form-control','placeholder'=>'Select Role', 'id' => 'role'])}}
                    @endif  
                    @if ($errors->has('role')) 
                        <span class="form-text text-danger">{{ $errors->first('role') }}</span>
                    @endif
                </div>
                
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-4"> 
                <div class="form-group form-group-error">
                    {{Form::label('Password')}} @if(!isset($user->id)) * @endif
                    {{Form::password('password',[
                        'class'=>'form-control',
                        'placeholder'=>'Password',
                        'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$',
                        'title' => 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.' 
                    ])}}

                    @if ($errors->has('pasfsword')) 
                        <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>  
                <div class="form-group form-group-error">
                    {{Form::label('Date of Joining')}} *
                    {{Form::date('date_of_joining', null,['class'=>'form-control'])}}

                    @if ($errors->has('date_of_joining')) 
                        <span class="form-text text-danger">{{ $errors->first('date_of_joining') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-4">
                <div class="form-group form-group-error">
                    {{Form::label('Address')}}
                    {{Form::textarea('address', null,['class'=>'form-control','placeholder'=>'Address','rows'=>4])}}

                    @if ($errors->has('address')) 
                        <span class="form-text text-danger">{{ $errors->first('address') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-4">
                @if(isset($user->profile_pic))
                    <img class="profile_pic" src="{{ asset($user->profile_pic) }}" alt="" width="100px">
                @else 
                    <img class="profile_pic" src="" alt="" width="100px">
                @endif
                <div class="form-group form-group-error">
                    {{Form::label('Profile Pic')}} 
                    {{Form::file('profile_pic', ['class'=>'form-control', 'id' => 'profile_pic'])}}
 
                    @if ($errors->has('profile_pic')) 
                        <span class="form-text text-danger">{{ $errors->first('profile_pic') }}</span>
                    @endif
                    @if(isset($user->profile_pic))
                        <input type="hidden" name="old_profile_pic" value="{{ $user->profile_pic }}">
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- professional details --}}
    <div class="tab-pane" id="professional_details">
        <div class="row">
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Salary')}} *
                    {{Form::number('salary', null,['class'=>'form-control valid-number','placeholder'=>'Salary', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('salary')) 
                        <span class="form-text text-danger">{{ $errors->first('salary') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Basic')}}
                    {{Form::number('basic', null,['class'=>'form-control valid-number','placeholder'=>'Basic', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('basic')) 
                        <span class="form-text text-danger">{{ $errors->first('basic') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-1">
                <div class="form-group form-group-error">
                    {{Form::label('PF')}}
                    {{Form::number('pf', null,['class'=>'form-control valid-number','placeholder'=>'PF', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('pf')) 
                        <span class="form-text text-danger">{{ $errors->first('pf') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Gratutity')}}
                    {{Form::number('gratutity', null,['class'=>'form-control valid-number','placeholder'=>'gratutity', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('gratutity')) 
                        <span class="form-text text-danger">{{ $errors->first('gratutity') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Others')}}
                    {{Form::number('others', null,['class'=>'form-control valid-number','placeholder'=>'Others', 'min' => 0, 'number' => true])}}

                    @if ($errors->has('others')) 
                        <span class="form-text text-danger">{{ $errors->first('others') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-1">
                <div class="form-group form-group-error">
                    {{Form::label('PT')}}
                    {{Form::number('pt', null,['class'=>'form-control valid-number','placeholder'=>'PT', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('pt')) 
                        <span class="form-text text-danger">{{ $errors->first('pt') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Income Tax')}}
                    {{Form::number('income_tax', null,['class'=>'form-control valid-number','placeholder'=>'Income Tax', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('income_tax')) 
                        <span class="form-text text-danger">{{ $errors->first('income_tax') }}</span>
                    @endif
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group form-group-error">
                    {{Form::label('Product Commission')}}% *
                    {{Form::number('product_commission', null,['class'=>'form-control valid-number','placeholder'=>'Product Commission', 'min' => 0, 'number' => true])}}
                    
                    @if ($errors->has('product commission')) 
                        <span class="form-text text-danger">{{ $errors->first('product commission') }}</span>
                    @else
                        <span class="text-muted">Product commission in percentage</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-3">
                <div class="form-group form-group-error">
                    {{Form::label('Service Commission')}}% *
                    {{Form::number('service_commission', null,['class'=>'form-control valid-number','placeholder'=>'Service Commission', 'min' => 0, 'number' => true])}}
                    
                    @if ($errors->has('over_time_ph')) 
                        <span class="form-text text-danger">{{ $errors->first('over_time_ph') }}</span>
                    @else
                        <span class="text-muted">Service commission in percentage</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-3">
                <div class="form-group form-group-error">
                    {{Form::label('Over Time P/H')}}
                    {{Form::number('over_time_ph', null,['class'=>'form-control valid-number','placeholder'=>'Over Time P/H', 'min' => 0, 'number' => true])}}
                    
                    @if ($errors->has('over_time_ph')) 
                        <span class="form-text text-danger">{{ $errors->first('over_time_ph') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-3">
                <div class="form-group form-group-error">
                    {{Form::label('Working Hours')}} *
                    {{Form::number('working_hours', null,['class'=>'form-control','placeholder'=>'Working Hours', 'number' => true, 'min' => 0])}}

                    @if ($errors->has('working_hours')) 
                        <span class="form-text text-danger">{{ $errors->first('working_hours') }}</span>
                    @endif
                </div> 
            </div>
        </div>
        <h5>Week-Off</h5> 

        <div class="table-responsive">
        <table class="week-off-table table">
            <tbody class="week-off-table-tbody">

                @if(isset($user->week_off))
                    <?php $i = 1; ?>
                    @foreach($week_offs as $week_off)
                    <tr class="week-off-tr">
                        <td class="week-off-year form-group-error">
                            {{Form::select('week_off['.$i.'][year]', $years , $week_off->year,['class'=>'form-control dynamic-input year','placeholder'=>'Year'])}}
                        </td>
                        <td class="week-off-year form-group-error">
                            {{Form::select('week_off['.$i.'][month]',$months, $week_off->month,['class'=>'form-control dynamic-input month','placeholder'=>'Month'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_1]', $week_off->date_1,['class'=>'form-control dynamic-input date-1 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_2]', $week_off->date_2,['class'=>'form-control dynamic-input date-2 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_3]', $week_off->date_3,['class'=>'form-control dynamic-input date-3 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_4]', $week_off->date_4,['class'=>'form-control dynamic-input date-4 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_5]', $week_off->date_5,['class'=>'form-control dynamic-input date-5 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            <a href="javascript:void(0)" class="remove-week-off-tr" data-toggle="tooltip" title="Remove Week-off">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                @else 
                    @for($i=1; $i<=1; $i++) 
                    <tr class="week-off-tr">
                        <td class="week-off-year form-group-error">
                            {{Form::select('week_off['.$i.'][year]', $years , null,['class'=>'form-control dynamic-input year','placeholder'=>'Year'])}}
                        </td>
                        <td class="week-off-year form-group-error">
                            {{Form::select('week_off['.$i.'][month]',$months, null,['class'=>'form-control dynamic-input month','placeholder'=>'Month'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_1]', null,['class'=>'form-control dynamic-input date-1 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_2]', null,['class'=>'form-control dynamic-input date-2 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_3]', null,['class'=>'form-control dynamic-input date-3 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_4]', null,['class'=>'form-control dynamic-input date-4 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            {{Form::date('week_off['.$i.'][date_5]', null,['class'=>'form-control dynamic-input date-5 date','placeholder'=>'dd/mm/yyyy'])}}
                        </td>
                        <td class="week-off-th form-group-error">
                            <a href="javascript:void(0)" class="remove-week-off-tr" data-toggle="tooltip" title="Remove Week-off">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                    @endfor
                @endif 
                
            </tbody>
        </table>
        </div>
        
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3" id="add-week-off" data-id="4" data-toggle="tooltip" title="Add Week-off">
            Add &nbsp; &nbsp;
            <i class="flaticon-plus icon-lg"></i>
        </a>
        <br>
        <hr>
        <br>
        <div class="row"> 
            <div class="form-group col-lg-4">
                {!! Form::label('services', __('Services'). ':', ['class' => '']) !!}
                {!!
                    Form::select('services[]',
                    $selected_services ?? [],
                    null, 
                    ['class' => 'form-control',
                    'id' => 'services', 
                    'multiple' => 'true', 
                    'style' => 'width:100%'])
                !!} 
                @if ($errors->has('deal_description'))  
                    <span class="form-text text-danger">{{ $errors->first('deal_description') }}</span>
                @else 
                    <span class="text-muted">Services that employee can provide</span>
                @endif
            </div> 
            <div class="col-lg-4 offset-lg-4">
                <div class="form-group">
                    {{Form::label('Total Experience')}}
                    {{Form::number('total_experience', null,['class'=>'form-control valid-number','placeholder'=>'Total Experience', 'number' => true, 'min' => 0])}}
                </div>
            </div>
        </div>
        <div class="table-responsive">
        <table class="employeer-table table">
            <thead>
                <th>employer</th>
                <th>From</th>
                <th>To</th>
            </thead>
            <tbody>
                @if(!empty($employeers)) 
                    <?php $i = 0; ?>
                    @foreach($employeers as $employeer)
                        <tr>
                            <td class="form-group-error">
                                {{Form::text('employer_data['.$i.'][employeer]', $employeer->employeer,['class'=>'form-control dynamic-input','placeholder'=>'Employeer'])}}
                            </td>
                            <td class="form-group-error">
                                {{Form::date('employer_data['.$i.'][from]', $employeer->from,['max' => date('Y-m-d') ,'class'=>'form-control date-from'])}}
                            </td>
                            <td class="form-group-error">
                                {{Form::date('employer_data['.$i.'][to]', $employeer->to,['max' => date('Y-m-d') ,'class'=>'form-control date-to'])}}
                            </td>
                            <td class="form-group-error">
                                <a href="javascript:void(0)" class="mr-3 remove-employeer-tr" data-toggle="tooltip" title="Remove Employeer">
                                    <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    <?php $i++; ?>
                    @endforeach
                @else 
                    <tr>
                        <td class="form-group-error">
                            {{Form::text('employer_data[1][employeer]', null,['class'=>'form-control','placeholder'=>'Employeer'])}}
                        </td>
                        <td class="form-group-error">
                            {{Form::date('employer_data[1][from]', null,['max' => date('Y-m-d') ,'class'=>'form-control date-from'])}}
                        </td>
                        <td class="form-group-error">
                            {{Form::date('employer_data[1][to]', null,['max' => date('Y-m-d') ,'class'=>'form-control date-to'])}}
                        </td>
                        <td class="form-group-error">
                            <a href="javascript:void(0)" class="mr-3 remove-employeer-tr" data-toggle="tooltip" title="Remove Employeer">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                @endif 
            </tbody>
        </table>
        </div>
        
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 add-employeer" data-id="2" data-toggle="tooltip" title="Add Employeer">
            Add &nbsp; &nbsp;
            <i class="flaticon-plus icon-lg"></i>
        </a>
    </div>

    <div class="tab-pane" id="other_details">
        <div class="table-responsive">
        <table class="certificate-table table">
            <thead>
                <th>Certificate Name</th>
                <th>From</th>
                <th>To</th>
            </thead>
            <tbody>
                @if(isset($user->certificates))
                    <?php $x = 1; ?>  
                    @foreach($certificates as $certificate) 
                        <tr>
                            <td class="form-group-error">
                                {{Form::text('certification_data['.$x.'][name]', $certificate->name,['class'=>'dynamic-input form-control','placeholder'=>'Certificate Name'])}}
                            </td>
                            <td class="form-group-error">
                                {{Form::date('certification_data['.$x.'][from]', $certificate->from,['max' => date('Y-m-d') ,'class'=>'form-control date-from'])}}
                            </td>
                            <td class="form-group-error">
                                {{Form::date('certification_data['.$x.'][to]', $certificate->to,['max' => date('Y-m-d') ,'class'=>'form-control date-to'])}}
                            </td>
                            <td class="form-group-error d-flex justify-content-between">
                                {{Form::file('certification_data['.$x.'][certification_attachment]', ['class' => 'dynamic-input form-control certificate-attachment'])}}
                                @if($certificate->attachment !== "") 
                                    <a href="{{ asset($certificate->attachment) }}" class="ml-3" target="_blank" data-toggle="tooltip" title="View old attachment">
                                        <i class="flaticon-eye icon-lg text-primary"></i>
                                    </a>
                                    <input type="hidden" name="certification_data[{{$x}}][old_certification_attachment]" value="{{ asset($certificate->attachment) }}">
                                @endif 
                            </td> 
                            <td class="form-group-error">
                                <a href="javascript:void(0)" class="remove-certificate-tr" data-toggle="tooltip" title="Remove Certificate">
                                    <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                                </a>
                            </td>
                        </tr>
                        <?php $x++;  ?>
                    @endforeach
                @else  
                    <tr>
                        <td class="form-group-error">
                            {{Form::text('certification_data[1][name]', null,['class'=>'dynamic-input form-control','placeholder'=>'Certificate Name'])}}
                        </td>
                        <td class="form-group-error">
                            {{Form::date('certification_data[1][from]', null,['max' => date('Y-m-d') ,'class'=>'form-control date-from'])}}
                        </td>
                        <td class="form-group-error">
                            {{Form::date('certification_data[1][to]', null,['max' => date('Y-m-d') ,'class'=>'form-control date-to'])}}
                        </td>
                        <td class="form-group-error">
                            {{Form::file('certification_data[1][certification_attachment]', ['class' => 'dynamic-input form-control certificate-attachment', 'style' => 'width:150px'])}}
                        </td> 
                        <td class="form-group-error">   
                            <a href="javascript:void(0)" class="remove-certificate-tr" data-toggle="tooltip" title="Remove Certificate">
                                <i class="flaticon2-rubbish-bin icon-lg text-danger"></i>
                            </a>
                        </td>
                    </tr>
                @endif 
                
            </tbody>
        </table>
        </div>
        
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 add-certificate" data-id="2" data-toggle="tooltip" title="Add Certificate">
            Add &nbsp; &nbsp;
            <i class="flaticon-plus icon-lg"></i> 
        </a>

        <br><br>
        <h5>Bank Details</h5>
        <br>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Account Number')}}
                    {{Form::text('account_number', null,['class'=>'form-control','placeholder'=>'Account Number'])}}

                    @if ($errors->has('account_number')) 
                        <span class="form-text text-danger">{{ $errors->first('account_number') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Holder Name')}}
                    {{Form::text('holder_name', null,['class'=>'form-control','placeholder'=>'Holder Name'])}}

                    @if ($errors->has('holder_name')) 
                        <span class="form-text text-danger">{{ $errors->first('holder_name') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Bank Name')}}
                    {{Form::text('bank_name', null,['class'=>'form-control','placeholder'=>'Bank Name'])}}

                    @if ($errors->has('bank_name')) 
                        <span class="form-text text-danger">{{ $errors->first('bank_name') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('ISFC Code')}}
                    {{Form::text('isfc_code', null,['class'=>'form-control','placeholder'=>'ISFC Code'])}}

                    @if ($errors->has('isfc_code')) 
                        <span class="form-text text-danger">{{ $errors->first('isfc_code') }}</span>
                    @endif
                </div>
               
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3 form-group-error">
                {{Form::label('bank_attachment')}}
                <div class="input-group-image d-flex">
                    {{Form::file('bank_attachment', ['class' => 'form-control'])}} 
                    @if(!empty($user->bank_attachment))
                        <a href="{{ asset($user->bank_attachment) }}" class="ml-3 mt-1" target="_blank" data-toggle="tooltip" title="View old attachment">
                            <i class="flaticon-eye icon-lg text-primary"></i>
                        </a>
                        <input type="hidden" name="old_bank_attachment" value="{{ $user->bank_attachment }}">
                    @endif
                </div> 
                @if ($errors->has('bank_attachment')) 
                    <span class="form-text text-danger">{{ $errors->first('bank_attachment') }}</span>
                @endif
            </div>
           
        </div>

    </div>
</div>
<div class="wizard-footer height-wizard d-flex flex-wrap"> 
    <input type='button' class='btn btn-previous btn-light-primary font-weight-bold btn-wd btn-sm mr-3 flex-grow' name='previous'
    value='Previous' /> 
    <div class="pull-right flex-grow">
        <input type='button' class='btn btn-next btn-fill btn-primary btn-wd btn-sm mr-3' name='next' value='Next' /> 
        {!! Form::hidden('id', null, ['id' => 'id']) !!}
        {!! Form::hidden('back_url', $back_url) !!}
        {!! Form::submit($submitButtonText, ['class' => 'btn btn-finish btn-fill btn-primary btn-wd btn-sm', 'id' => 'submitEmployeee']) !!} 
    </div>
    <input type='reset' class='btn btn-light-primary font-weight-bold btn-wd btn-sm flex-grow' name='cancel' value='cancel' /> 
</div>


<script>
$(document).ready(function () { 

    init_select2_branch();
    showHideDealers(false);

    $(document).on('change', '#user_type', function (e) {
        showHideDealers();
    });

    
	$('#services').select2({
		placeholder: "Select Services",
		allowClear: true,
		ajax: {
			url: '{!! route('product.servicesByname') !!}',
			dataType: 'json', 
			data: function (params) { 
				ultimaConsulta = params.term;
				var distributor_id = $("#distributor_id").val();
				return {
					name: params.term, // search term
					distributor_id: distributor_id,
				};
			},
			processResults: function (data, param) {  
				return {
					results: $.map(data, function (item) { 
						return {
							text: item.name, 
							id: item.id
						}
					})
				};
			}
		}
	});

    function showHideDealers(clear_role = true)
    {
        let triggerValue = $("#user_type").val();

        console.log(triggerValue);
 
        if(triggerValue == 0 || triggerValue == 2) {
            $(".distributor-container").addClass('d-none');
            $(".branch-container").addClass('d-none');
            $("#distributor_id").val("").trigger("change"); 
            $("#branch_id").val("").trigger("change");
        } else {
            $("#distributor_id").attr('required', true);
            $("#branch_id").attr('required', true);
            $(".branch-container").removeClass('d-none');
            $(".distributor-container").removeClass('d-none');
        }

        if(triggerValue == 2) {
            if(clear_role == true) {
                $('#role').val("");
            } 
            $("#role").val(4);
            $("#role").attr('readonly', true);
            $('#role option[value="4"]').removeAttr('disabled'); 
            $('#role option:not(option[value="4"])').attr('disabled', true);  
            $("#plan_commissin").val("");
            $(".plan-commission-container").removeClass('d-none');
        } else {
            if(clear_role == true) {
                $('#role').val("");
            }  
            $('#role option[value="4"]').attr('disabled', true);  
            $('#role option:not(option[value="4"])').removeAttr('disabled');  
            $("#plan_commissin").val("");
            $(".plan-commission-container").addClass('d-none');
        }
    }

    $(document).on("change", "#distributor_id", function (e) {
        $("#branch_id").val("").trigger('change');
        init_select2_branch();
    });

    function init_select2_branch()
    {
        $("#branch_id").select2({
            placeholder: "Select Branch",
            allowClear: true,
            ajax: {
				url: '{!! route('branch.branchByDistributor') !!}',
				dataType: 'json', 
				data: function (params) { 
					ultimaConsulta = params.term;
					var distributor_id = $("#distributor_id").val();
					return {
						name: params.term, // search term
						distributor_id: distributor_id,
					};
				},
				processResults: function (data, param) {  
					return {
						results: $.map(data, function (item) { 
							return {
								text: item.name, 
								id: item.id
							}
						})
					};
				}
			}
        }); 
    }

    <?php if($is_system_user && $distributor == false && !isset($user)): ?>
        $('#distributor_id').select2({
            placeholder: "Select Salon",
            allowClear: true,
            ajax: {
                url: '{!! route('salons.byname') !!}',
                dataType: 'json', 
                processResults: function (data, param) {  
                    return {
                        results: $.map(data, function (item) { 
                            return {
                                text: item.name, 
                                id: item.id
                            }
                        })
                    };
                }
            }
        })
    <?php endif; ?>
    

    var today = new Date().toISOString().split("T")[0]; 
  
    $(document).on('change', '#profile_pic', function(e){
        $('.profile_pic').attr('src', URL.createObjectURL(e.target.files[0]));
    });

    // Add min date to the date employeer fields
    $(document).on('click', '.date-from', function (e){   
        $(e.target).attr('max', today); 
    });

    // Add min date to the date employeer fields
    $(document).on('change', '.date-from', function (e){  
        $(e.target).closest('tr').find('.date-to').attr('min', $(e.target).val()); 
        $(e.target).closest('tr').find('.date-to').attr('max', today); 
    });

    // Check if min date is set or not (employeer fields)
    $(document).on('click', '.date-to', function (e){
        let from_date = $(e.target).closest('tr').find('.date-from').val();
 
        if(from_date == "") {
            alert('Please select from date first');
            $(e.target).val("");
            return false;
        } else {
            $(e.target).closest('tr').find('.date-to').attr('min', from_date);
            $(e.target).closest('tr').find('.date-to').attr('max', today);
        }
    });
  


    // Add more week off tr
    $(document).on('click', '#add-week-off', function () {

        let table_length = $('.week-off-tr').length + 1;
  
        let clone = $(".week-off-tr:first").clone();
        $(clone).find('.dynamic-input').val("");

        $(clone).find('.year').attr('name', `week_off[${table_length}][year]`);
        $(clone).find('.month').attr('name', `week_off[${table_length}][month]`);
        $(clone).find('.date-1').attr('name', `week_off[${table_length}][date_1]`);
        $(clone).find('.date-2').attr('name', `week_off[${table_length}][date_2]`);
        $(clone).find('.date-3').attr('name', `week_off[${table_length}][date_3]`);
        $(clone).find('.date-4').attr('name', `week_off[${table_length}][date_4]`);
        $(clone).find('.date-5').attr('name', `week_off[${table_length}][date_5]`);

        $(clone).find('.date').removeAttr('min');
        $(clone).find('.date').removeAttr('max');
        $(".week-off-table").append(clone);

        $('[data-toggle="tooltip"]').tooltip();
    });

    // Add min max date on change of month  
    $(document).on('change', '.month', function (e) {
         
        let selected_year = $(e.target).closest('tr').find('.year').val();
        let selected_month = $(e.target).val();

        if(selected_year == "") {
            alert("Please select year first");
            $(e.target).val("");
            return false;
        } 
        set_min_max_dates(e, selected_year,selected_month);
    });
 
    // check if month & year is not selected
    $(document).on('click', '.date', function (e) {

        let selected_year = $(e.target).closest('tr').find('.year').val();
        let selected_month = $(e.target).closest('tr').find('.month').val();

        if(selected_year == "" || selected_month == "") {
            alert("Please select year and month");
            $(e.target).val("");
            return false;
        }

        set_min_max_dates(e, selected_year,selected_month);
    });

    
    function set_min_max_dates(e, selected_year, selected_month)
    { 
        let date_start;
        let date_end;
        if(selected_month.length == 1) {
            date_start =  selected_year +'-0'+ selected_month +'-01'; 
            date_end =  selected_year +'-0'+ selected_month +'-'+ new Date(selected_year, selected_month, 0).getDate();  
        } else {
            date_start =  selected_year +'-'+ selected_month +'-01';  
            date_end =  selected_year +'-'+ selected_month +'-'+ new Date(selected_year, selected_month, 0).getDate(); 
        } 

        $(e.target).closest('tr').find('.date').attr('min', date_start);
        $(e.target).closest('tr').find('.date').attr('max', date_end);
        console.log(date_start, date_end);
    }
 

    // Remove week off tr
    $(document).on('click','.remove-week-off-tr',function(){
        $('.tooltip').tooltip().remove()
        $(this).closest('tr').remove();
    });
   
    
    var value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null;
    $(this).val(value);
});
</script>