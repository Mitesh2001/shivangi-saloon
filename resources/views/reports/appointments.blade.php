{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Appointment Report')

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
						<i class="flaticon-grid-menu text-primary"></i>
					</span>
					<h3 class="card-label">Appointment Report</h3>
				</div>
				<div class="card-toolbar">

					<!--begin::Dropdown-->
					<div class="dropdown dropdown-inline mr-2">
						<button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="svg-icon svg-icon-md">
							<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Design/PenAndRuller.svg-->
							<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24" />
									<path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
									<path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
								</g>
							</svg>
							<!--end::Svg Icon-->
						</span>Export</button>
						<!--begin::Dropdown Menu-->
						<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
							<!--begin::Navigation-->
							<ul class="navi flex-column navi-hover py-2">
								<li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Choose an option:</li>
								<li class="navi-item">
									<a href="#" class="navi-link export-print">
										<span class="navi-icon">
											<i class="la la-print"></i>
										</span>
										<span class="navi-text">Print</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link export-copy">
										<span class="navi-icon">
											<i class="la la-copy"></i>
										</span>
										<span class="navi-text">Copy</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link export-excel">
										<span class="navi-icon">
											<i class="la la-file-excel-o"></i>
										</span>
										<span class="navi-text">Excel</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link export-csv">
										<span class="navi-icon">
											<i class="la la-file-text-o"></i>
										</span>
										<span class="navi-text">CSV</span>
									</a>
								</li>
								<li class="navi-item">
									<a href="#" class="navi-link export-pdf">
										<span class="navi-icon">
											<i class="la la-file-pdf-o"></i>
										</span>
										<span class="navi-text">PDF</span>
									</a>
								</li>
							</ul>
							<!--end::Navigation-->
						</div>
						<!--end::Dropdown Menu-->
					</div>
					<!--end::Dropdown-->
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3 py-3 custom-records-per-page d-flex text-left"></div>
					<div class="col-md-3 py-3 d-flex text-left"><label>From Date: <input type='date' id='search_fromdate' class="form-control" onchange="setMinimumEndDate()"></label></div>
					<div class="col-md-3 py-3 d-flex text-left"><label>To Date: <input type='date' id='search_todate' class="form-control"></label></div>
					<div class="col-md-3 py-3 custom-searchbar d-flex justify-content-end"></div>
				</div>
				<div class="table-responsive">
					<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
						<thead>
							<tr>
								<th>Salon</th>
								<th>
									<select name="distributor_filter" id="distributor_filter" style="width:100%">
										<option value="" data-searchid>All Salons</option>
										@foreach($distributors as $distributor)
											<option value="{{ $distributor->name }}" data-searchid="{{ $distributor->id }}">{{ $distributor->name }}</option>
										@endforeach
									</select>
								</th>
								<th>
								{!! Form::select('branch_filter',
									[],
									null,
									['class' => 'form-control','id' => 'branch_filter', 'style' => "width:100%"])
								!!}
								</th>
								<th class="branch-hidden">Branch</th>
								<th style="width: 150px">
								{!!
									Form::select('client_external_id',
									[],
									old('client_external_id'),
									['class' => 'form-control',
									'id' => 'client_external_id', 'style' => "width:100%"])
								!!}
								</th>
								<th class="client-hidden">Client</th>
								<th>Contact</th>
								<th>Email</th>
								<th>Appointment For</th>
								<th>Date</th>
								<th>Start At</th>
								<th>End At</th>
								<th>
									<select name="status_type" id="filter-status" class="form-control width-auto">
										<option value="all" selected>{{ __('All Status') }}</option>
										@foreach($statuses as $status)
											<option class="table-status-input-option" value="{{$status->title}}">{{$status->title}}</option>
										@endforeach
										<!-- <option value="all">All</option> -->
									</select>
								</th>
								<th class="status-hidden">Status</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="row">
					<div class="col-md-6 py-3 custom-info text-left"></div>
					<div class="col-md-6 py-3 custom-pagination d-flex justify-content-end"></div>
				</div>
				<!--begin: Datatable-->

				<!--end: Datatable-->
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

		$(document).ready(function () {
			
			var distributor_search_id = "";

			$('#distributor_filter').select2()

			$('#distributor_filter').on('select2:select', function (e) {
				var return_option = $(this).select2().find(":selected")[0];
				distributor_search_id = $(return_option).attr('data-searchid');
				$("#branch_filter").val("").trigger('change');
				$("#client_external_id").val("").trigger('change');
			});

			$('#branch_filter').select2({
				placeholder: "Select Branch",
				allowClear: true,
				ajax: {
					url: '{!! route('branch.getBranchByName') !!}',
					dataType: 'json',
					data: function (params) {
						ultimaConsulta = params.term;
						var distributor_id = distributor_search_id;
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
									id: item.name
								}
							})
						};
					}
				}
			});


			$('#client_external_id').select2({
				placeholder: "Client Name",
				allowClear: true,
				ajax: {
					url: '{!! route('leads.clientsbyname') !!}',
					dataType: 'json',
					data: function (params) {
						ultimaConsulta = params.term;
						var distributor_id = distributor_search_id;
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
									id: item.name
								}
							})
						};
					}
				}
			});

		});

		function setMinimumEndDate(){
			var start_date = new Date($("#search_fromdate").val());
			var day = ("0" + start_date.getDate()).slice(-2);
			var month = ("0" + (start_date.getMonth() + 1)).slice(-2);
			var date = start_date.getFullYear()+"-"+(month)+"-"+(day) ;
			$("#search_todate").attr("min", date);			
		}

		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";

		var KTDatatablesDataSourceAjaxServer = {
		init: function () {

			if(is_system_user) {
				var print_columns = [0, 3, 5, 6, 7, 8, 9, 10, 11, 13]
			} else {
				var print_columns = [3, 5, 6, 7, 8, 9, 10, 11, 13]
			}
			var to = $("#search_todate").val();var from = $("#search_fromdate").val();
			var date = from+'||'+to;
		
			var table = $("#kt_datatable").DataTable({
				"aaSorting": [],
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				ajax: {
					'url':'{!! route('appointments.data') !!}',
					'data': function ( d ) {
						d.fromdate = $('#search_fromdate').val();
						d.todate = $('#search_todate').val();
					}
				},
				
				name:'search',
					language: {
						searchPlaceholder: "Type here...",
					},
				drawCallback: function(){
					var length_select = $(".dataTables_length");
					var select = $(".dataTables_length").find("select");
					select.addClass("tablet__select");
				},
				buttons: [
					{
						extend: 'print',
						exportOptions: {
							columns: print_columns,
						},
						orientation: 'landscape'
					},
					{
						extend: 'copy',
						exportOptions: {
							columns: print_columns,
						},
					},
					{
						extend: 'excel',
						exportOptions: {
							columns: print_columns,
						},
					},
					{
						extend: 'csv',
						exportOptions: {
							columns: print_columns,
						},
					},
					{
						extend: 'pdf',
						exportOptions: {
							columns: print_columns,
						},
						orientation: 'landscape'
					},
				],
				autoWidth: false,
				initComplete: (settings, json)=>{
					$('.dataTables_info').appendTo('.custom-info');
					$('.dataTables_paginate').appendTo('.custom-pagination');
					$('.dataTables_filter').appendTo('.custom-searchbar');
					$('.dataTables_length').appendTo('.custom-records-per-page');
				},
				columns: [
					{data: 'distributor', name: 'distributor', visible: false},
					{data: 'distributor', name: 'distributor', visible: is_system_user, width: '10%'},
					{data: 'branch', name: 'branch', width: '15%'},
					{data: 'branch', name: 'branch', visible: false},
					{data: 'client_name', name: 'client_name', width: '20%'},
					{data: 'client_name', name: 'client_name', visible: false},
					{data: 'contact', name: 'contact'},
					{data: 'email', name: 'email'},
					{data: 'appointment_for', name: 'appointment_for'},
					{data: 'date', name: 'date', render:function(data){
						return moment(data).format('DD-MM-YYYY');
					}},
					{data: 'start_at', name: 'start_at', visible: true},
					{data: 'end_at', name: 'end_at', visible: false},
					{data: 'status', name: 'status', width: "20%"},
					{data: 'status', name: 'status', visible: false},
				]
			});

			$('.export-print').click(() => {
			$('#kt_datatable').DataTable().buttons(0,0).trigger()
			})
			$('.export-copy').click(() => {
				$('#kt_datatable').DataTable().buttons(0,1).trigger()
			})
			$('.export-excel').click(() => {
				$('#kt_datatable').DataTable().buttons(0,2).trigger()
			})
			$('.export-csv').click(() => {
				$('#kt_datatable').DataTable().buttons(0,3).trigger()
			})
			$('.export-pdf').click(() => {
				$('#kt_datatable').DataTable().buttons(0,4).trigger()
			})

			$('#distributor_filter').change(function () {
				var selected = $("#distributor_filter").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(0).search('').draw();
				} else {
					table.column(0).search(selected, true, false).draw();
				}
			});
			$('#branch_filter').change(function () {
				var selected = $("#branch_filter").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(2).search('').draw();
				} else {
					table.column(2).search(selected, true, false).draw();
				}
			});
			$('#filter-status').change(function () {
				var selected = $("#filter-status").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(12).search('').draw();
				} else {
					table.column(12).search(selected, true, false).draw();
				}
			});
			$('#client_external_id').change(function () {
				var selected = $("#client_external_id").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(4).search('').draw();
				} else {
					table.column(4).search('^' + selected + '$', true, false).draw();
				}
			});
			
			$('#search_fromdate').change(function () {
				var selected = $(this).val();
				var to = $("#search_todate").val();
				
				var date = selected+'||'+to;
				table.draw();
				//table.column(9).search(date, true, false).draw();
			});
			
			$('#search_todate').change(function () {
				var selected = $(this).val();
				var from = $("#search_fromdate").val();

				var date = from+'||'+selected;
				table.draw();
				//table.column(9).search(date, true, false).draw();
			});
			}
		};
		jQuery(document).ready((function () {
			KTDatatablesDataSourceAjaxServer.init()
		}));

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