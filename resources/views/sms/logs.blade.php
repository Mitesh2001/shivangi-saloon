{{-- Extends layout --}}
@extends('layouts.default')
	
{{-- Content --}}
@section('content')
@include('layouts.alert')
<div class="row">
	<div class="col-lg-12">
	<!--begin::Card-->
<div class="card card-custom">
	<div class="card-header">
		<div class="card-title">
			<span class="card-icon">
				<i class="flaticon2-email text-primary"></i>
			</span>
			<h3 class="form_title">SMS Logs</h3>
		</div> 
		<div class="card-toolbar"> 
		</div>
	</div>
	<div class="card-body">
	 @include('layouts.alert')

	 			<div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div> 
		<div class="table-responsive">
		<!--begin: Datatable-->
			<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
				<thead>
					<tr>
						@if($distributor_id == 0)
						<th>Salon Name</th>
						@endif 
						<th>Client Name</th> 
						<th>Number</th> 
						<th>Event Type</th> 
						<th>Entry Date</th> 
					</tr>
				</thead>
			</table>
			<!--end: Datatable-->
		 </div>
		 <div class="row">
			<div class="col-md-6 py-3 custom-info text-left"></div>
			<div class="col-md-6 py-3 custom-pagination d-flex justify-content-end"></div>
		</div>
	</div>
</div>
<!--end::Card-->
	</div>
</div>
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script>   
		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
 
        var KTDatatablesDataSourceAjaxServer = {
            init: function () {
                $("#kt_datatable").DataTable({
					"aaSorting": [],
                    processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    ajax: '{!! route('sms.logsData') !!}',
                    name:'search',
					language: {
						searchPlaceholder: "Type here...",
					},
                    drawCallback: function(){
                        var length_select = $(".dataTables_length");
                        var select = $(".dataTables_length").find("select");
                        select.addClass("tablet__select");
                    },
                    autoWidth: false,
					initComplete: (settings, json)=>{
						$('.dataTables_info').appendTo('.custom-info');
						$('.dataTables_paginate').appendTo('.custom-pagination'); 
						$('.dataTables_filter').appendTo('.custom-searchbar'); 
						$('.dataTables_length').appendTo('.custom-records-per-page'); 
					},
                    columns: [   
						@if($distributor_id == 0)
                        {data: 'salon_name', name: 'salon_name'},
						@endif
                        {data: 'client_name', name: 'client_name'},
                        {data: 'number', name: 'number'},
                        {data: 'event_type', name: 'event_type'},
                        {data: 'created_at', name: 'created_at'},
                    ]
                })
            }
        };
        jQuery(document).ready((function () {
            KTDatatablesDataSourceAjaxServer.init()
        })); 

		$('#distributor_filter').change(function () { 
			var selected = $("#distributor_filter").val();// option:selected

			if (selected == "all" || selected == null || selected == "") {
				$("#kt_datatable").DataTable().column(0).search('').draw();
			} else { 
				$("#kt_datatable").DataTable().column(0).search(selected, true, false).draw();
			}
		});
		  
		$( document ).ajaxComplete(function() {
            // Required for Bootstrap tooltips in DataTables
            $('[data-toggle="tooltip"]').tooltip({
                "html": true,
                "delay": {"show": 100, "hide": 0},
            });
        });
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection