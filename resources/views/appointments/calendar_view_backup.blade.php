{{-- Extends layout --}}
@extends('layouts.default')

@section('content')

<link rel="stylesheet" href="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.2.8') }}">

<div class="card card-custom">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                Appointments
            </h3>
        </div>
        <div class="card-toolbar">
            @if(\Entrust::can('appointment-create'))
                <a href="{{ route('appointments.create') . '?caledar=1' }}" class="btn btn-primary mr-3">Create Appointment</a>
            @endif
        </div>
    </div>
    <div class="card-body">
        {!! Form::open([
            'route' => 'appointments.calendar',
            'method' => 'GET',
            'class' => 'ui-form',
            'id' => 'calendarFiltersForm'
        ]) !!}
        <div class="row d-flex justify-content-end align-items-center px-3"> 
            @if($distributor_id == 0)  
                <div class="col-lg-3 form-group form-group-error"> 
                    {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
                    <select name="distributor_id" id="distributor_id" class="form-control">
                        @if(isset($selected_distributor))
                            <option value="{{ $selected_distributor->external_id }}">{{ $selected_distributor->name }}</option>
                        @endif
                    </select>
                    @if ($errors->has('distributor_id'))  
                        <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                    @endif
                </div>  
            @else 
                <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $distributor_data->external_id }}"> 
            @endif 
            <div class="col-lg-3 form-group form-group-error">
                {{Form::label('Branch')}} *
                <select name="branch_id" id="branch_id" class="form-control">
                    @if(isset($selected_branch))
                        <option value="{{ $selected_branch->external_id }}">{{ $selected_branch->name }}</option>
                    @endif
                </select>
                @if ($errors->has('branch_id'))  
                    <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                @endif 
            </div>
            <div class="col-lg-3 form-group form-group-error"> 
                {!! Form::label('status_id', __('Status'). ': *', ['class' => '']) !!}  
                <select name="status_id" id="status_id" class="form-control">
                    <option value="">Select Status</option>
                    @foreach($statuses as $status) 
                        @if(isset($selected_status))
                            @if($selected_status->external_id == $status->external_id)
                                <option value="{{ $status->external_id }}" selected>{{ $status->title }}</option>
                            @else 
                                <option value="{{ $status->external_id }}">{{ $status->title }}</option>
                            @endif
                        @else
                            <option value="{{ $status->external_id }}">{{ $status->title }}</option>
                        @endif
                    @endforeach 
                </select>
                @if ($errors->has('status_id'))  
                    <span class="form-text text-danger">{{ $errors->first('status_id') }}</span>
                @endif
            </div> 
            <div class="button-wrapper">
                <button class="btn btn-primary">Search</button>
            </div>
        </div>
        {!! Form::close() !!}
        <div id="appointment_calendar"></div>
    </div> 
</div>
 
@include('appointments.view_appointment_modal', ['statuses' => $statuses, 'branches' => $branches])


<script src="{{ asset('plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.2.8') }}"></script>
<script>            
$(document).ready(function () {

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
								id: item.external_id
							}
						})
					};
				}
			}
		})

		$(document).on('change', '#distributor_id', function (e) { 
			$("#branch_id").val("").trigger('change'); 
		});

	<?php endif; ?>

    $('#branch_id').select2({
		placeholder: "Select Branch",
		allowClear: true,
		ajax: {
			url: '{!! route('branch.getBranchByDistributor') !!}',
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
							id: item.external_id
						}
					})
				};
			}
		}
	});

     
    var KTCalendarBasic = function() {

        return {
            //main function to initiate the module
            init: function() {
                var todayDate = moment().startOf('day');
                var YM = todayDate.format('YYYY-MM');
                var YESTERDAY = todayDate.clone().subtract(1, 'day').format('YYYY-MM-DD');
                var TODAY = todayDate.format('YYYY-MM-DD');
                var TOMORROW = todayDate.clone().add(1, 'day').format('YYYY-MM-DD');

                var calendarEl = document.getElementById('appointment_calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: [ 'bootstrap', 'interaction', 'dayGrid', 'timeGrid', 'list' ],
                    themeSystem: 'bootstrap',

                    isRTL: KTUtil.isRTL(),

                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },

                    height: 800,
                    contentHeight: 780,
                    aspectRatio: 3,  // see: https://fullcalendar.io/docs/aspectRatio

                    nowIndicator: true,
                    now: "<?php echo now(); ?>",

                    views: {
                        dayGridMonth: { buttonText: 'month' },
                        timeGridWeek: { buttonText: 'week' },
                        timeGridDay: { buttonText: 'day' }
                    },

                    defaultView: 'dayGridMonth',
                    defaultDate: TODAY,

                    editable: true,
                    eventLimit: true, // allow "more" link when too many events
                    navLinks: true,
                    events: {
                        url: '{!! route('appointments.calendarData') !!}', 
                        extraParams: {
                            distributor_id: "<?php echo $_GET['distributor_id'] ?? ''; ?>", 
                            branch_id: "<?php echo $_GET['branch_id'] ?? ''; ?>", 
                            status_id: "<?php echo $_GET['status_id'] ?? ''; ?>", 
                        },
                        failure: function() {
                            
                        }, 
                    },
                    eventClick:  function(event, jsEvent, view) { 
                        let external_id = event.event.id; 
                        showAppointment(external_id); 
                    }, 
                });

                calendar.render(); 
            }
        };
    }();

    function showAppointment(external_id)
    { 
        $.ajax({
            url: '{!! route('appointments.findById') !!}',
            type: "POST",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                external_id: external_id
            },
            success: function (res) {  
                // console.log(res);
                $("#distributor-modal").html(res.distributor);
                $("#name-modal").html(res.client_name);
                $("#contact-number-modal").html(res.contact_number);
                $("#email-modal").html(res.email);
                $("#address-modal").html(res.address);
                $("#appointment-for-modal").html(res.appointment_for);
                $("#appointment-source-modal").html(res.appointment_source);
                $("#representative-modal").html(res.representative);
                $("#appointment-status-modal").html(res.status_tag);
                $("#appointment-branch-modal").html(res.branch_name); 				
                $("#date-modal").html(res.date);
                $("#start-time-modal").html(res.start_at);
                $("#end-time-modal").html(res.end_at);

                $("#description-modal").html(res.description);
                $('#appointment-external-id').val(external_id);
                $("#view-enquiry-modal").modal('show');
            }
        })
    }

    jQuery(document).ready(function() {
        KTCalendarBasic.init();  
        $(".fc-dayGridMonth-button").html("Month");
        $(".fc-timeGridWeek-button").html("Week");
        $(".fc-timeGridDay-button").html("Day");
    });
 
});
</script>
@stop