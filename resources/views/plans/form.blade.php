<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form"> 
		<div class="card-body"> 
			<div class="row"> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						$data['name'] ?? old('name'), 
						['class' => 'form-control',
						'placeholder' => "Name"]) 
					!!}
					@if ($errors->has('name'))  
                        <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                    @endif  
				</div>    
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('price', __('Price'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('price',  
						$data['price'] ?? old('price'), 
						['class' => 'form-control',
						'placeholder' => "Price",
						'min' => 0 ]) 
					!!}
					@if ($errors->has('price'))  
                        <span class="form-text text-danger">{{ $errors->first('price') }}</span>
                    @endif  
					<span class="text-muted">Inclusive GST</span>
				</div>  
			</div> 
			<div class="row">  
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('sgst', __('SGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('sgst',  
						$data['sgst'] ?? old('sgst'), 
						['class' => 'form-control',
						'placeholder' => "SGST",
						'min' => 0,
						'step' => 0.1 ]) 
					!!}
					@if ($errors->has('sgst'))  
                        <span class="form-text text-danger">{{ $errors->first('sgst') }}</span>
                    @endif  
					<span class="text-muted">Please enter SGST in %</span>
				</div>  
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('cgst', __('CGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('cgst',  
						$data['cgst'] ?? old('cgst'), 
						['class' => 'form-control',
						'placeholder' => "CGST",
						'min' => 0,
						'step' => 0.1 ]) 
					!!}
					@if ($errors->has('cgst'))  
                        <span class="form-text text-danger">{{ $errors->first('cgst') }}</span>
                    @endif  
					<span class="text-muted">Please enter CGST in %</span>
				</div>  
			</div> 
			<div class="row">  
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('igst', __('IGST'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('igst',  
						$data['igst'] ?? old('igst'), 
						['class' => 'form-control',
						'placeholder' => "IGST",
						'min' => 0,
						'step' => 0.1 ]) 
					!!}
					@if ($errors->has('igst'))  
                        <span class="form-text text-danger">{{ $errors->first('igst') }}</span>
                    @endif  
					<span class="text-muted">Please enter IGST in %</span>
				</div>   
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('duration_months', __('Duration In Months'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('duration_months',  
						$data['duration_months'] ?? old('duration_months'), 
						['class' => 'form-control',
						'placeholder' => "Duration in Months",
						'min' => 0,
						'step' => 1]) 
					!!}
					@if ($errors->has('duration_months'))  
                        <span class="form-text text-danger">{{ $errors->first('duration_months') }}</span>
                    @endif  
				</div>   
			</div> 
			<div class="row"> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('no_of_users', __('Number of Employees'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('no_of_users',  
						$data['no_of_users'] ?? old('no_of_users'), 
						['class' => 'form-control',
						'placeholder' => "Number of Employees",
						'min' => 0,
						'step' => 1]) 
					!!}
					@if ($errors->has('no_of_users'))  
                        <span class="form-text text-danger">{{ $errors->first('no_of_users') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('no_of_branches', __('Number of Branches'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('no_of_branches',  
						$data['no_of_branches'] ?? old('no_of_branches'), 
						['class' => 'form-control',
						'placeholder' => "Number of Branches",
						'min' => 0,
						'step' => 1]) 
					!!}
					@if ($errors->has('no_of_branches'))  
                        <span class="form-text text-danger">{{ $errors->first('no_of_branches') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('no_of_email', __('Number of Email'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('no_of_email',  
						$data['no_of_email'] ?? old('no_of_email'), 
						['class' => 'form-control',
						'placeholder' => "Number of Email",
						'min' => 0,
						'step' => 1]) 
					!!}
					@if ($errors->has('no_of_email'))  
                        <span class="form-text text-danger">{{ $errors->first('no_of_email') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('no_of_sms', __('Number of SMS'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('no_of_sms',  
						$data['no_of_sms'] ?? old('no_of_sms'), 
						['class' => 'form-control',
						'placeholder' => "Number of SMS",
						'min' => 0,
						'step' => 1]) 
					!!}
					@if ($errors->has('no_of_sms'))  
                        <span class="form-text text-danger">{{ $errors->first('no_of_sms') }}</span>
                    @endif  
				</div> 
			</div> 
			<div class="row">  
				<div class="col-lg-6 form-group form-group-error">
					{!! Form::label('description', __('Description'). ': ', ['class' => '']) !!}
					{!! 
						Form::textarea('description',  
						$data['description'] ?? old('description'), 
						['class' => 'form-control',
						'placeholder' => "Description",
						'rows' => 3]) 
					!!}
					@if ($errors->has('description'))  
                        <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                    @endif  
				</div>    
			</div> 
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitClient']) !!}
				</div> 
			</div>
		</div>
	</duv>
<!--end::Form-->
</div>
<!--end::Card--> 