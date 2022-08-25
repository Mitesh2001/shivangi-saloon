<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<form class="form">
		<div class="card-body">
			<div class="row">
				<div class="col-lg-6 form-group">
					{!! Form::label('name', __('Product Name'). ': *', ['class' => '']) !!}
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
				<div class="col-lg-6 form-group">
					{!! Form::label('price', __('Price'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('price',  
						$data['price'] ?? old('price'), 
						['class' => 'form-control',
						'placeholder' => "Price"]) 
					!!}
					@if ($errors->has('price'))  
                        <span class="form-text text-danger">{{ $errors->first('price') }}</span>
                    @endif
				</div> 
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						{!! Form::label('category_id', __('Category'). ': *', ['class' => '']) !!} 
						{!! Form::select('category_id',
							$selected_category ?? [],
							old('category_id'),	
							['class' => 'form-control searchable-select'])
						!!}
						@if ($errors->has('category_id'))  
							<span class="form-text text-danger">{{ $errors->first('category_id') }}</span>
						@endif 
					</div>
					<div class="form-group">
						<div class="col-lg-6">
							@if(isset($product->thumbnail))
								<img src="{{ asset($product->thumbnail) }}" alt="" height="100px">
							@endif 
						</div>
						<div class="col-lg-6">
							{!! Form::label('thumbnail', __('Image'). ': *', ['class' => '']) !!}
							{!! 
								Form::file('thumbnail',  
								null, 	
								['class' => 'form-control']) 
							!!}
							@if ($errors->has('thumbnail'))  
								<span class="form-text text-danger">{{ $errors->first('thumbnail') }}</span>
							@endif
						</div> 
						{!! Form::hidden('old_thumbnail', $product->thumbnail ?? NULL) !!}
					</div>  
				</div>

				<div class="col-lg-6 form-group">
					{!! Form::label('description', __('Description'). ': *', ['class' => '']) !!}
					{!! 
						Form::textarea('description',  
						old('description'), 
						['rows' => 5,'class' => 'form-control',
						'placeholder' => "Description"]) 
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
					{!! Form::reset("Cancel", ['class' => 'btn btn-md btn-secondary', 'id' => 'submitClient']) !!}
				</div> 
			</div>
		</div>
	</form>
<!--end::Form-->
</div>
<!--end::Card-->

<script>
$(document).ready(function () {
	$('#category_id').select2({
		placeholder: "Select Category",
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