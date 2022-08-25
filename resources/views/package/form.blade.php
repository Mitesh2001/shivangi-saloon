<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row"> 
				@if($is_system_user == 0)
					@if(isset($package))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $package->distributor_id }}">
					@else
						<div class="col-lg-3 form-group-error"> 
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
				<div class="col-lg-3 form-group-error">
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
$(document).ready(function () {

	<?php if(!isset($package)): ?>
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