{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Products List')

{{-- Content --}}
@section('content')
<div class="row">
	<div class="col-lg-12">   
		@if(Session::has('success')) 
			<div class="alert alert-success" role="alert">
				{{Session::get('success') }}
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="ki ki-close"></i></span>
				</button>
			</div>
		@endif
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
	<!--begin::Card-->
<div class="card card-custom">
	<div class="card-header">
		<div class="card-title">
			<span class="card-icon">
				<i class="flaticon2-chart text-primary"></i>
			</span>
			<h3 class="card-label">Reports</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('reports-create') && !$allow_view_only)
			<a href="{{ route('reports.create') }}" class="btn btn-primary">Create Report</a>
		@endif

			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline"> </div>
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
			<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important; width:100%">
				<thead>
					<tr> 
						<th>Salon</th> 				
						<th>
							<select name="distributor_filter" class="form-control" id="distributor_filter">
								<option value="" data-searchid>All Salons</option>
								@foreach($distributors as $distributor) 
									<option value="{{ $distributor->name }}" data-searchid="{{ $distributor->id }}">{{ $distributor->name }}</option>
								@endforeach
							</select>
						</th> 
						<th>Name</th>
						<th>Module</th> 				
						<th class="action-header">Actions</th>
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

		$(document).ready(function () {
			$(document).on('click', '.delete-reports', function (e){ 

				let external_id = $(e.target).closest('form').find('.reports_id').val(); 
  
				Swal.fire({ 
					text: "Do you want to remove this report ?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#ccc',
					confirmButtonText: 'Delete'
				}).then((result) => { 
					if (result.isConfirmed) {
						$.ajax({
							url: '{!! route('reports.delete') !!}',
							type: "POST",
							dataType: 'json',
							cache: false,
							data: {
								_token: "{{ csrf_token() }}", 
								external_id: external_id
							},
							success: function (res) {
								Swal.fire(
									'Deleted!',
									'Report Deleted Successfully!.',
									'success'
								);
								reload_page();
							}
						}) 
					} 
				}); 
			});
		});

		// Reload page after 2 seconds
		function reload_page()
		{
			setTimeout(() => {
				location.reload();
			}, 2000);
		}

		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";

		var KTDatatablesDataSourceAjaxServer = {
			init: function () {
				$("#kt_datatable").DataTable({
					"aaSorting": [],
					processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					ajax: '{!! route('reports.data') !!}',
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
						{data: 'distributor', name: 'distributor', visible: false,},    
						{data: 'distributor', name: 'distributor', visible: is_system_user, width: "15%"},
						{data: 'name', name: 'name', width: "50%"},  
						{data: 'module', name: 'module'},  
					
						@if(Entrust::can('reports-view'))
							{ data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions', width: "20%"},
						@endif 
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