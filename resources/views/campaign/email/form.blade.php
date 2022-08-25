<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body">
            <div class="row form-row">
                <div class="offset-lg-2 col-lg-4 form-group form-group-error">
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
                <div class="col-lg-4 form-group form-group-error">
                    {!! Form::label('subject', __('Subject'). ': *', ['class' => '']) !!}
                    {!!
                    Form::text('subject',
                    $data['subject'] ?? old('subject'),
                    ['class' => 'form-control dynamic-input',
                    'placeholder' => "Subject"])
                    !!}
                    @if ($errors->has('subject'))
                    <span class="form-text text-danger">{{ $errors->first('subject') }}</span>
                    @endif
                </div>
            </div>
			<div class="offset-lg-2 col-lg-8 form-group form-group-error">
				{!! Form::label('audience_type', __('Audience Type'). ':', ['class' => '']) !!} 
				{!! Form::select('audience_type',
					$audience_types,
					old('audience_type'),	
					['class' => 'form-control', 'id' => 'audience_type'])
				!!}
				@if ($errors->has('audience_type'))  
					<span class="form-text text-danger">{{ $errors->first('audience_type') }}</span>
				@endif 
			</div> 
            <div class="row form-row">
                <div class="offset-lg-2 col-lg-8 form-group form-group-error email-template-row">
                    {!! Form::label('message', __('Message'). ': *', ['class' => '']) !!}
                    {!!
                    Form::textarea('message',
                    $data['message'] ?? old('message'),
                    ['class' => 'form-control dynamic-input',
                    'placeholder' => "Message",
                    'id' => "message"])
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
                    {!! Form::hidden('type', 1, ['id' => 'type']) !!}
                    {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient'])
                    !!}
                    {!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' =>
                    'submitClient']) !!}
                </div>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<!--end::Card-->

<script src="https://preview.keenthemes.com/metronic/theme/html/demo1/dist/assets/plugins/custom/tinymce/tinymce.bundle.js?v=7.2.8"></script>
<script>
$(document).ready(function() { 

    var KTTinymce = function() {
        var invoiceEditor = function() {
            tinymce.init({
                selector: '#message',
                plugins: ['table'],
                forced_root_block: "",
                force_br_newlines: true,
                force_p_newlines: false,
                statusbar: false,
            });
        }
        return {
            init: function() {
                invoiceEditor();
            }
        };
    }();

    jQuery(document).ready(function() {
        KTTinymce.init();
    });
})
</script>