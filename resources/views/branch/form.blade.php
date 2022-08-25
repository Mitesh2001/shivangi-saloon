<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="row">
				@if($is_system_user || $is_distributor_user)
					@if(isset($branch))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $branch->distributor_id }}">
					@elseif(isset($distributor_id) && !empty($distributor_id))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $distributor_id }}">
					@else
						<div class="col-lg-4 form-group form-group-error"> 
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
				<div class="col-lg-4 form-group form-group-error">
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
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('primary_contact_person', __('Primary Contact Person'). ': ', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('primary_contact_person', $primary_contact_person ?? [], null, ['class' => 'form-control', 'id' => 'primary_contact_person']) !!}
					@if ($errors->has('primary_contact_person'))  
						<span class="form-text text-danger">{{ $errors->first('primary_contact_person') }}</span>
					@endif
				</div>
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('secondary_contact_person', __('Secondary Contact Person'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('secondary_contact_person', $secondary_contact_person ?? [], null, ['class' => 'form-control', 'id' => 'secondary_contact_person']) !!}
					@if ($errors->has('user_assigned_di'))  
						<span class="form-text text-danger">{{ $errors->first('secondary_contact_person') }}</span>
					@endif
				</div> 
			</div>
			<div class="row"> 
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('primary_contact_number', __('Primary Number'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('primary_contact_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Primary Number"]) 
					!!}
                    @if ($errors->has('primary_contact_number')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_contact_number') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('secondary_contact_number', __('Secondary Number'). ':', ['class' => '']) !!}
					{!! 
						Form::text('secondary_contact_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Secondary Number"]) 
					!!}
                    @if ($errors->has('secondary_contact_number')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_contact_number') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('primary_email', __('Primary Email'). ':', ['class' => '']) !!}
					{!! 
						Form::email('primary_email',  
						null, 
						['class' => 'form-control',
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
						'placeholder' => "Primary Email"]) 
					!!}
                    @if ($errors->has('Primary Email')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_email') }}</span>
                    @endif  
				</div>
			</div>
			<div class="row">  
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('secondary_email', __('Secondary Email'). ':', ['class' => '']) !!}
					{!! 
						Form::email('secondary_email',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Secondary Email",
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!'
						]) 
					!!}
                    @if ($errors->has('Secondary Email')) 
                        <span class="form-text text-danger">{{ $errors->first('secondary_email') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-4 form-group form-group form-group-error"> 
					{!! Form::label('country_id', __('Country'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('country_id',
						$countries,
						isset($branch->country_id) ? $branch->country_id : 101,
						['class' => 'form-control ui search selection top right pointing country_id country-val searchpicker',
						'id' => 'country_id','placeholder'=>'Please select country','data-statepicker'=>'state-drop-down-client','data-statetext'=>'state-textbox-client','data-postcode'=>'postcode-client','required'])
					!!} 
					@if ($errors->has('country_id'))  
						<span class="form-text text-danger">{{ $errors->first('country_id') }}</span>
					@endif
				</div>
				@php
					$checkStatePicker =  empty($branch) || (!empty($branch) && $branch->country_id == 101) ? '' : 'd-none';
					$checkStatePickerAttr =  empty($branch) || (!empty($branch) && $branch->country_id == 101) ? 'required' : '';
					$checkStateText = !empty($checkStatePicker) ? '' : 'd-none';
					$checkStateTextAttr =  empty($checkStatePicker) ? '' : 'required';
				@endphp
				<div class="{{'col-lg-4 state-drop-down-client '.$checkStatePicker}} form-group form-group-error"> 
					{!! Form::label('state_id', __('State'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('state_id',
						$states,
						isset($data['state_id']) ? $data['state_id'] : null,
						['class' => 'form-control ui search selection top right pointing state_id searchpicker state-drop-down-client-picker',
						'id' => 'state_id','placeholder'=>'Please select state',$checkStatePickerAttr, 'style' => 'width:100%'])
					!!}
					@if ($errors->has('state_id'))  
						<span class="form-text text-danger">{{ $errors->first('state_id') }}</span>
					@endif
				</div>
				<div class="{{'col-lg-4 state-textbox-client '.$checkStateText}} form-group form-group-error">
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
			</div>
			<div class="row"> 
			<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('city', __('City'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('city',  
						null, 
						['class' => 'form-control',
						'placeholder' => "City"]) 
					!!}
                    @if ($errors->has('city')) 
                        <span class="form-text text-danger">{{ $errors->first('city') }}</span>
                    @endif  
				</div>
				<div class="col-lg-4 form-group form-group-error">
					{!! Form::label('zipcode', __('Zipcode'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('zipcode',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Zipcode"]) 
					!!}
                    @if ($errors->has('zipcode')) 
                        <span class="form-text text-danger">{{ $errors->first('zipcode') }}</span>
                    @endif  
				</div>  
				<div class="col-lg-4 form-group form-group-error">
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
				<div class="col-lg-4 form-group col-form-labe form-group form-group-error">
					<div class="checkbox-inline mt-2">
						<label class="checkbox checkbox-primary"> 
							@if(isset($branch)) 
								@if($branch->is_primary == 1) 
									<input type="checkbox" name="is_primary" checked/>
								@else
									<input type="checkbox" name="is_primary" />
								@endif
							@else
								<input type="checkbox" name="is_primary" />
							@endif
							<span></span>
							Mark as Primary Branch
						</label>
					</div> 
				</div>    
			</div>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::hidden('back_url', $back_url) !!}
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

	$("#country_id").select2();
	$("#state_id").select2();
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
	<?php if(!isset($branch) && !isset($distributor_id)): ?>
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


	$('#primary_contact_person').select2({
		placeholder: "Select User",
		allowClear: true,
		ajax: {
			url: '{!! route('users.userbyname') !!}',
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
							text: item.first_name +" "+ item.last_name, 
							id: item.id
						}
					})
				};
			}
		}
	});

	$('#secondary_contact_person').select2({
		placeholder: "Select User",
		allowClear: true,
		ajax: {
			url: '{!! route('users.userbyname') !!}',
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
							text: item.first_name +" "+ item.last_name, 
							id: item.id
						}
					})
				};
			}
		}
	});
});
</script>