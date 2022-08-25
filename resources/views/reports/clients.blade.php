{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Clients List')

{{-- Content --}}
@section('content')
	@include('layouts.alert')
<div class="row">
	<div class="col-lg-12">

<div class="card card-custom">
	<div class="card-header">
		<div class="card-title">
			<span class="card-icon">
				<i class="flaticon2-menu text-primary"></i>
			</span>
			<h3 class="card-label">
				Client Report
			</h3>
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
						<li class="navi-header font-weight-bolder text-uppercase  d-flex justify-content-around text-primary pb-2">Choose an option:</li>
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

			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">
				<button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="svg-icon svg-icon-md">
					<!--end::Svg Icon-->
				</span>Options</button>
				<!--begin::Dropdown Menu-->
				<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
					<!--begin::Navigation-->
					<ul class="navi flex-column navi-hover py-2">
						<form action="" id="column-visibility">
							<li class="navi-header font-weight-bolder text-uppercase  d-flex justify-content-around text-primary pb-2">Select Columns:</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(2);return false;" checked>
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Name</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(4);return false;" checked>
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Email</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(5);return false;" checked>
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Contact Number</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(6);return false;" >
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">WhatsApp Number</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(7);return false;" >
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Birthday</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(8);return false;" >
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Anniversary</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(9);return false;" checked>
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">City</span>
										</span>
									</span>
								<label>
							</li>
							<li class="navi-item">
								<label class="option p-2 border-0">
									<span class="option-control">
										<label class="checkbox">
											<input type="checkbox" class="form" onChange="setColVisible(10);return false;" >
											<span></span>
										</label>
									</span>
									<span class="option-label">
										<span class="option-head">
											<span class="option-title">Zip Code</span>
										</span>
									</span>
								<label>
							</li>
						</form>
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
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div>
		<div class="table-responsive">
			<!--begin: Datatable-->
			<table class="table table-hover <?php echo $status_details['bg_color'] ?? "" ; ?>" id="kt_datatable" style="margin-top: 13px !important; min-height:200px !important">
				<thead>
					<tr>
						<th>Salon</th>
						<th style="width:150px !important">
						{!! Form::select('distributor_filter',
							[],
							null,
							['class' => 'form-control','id' => 'distributor_filter', 'style' => "width:100%"])
						!!}
						</th>
						<th>Name</th>
						<th class="hidden-name">Name</th>
						<th>Email</th>
						<th>Contact Number</th>
						<th>WhatsApp Number</th>
						<th>Birthday</th>
						<th>Anniversary</th>
						<th>City</th>
						<th>Zip Code</th>
						<th>Client Type</th>
						<th>Appointments</th>
						<th>Total Sales</th>
						<th>Outstanding</th>
						<th>Added Date</th>
						<th>Last Appointment</th>
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

	<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>

    {{-- page scripts --}}
    <script>

		$(document).ready(function () {

			var card = new KTCard('clients_status_card');

			$('#distributor_filter').select2({
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
									id: item.name
								}
							})
						};
					}
				}
			})

			document.getElementById('column-visibility').reset();

			$.ajax({
				url: '{!! route('clients_timeline.reportApi') !!}',
				type: "POST",
				cache: false,
				data: {
					_token: "{{ csrf_token() }}"
				},
				success: function (data) {
					$("#new-clients-display").html(data.new_clients);
					$("#repeating-clients-display").html(data.repeating_clients);
					$("#regular-clients-display").html(data.regular_clients);
					$("#never-visited-clients-display").html(data.never_visited);
					$("#no-risk-clients-display").html(data.no_risk);
					$("#dormant-clients-display").html(data.dormant_clients);
					$("#at-risk-clients-display").html(data.at_risk);
					$("#lost-clients-display").html(data.lost_clients);
				}
			})

			
		});

		// Reload page after 2 seconds
		/* function reload_page()
		{
			setTimeout(() => {
				location.reload();
			}, 2000);
		} */

		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
		var status = "<?php echo $status ?? ""; ?>";

		var KTDatatablesDataSourceAjaxServer = {
		init: function () {
			if(is_system_user) {
				var print_columns = [0, 3, 4, 5, 6, 7, 8, 9, 10, 11];
			} else {
				var print_columns = [3, 4, 5, 6, 7, 8, 9, 10, 11];
			}
			$("#kt_datatable").DataTable({
				"aaSorting": [],
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				ajax: '{!! route('clients.data') !!}',
				name:'search',
					language: {
						searchPlaceholder: "Type here...",
					},
				drawCallback: function(){
					var length_select = $(".dataTables_length");
					var select = $(".dataTables_length").find("select");
					select.addClass("tablet__select");
				},
				// dom: 'Bfrtip',
				buttons: [
					'colvis',
					{
						extend: 'print',
						exportOptions: {
							columns: print_columns
						},
						orientation: 'landscape'
					},
					{
						extend: 'copy',
						exportOptions: {
							columns: print_columns
						},
					},
					{
						extend: 'excel',
						exportOptions: {
							columns: print_columns
						},
					},
					{
						extend: 'csv',
						exportOptions: {
							columns: print_columns
						},
					},
					{
						extend: 'pdf',
						exportOptions: {
							columns: print_columns
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
					{data: 'namelink', name: 'namelink', width: '20%'},
					{data: 'name', name: 'name', visible: false},
					{data: 'email', name: 'email'},
					{data: 'primary_number', name: 'primary_number'},
					{data: 'secondary_number', name: 'secondary_number', visible: false},
					{data: 'date_of_birth', name: 'date_of_birth', visible: false},
					{data: 'anniversary', name: 'anniversary', visible: false},
					{data: 'city', name: 'city'},
					{data: 'zipcode', name: 'zipcode', visible: false},
					{data: 'client_type', name: 'client_type'},
					{data: 'total_appointments', name: 'total_appointments'},
					{data: 'total_sales', name: 'total_sales'},
					{data: 'outstanding', name: 'outstanding'},
					{data: 'added_date', name: 'added_date'},
					{data: 'last_appointment', name: 'last_appointment'},
				]
			})
		}
		};
		jQuery(document).ready((function () {
			var table = KTDatatablesDataSourceAjaxServer.init()
		}));

		$('#distributor_filter').change(function () {
			var selected = $("#distributor_filter").val();// option:selected

			if (selected == "all" || selected == null || selected == "") {
				$("#kt_datatable").DataTable().column(0).search('').draw();
			} else {
				$("#kt_datatable").DataTable().column(0).search(selected, true, false).draw();
			}
		});

		function setColVisible(columnIndex){
			$('#kt_datatable').DataTable().column(columnIndex).visible(!$('#kt_datatable').DataTable().column(columnIndex).visible());
		}


		$('.export-print').click(() => {
			$('#kt_datatable').DataTable().buttons(0,1).trigger()
		})
		$('.export-copy').click(() => {
			$('#kt_datatable').DataTable().buttons(0,2).trigger()
		})
		$('.export-excel').click(() => {
			$('#kt_datatable').DataTable().buttons(0,3).trigger()
		})
		$('.export-csv').click(() => {
			$('#kt_datatable').DataTable().buttons(0,4).trigger()
		})
		$('.export-pdf').click(() => {
			$('#kt_datatable').DataTable().buttons(0,5).trigger()
		})

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