<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="row"> 
				@if($is_system_user == 0)
					@if(isset($client))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $client->distributor_id }}">
					@else
						<div class="col-lg-6 form-group form-group-error"> 
							{!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
							<select name="distributor_id" id="distributor_id" class="form-control">
								@if(isset($selected_distributor))
									<option value="{{ $selected_distributor->id }}">{{ $selected_distributor->name }}</option>
								@endif
							</select>
							@if ($errors->has('distributor_id'))  
								<span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
							@endif
						</div> 
					@endif
				@endif 
			</div>
			<div class="row">
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						isset($data['owners']) ? $data['owners'][0]['name'] : old('name'), 
						['class' => 'form-control',
						'placeholder' => 'Name']) 
					!!}
					@if ($errors->has('name'))  
						<span class="form-text text-danger">{{ $errors->first('name') }}</span>
					@endif
				</div>
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('email', __('Email'). ': ', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::email('email',
						isset($data['email']) ? $data['email'] : old('email'), 
						['class' => 'form-control',
						'placeholder' => 'Email',
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!'
						]) 
					!!}
					@if ($errors->has('email'))  
						<span class="form-text text-danger">{{ $errors->first('email') }}</span>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('primary_number', __('Contact Number'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('primary_number',  
						isset($data['phone']) ? $data['phone'] : old('primary_number'), 
						['class' => 'form-control',
						'placeholder' => 'Contact Number']) 
					!!} 
					@if ($errors->has('primary_number'))  
						<span class="form-text text-danger">{{ $errors->first('primary_number') }}</span>
					@endif
				</div>
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('secondary_number', __('WhatsApp Number'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('secondary_number',  
						old('secondary_number'), 
						['class' => 'form-control',
						'placeholder' => 'WhatsApp Number']) 
					!!} 
					@if ($errors->has('secondary_number'))  
						<span class="form-text text-danger">{{ $errors->first('secondary_number') }}</span>
					@endif
				</div>
			</div> 
			<div class="row">
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('date_of_birth', __('Date of Birth'). ': ', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::date('date_of_birth',  
						old('date_of_birth') ?? null, 
						['class' => 'form-control',
						'placeholder' => 'Date of Birth']) 
					!!} 
					@if ($errors->has('date_of_birth'))  
						<span class="form-text text-danger">{{ $errors->first('date_of_birth') }}</span>
					@endif
				</div>
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('anniversary', __('Anniversary'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::date('anniversary',  
						old('anniversary') ?? null, 
						['class' => 'form-control',
						'placeholder' => 'Anniversary']) 
					!!} 
					@if ($errors->has('anniversary'))  
						<span class="form-text text-danger">{{ $errors->first('anniversary') }}</span>
					@endif
				</div>
			</div> 
			<div class="row">
				<!-- <div class="col-lg-6 form-group form-group-error">
				<?php $types = array('Individual','Corporate','Dealers');?>
					{!! Form::label('company_type', __('Company type'). ': *', ['class' => 'control-label thin-weight']) !!}
					
					{!!
						Form::select('company_type',
						$types,
						old('company_type'),
						['class' => 'form-control ui search selection top right pointing company_type-select',
						'id' => 'company_type-select'])
					!!}
					@if ($errors->has('company_type'))  
						<span class="form-text text-danger">{{ $errors->first('company_type') }}</span>
					@endif
				</div> -->
				<div class="col-lg-3 form-group form-group-error"> 
					{!! Form::label('country_id', __('Country'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('country_id',
						$countries,
						isset($client->country_id) ? $client->country_id : 101,
						['class' => 'form-control ui search selection top right pointing country_id-select country-val searchpicker',
						'id' => 'country_id-select','placeholder'=>'Please select country','data-statepicker'=>'state-drop-down-client','data-statetext'=>'state-textbox-client','data-postcode'=>'postcode-client','required'])
					!!} 
					@if ($errors->has('country_id'))  
						<span class="form-text text-danger">{{ $errors->first('country_id') }}</span>
					@endif
				</div>
				@php
					$checkStatePicker =  empty($client) || (!empty($client) && $client->country_id == 101) ? '' : 'd-none';
					$checkStatePickerAttr =  empty($client) || (!empty($client) && $client->country_id == 101) ? 'required' : '';
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
					{!! Form::label('city', __('City'). ':', ['class' => 'control-label thin-weight']) !!}
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
			<!-- <div class="col-lg-6 form-group form-group-error">
					{!! Form::label('user_id', __('Assign user'). ': *', ['class' => 'control-label thin-weight']) !!}
					<select name="user_id" id="user_id" class='form-control'>
						@if(isset($user)) 
							<option value="{{ $user->id }}">{{ $user->first_name ." ". $user->last_name }}</option>
						@endif
					</select>
					@if ($errors->has('user_id'))  
						<span class="form-text text-danger">{{ $errors->first('user_id') }}</span>
					@endif
				</div> -->
			<div class="row"> 
				<div class="col-lg-3"> 
					<div class="form-group form-group-error">
						{!! Form::label('gender', __('Gender'). ':', ['class' => 'control-label thin-weight']) !!}
						{!!
							Form::select('gender',
							[
								'Male' => 'Male',
								'Female' => 'Female', 
							],
							isset($client->gender) ? $client->gender : null,
							['class' => 'form-control',
							'id' => 'gender',
							'placeholder'=>'Please select gender'])
						!!}
						@if ($errors->has('gender'))  
							<span class="form-text text-danger">{{ $errors->first('gender') }}</span>
						@endif
					</div> 
					<div class="form-group form-group-error">
						{!! Form::label('client_type', __('Client Type'). ': *', ['class' => 'control-label thin-weight']) !!}
						{!!
							Form::select('client_type',
							[
								'A' => 'A',
								'B' => 'B',
								'C' => 'C',
							],
							isset($client->client_type) ? $client->client_type : null,
							['class' => 'form-control',
							'id' => 'client_type-select','placeholder'=>'Please select client type',$checkStatePickerAttr, 'style' => 'width:100%'])
						!!}
						@if ($errors->has('client_type'))  
							<span class="form-text text-danger">{{ $errors->first('client_type') }}</span>
						@endif
					</div>
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('notes', __('Notes'). ':', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::textarea('notes',
							isset($data['notes']) ? $data['notes'] : old('notes'), 
							['class' => 'form-control',
							'rows'=>4,
							'placeholder' => 'Notes'])
						!!} 
					</div>
					@if ($errors->has('notes'))  
						<span class="form-text text-danger">{{ $errors->first('notes') }}</span>
					@endif
				</div> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('address', __('Address'). ':', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::textarea('address',
							isset($data['address']) ? $data['address'] : old('address'), 
							['class' => 'form-control',
							'rows'=>4,
							'placeholder' => 'Address'])
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					@if ($errors->has('address'))  
						<span class="form-text text-danger">{{ $errors->first('address') }}</span>
					@endif
				</div> 
			</div>
			<div class="row">
				<div class="col-lg-6 form-group form-group-error checkbox-inline">
					<label class="checkbox">
						@if(isset($client)) 
							@if($client->allow_notifications == 1) 
							<input type="checkbox" checked="checked" name="allow_notifications" id="allow_notifications"/>
							@else 
								<input type="checkbox" name="allow_notifications" id="allow_notifications"/>
							@endif 
						@else 
							<input type="checkbox" name="allow_notifications" id="allow_notifications"/>
						@endif 
						<span></span>
						Allow Marketing Notification
					</label>
					@if ($errors->has('allow_notifications')) 
						<span class="form-text text-danger">{{ $errors->first('allow_notifications') }}</span>
					@endif  
				</div>
			</div> 
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitClient']) !!} 
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->

<script>
	$(document).ready(function (){

		<?php if(!isset($client)): ?>
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
		
 
		$('#industry_id').select2({
			placeholder: "Select Industry",
    		allowClear: true,
			ajax: {
				url: '{!! route('products.industrybyname') !!}',
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
		});

		// set select2 picker
		$('.searchpicker').select2();
		var fixNewLine = {
			exportOptions: {
				format: {
					body: function ( data, column, row ) {
						if (row >= 0 && row <= 2) {
							return data.replace(/<.*?>/ig, "")
						} else if (row >= 4 && row <= 4) {
							return data.replace(/<.*?>/ig, "")
						}
						return data;
					}
				}
			}
		};

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
		// $('#user_id').select2({
		// 	placeholder: "Select User",
    	// 	allowClear: true,
		// 	ajax: {
		// 		url: '{!! route('users.userbyname') !!}',
		// 		dataType: 'json', 
		// 		processResults: function (data, param) {  
		// 			return {
		// 				results: $.map(data, function (item) { 
		// 					return {
		// 						text: item.first_name +" "+ item.last_name, 
		// 						id: item.id
		// 					}
		// 				})
		// 			};
		// 		}
		// 	}
		// });
	});
</script>