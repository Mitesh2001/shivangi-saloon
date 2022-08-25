<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="row form-row"> 
				<div class="offset-lg-3 col-lg-6 form-group form-group-error">
					{!! Form::label('name', __('Campaign Name'). ': *', ['class' => '']) !!}  
					{!! 
						Form::text('name',  
						$data['name'] ?? old('name'), 
						['class' => 'form-control',
						'placeholder' => "Product Name"]) 
					!!}
					@if ($errors->has('name'))  
						<span class="form-text text-danger">{{ $errors->first('name') }}</span>
					@endif 
				</div> 
			</div> 
			<div class="row form-row"> 
				<div class="offset-lg-3 col-lg-6 form-group form-group-error message-row">
					{!! Form::label('message', __('Message'). ': *', ['class' => '']) !!}  
					{!! 
						Form::textarea('message',  
						$data['message'] ?? old('message'), 
						['class' => 'form-control dynamic-input',
						'rows' => 5,
						'placeholder' => "message"]) 
					!!}
					@if ($errors->has('message'))  
						<span class="form-text text-danger">{{ $errors->first('message') }}</span>
					@endif 
				</div>  
			</div>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::hidden('type', 0, ['id' => 'type']) !!}
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
$(document).ready(function () { 
	
})
</script>