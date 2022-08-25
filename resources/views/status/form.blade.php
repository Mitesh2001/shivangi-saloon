<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row"> 
				<div class="col-lg-6">
					{!! Form::label('title', __('Title'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('title',  
						null, 
						['class' => 'form-control',
						'placeholder' => "title"]) 
					!!}
                    @if ($errors->has('title')) 
                        <span class="form-text text-danger">{{ $errors->first('title') }}</span>
                    @endif  
				</div> 
				<div class="col-lg-6">
					{!! Form::label('color', __('Color'). ': *', ['class' => '']) !!}
					{!! 
						Form::color('color',  
						null, 
						['class' => 'form-control',
						'placeholder' => "color"]) 
					!!}
                    @if ($errors->has('color')) 
                        <span class="form-text text-danger">{{ $errors->first('color') }}</span>
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