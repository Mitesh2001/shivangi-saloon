<!--begin::Card-->
 
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="row"> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						null, 
						['class' => 'form-control',
						'placeholder' => "name"]) 
					!!}
                    @if ($errors->has('name')) 
                        <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('gst_number', __('GST Number'). ':', ['class' => '']) !!}
					{!! 
						Form::text('gst_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "GST Number"]) 
					!!}
                    @if ($errors->has('gst_number')) 
                        <span class="form-text text-danger">{{ $errors->first('gst_number') }}</span>
                    @endif  
				</div>    
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('pan_number', __('Pan Number'). ':', ['class' => '']) !!}
					{!! 
						Form::text('pan_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Pan Number",
						'pattern' => '[A-Z]{5}[0-9]{4}[A-Z]{1}']) 
					!!}
                    @if ($errors->has('pan_number')) 
                        <span class="form-text text-danger">{{ $errors->first('pan_number') }}</span>
                    @endif  
				</div>    
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('number_of_employees', __('Number of Emplyees'). ':', ['class' => '']) !!}
					{!! 
						Form::number('number_of_employees',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Number of Emplyees", 'number' => true]) 
					!!}
                    @if ($errors->has('number_of_employees')) 
                        <span class="form-text text-danger">{{ $errors->first('number_of_employees') }}</span>
                    @endif  
				</div>    
			</div>  
			<div class="row">  
			<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('primary_number', __('Primary Number'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('primary_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Primary Number"]) 
					!!}
                    @if ($errors->has('primary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_number') }}</span>
                    @endif  
				</div>    
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('secondary_number', __('Secondary Number'). ':', ['class' => '']) !!}
					{!! 
						Form::text('secondary_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Secondary Number"]) 
					!!}
                    @if ($errors->has('secondary_number')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_number') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('primary_email', __('Primary Email'). ': *', ['class' => '']) !!}
					{!! 
						Form::email('primary_email',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Primary Email",
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!']) 
					!!}
                    @if ($errors->has('primary_email')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_email') }}</span>
                    @endif  
				</div>    
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('secondary_email', __('Secondary Email'). ':', ['class' => '']) !!}
					{!! 
						Form::email('secondary_email',  
						null, 
						['class' => 'form-control', 
						'placeholder' => "Secondary Email",
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!']) 
					!!}
                    @if ($errors->has('secondary_email')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_email') }}</span>
                    @endif  
				</div>   
			</div>  
			<div class="row">  
				<div class="col-lg-3 form-group form-group-error"> 
					{!! Form::label('country_id', __('Country'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('country_id',
						$countries,
						isset($distributor->country_id) ? $distributor->country_id : 101,
						['class' => 'form-control ui search selection top right pointing country_id-select country-val searchpicker',
						'id' => 'country_id-select','placeholder'=>'Please select country','data-statepicker'=>'state-drop-down-client','data-statetext'=>'state-textbox-client','data-postcode'=>'postcode-client','required'])
					!!} 
					@if ($errors->has('country_id'))  
						<span class="form-text text-danger">{{ $errors->first('country_id') }}</span>
					@endif
				</div>
				@php
					$checkStatePicker =  empty($distributor) || (!empty($distributor) && $distributor->country_id == 101) ? '' : 'd-none';
					$checkStatePickerAttr =  empty($distributor) || (!empty($distributor) && $distributor->country_id == 101) ? 'required' : '';
					$checkStateText = !empty($checkStatePicker) ? '' : 'd-none';
					$checkStateTextAttr =  empty($checkStatePicker) ? '' : 'required';
				@endphp
				<div class="{{'col-lg-3 state-drop-down-client '.$checkStatePicker}} form-group form-group-error"> 
					{!! Form::label('state_id', __('State'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('state_id',
						$states,
						isset($data['state_id']) ? $data['state_id'] : null,
						['class' => 'form-control ui search selection top right pointing state_id-select searchpicker state-drop-down-client-picker',
						'id' => 'state_id-select','placeholder'=>'Please select state',$checkStatePickerAttr, 'style' => 'width:100%'])
					!!}
					@if ($errors->has('state_id'))  
						<span class="form-text text-danger">{{ $errors->first('state_id') }}</span>
					@endif
				</div>
				<div class="{{'col-lg-3 state-textbox-client '.$checkStateText}} form-group form-group-error">
					{!! Form::label('state_name', __('State'), ['class' => '']) !!}
					<span>*</span>
					<div class="input-group">
						{!! 
							Form::text('state_name',  
							isset($data['state_name']) ? $data['state_name'] : null, 
							['class' => 'form-control state-textbox-client-text', 'placeholder' => "State name",$checkStateTextAttr]) 
						!!}
					</div>
					@if ($errors->has('state_name'))  
						<span class="form-text text-danger">{{ $errors->first('state_name') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('city', __('City'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('city',
						isset($data['city']) ? $data['city'] : old('city'),
						['class' => 'form-control',
						'placeholder' => 'City']) 
					!!}
					@if ($errors->has('city'))  
						<span class="form-text text-danger">{{ $errors->first('city') }}</span>
					@endif
				</div> 
				<div class="col-lg-3">
					<div class="form-group form-group form-group-error">
						{!! Form::label('zipcode', __('Zipcode'). ':', ['class' => 'control-label thin-weight']) !!}
						<div class="input-group">
							{!! 
								Form::text('zipcode',
								isset($data['zipcode']) ? $data['zipcode'] : old('zipcode'), 
								['class' => 'form-control',
								'placeholder' => 'Zipcode']) 
							!!}
							<div class="input-group-append"><span class="input-group-text"><i class="la la-bookmark-o"></i></span></div>
						</div>
						@if ($errors->has('zipcode'))  
							<span class="form-text text-danger">{{ $errors->first('zipcode') }}</span>
						@endif
					</div>  
				</div>
			</div> 
			<div class="row">   
				<div class="col-lg-6">
					<div class="form-group form-group-error">
						{!! Form::label('contact_person', __('Contact Person Name'). ': *', ['class' => '']) !!}
						{!! 
							Form::text('contact_person',  
							null, 
							['class' => 'form-control',
							'placeholder' => "Contact Person Name"]) 
						!!}
						@if ($errors->has('contact_person')) 
							<span class="form-text text-danger">{{ $errors->first('contact_person') }}</span>
						@endif  
					</div>    
					<div class="form-group form-group-error">
						{!! Form::label('contact_person_number', __('Contact Person Number'). ': *', ['class' => '']) !!}
						{!! 
							Form::text('contact_person_number',  
							null, 
							['class' => 'form-control',
							'placeholder' => "Contact Person Number"]) 
						!!}
						@if ($errors->has('contact_person_number')) 
							<span class="form-text text-danger">{{ $errors->first('contact_person_number') }}</span>
						@endif  
					</div> 
				</div>
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('address', __('Address'). ':', ['class' => '']) !!}
					{!! 
						Form::textarea('address',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Address",
						'rows' => 4]) 
					!!}
                    @if ($errors->has('address')) 
                        <span class="form-text text-danger">{{ $errors->first('address') }}</span>
                    @endif  
				</div>    
			</div> 
			<hr>
			<h3 class="text-muted mb-5">Email & SMS Service</h3>
			<div class="row"> 
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('sender_id', __('Sender Id'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('sender_id',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Sender Id"]) 
					!!}
                    @if ($errors->has('sender_id')) 
                        <span class="form-text text-danger">{{ $errors->first('sender_id') }}</span>
                    @endif  
				</div>
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('from_email', __('From Email'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('from_email',  
						null, 
						['class' => 'form-control',
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please enter valid email address!',
						'placeholder' => "From Email"]) 
					!!}
                    @if ($errors->has('from_email')) 
                        <span class="form-text text-danger">{{ $errors->first('from_email') }}</span>
                    @endif  
				</div>
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('from_name', __('From Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('from_name',  
						null, 
						['class' => 'form-control',
						'placeholder' => "From Name"]) 
					!!}
                    @if ($errors->has('from_name')) 
                        <span class="form-text text-danger">{{ $errors->first('from_name') }}</span>
                    @endif  
				</div>
			</div>
			<div class="row"> 
				<div class="col-lg-4 form-group form-group-error checkbox-inline">
					<label class="checkbox">
						@if(isset($distributor)) 
							@if($distributor->sms_service == 1) 
								<input type="checkbox" checked="checked" name="sms_service" id="sms_service"/>
							@else 
								<input type="checkbox" name="sms_service" id="sms_service"/>
							@endif 
						@else 
						<input type="checkbox" name="sms_service" id="sms_service"/>
						@endif
						<span></span>
						SMS Service (Enable/Disable)
					</label>
                    @if ($errors->has('sms_service')) 
                        <span class="form-text text-danger">{{ $errors->first('sms_service') }}</span>
                    @endif  
				</div>
				<div class="col-lg-4 form-group form-group-error checkbox-inline">
					<label class="checkbox">
						@if(isset($distributor)) 
							@if($distributor->email_service == 1) 
							<input type="checkbox" checked="checked" name="email_service" id="email_service"/>
							@else 
								<input type="checkbox" name="email_service" id="email_service"/>
							@endif 
						@else 
							<input type="checkbox" name="email_service" id="email_service"/>
						@endif 
						<span></span>
						Email Service (Enable/Disable)
					</label>
                    @if ($errors->has('email_service')) 
                        <span class="form-text text-danger">{{ $errors->first('email_service') }}</span>
                    @endif  
				</div> 
			</div>
			<hr>
			<h3 class="text-muted mb-5">Logo</h3>
			<div class="row">
				<div class="form-group col-md-3">
					<div class="form-group-error custom-file">
						{!! Form::label('logo', __('Logo'). ':', ['class' => '']) !!}
						{!! 
							Form::file('logo', 	
							['class' => 'custom-file-input',
							'id' => 'logo']) 
							!!}
						{!! Form::label('logo', __('Logo'). ':', ['class' => 'custom-file-label']) !!}
						<span class="text-muted form-text">Logo size should be 30 X 30</span>
						@if ($errors->has('logo'))  
							<span class="form-text text-danger">{{ $errors->first('logo') }}</span>
						@endif 
						<br><br>
						{!! Form::hidden('old_logo', $distributor->logo ?? NULL) !!}
						@if(isset($distributor->logo))
							<img src="{{ asset($distributor->logo) }}" id="distributor-logo" alt="" height="100px">
						@else 
							<img src="" id="distributor-logo" alt="" height="100px">
						@endif 
					</div> 	
				</div>
			</div>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitVendor']) !!} 
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitVendor']) !!}
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->  

<script>
$(document).ready(function (e) {

	$("#state_id-select").select2();
	$("#country_id-select").select2();

	$(document).on('change', '#logo', function (event){
		$('#distributor-logo').attr('src', URL.createObjectURL(event.target.files[0]));
	});

	$(document).on('change','.country-val',function(){
		var value = $(this).val();
		var statePicker = $(this).data('statepicker');
		var stateText = $(this).data('statetext');
		var postCode = $(this).data('postcode');
		if(value != 101)
		{
		$('.'+postCode).removeClass('valid-number'); 
		$('.'+statePicker).addClass('d-none');
		$('.'+stateText).removeClass('d-none');
		$('.'+statePicker+'-picker').prop('required',false);
		$('.'+stateText+'-text').prop('required',true);
		}else{
			$('.'+postCode).val('');
			$('.'+postCode).addClass('valid-number');
			$('.'+statePicker).removeClass('d-none');
			$('.'+stateText).addClass('d-none');
			$('.'+statePicker+'-picker').prop('required',true);
			$('.'+stateText+'-text').prop('required',false);
		}
	});
});
</script>