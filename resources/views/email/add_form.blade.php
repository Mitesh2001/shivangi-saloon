<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body"> 
			<div class="row form-group"> 
                @if($is_system_user == 0)
                    @if(isset($email))
                        <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $email->distributor_id }}">
                    @else 
                    <div class="col-md-6 form-group-error"> 
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
			</div>  
            <div class="row">
                <div class="col-lg-6 form-group form-group-error">
                    {!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
                    @if(isset($email))
                        @if($email->default_template == 1 || in_array($email->name, array('Invoice email send'))) 
                            {!! 
                                Form::text('name',
                                isset($emailTemplate['name']) ? $emailTemplate['name'] : null, 
                                ['class' => 'form-control','required', 'disabled', 'placeholder' => 'Template Name']) 
                            !!}  
                        @else    
                            {!!  
                                Form::text('name',
                                isset($emailTemplate['name']) ? $emailTemplate['name'] : null, 
                                ['class' => 'form-control','required', 'placeholder' => 'Template Name']) 
                            !!}
                        @endif  
                    @else 
                        {!! 
                            Form::text('name',
                            isset($emailTemplate['name']) ? $emailTemplate['name'] : null, 
                            ['class' => 'form-control','required', 'placeholder' => 'Template Name']) 
                        !!}
                    @endif 
                    {{Form::hidden('id',!empty($emailTemplate) ? $emailTemplate->email_template_id : null)}}
                </div>
                <div class="col-lg-6 form-group form-group-error">
                    {!! Form::label('subject', __('Subject'), ['class' => '']) !!} * 
                    {!! 
                        Form::text('subject',
                        isset($emailTemplate['subject']) ? $emailTemplate['subject'] : null, 
                        ['class' => 'form-control','required', 'placeholder' => 'Email Subject']) 
                    !!}     
                </div>
            </div>
            <div class="row form-group">
                <div class="col-lg-12 form-group-error"> 
                    {!! Form::label('content', __('Email Template'), ['class' => '']) !!} *
                    {!! 
                        Form::textarea('content',  
                        null, 
                        ['class' => 'form-control',
						'id'=>'kt_tinymce_2',
						'required']) 
                    !!} 
                </div>
            </div>
			<div class="row">

				@if(isset($email)) 
					@if($email->default_template != 1) 
					<div class="form-group col-md-6 form-group-error">
						{!! Form::label('event_type', __('Event Type'). ':', ['class' => '']) !!}
						{{Form::select('event_type',
							['date' => "Date",
							'birthday' => "Brithday",
							'anniversary' => "Anniversary"],
							$email->event_type ?? NULL,
							['class'=>'form-control','placeholder'=>'Select Event Type'])}}
						@if ($errors->has('event_type'))  
							<span class="form-text text-danger">{{ $errors->first('event_type') }}</span>
						@endif
					</div>
					@else
						{!! Form::hidden('event_type', $email->event_type, ['class' => 'event_type', 'id' => 'event_type']) !!}
					@endif
				@else
					<div class="form-group col-md-6 form-group-error">
						{!! Form::label('event_type', __('Event Type'). ':', ['class' => '']) !!}
						{{Form::select('event_type',
							['date' => "Date",
							'birthday' => "Brithday",
							'anniversary' => "Anniversary"],
							$email->event_type ?? NULL,
							['class'=>'form-control','placeholder'=>'Select Event Type'])}}
						@if ($errors->has('event_type'))  
							<span class="form-text text-danger">{{ $errors->first('event_type') }}</span>
						@endif
					</div>
				@endif 
				<div class="form-group col-md-6 form-group-error event-date-input d-none">
					{!! Form::label('event_date', __('Event Date'). ': *', ['class' => '']) !!}
					@if(isset($email))
						{!! 
							Form::date('event_date',  
							$email->event_type == "date" ? date('Y-m-d', strtotime($email->event_date)) : "", 
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
				<div class="form-group col-md-6 form-group-error event-days-input">
					{!! Form::label('before_days', __('Event before days'). ': *', ['class' => '']) !!}
					{!! 
						Form::number('before_days',  
						$email->before_days  ?? 0, 
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
                <div class="col-lg-6 form-group">
					@if(isset($email))
						{!! Form::submit('Update Email Template', ['class' => 'btn btn-md btn-primary mr-3']) !!}
					@else 
						{!! Form::submit('Create Email Template', ['class' => 'btn btn-md btn-primary mr-3']) !!}
					@endif
                    
                    <a href="#" id="reset-email" class="btn btn-light-primary font-weight-bold">Cancel</a>
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
		} else if (event_type == "birthday" || event_type == "anniversary" || event_type == "reminder") {
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
  
	<?php if(!isset($email)): ?>
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

 
<script src="https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js"></script>
<script> 
$(document).ready(function (){

	<?php if(!isset($email)): ?>
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

    $(document).on('click', '#reset-email', function (){
        location.reload(); 
    });
});



CKEDITOR.plugins.add('strinsert', {
   requires: ['richcombo'],
   init: function(editor) { 
		var strings = [];
		var o_b = '&lcub;&lcub;';
		var c_b = '&rcub;&rcub;';  

		strings.push([o_b+'#client_name'+c_b, 'Client Name']); 
		strings.push([o_b+'#client_email'+c_b, 'Client Email']); 
		strings.push([o_b+'#client_contact_number'+c_b, 'Client Contact Number']); 
		strings.push([o_b+'#client_whatsapp_number'+c_b, 'Client WhatsApp Number']);  
		strings.push([o_b+'#salon_name'+c_b, 'Salon Name']); 
		strings.push([o_b+'#contact_person'+c_b, 'Contact Person']); 
		@if(!$is_system_user)
			strings.push([o_b+'#subscription_expiry_date'+c_b, 'Subscription Expiry Date']); 
		@endif
		
      	strings = strings.sort(function(a, b) {
            return a[0].localeCompare(b[0], undefined, {
              sensitivity: 'accent'
            });
        });

      // add the menu to the editor
      editor.ui.addRichCombo('strinsert', {
         label: 'Variable',
         title: 'Insert Variable',
         voiceLabel: 'Insert Variable',
         className: 'cke_format',
         multiSelect: false,
         panel: {
            css: [editor.config.contentsCss, CKEDITOR.skin.getPath('editor')],
            voiceLabel: editor.lang.panelVoiceLabel
         },

         init: function() {
            this.startGroup("Insert Variable");
            for (var i in strings) {
               this.add(strings[i][0], strings[i][1], strings[i][1]);
            }
         },

         onClick: function(value) {
            editor.focus();
            editor.fire('saveSnapshot');
            editor.insertHtml(value);
            editor.fire('saveSnapshot');
         }
      });
   }
});

var editor = CKEDITOR.replace( 'kt_tinymce_2',{
      height: 400,
      allowedContent : true,
      removeButtons : 'Image,Source,About,Scayt',
      extraPlugins: 'strinsert, notification',
} );

editor.on( 'required', function( evt ) {
    editor.showNotification( 'Template content is required!.', 'warning' );
    evt.cancel();
} );


</script>
 
	