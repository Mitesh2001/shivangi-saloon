<!-- Modal-->
<div class="modal fade" id="add-new-client" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">Create Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body" style="height: 300px;">
            {!! Form::open([
                'route' => 'clients.storeBasic',
                'class' => 'ui-form',
                'id' => 'clientCreateForm'
            ]) !!}
                
            <div class="row">
				<div class="form-group col-lg-6">
					{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						null, 
						['class' => 'form-control',
						'placeholder' => 'Name']) 
					!!}
					@if ($errors->has('name'))  
						<span class="form-text text-danger">{{ $errors->first('name') }}</span>
					@endif
				</div>
				<div class="form-group col-lg-6">
                    {!! Form::label('gender', __('Gender'). ':', ['class' => 'control-label thin-weight']) !!}
                    {!!
                        Form::select('gender',
                        [
                            'Male' => 'Male',
                            'Female' => 'Female', 
                        ],
                        null,
                        ['class' => 'form-control', 
                        'placeholder'=>'Please select gender'])
                    !!}
                    @if ($errors->has('gender'))  
                        <span class="form-text text-danger">{{ $errors->first('gender') }}</span>
                    @endif
                </div>
			</div>
            <div class="row"> 
                <div class="form-group col-lg-6">
					{!! Form::label('primary_number', __('Contact number'). ': *', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::text('primary_number',  
						null, 
						['class' => 'form-control',
						'placeholder' => 'Primary number']) 
					!!} 
					@if ($errors->has('primary_number'))  
						<span class="form-text text-danger">{{ $errors->first('primary_number') }}</span>
					@endif
				</div>
                <div class="form-group col-lg-6">
					{!! Form::label('email', __('Email'). ':', ['class' => 'control-label thin-weight']) !!}
					{!! 
						Form::email('email',
						null, 
						['class' => 'form-control',
						'placeholder' => 'Email',
                        'id' => 'email_modal',
                        'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
                        'title' => 'Please eter valid email address!']) 
					!!}
					@if ($errors->has('email'))  
						<span class="form-text text-danger">{{ $errors->first('email') }}</span>
					@endif
				</div> 
				<div class="form-group col-lg-6">
					{!! Form::label('address', __('Address'). ': *', ['class' => 'control-label thin-weight']) !!}
					<div class="input-group">
						{!! 
							Form::textarea('address',
							null, 
							['class' => 'form-control','rows'=>1,
							'placeholder' => 'Address'])
						!!}
						<div class="input-group-append"><span class="input-group-text"><i class="la la-map-marker"></i></span></div>
					</div>
					@if ($errors->has('address'))  
						<span class="form-text text-danger">{{ $errors->first('address') }}</span>
					@endif
				</div>
                <input type="hidden" name="distributor_id" id="distributor_id_model" value=""> 
			</div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Cancle</button>
                <button type="submit" id="submit-client" class="btn btn-primary font-weight-bold">Create Client</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    
    $("#clientCreateForm").validate({
        rules: {  
            name: {
                required: true,
            },
            primary_number: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 10,
                remote: {
                    url: '{!! route('clients.checkPrimaryNumber') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        number: function () {
                            return $("#primary_number").val();
                        },
                        id: function () {
                            return $("#id").val();
                        },
                        distributor_id: function () {
                            return $("#distributor_id").val();
                        },
                    }
                }
            }, 
            email: { 
                email: true,
                remote: {
                    url: '{!! route('clients.checkemail') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        email: function () { 
                            return $("#email_modal").val();
                        },
                        id: function () {
                            return $("#id").val();
                        },
                        distributor_id: function () {
                            return $("#distributor_id").val();
                        },
                    }
                }
            }, 
            address: {
                required: true, 
            },  
        },
        messages: {  
            name: {
                required: "Please enter name!",
            },  
            primary_number: {
                required: "Please enter contact number!",
                number: "Please enter valid contact number!",
                minlength: "Please enter valid contact number!",
                maxlength: "Please enter valid contact number!",
                remote: "Number already exist!"
            },
            email: { 
                email: "Please enter valid email address!",
                remote: "Email already exist!"
            },
            address: { 
                required: "Please enter address!", 
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
    

    $(document).on('submit', '#clientCreateForm', function (e) {
        e.preventDefault();
        if($("#clientCreateForm").valid()) {
            $('#submit-client').prop('disabled', true);
            let client_data = $("#clientCreateForm").serialize();
            
            $.ajax({
                url: '{!! route('clients.storeBasic') !!}',
                type: "POST",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    data: client_data
                },
                success: function (res) {
                    $('#submit-client').prop('disabled', false);
                    $("#add-new-client").modal('toggle'); 

                    var newOption = new Option(res.data.name, res.data.id, false, false);
                    $('#client_external_id').append(newOption).trigger('change');
                    $("#client_gender").val(res.data.gender);
                    $("#contact_number").val(res.data.name);
                    $("#email").val(res.data.email);
                    $("#address").val(res.data.address);

                    Swal.fire({ 
                        icon: 'success',
                        title: 'Client created successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }); 
                }
            })

        }
    });
})
</script>