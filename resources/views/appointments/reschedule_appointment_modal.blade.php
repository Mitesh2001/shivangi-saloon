<!-- Modal-->
<div class="modal fade" id="edit-appointment-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">
                    Edit Appointment
                    @if($is_system_user)
                    <span class="text-muted"> (Salon : <span id="distributor-edit-modal"></span> )</span>
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body" >
            {!! Form::open([
                'route' => 'appointments.reschedule',
                'class' => 'ui-form',
                'id' => 'editAppointmentForm'
            ]) !!}
                 
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('date', __('Date of Appointment'). ': *', ['class' => '']) !!}
                        {!! 
                            Form::date('date',  
                            null, 
                            ['class' => 'form-control edit-input date-picker',
                            'placeholder' => 'Date of Appointment',
                            'id' => 'date-edit', 
                            'min' => date('Y-m-d')]) 
                        !!}
                        @if ($errors->has('date'))  
                            <span class="form-text text-danger">{{ $errors->first('date') }}</span>
                        @endif
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('user_assigned_id', __('Representative'). ': *', ['class' => 'control-label thin-weight']) !!}
                        {!! Form::select('user_assigned_id', [], null, ['class' => 'form-control edit-input', 'id' => 'representative-edit', 'style' => 'width:100%']) !!}
                        @if ($errors->has('user_assigned_id'))  
                            <span class="form-text text-danger">{{ $errors->first('user_assigned_id') }}</span>
                        @endif 
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        {!! Form::label('start_at', __('Start at'). ': *', ['class' => 'control-label thin-weight']) !!}
                        <div class="input-group">
                            {!! 
                                Form::text('start_at',
                                isset($data['start_at']) ? $data['start_at'] : null, 
                                ['class' => 'form-control edit-input',
                                'id' => 'start-at-edit']) 
                            !!}
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        {!! Form::label('end_at', __('End at'). ': *', ['class' => 'control-label thin-weight']) !!}
                        <div class="input-group">
                            {!! 
                                Form::text('end_at',
                                isset($data['end_at']) ? $data['end_at'] : null, 
                                ['class' => 'form-control edit-input',
                                'id' => 'end-at-edit']) 
                            !!}
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div> 
                    </div> 
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="distributor_id" id="distributor-edit-id" value="">
                <input type="hidden" name="branch_id" id="branch-edit-id" value="">
                <input type="hidden" name="appointment_id" id="appointment_external_id" value="">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancle</button>
                <button type="submit" class="btn btn-primary font-weight-bold submit-appointment">Update</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    $('#start_at').timepicker();
	$('#end_at').timepicker();
    
    $("#editAppointmentForm").validate({
        rules: {  
            date: {
                required: true,
            }, 
            user_assigned_id: {
                required: true,
            }, 
            start_at: {
                required: true,
            }, 
            end_at: {
                required: true,
            }, 
        },
        messages: {  
            date: {
                required: "Please select date of appointment!",
            },   
            user_assigned_id: {
                required: "Please select representative!",
            },   
            start_at: {
                required: "Please select appointment start time!",
            }, 
            end_at: {
                required: "Please select appointment end time!",
            }, 
        },
        normalizer: function( value ) { 
            return $.trim( value );
        },
        errorElement: "span",
        errorClass: "form-text text-danger",
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        errorPlacement: function(error, element) {
            $(element).closest('.col-lg-6').append(error);
        }
    });


	$('#representative-edit').select2({
		placeholder: "Select Lead Representative",
		allowClear: true,
		ajax: {
			url: '{!! route('leads.usersbyname') !!}',
			dataType: 'json', 
            data: function (params) { 
				ultimaConsulta = params.term;
				var branch_id = $("#branch-edit-id").val();
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
     
})
</script>