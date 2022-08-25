<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body"> 
			<div class="row">
				<div class="col-md-8 offset-md-2 row">
					@if($is_system_user == 0)
						@if(isset($sms))
							<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $sms->distributor_id }}">
						@else
							<div class="form-group col-md-6 form-group-error"> 
								{!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
								<select name="distributor_id" id="distributor_id" class="form-control" style="width:100%">
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
					<div class="form-group col-md-6 form-group-error">
						@if(isset($sms))
							@if($sms->name == "Appointment SMS Template")
								{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
								{!! 
									Form::text('name',  
									null, 
									['class' => 'form-control',
									'placeholder' => 'Name', 'readonly']) 
								!!}
							@else
								{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
								{!! 
									Form::text('name',  
									null, 
									['class' => 'form-control',
									'placeholder' => 'Name']) 
								!!}
							@endif
						@else 
							{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
							{!! 
								Form::text('name',  
								null, 
								['class' => 'form-control',
								'placeholder' => 'Name']) 
							!!}
						@endif
						
						@if ($errors->has('name'))  
							<span class="form-text text-danger">{{ $errors->first('name') }}</span>
						@endif
					</div>
				</div> 
			</div> 
			<div class="row">
				<div class="form-group col-md-8 offset-md-2 form-group-error">
					{!! Form::label('message', __('Message'). ': *', ['class' => '']) !!}
					{!! 
						Form::textarea('message',  
						null, 
						['class' => 'form-control',
						'placeholder' => 'Message...']) 
					!!}
					@if ($errors->has('message'))  
						<span class="form-text text-danger">{{ $errors->first('message') }}</span>
					@endif
					<span class="text-danger" id="sms-count-message"></span>
				</div> 
			</div>
			<div class="row">
				<div class="form-group col-md-4 offset-md-2 form-group-error"> 
					@if(isset($sms))
						@if($sms->name == "Appointment SMS Template")
							{!! Form::label('event_type', __('Event Type'). ': *', ['class' => '']) !!}
							{!! 
								Form::text('event_type',  
								"appointment", 
								['class' => 'form-control',
								'placeholder' => 'Event Type', 'readonly']) 
							!!}				
						@else
							{!! Form::label('event_type', __('Event Type'). ':', ['class' => '']) !!}
							{{Form::select('event_type',
								['date' => "Date",
								'birthday' => "Brithday",
								'anniversary' => "Anniversary",
								'appointment' => "Appointment Booking",
								],
							$sms->event_type ?? NULL,
							['class'=>'form-control','placeholder'=>'Select Event Type'])}}
						@endif
					@else 
						{!! Form::label('event_type', __('Event Type'). ':', ['class' => '']) !!}
						{{Form::select('event_type',
							['date' => "Date",
							'birthday' => "Brithday",
							'anniversary' => "Anniversary", 
							],
						$sms->event_type ?? NULL,
						['class'=>'form-control','placeholder'=>'Select Event Type'])}}
					@endif 
					
					@if ($errors->has('event_type'))  
						<span class="form-text text-danger">{{ $errors->first('event_type') }}</span>
					@endif
				</div>
				<div class="form-group col-md-4 form-group-error event-date-input d-none">
					{!! Form::label('event_date', __('Event Date'). ': *', ['class' => '']) !!}
					@if(isset($sms))
						{!! 
							Form::date('event_date',  
							$sms->event_type == "date" ? date('Y-m-d', strtotime($sms->event_date)) : "", 
							['class' => 'form-control',
							'placeholder' => 'Event Date',
							'min' => date('Y-m-d')]) 
						!!}
					@else 
						{!! 
							Form::date('event_date',  
							null, 
							['class' => 'form-control',
							'placeholder' => 'Event Date',
							'min' => date('Y-m-d')]) 
						!!}
					@endif
					
					@if ($errors->has('event_date'))  
						<span class="form-text text-danger">{{ $errors->first('event_date') }}</span>
					@endif
				</div>
				<div class="form-group col-md-4 form-group-error event-days-input d-none">
					{!! Form::label('before_days', __('Event before days'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('before_days',  
						$sms->before_days  ?? 0, 
						['class' => 'form-control',
						'placeholder' => 'Event before days',
						'min' => 0]) 
					!!}
					@if ($errors->has('before_days'))  
						<span class="form-text text-danger">{{ $errors->first('before_days') }}</span>
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

	$(document).on('keyup', '#message', function (){
		countSms();
	});

	function countSms()
	{
		let message_length = $("#message").val().length; 
		let messages_count = Math.ceil(message_length / 160); 
		let insertText = messages_count + " SMS credit will be deducted for every SMS"; 

		if(messages_count > 0) {
			$("#sms-count-message").html(insertText);
		} else {
			$("#sms-count-message").html("");
		}  
	}

	toggleEventType(false);

	$(document).on('change', '#event_type', function (e){
		toggleEventType();
	});

	function toggleEventType(clearVals = true)
	{
		let event_type = $("#event_type").val();  
 
		if(event_type == "date") { 
			$(".event-days-input").addClass('d-none');
			$(".event-date-input").removeClass('d-none'); 
			if(clearVals == true) {
				$("#event_days").val(""); 
			} 
		} else if (event_type == "birthday" || event_type == "anniversary") {
			$(".event-date-input").addClass('d-none');
			$(".event-days-input").removeClass('d-none');
			if(clearVals == true) {
				$("#event_date").val("");
			}  
		} else {
			$(".event-date-input").addClass('d-none');
			$(".event-days-input").addClass('d-none');
		}
	}
  
	<?php if(!isset($sms)): ?>
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