{{-- Extends layout --}}
@extends('layouts.default')
@section('content') 
<div class="row">
	@if(Session::has('success')) 
	<div class="col-lg-12">   
		<div class="alert alert-success" role="alert">
			{{Session::get('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="ki ki-close"></i></span>
			</button>
		</div>
	</div>
	@endif
	@if(Session::has('errors')) 
	<div class="col-lg-12">   
		<div class="alert alert-danger" role="alert">
			{!! implode('', $errors->all('<div>:message</div>')) !!} 
		</div>
	</div>
	@endif
</div>

<div class="card card-custom pb-6">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-label">
                Appointments
            </h3>
        </div>
        <div class="card-toolbar">
            @if(\Entrust::can('appointment-create') && !$allow_view_only)
                <a href="{{ route('appointments.create') . '?caledar=1' }}" class="btn btn-primary mr-3">Create Appointment</a>
            @endif
        </div>
    </div>
    <div class="card-body remove-padding-mobile">
        {!! Form::open([
            'route' => 'appointments.calendar',
            'method' => 'GET',
            'class' => 'ui-form',
            'id' => 'calendarFiltersForm'
        ]) !!}
        <div class="row form-group d-flex justify-content-end align-items-center px-3"> 
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
            <div class="button-wrapper mr-5">
                <button class="btn btn-primary">Search</button>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-4 form-group">
                <label for="date_search">Jump to</label>
                <input class="form-control" type="text" name="date_search" id="date_search" value="">
            </div>
        </div>
        {!! Form::close() !!}
        <div id="calendar"></div>
    </div> 
</div>
@include('appointments.view_appointment_modal', ['statuses' => $statuses, 'branches' => $branches])
<script>
$(document).ready(function() {

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
 
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear(); 

    <?php  
        // Prepare URL for events - with filters
        $distributor_id = $_GET['distributor_id'] ??  "";
        $branch_id = $_GET['branch_id'] ??  "";
        $status_id = $_GET['status_id'] ??  "";
        $url = route('appointments.calendarData')."?distributor_id=".$distributor_id."&branch_id=".$branch_id."&status_id=".$status_id; 
 
    ?>
  
});

document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var toggledLines = {};
  
  window.toggleLine = function(id) {
    console.log('toggle', id)
    // Change toggled variables
    toggledLines[id] = !toggledLines[id];
    calendar.render()
  }

  var calendar = new FullCalendar.Calendar(calendarEl, {
    schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
    timeZone: 'UTC', 
    themeSystem: 'bootstrap',
    droppable: false, 
    initialView: 'resourceTimelineDay',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'resourceTimelineDay,resourceTimelineWeek,dayGridMonth'
    }, 
    views: {
        dayGridMonth: { buttonText: 'Month' },
        resourceTimeline: { buttonText: 'Week' },
        resourceTimelineDay: { buttonText: 'Day' }
    }, 
    datesSet : function (e) { 
    },
    resourceAreaHeaderContent: 'Employees', 
    resources: <?php print_r($employees); ?>,
    events: '{!! $url !!}',
    eventClick:  function(event, jsEvent, view) { 
        // console.log(event, jsEvent, view)
        let external_id = event.event.id; 
        showAppointment(external_id); 
    },
    eventTimeFormat: { // like '14:30:00'
        hour: '2-digit',
        minute: '2-digit', 
        meridiem: '2-digit',
    }
  });

  calendar.render();

  $(document).on('change', '#date_search', function (e) {
    let selected_date = moment($("#date_search").val()); 
    let date_utc = Date.UTC(selected_date.format('Y'), selected_date.format('M') - 1, selected_date.format('D')); 
    calendar.gotoDate(date_utc)
  });   

    $('input[name="date_search"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true, 
    });

  
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
                $(".appointment_id").val(res.id);
                $("#distributor-modal").html(res.distributor);
                $("#name-modal").html(res.client_name);
                $("#contact-number-modal").html(res.contact_number);
                $("#email-modal").html(res.email);
                $("#address-modal").html(res.address);
                $("#appointment-for-modal").html(res.appointment_for);
                $("#appointment-source-modal").html(res.appointment_source);
                $("#representative-modal").html(res.representative);
                $("#status_id_appointment").val(res.status);
                // $("#appointment-status-modal").html(res.status_tag);
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
});
</script>
@stop
