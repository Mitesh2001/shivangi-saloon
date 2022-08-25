<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="row"> 
				@if($is_system_user == 0)
					@if(isset($vendor))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $vendor->distributor_id }}">
					@else
						<div class="col-lg-3 form-group form-group-error"> 
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
						'pattern' => '^([0][1-9]|[1-2][0-9]|[3][0-7])([a-zA-Z]{5}[0-9]{4}[a-zA-Z]{1}[1-9a-zA-Z]{1}[zZ]{1}[0-9a-zA-Z]{1})+$',
						'title' => "Please enter valid GST number!",
						'placeholder' => "GST Number"]) 
					!!}
                    @if ($errors->has('gst_number')) 
                        <span class="form-text text-danger">{{ $errors->first('gst_number') }}</span>
                    @endif  
				</div>    
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
			</div>  
			<div class="row">  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('primary_email', __('Primary Email'). ': ', ['class' => '']) !!}
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
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('contact_person', __('Contact Person Name'). ':', ['class' => '']) !!}
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
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('contact_person_number', __('Contact Person Number'). ':', ['class' => '']) !!}
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
			<div class="row">  
				<div class="col-lg-6">
					<div class="form-group form-group form-group-error">
						{!! Form::label('contact_person_email', __('Contact Person Email'). ':', ['class' => '']) !!}
						{!! 
							Form::email('contact_person_email',  
							null, 
							['class' => 'form-control',
							'placeholder' => "Contact Person Email",
							'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        	'title' => 'Please eter valid email address!']) 
						!!}
						@if ($errors->has('contact_person_email')) 
							<span class="form-text text-danger">{{ $errors->first('contact_person_email') }}</span>
						@endif  
					</div>    
					<div class="form-group form-group form-group-error">
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
					<div class="form-group form-group-error">
						{!! Form::label('zipcode', __('Zip Code'). ':', ['class' => '']) !!}
						{!! 
							Form::text('zipcode',  
							null, 
							['class' => 'form-control',
							'placeholder' => "Zip Code"]) 
						!!}
						@if ($errors->has('zipcode')) 
							<span class="form-text text-danger">{{ $errors->first('zipcode') }}</span>
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
						'rows' => 8]) 
					!!}
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
$(document).ready(function () {

	<?php if(!isset($vendor)): ?>
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

});
</script>