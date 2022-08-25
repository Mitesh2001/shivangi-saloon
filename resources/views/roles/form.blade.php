<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body remove-paddin-mobile">
			<div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-error">
                        {!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
                        {!! 
                            Form::text('name',  
                            $data['name'] ?? old('name'), 
                            ['class' => 'form-control',
                            'placeholder' => "Role Name"]) 
                        !!}
                        @if ($errors->has('name'))  
                            <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                        @endif  
                    </div> 
                    <div class="form-group form-group-error">
                        {!! Form::label('description', __('Description'). ':', ['class' => '']) !!}
                        {!! 
                            Form::textarea('description',  
                            $data['description'] ?? old('description'), 
                            ['class' => 'form-control',
                            'rows' => 2,
                            'placeholder' => "Description"]) 
                        !!}
                        @if ($errors->has('description'))  
                            <span class="form-text text-danger">{{ $errors->first('description') }}</span>
                        @endif  
                    </div> 
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