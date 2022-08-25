<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row">
				<div class="col-lg-6"> 
					<div class="form-group">
						{!! Form::label('parent_id', __('Parent Category'). ':', ['class' => '']) !!} 
						{!! Form::select('parent_id',
							$selected_category ?? [],
							old('parent_id'),	
							['class' => 'form-control'])
						!!}
						@if ($errors->has('parent_id'))  
							<span class="form-text text-danger">{{ $errors->first('parent_id') }}</span>
						@endif 
					</div> 
				</div> 
				<div class="col-lg-6">
					{!! Form::label('name', __('Category Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						isset($data['owners']) ? $data['owners'][0]['name'] : null, 
						['class' => 'form-control',
						'placeholder' => "Category Name"]) 
					!!}
                    @if ($errors->has('name')) 
                        <!-- <span class="form-text text-muted">Please enter category name</span> -->
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
$(document).ready(function (){

	$(document).on('change', '#parent_id', function (e) {
		let id = $("#id").val();
		let selected_id = $(e.target).val();
		if(selected_id == id) {
			alert("Can not select same category as parent!");
			$(e.target).val("").trigger('change');
			return false;
		}
	});

	$('#parent_id').select2({
		placeholder: "Select Parent Category",
		allowClear: true,
		ajax: {
			url: '{!! route('category.categorybyname') !!}',
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
});
</script>