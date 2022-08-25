{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Inquiries List')

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
				<i class="flaticon2-phone text-primary"></i>
			</span>
			<h3 class="card-label">All Inquiry</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('inquiry-create') && !$allow_view_only)
			<a href="{{ route('leads.create') }}" class="btn btn-primary mr-3">Create Inquiry</a>
		@endif

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
		<!--begin: Datatable-->
				<div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
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
						<select name="branch_filter" id="branch_filter" class="form-control" style="width:100%">
						</select>
					</th>
					<th class="enquiry-status-hidden">
						Branch
					</th>
					<th style="width: 150px;">
					{!!
						Form::select('client_external_id',
						[],
						old('client_external_id'),
						['class' => 'form-control',
						'id' => 'client_external_id', 'style' => "width:100%"])
					!!}
					</th>
					<th>Contact</th>
					<th>Email</th>
					<th>Inquiry For</th>  
					<th>
						<select name="enquiry_type" id="enquiry-type" class="form-control" style="width:auto">
							<option value="all" selected>{{ __('All Inquiry Types') }}</option>
							@foreach($enquiry_types as $enquiry)
								<option class="table-status-input-option" value="{{$enquiry->name}}">{{$enquiry->name}}</option>
							@endforeach
							<!-- <option value="all">All</option> -->
						</select>
					</th> 
					<th class="enquiry-type-hidden">
						Inquiry Type
					</th> 
					<th>
						<select name="status_type" id="stage-lead" class="form-control" style="width:auto">
							<option value="all" selected>{{ __('All Stage') }}</option> 
							<option class="table-status-input-option" value="inquiry">Inquiry</option> 
							<option class="table-status-input-option" value="appointment">Appointment</option> 
							<!-- <option value="all">All</option> -->
						</select>
					</th>
					<th class="enquiry-status-hidden">
						Inquiry Stage
					</th>
					<th>
						<select name="status_type" id="status-lead" class="form-control" style="width:auto">
							<option value="all" selected>{{ __('All Status') }}</option>
							@foreach($statuses as $status)	
								<option class="table-status-input-option" value="{{$status->title}}">{{$status->title}}</option>
							@endforeach
							<!-- <option value="all">All</option> -->
						</select>
					</th>
					<th class="enquiry-status-hidden">
						Inquiry Status
					</th>
					<th>Date to follow</th> 
					<th>Action</th>
				</tr>
			</thead>
		</table>
		</div>
		<div class="row">
			<div class="col-md-6 py-3 custom-info text-left"></div>
			<div class="col-md-6 py-3 custom-pagination d-flex justify-content-end"></div>
		</div>
		<!--end: Datatable-->
	</div>
</div>
<!--end::Card-->
	</div>
</div>

	@include('leads.view_enquiry_modal', ['statuses' => $statuses])
  
@endsection

{{-- Styles Section --}}
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection


{{-- Scripts Section --}}
@section('scripts')
    {{-- vendors --}}
	
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
	<script src="http://cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js"></script>

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

			$(document).on('click', '.view-in-modal', function (e) { 
				let external_id = $(e.target).data('enquiry-id');
				
				$.ajax({
					url: '{!! route('leads.detailbyid') !!}',
					type: "POST",
					cache: false,
					data: {
						_token: "{{ csrf_token() }}",
						external_id: external_id
					},
					success: function (res) {  
						console.log(res);
						$("#distributor-modal").html(res.distributor);
						$("#client-name-modal").html(res.client_name);
						$("#client-gender-modal").html(res.gender);
						$("#client-contact-number-modal").html(res.contact_number);
						$("#client-email-modal").html(res.email);
						$("#client-address-modal").html(res.address);
						$("#enquiry-for-modal").html(res.enquiry_for);
						$("#enquiry-type-modal").html(res.enquiry_type);
						$("#enquiry-response-modal").html(res.enquiry_response);
						$("#date-to-follow-modal").html(res.date_to_follow);
						$("#source-of-enquiry-modal").html(res.source_of_enquiry);
						$("#lead-representative-modal").html(res.lead_representative);
						$("#lead-status-modal").val(res.lead_status);
						$("#status_id").val(res.external_id);
						$("#enquiry-description-modal").html(res.description);

					}
				})
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

		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
		$.fn.dataTable.moment( 'DD-MM-YYYY');
		var KTDatatablesDataSourceAjaxServer = {
		init: function () {

			if(is_system_user) {
				var print_columns = [0, 3, 4, 5, 6, 7, 9, 11, 13]
			} else {
				var print_columns = [3, 4, 5, 6, 7, 9, 11, 13]
			}

			var table = $("#kt_datatable").DataTable({
				"aaSorting": [],
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				ajax: '{!! route('leads.alldata') !!}',
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
					$('.dataTables_paginate').appendTo('.custom-pagination'); 
				},
				columns: [
					{data: 'distributor', name: 'distributor', visible: false},    
					{data: 'distributor', name: 'distributor', visible: is_system_user, width: '10%'},
					{data: 'branch', name: 'branch'},
					{data: 'branch', name: 'branch', visible: false},
					{data: 'client_name', name: 'client_name'},
					{data: 'contact', name: 'contact'},
					{data: 'email', name: 'email'},
					{data: 'enquiry_for', name: 'enquiry_for'},
					{data: 'enquiry_type', name: 'enquiry_type'},
					{data: 'enquiry_type', name: 'enquiry_type', visible: false}, 
					{data: 'stage', name: 'stage', width: "20%"},
					{data: 'stage', name: 'stage', visible: false},
					{data: 'status', name: 'status', width: "20%"},
					{data: 'status', name: 'status', visible: false},
					{data: 'date_to_follow', name: 'date_to_follow', render:function(data){
						return moment(data).format('DD-MM-YYYY'); 
					}}, 

					@if(Entrust::can('inquiry-update'))
					{ data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
					@endif
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
			$('#enquiry-type').change(function () { 
				var selected = $("#enquiry-type").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(9).search('').draw();
				} else { 
					table.column(9).search('^' + selected + '$', true, false).draw();
				}
			});
			$('#filter-branch').change(function () { 
				var selected = $("#filter-branch").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(6).search('').draw();
				} else { 
					table.column(6).search(selected, true, false).draw();
				}
			});
			$('#stage-lead').change(function () { 
				var selected = $("#stage-lead").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(11).search('').draw();
				} else { 
					table.column(11).search(selected, true, false).draw();
				}
			});
			$('#status-lead').change(function () { 
				var selected = $("#status-lead").val();// option:selected

				if (selected == "all" || selected == null || selected == "") {
					table.column(13).search('').draw();
				} else { 
					table.column(13).search(selected, true, false).draw();
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

				// table.columns().every( function() {	
				// 	var that = this;
			
				// 	$('input', this.footer()).on('keyup change', function() {
				// 		if (that.search() !== this.value) {
				// 			that
				// 				.search(this.value)
				// 				.draw();
				// 		}
				// 	});
				// }); 
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