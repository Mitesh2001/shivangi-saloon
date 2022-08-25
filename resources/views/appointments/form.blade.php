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
			<div class="row">
				
					<div class="col-lg-3 form-group form-group-error"> 
						{!! Form::label('client_external_id', __('Client Name'). ': *', ['class' => 'control-label thin-weight']) !!}
						@if(!isset($client))
							<div class="input-group">
								{!!
									Form::select('client_external_id',
									[],
									old('client_external_id'),
									['class' => 'form-control',
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
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('contact_number', __('Contact Number'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('contact_number',  
						$contact_number ?? "", 
						['class' => 'form-control',
						'placeholder' => "Contact number"]) 
					!!}
					@if ($errors->has('contact_number'))  
						<span class="form-text text-danger">{{ $errors->first('contact_number') }}</span>
					@endif	
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('email', __('Email'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::email('email',
						$email ?? "",  
						['class' => 'form-control',
						'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!',
						'placeholder' => "Email"]) 
					!!}
					@if ($errors->has('email'))  
						<span class="form-text text-danger">{{ $errors->first('email') }}</span>
					@endif
				</div> 
			</div>
			<div class="row">  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('address', __('Address'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::text('address',
							$address ?? "", 
							['class' => 'form-control',
							'placeholder' => "Address"])
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					@if ($errors->has('address'))  
						<span class="form-text text-danger">{{ $errors->first('address') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{{Form::label('Branch')}} *
					@if(!isset($selected_branch)) 
						{{Form::select('branch_id', [], null,['class'=>'form-control', 'id' => 'branch_id'])}}
						@if ($errors->has('branch_id')) 
							<span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
						@endif
					@else
						<select name="branch_id" class="form-control" id="branch_id">
							<option value="{{ $selected_branch->id }}" seleted>{{ $selected_branch->name }}</option>
						</select>
					@endif
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('user_assigned_id', __('Representative'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('user_assigned_id', [], null, ['class' => 'form-control ui search selection top right pointing searchable-select', 'id' => 'user_assigned_id']) !!}
					@if ($errors->has('user_assigned_id'))  
						<span class="form-text text-danger">{{ $errors->first('user_assigned_id') }}</span>
					@endif
				</div> 
				<div class="col-lg-3 form-group form-group-error"> 
					{!! Form::label('appointment_for', __('Appointment For'). ':*', ['class' => '']) !!}
					{!!
						Form::select('appointment_for[]',
						[],
						null, 
						['class' => 'form-control',
						'id' => 'appointment_for', 
						'multiple' => 'true', 
						'style' => 'width:100%'])
					!!} 
					@if ($errors->has('appointment_for'))  
						<span class="form-text text-danger">{{ $errors->first('appointment_for') }}</span>
					@endif
				</div>  
			</div>
			<div class="row">  
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('status_id', __('Appointment Status'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! Form::select('status_id', $statuses, $default_status ?? null, ['class' => 'form-control ui search selection top right pointing search-select', 'id' => 'search-select']) !!}
					@if ($errors->has('status_id'))  
						<span class="form-text text-danger">{{ $errors->first('status_id') }}</span>
					@endif
				</div> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('source_type_string', __('Source of Appointment'). ':', ['class' => 'control-label thin-weight']) !!} 
					{!! 
						Form::text('source_type_string',
							$source ?? "", 
							['class' => 'form-control',
							'placeholder' => "Source of appointment",
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
					@if ($errors->has('source_type_string'))  
						<span class="form-text text-danger">{{ $errors->first('source_type_string') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('date', __('Date of appointment'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group date">
						{!! 
							Form::date('date',
							isset($data['date']) ? $data['date'] : null, 
							['class' => 'form-control',
							'min' => date('Y-m-d')]) 
						!!}  
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-calendar-check-o"></i>
							</span>
						</div> 
					</div> 
					@if ($errors->has('date'))  
						<span class="form-text text-danger">{{ $errors->first('date') }}</span>
					@endif
				</div>
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('start_at', __('Start at'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::text('start_at',
							isset($data['start_at']) ? $data['start_at'] : null, 
							['class' => 'form-control',
							'id' => 'start_at']) 
						!!}
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-clock-o"></i>
							</span>
						</div>
					</div>
					
					@if ($errors->has('start_at'))  
						<span class="form-text text-danger">{{ $errors->first('start_at') }}</span>
					@endif
				</div> 
				<div class="col-lg-3 form-group form-group-error">
					{!! Form::label('end_at', __('End at'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::text('end_at',
							isset($data['end_at']) ? $data['end_at'] : null, 
							['class' => 'form-control',
							'id' => 'end_at']) 
						!!}
						<div class="input-group-append">
							<span class="input-group-text">
								<i class="la la-clock-o"></i>
							</span>
						</div>
					</div> 
					@if ($errors->has('end_at'))  
						<span class="form-text text-danger">{{ $errors->first('end_at') }}</span>
					@endif
				</div>   
			</div> 
			<div class="row">
				<div class="col-lg-12">
					{!! Form::label('description', __('Description'). ':', ['class' => 'control-label thin-weight']) !!} 
					{!! 
						Form::textarea('description',
							$description ?? "", 
							['class' => 'form-control',
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
					<input type="hidden" name="inquiry_id" id="inquiry_id" value="{{ $inquiry_id ?? 0 }}">
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'createTask']) !!}
					<button type="button" class="btn btn-light-primary font-weight-bold" onClick="window.location.reload(true);">Cancle</button>
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->
 
<script>
$(document).ready(function () {

	var CKEditor = ClassicEditor.create(document.querySelector('#description'));
  
	CKEditor.replace = function( config ) { 
		config.removePlugins = 'blockquote,save,flash,iframe,tabletools,pagebreak,templates,about,showblocks,newpage,language,print,div';
		config.removeButtons = 'Print,Form,TextField,Textarea,Button,CreateDiv,PasteText,PasteFromWord,Select,HiddenField,Radio,Checkbox,ImageButton,Anchor,BidiLtr,BidiRtl,Font,Format,Styles,Preview,Indent,Outdent';
	};

	$(document).on('change', '#branch_id', function (){
		$("#user_assigned_id").html("");
		$("#appointment_for").html("");
	});

	$(document).on('change', '#user_assigned_id', function (){ 
		$("#appointment_for").html("");
	});
	

	$('#appointment_for').select2({
		placeholder: "Select Services",
		allowClear: true,
		ajax: {
			url: '{!! route('users.getServices') !!}',
			dataType: 'json', 
			data: function (params) { 
				ultimaConsulta = params.term;
				var user_assigned_id = $("#user_assigned_id").val();
				return {
					name: params.term, // search term
					user_id: user_assigned_id,
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
 
	$('#start_at').timepicker({
		showMeridian: false,
	});
	$('#end_at').timepicker({
		showMeridian: false,
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
				$("#email").val(data.email) ;
				$("#address").val(data.address);
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
				var branch_id = $("#branch_id").val();
				var distributor_id = $("#distributor_id").val();
				return {
					name: params.term, // search term
					branch_id : branch_id,
					distributor_id: distributor_id,
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