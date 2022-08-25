
<div class="wizard-header">
    <h3>Create <b>Employee</b> </h3>
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
                <div class="form-group form-group-error">
                    {{Form::label('First Name')}} *
                    {{Form::text('first_name','',['class'=>'form-control','placeholder'=>'First Name'])}}
                    
                    @if ($errors->has('first_name')) 
                        <span class="form-text text-danger">{{ $errors->first('first_name') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Last Name')}} *
                    {{Form::text('last_name','',['class'=>'form-control','placeholder'=>'Last Name'])}}

                    @if ($errors->has('last_name')) 
                        <span class="form-text text-danger">{{ $errors->first('last_name') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Nick Name')}}
                    {{Form::text('nick_name','',['class'=>'form-control','placeholder'=>'Nick Name'])}}

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
                    {{Form::email('email','',['class'=>'form-control','id' => 'email', 'placeholder'=>'Email', 'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!'])}}

                    @if ($errors->has('email')) 
                        <span class="form-text text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Primary Number')}} *
                    {{Form::text('primary_number','',['class'=>'form-control valid-number','placeholder'=>'Primary Number'])}}

                    @if ($errors->has('primary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_number') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Secondary Number')}}<small> </small>
                    {{Form::text('secondary_number','',['class'=>'form-control valid-number','placeholder'=>'Secondary Number'])}}

                    @if ($errors->has('secondary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_number') }}</span>
                    @endif
                </div>
                
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Branch')}} *
                    {{Form::select('branch_id', $branch,'',['class'=>'form-control','placeholder'=>'Select Branch'])}}

                    @if ($errors->has('branch_id')) 
                        <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Expertise')}}
                    {{Form::text('expertise','',['class'=>'form-control','placeholder'=>'Expertise'])}}
                    
                    @if ($errors->has('expertise')) 
                        <span class="form-text text-danger">{{ $errors->first('expertise') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-4">
                <div class="form-group form-group-error">
                    {{Form::label('Role')}} *
                    {{Form::select('role',$roles,'',['class'=>'form-control','placeholder'=>'Select Role'])}}

                    @if ($errors->has('role')) 
                        <span class="form-text text-danger">{{ $errors->first('role') }}</span>
                    @endif
                </div>
                
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-4"> 
                <div class="form-group form-group-error">
                    {{Form::label('Password')}} *
                    {{Form::password('password', [
                        'class'=>'form-control','placeholder'=>'Password',
                        'pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,20}$',
                        'title' => 'At least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.' 
                    ])}}

                    @if ($errors->has('password')) 
                        <span class="form-text text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div> 
                

                <div class="form-group form-group-error">
                    {{Form::label('Date of Joining')}} *
                    {{Form::date('date_of_joining','',['class'=>'form-control'])}}

                    @if ($errors->has('date_of_joining')) 
                        <span class="form-text text-danger">{{ $errors->first('date_of_joining') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-md-8">
                <div class="form-group form-group-error">
                    {{Form::label('Address')}}
                    {{Form::textarea('address','',['class'=>'form-control','placeholder'=>'Address','rows'=>5])}}

                    @if ($errors->has('address')) 
                        <span class="form-text text-danger">{{ $errors->first('address') }}</span>
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
                    {{Form::text('salary','',['class'=>'form-control valid-number','placeholder'=>'Salary'])}}

                    @if ($errors->has('salary')) 
                        <span class="form-text text-danger">{{ $errors->first('salary') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Basic')}}
                    {{Form::text('basic','',['class'=>'form-control valid-number','placeholder'=>'Basic'])}}

                    @if ($errors->has('basic')) 
                        <span class="form-text text-danger">{{ $errors->first('basic') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-1">
                <div class="form-group form-group-error">
                    {{Form::label('PF')}}
                    {{Form::text('pf','',['class'=>'form-control valid-number','placeholder'=>'PF'])}}

                    @if ($errors->has('pf')) 
                        <span class="form-text text-danger">{{ $errors->first('pf') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Gratuity')}}
                    {{Form::text('gratuity','',['class'=>'form-control valid-number','placeholder'=>'Gratuity'])}}

                    @if ($errors->has('gratuity')) 
                        <span class="form-text text-danger">{{ $errors->first('gratuity') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Others')}}
                    {{Form::text('others','',['class'=>'form-control valid-number','placeholder'=>'Others'])}}

                    @if ($errors->has('others')) 
                        <span class="form-text text-danger">{{ $errors->first('others') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-1">
                <div class="form-group form-group-error">
                    {{Form::label('PT')}}
                    {{Form::text('pt','',['class'=>'form-control valid-number','placeholder'=>'PT'])}}

                    @if ($errors->has('pt')) 
                        <span class="form-text text-danger">{{ $errors->first('pt') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-2">
                <div class="form-group form-group-error">
                    {{Form::label('Income Tax')}}
                    {{Form::text('income_tax','',['class'=>'form-control valid-number','placeholder'=>'Income Tax'])}}

                    @if ($errors->has('income_tax')) 
                        <span class="form-text text-danger">{{ $errors->first('income_tax') }}</span>
                    @endif
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group form-group-error">
                    {{Form::label('Over Time P/H')}}
                    {{Form::text('over_time_p_h','',['class'=>'form-control valid-number','placeholder'=>'Over Time P/H'])}}
                    
                    @if ($errors->has('over_time_p_h')) 
                        <span class="form-text text-danger">{{ $errors->first('over_time_p_h') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-error">
                    {{Form::label('Working Hours')}} *
                    {{Form::text('working_hours','',['class'=>'form-control','placeholder'=>'Working Hours'])}}

                    @if ($errors->has('working_hours')) 
                        <span class="form-text text-danger">{{ $errors->first('working_hours') }}</span>
                    @endif
                </div>
                
            </div>
        </div>
        
        <h5>Week-Off</h5>
        <table class="week-off-table table">
            <tbody class="week-off-table-tbody">
                @for($i=1; $i<=1; $i++) 
                <tr class="week-off-tr">
                    <td class="week-off-year form-group-error">
                        {{Form::select('week_off['.$i.'][year]', $years ,'',['class'=>'form-control dynamic-input year','placeholder'=>'Year'])}}
                    </td>
                    <td class="week-off-year form-group-error">
                        {{Form::select('week_off['.$i.'][month]',$months,'',['class'=>'form-control dynamic-input month','placeholder'=>'Month'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        {{Form::date('week_off['.$i.'][date_1]','',['class'=>'form-control dynamic-input date-1 date'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        {{Form::date('week_off['.$i.'][date_2]','',['class'=>'form-control dynamic-input date-2 date'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        {{Form::date('week_off['.$i.'][date_3]','',['class'=>'form-control dynamic-input date-3 date'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        {{Form::date('week_off['.$i.'][date_4]','',['class'=>'form-control dynamic-input date-4 date'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        {{Form::date('week_off['.$i.'][date_5]','',['class'=>'form-control dynamic-input date-5 date'])}}
                    </td>
                    <td class="week-off-th form-group-error">
                        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 remove-week-off-tr">Remove</a>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3" id="add-week-off" data-id="4">Add</a>
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
                    <td class="form-group-error">
                        {{Form::text('employer_data[1][employeer]','',['class'=>'form-control','placeholder'=>'Employeer'])}}
                    </td>
                    <td class="form-group-error">
                        {{Form::date('employer_data[1][from]','',['class'=>'form-control date-from'])}}
                    </td>
                    <td class="form-group-error">
                        {{Form::date('employer_data[1][to]','',['class'=>'form-control date-to'])}}
                    </td>
                    <td class="form-group-error">
                        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 remove-employeer-tr">Remove</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 add-employeer" data-id="2">Add</a>
    </div>

    <div class="tab-pane" id="other_details">
        <table class="certificate-table table">
            <thead>
                <th>Certificate Name</th>
                <th>From</th>
                <th>To</th>
            </thead>
            <tbody>
                <tr>
                    <td class="form-group-error">
                        {{Form::text('certification_data[1][name]','',['class'=>'form-control','placeholder'=>'Certificate Name'])}}
                    </td>
                    <td class="form-group-error">
                        {{Form::date('certification_data[1][from]','',['class'=>'form-control date-from'])}}
                    </td>
                    <td class="form-group-error">
                        {{Form::date('certification_data[1][to]','',['class'=>'form-control date-to'])}}
                    </td>
                    <td class="form-group-error">
                        {{Form::file('certification_data[1][certification_attachment]', ['class' => 'form-control'])}}
                    </td>
                    <td class="form-group-error">
                        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 remove-certificate-tr">Remove</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 add-certificate" data-id="2">Add</a>

        <br><br>
        <h5>Bank Details</h5>
        <br>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Account Number')}}
                    {{Form::text('account_number','',['class'=>'form-control','placeholder'=>'Account Number'])}}

                    @if ($errors->has('account_number')) 
                        <span class="form-text text-danger">{{ $errors->first('account_number') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Holder Name')}}
                    {{Form::text('holder_name','',['class'=>'form-control','placeholder'=>'Holder Name'])}}

                    @if ($errors->has('holder_name')) 
                        <span class="form-text text-danger">{{ $errors->first('holder_name') }}</span>
                    @endif
                </div>
                
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('Bank Name')}}
                    {{Form::text('bank_name','',['class'=>'form-control','placeholder'=>'Bank Name'])}}

                    @if ($errors->has('bank_name')) 
                        <span class="form-text text-danger">{{ $errors->first('bank_name') }}</span>
                    @endif
                </div>
               
            </div>
            <div class="col-sm-3">
                <div class="form-group form-group-error">
                    {{Form::label('ISFC Code')}}
                    {{Form::text('isfc_code','',['class'=>'form-control','placeholder'=>'ISFC Code'])}}

                    @if ($errors->has('isfc_code')) 
                        <span class="form-text text-danger">{{ $errors->first('isfc_code') }}</span>
                    @endif
                </div>
               
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-3 form-group-error">
                {{Form::label('bank_attachment')}}
                {{Form::file('bank_attachment', ['class' => 'form-control'])}}

                @if ($errors->has('bank_attachment')) 
                    <span class="form-text text-danger">{{ $errors->first('bank_attachment') }}</span>
                @endif
            </div>
           
        </div>

    </div>
</div>
<div class="wizard-footer height-wizard d-flex">
    <div class="mr-3">
        <input type='button' class='btn btn-previous btn-fill btn-secondary btn-wd btn-sm' name='previous'
            value='Previous' />
    </div>
    <div class="pull-right">
        <input type='button' class='btn btn-next btn-fill btn-primary btn-wd btn-sm' name='next' value='Next' /> 
        {!! Form::hidden('id', null, ['id' => 'id']) !!}
        {!! Form::submit($submitButtonText, ['class' => 'btn btn-finish btn-fill btn-primary btn-wd btn-sm', 'id' => 'submitEmployeee']) !!} 
    </div>
</div>


<script>
$(document).ready(function () {  
 
    // Add min date to the date employeer fields
    $(document).on('change', '.date-from', function (e){  
        $(e.target).closest('tr').find('.date-to').attr('min', $(e.target).val()); 
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
        $(this).closest('tr').remove();
    });
   
    
    var value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null;
    $(this).val(value);
});
</script>