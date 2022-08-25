<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
	
		<div class="card-body"> 
			<div class="row"> 
				@if($is_system_user == 0)  
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
				@else 
					<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $is_system_user }}"> 
				@endif 
			</div>
			<div class="form-group row">
				<div class="col-lg-3 form-group"> 
					{!! Form::label('client_external_id', __('Client Name'). ': *', ['class' => 'control-label thin-weight']) !!}

					@if(!isset($client))
					<div class="input-group">
							{!!
								Form::select('client_external_id',
								[],
								old('client_external_id'),
								['class' => 'form-control ui search selection top right pointing company_type-select',
								'id' => 'client_external_id'])
							!!} 
							<div class="input-group-append">
								<span class="input-group-text bg-primary" id="add-new-client-btn" type="button" data-toggle="tooltip" title="New Client">
									<i class="fas fa-plus text-white"></i>
								</span>
							</div>
						</div>
					@else 
						<select name="client_external_id" class="form-control" id="id">
							<option value="{{ $client->id }}" seleted>{{ $client->name }}</option>
						</select>
					@endif 
					@if ($errors->has('client_external_id'))  
						<span class="form-text text-danger">{{ $errors->first('client_external_id') }}</span>
					@endif	
				</div> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('gender', __('Gender'). ':', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('gender',
						[
							'Male' => 'Male',
							'Female' => 'Female', 
						],
						isset($client->gender) ? $client->gender : null,
						['class' => 'form-control',
						'id' => 'client_gender',
						'placeholder'=>'Please select gender'])
					!!}
					@if ($errors->has('gender'))  
						<span class="form-text text-danger">{{ $errors->first('gender') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('contact_number', __('Contact Number'). ': *', ['class' => 'control-label thin-weight']) !!} 
						{!! 
							Form::text('contact_number',  
							$client->primaryContact->primary_number ?? "", 
							['class' => 'form-control',
							'placeholder' => "Contact number"]) 
						!!} 
					@if ($errors->has('contact_number'))  
						<span class="form-text text-danger">{{ $errors->first('contact_number') }}</span>
					@endif	
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('email', __('Email'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::email('email',
						$client->primaryContact->email ?? "", 
						['class' => 'form-control',
						'placeholder' => "Email",
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!']) 
					!!}
					@if ($errors->has('email'))  
						<span class="form-text text-danger">{{ $errors->first('email') }}</span>
					@endif
				</div> 
			</div>
			<div class="form-group row">
				<div class="col-lg-3 form-group">
					{!! Form::label('address', __('Address'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::text('address',
							$client->address ?? "", 
							['class' => 'form-control',
							'placeholder' => "Address"])
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					@if ($errors->has('address'))  
						<span class="form-text text-danger">{{ $errors->first('address') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('enquiry_for', __('Inquiry For'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('enquiry_for',
						isset($data['enquiry_for']) ? $data['enquiry_for'] : null, 
						['class' => 'form-control',
						'placeholder' => "Inquiry For"]) 
					!!}
					@if ($errors->has('enquiry_for'))  
						<span class="form-text text-danger">{{ $errors->first('enquiry_for') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
				<?php $types = array();?>
					{!! Form::label('enquiry_type', __('Inquiry Type'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!!
						Form::select('enquiry_type',
						$enquiry_types ?? [],
						null,
						['class' => 'form-control'])
					!!}		
					@if ($errors->has('enquiry_type'))  
						<span class="form-text text-danger">{{ $errors->first('enquiry_type') }}</span>
					@endif			
				</div>
			
				<div class="col-lg-3 form-group">
					{!! Form::label('enquiry_response', __('Inquiry Response'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('enquiry_response',
						isset($data['enquiry_response']) ? $data['enquiry_response'] : null, 
						['class' => 'form-control',
						'placeholder' => "Inquiry Response"]) 
					!!}
					@if ($errors->has('enquiry_response'))  
						<span class="form-text text-danger">{{ $errors->first('enquiry_response') }}</span>
					@endif
				</div>
			</div>
			<div class="form-group row">
				<div class="col-lg-3 form-group">
					{!! Form::label('date_to_follow', __('Date To Follow'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::date('date_to_follow',
						isset($data['date_to_follow']) ? $data['date_to_follow'] : null, 
						['class' => 'form-control', 'min' => date('Y-m-d')]) 
					!!}
					@if ($errors->has('date_to_follow'))  
						<span class="form-text text-danger">{{ $errors->first('date_to_follow') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
					{{Form::label('Branch')}} *
                    {{Form::select('branch_id', [], null,['class'=>'form-control', 'id' => 'branch_id'])}}
                    @if ($errors->has('branch_id')) 
                        <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                    @endif
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('enquiry_source', __('Source of Inquiry'). ': *', ['class' => 'control-label thin-weight']) !!} 
					{!! 
						Form::text('enquiry_source',
							isset($data['enquiry_source']) ? $data['enquiry_source'] : null, 
							['class' => 'form-control',
							'placeholder' => "Source of Inquiry",
							'list' => 'source_type_list']) 
					!!}
					<datalist id="source_type_list">
						<option>Walk-in Client</option>
						<option>Call</option>
						<option>Tele Caller</option>
						<option>Facebook Ad</option> 
						<option>Instagram Ad</option> 
						<option>Client Reference</option> 
					</datalist>
					@if ($errors->has('enquiry_source'))  
						<span class="form-text text-danger">{{ $errors->first('enquiry_source') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('user_assigned_id', __('Lead Representative'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('user_assigned_id', [], null, ['class' => 'form-control ui search selection top right pointing searchable-select', 'id' => 'user_assigned_id']) !!}
					@if ($errors->has('user_assigned_di'))  
						<span class="form-text text-danger">{{ $errors->first('user_assigned_id') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group">
					{!! Form::label('status_id', __('Inquiry Status'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('status_id', $statuses, null, ['class' => 'form-control ui search selection top right pointing search-select', 'id' => 'search-select']) !!}
					@if ($errors->has('status_id'))  
						<span class="form-text text-danger">{{ $errors->first('status_id') }}</span>
					@endif
				</div>
			</div> 
			<div class="form-group row">
				<div class="col-lg-12 form-group">
					{!! Form::label('description', __('Description'). ':', ['class' => 'control-label thin-weight']) !!} 
					{!! 
						Form::textarea('description',
							isset($data['description']) ? $data['description'] : null, 
							['class' => 'form-control',
							'placeholder' => "Description"]) 
					!!}
					@if ($errors->has('description'))  
						<span class="form-text text-danger">{{ $errors->first('enquiry_source') }}</span>
					@endif
				</div> 
			</div> 
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'createTask']) !!}
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'cancelInquiry']) !!}
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->
 
<script>
$(document).ready(function () {

	$(document).on('click', '#cancelInquiry', function () {
		location.reload();
	});

	ClassicEditor.create(document.querySelector('#description'));

	$(document).on('change', '#branch_id', function (){
		$("#user_assigned_id").val("").trigger('change');
	});

	<?php if($is_system_user == 0 && !isset($selected_distributor)): ?>
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

		$(document).on('change', '#distributor_id', function (e) {
			$("#client_external_id").val("").trigger('change');
			$("#branch_id").val("").trigger('change');
			$("#user_assigned_id").val("").trigger('change');
		});

	<?php endif; ?>

	$('#branch_id').select2({
		placeholder: "Select Branch",
		allowClear: true,
		ajax: {
			url: '{!! route('branch.getBranchByName') !!}',
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
							text: item.name, 
							id: item.id
						}
					})
				};
			}
		}
	});


	$(document).on('change', '#client_external_id', function (e){

		let client_id = $("#client_external_id").val(); 

		$.ajax({
			url: '{!! route('clients.findbyid') !!}',
			type: "POST",
			cache: false,
			data: {
				_token: "{{ csrf_token() }}",
				id: client_id
			},
			success: function (data) {
				$("#contact_number").val(data.contact_number) ;
				$("#gender").val(data.gender) ;
				$("#email").val(data.email) ;
				$("#address").val(data.address) ;
			}
		})
	});
 
	$('#client_external_id').select2({
		placeholder: "Select Client",
		allowClear: true,
		ajax: {
			url: '{!! route('leads.clientsbyname') !!}',
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
							text: item.name, 
							id: item.id
						}
					})
				};
			}
		}
	});

	$(document).on('click', '#add-new-client-btn', function (){
		let distributor_id = $("#distributor_id").val();

		if(distributor_id != null) {
			$("#client_external_id").select2("close");
			$("#add-new-client").modal('show');
			$("#distributor_id_model").val(distributor_id).trigger('change');
		} else {
			alert("please select salon");
			return false;
		} 
	});	

  
	$('#user_assigned_id').select2({
		placeholder: "Select Lead Representative",
		allowClear: true,
		ajax: {
			url: '{!! route('leads.usersbyname') !!}',
			dataType: 'json', 
			data: function (params) { 
				ultimaConsulta = params.term;
				var distributor_id = $("#distributor_id").val();
				var branch_id = $("#branch_id").val();
				return {
					name: params.term, // search term
					distributor_id: distributor_id,
					branch_id: branch_id,
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