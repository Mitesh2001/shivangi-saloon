@extends('layouts.default')

@section('content')
    @push('scripts') 
    @endpush 
    <?php
        $data = Session::get('data');
    ?>

<div class="card card-custom">
    <div class="card-header justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-tag text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Create Tag') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('tags.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body">
    {!! Form::open([
        'route' => 'tags.store',
        'class' => 'ui-form',
        'id' => 'tagsCreateForm'
    ]) !!}
            
        @include('tags.form', ['submitButtonText' => __('Create Tag')])

    {!! Form::close() !!}  
    </div>
</div>
 
<script>
    $(document).ready(function () {

        // Remove condition TR 
        $(document).on('click', '.remove-condition', function (e){  
            $('.tooltip').tooltip().remove(); 
            $(e.target).closest('tr').remove(); 
        }); 

        $("#tagsCreateForm").validate({
            rules: {
                distributor_id: {
                    required: true,
                },
                name: {
                    required: true, 
                    remote: {
                        url: '{!! route('tags.checkname') !!}',
                        type: "POST",
                        cache: false,
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: function () {
                                return $("#name").val();
                            },
                            id: function () {
                                return $("#id").val();
                            },
                            distributor_id : function () {
                                return $("#distributor_id").val();
                            },
                        },
                    },
                },   
            },
            messages: { 
                distributor_id: {
                    required: "please select salon!",
                },
                name: {
                    required: "Please enter tag name!", 
                    remote: "Tag name already exist!",
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
                $(element).closest('.form-group-error').append(error);
            }
        });

    });
</script>
@stop
