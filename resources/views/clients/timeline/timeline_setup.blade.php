@extends('layouts.default')
@section('content')
@include('layouts.alert')
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-menu text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Clients Timeline Setup') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('clients.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">

        {!! Form::open([
            'route' => 'clients_timeline.updateTimeline',
            'class' => 'ui-form',
            'id' => 'clientsTimelineForm'
        ]) !!}

        <div class="row"> 
            @if($distributor_id == 0) 
                <div class="col-md-3 form-group form-group-error"> 
                    {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
                    <select name="distributor_id" id="distributor_id" class="form-control">
                        @if(isset($selected_distributor))
                            <option value="{{ $selected_distributor->id }}">{{ $selected_distributor->name }}</option>
                        @endif
                        @if($selected_distributor = Session::get('selected_distributor'))
                            <!-- <option value="{{ $selected_distributor['id'] }}">{{ $selected_distributor['name'] }}</option> -->
                        @endif
                    </select>
                    @if ($errors->has('distributor_id'))  
                        <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                    @endif
                </div>  
            @endif 
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-3"> 
                <div class="card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">New Clients</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="new-clients-display"></span>
                    </div>
                       
                        <div class="range-values text-center mt-6">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">First Visit</span> 
                            <input type="hidden" name="new_clients" id="new_clinets_input" value="0">
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="new_clients_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
            <div class="col-md-6 col-lg-3"> 
                <div class="card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Repeating Clients</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="repeating-clients-display"></span></div>
                        <div class="mt-3">
                            <div id="repeat_clients" style="width: 100%"></div> 
                        </div>
                        <div class="range-values text-center mt-4">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">From : &nbsp;<span id="repeat-from">0</span></span> 
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">To : &nbsp;<span id="repeat-to">200</span></span> 

                            <input type="hidden" name="repeat_clients_min" id="repeat_clients_min_input" value="0">
                            <input type="hidden" name="repeat_clients_max" id="repeat_clients_max_input" value="0">
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="repeating_clients_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
            <div class="col-md-6 col-lg-3"> 
                <div class="card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Regular Clients</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="regular-clients-display"></span></div>
                        <div class="mt-3">
                            <div id="regular_clients" style="width: 100%"></div> 
                        </div>
                        <div class="range-values text-center mt-4">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">Above : &nbsp;<span id="regular-above">0</span></span>  
                            <input type="hidden" name="regular_clients" id="regular_clients_input" value="0"> 
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="regular_clients_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
            <div class="col-md-6 col-lg-3"> 
                <div class="card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Never Visited</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="never-visited-clients-display"></span></div>
                       
                        <div class="range-values text-center mt-6">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">Vistits : 0</span> 
                            <input type="hidden" name="never_visited" id="never_visited_input" value="0"> 
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="never_visited_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
        </div> 
        <div class="row">
            <div class="col-md-6 col-lg-3"> 
                <div class="bg-green card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->  
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Green</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="no-risk-clients-display"></span></div>
                        {{-- <div class="mt-3">
                            <div id="no_risk" style="width: 100%"></div> 
                        </div> --}}
                        <div class="range-values text-center mt-6">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">Last visits &nbsp; <span id="">30</span> &nbsp; days back </span> 
                            <input type="hidden" name="no_risk" id="no_risk_input" value="0"> 
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="no_risk_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
            <div class="col-md-6 col-lg-3"> 
                <div class="bg-warning card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Yellow</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="dormant-clients-display"></span></div>
                        {{-- <div class="mt-3">
                            <div id="dormant_clients" style="width: 100%"></div> 
                        </div> --}}
                        <div class="range-values text-center mt-4">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">Last visits &nbsp; <span id="">60</span> &nbsp; days back </span> 

                            {{-- <span class="label label-md label-primary label-inline font-weight-bolder mr-2">From : &nbsp;<span id="dormant-from">0</span></span> 
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">To : &nbsp;<span id="dormant-to">200</span></span>  --}}

                            {{-- <input type="hidden" name="dormant_clients_min" id="dormant_clients_min_input" value="0">
                            <input type="hidden" name="dormant_clients_max" id="dormant_clients_max_input" value="0"> --}}
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="dormant_clients_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div>   
            <div class="col-md-6 col-lg-3"> 
                <div class="bg-danger card shadow-sm separator separator-dashed separator-border-2 separator-primary card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url( {{ asset('media/svg/shapes/abstract-3.svg') }} )">
                    <!--begin::Body-->
                    <div class="card-body my-4">
                        <p class="card-title remove-flex font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Red</p>
                        <div class="font-weight-bold text-muted d-flex flex-column text-center justify-content-around">
                        <span class="text-dark-75 font-weight-bolder font-size-h1 mr-2" id="lost-clients-display"></span></div>
                        {{-- <div class="mt-3">
                            <div id="lost_clients" style="width: 100%"></div> 
                        </div> --}}
                        <div class="range-values text-center mt-6">
                            <span class="label label-md label-primary label-inline font-weight-bolder mr-2">Last visits &nbsp; <span id="">120</span> &nbsp; days back </span> 
 
                            {{-- <input type="hidden" name="lost_clients" id="lost_clients_input" value="0"> --}}
                            @if($distributor_id == 0) 
                            <a href="#" class="btn btn-primary btn-block mt-5" id="lost_clients_view">View</a>
                            @endif
                        </div>
                    </div>
                    <!--end::Body-->
                </div>
            </div> 
        </div>   
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-lg-6">
                {!! Form::submit("Update Timeline", ['class' => 'btn btn-md btn-primary', 'id' => 'submitClienttimeline']) !!} 
                {!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold']) !!} 
            </div> 
        </div>
    </div>
    {!! Form::close() !!}
</div>

<script>
$(document).ready( function() { 

    $(document).on('click', 'input:reset', function (){
        location.reload();
    });

    <?php if($selected_distributor = Session::get('selected_distributor')): ?>
        let selected_distributor = "<?php echo $selected_distributor['id']; ?>"

        $("#distributor_id").val(selected_distributor).trigger("change");
    <?php endif ?>

    <?php if($distributor_id == 0): ?>
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

    getStatuses(); 
    getReports();

    $(document).on('change', '#distributor_id', function(){ 
        destroySliders();   
        getStatuses();
        getReports();
    });

    function destroySliders()
    {
        document.getElementById('repeat_clients').noUiSlider.destroy();
        document.getElementById('regular_clients').noUiSlider.destroy();
        // document.getElementById('no_risk').noUiSlider.destroy();
        // document.getElementById('dormant_clients').noUiSlider.destroy();
        // document.getElementById('at_risk').noUiSlider.destroy();
        // document.getElementById('lost_clients').noUiSlider.destroy(); 
    }

    function getStatuses()
    {
        $.ajax({
        url: '{!! route('clients_timeline.allStatuses') !!}',
        type: "POST",
        cache: false,
        data: {
            _token: "{{ csrf_token() }}",
            distributor_id: $("#distributor_id").val(),
        },
        success: function (data) { 
            var distributor_id = $("#distributor_id").val();
            $("#new_clients_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=new_clients&distributor=" ?>"+ distributor_id); 
            $("#repeating_clients_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=repeating_clients&distributor=" ?>"+ distributor_id); 
            $("#regular_clients_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=regular_clients&distributor=" ?>"+ distributor_id); 
            $("#never_visited_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=never_visited&distributor=" ?>"+ distributor_id); 
            $("#no_risk_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=no_risk&distributor=" ?>"+ distributor_id); 
            $("#dormant_clients_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=dormant_clients&distributor=" ?>"+ distributor_id); 
            $("#at_risk_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=at_risk&distributor=" ?>"+ distributor_id); 
            $("#lost_clients_view").attr('href', "<?php echo route('clients_timeline.ClientsList') . "?status=lost_clients&distributor=" ?>"+ distributor_id); 
  
            // Repeat clients
            var repeat_clients_slider = document.getElementById('repeat_clients'); 
            noUiSlider.create(repeat_clients_slider, {
                start: [data.repeating_clients_from, data.repeating_clients_to],
                connect: true,
                step: 1,
                range: {
                    'min': 0,
                    'max': 200
                }
            }); 
            repeat_clients_slider.noUiSlider.on('update', function (values, handle) {
                var snapValues = [
                    document.getElementById('repeat-from'),
                    document.getElementById('repeat-to')
                ];
                var inputs = [
                    document.getElementById('repeat_clients_min_input'),
                    document.getElementById('repeat_clients_max_input')
                ];
                snapValues[handle].innerHTML = Math.abs(values[handle]);
                inputs[handle].value = Math.abs(values[handle]);
            });

            // Regular clients
            var regular_clients_slider = document.getElementById('regular_clients'); 
            noUiSlider.create(regular_clients_slider, {
                start: [data.regular_clients], 
                step: 1,
                range: {
                    'min': 0,
                    'max': 200
                }
            }); 
            regular_clients_slider.noUiSlider.on('update', function (values, handle) {
                var snapValues = [
                    document.getElementById('regular-above'), 
                ];
                var inputs = [
                    document.getElementById('regular_clients_input') 
                ];
                snapValues[handle].innerHTML = Math.abs(values[handle]);
                inputs[handle].value = Math.abs(values[handle]);
            });

            // No risk clients
            var no_risk_slider = document.getElementById('no_risk'); 
            noUiSlider.create(no_risk_slider, {
                start: [data.no_risk], 
                step: 1,
                range: {
                    'min': 0,
                    'max': 200
                }
            }); 
            // no_risk_slider.noUiSlider.on('update', function (values, handle) {
            //     var snapValues = [
            //         document.getElementById('no-risk-days'), 
            //     ];
            //     var inputs = [
            //         document.getElementById('no_risk_input'), 
            //     ];
            //     snapValues[handle].innerHTML = Math.abs(values[handle]);
            //     inputs[handle].value = Math.abs(values[handle]);
            // });

            // Repeat clients
            var dormant_clients_slider = document.getElementById('dormant_clients'); 
            noUiSlider.create(dormant_clients_slider, {
                start: [data.dormant_clients_from, data.dormant_clients_to],
                connect: true,
                step: 1,
                range: {
                    'min': 0,
                    'max': 200
                }
            }); 
            // dormant_clients_slider.noUiSlider.on('update', function (values, handle) {
            //     var snapValues = [
            //         document.getElementById('dormant-from'),
            //         document.getElementById('dormant-to')
            //     ];
            //     var inputs = [
            //         document.getElementById('dormant_clients_min_input'),
            //         document.getElementById('dormant_clients_max_input')
            //     ];
            //     snapValues[handle].innerHTML = Math.abs(values[handle]);
            //     inputs[handle].value = Math.abs(values[handle]);
            // });

            // // At Risk clients
            // var at_risk_slider = document.getElementById('at_risk'); 
            // noUiSlider.create(at_risk_slider, {
            //     start: [data.at_risk_from, data.at_risk_to],
            //     connect: true,
            //     step: 1,
            //     range: {
            //         'min': 0,
            //         'max': 200
            //     }
            // }); 
            // at_risk_slider.noUiSlider.on('update', function (values, handle) {
            //     var snapValues = [
            //         document.getElementById('at-risk-from'),
            //         document.getElementById('at-risk-to')
            //     ];
            //     var inputs = [
            //         document.getElementById('at_risk_min_input'),
            //         document.getElementById('at_risk_max_input'),
            //     ];
            //     snapValues[handle].innerHTML = Math.abs(values[handle]);
            //     inputs[handle].value = Math.abs(values[handle]);
            // });

            // Lost clients
            var lost_clients_slider = document.getElementById('lost_clients'); 
            noUiSlider.create(lost_clients_slider, {
                start: [data.lost_clients], 
                step: 1,
                range: {
                    'min': 0,
                    'max': 200
                }
            }); 
            lost_clients_slider.noUiSlider.on('update', function (values, handle) {
                var snapValues = [
                    document.getElementById('lost-days'), 
                ]; 
                snapValues[handle].innerHTML = Math.abs(values[handle]);
                document.getElementById('lost_clients_input').value = Math.abs(values[handle]);
            }); 

        }
    })
    }


    function getReports()
    {
        $.ajax({
            url: '{!! route('clients_timeline.reportApi') !!}',
            type: "POST",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                distributor_id: $("#distributor_id").val(),
            },
            success: function (data) {
                $("#new-clients-display").html(data.new_clients);
                $("#repeating-clients-display").html(data.repeating_clients);
                $("#regular-clients-display").html(data.regular_clients);
                $("#never-visited-clients-display").html(data.never_visited);
                $("#no-risk-clients-display").html(data.no_risk);
                $("#dormant-clients-display").html(data.dormant_clients);
                // $("#at-risk-clients-display").html(data.at_risk);
                $("#lost-clients-display").html(data.lost_clients); 
            }
        })
    }

 
    $("#clientCreateForm").validate({
        rules: {
            name: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "Please enter name!",
            },
        },
                normalizer: function( value ) { 
                    return $.trim( value );
                },
        errorElement: "span",
        errorClass: "form-text text-primary",
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