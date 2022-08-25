<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row"> 
				<div class="col-lg-4 form-group-error">
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
				<div class="col-lg-4 form-group-error">
					{!! Form::label('primary_contact_person', __('Primary Contact Person'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('primary_contact_person', $primary_contact_person ?? [], null, ['class' => 'form-control', 'id' => 'primary_contact_person']) !!}
					@if ($errors->has('primary_contact_person'))  
						<span class="form-text text-danger">{{ $errors->first('primary_contact_person') }}</span>
					@endif
				</div>
				<div class="col-lg-4 form-group-error">
					{!! Form::label('secondary_contact_person', __('Secondary Contact Person'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('secondary_contact_person', $secondary_contact_person ?? [], null, ['class' => 'form-control', 'id' => 'secondary_contact_person']) !!}
					@if ($errors->has('user_assigned_di'))  
						<span class="form-text text-danger">{{ $errors->first('secondary_contact_person') }}</span>
					@endif
				</div> 
			</div>
			<div class="form-group row"> 
				<div class="col-lg-4 form-group-error">
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
				<div class="col-lg-4 form-group-error">
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
				<div class="col-lg-4 form-group-error">
					{!! Form::label('city', __('City'). ':', ['class' => '']) !!}
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
			</div>
			<div class="form-group row"> 
				<div class="col-lg-4 form-group-error">
					{!! Form::label('primary_email', __('Primary Email'). ':', ['class' => '']) !!}
					{!! 
						Form::email('primary_email',  
						null, 
						['class' => 'form-control',
						'placeholder' => "Primary Email",
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!'
						]) 
					!!}
                    @if ($errors->has('Primary Email')) 
                        <span class="form-text text-danger">{{ $errors->first('primary_email') }}</span>
                    @endif  
				</div>   
				<div class="col-lg-4 form-group-error">
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
				<div class="col-lg-4 form-group-error">
					{!! Form::label('zipcode', __('Zipcode'). ':', ['class' => '']) !!}
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
			</div>
			<div class="form-group row"> 
				<div class="col-lg-6 form-group-error">
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
	$('#primary_contact_person').select2({
		placeholder: "Select User",
		allowClear: true,
		ajax: {
			url: '{!! route('users.userbyname') !!}',
			dataType: 'json', 
			processResults: function (data, param) {  
				return {
					results: $.map(data, function (item) {  
						return {
							text: item.fist_name +" "+ item.last_name, 
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
			processResults: function (data, param) {  
				return {
					results: $.map(data, function (item) { 
						return {
							text: item.fist_name +" "+ item.last_name, 
							id: item.id
						}
					})
				};
			}
		}
	});
});
</script>