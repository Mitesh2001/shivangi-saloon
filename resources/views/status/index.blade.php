{{-- Extends layout --}}
@extends('layouts.default')

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
				<i class="flaticon2-list-3 text-primary"></i>
			</span>
			<h3 class="card-label">Statuses</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('status-create'))
            <a href="{{ route('status.create') }}" class="btn btn-primary mr-3">Create Status</a>
		@endif

			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">
 
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
			<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
				<thead>
					<tr> 				
						<th>Title</th> 				
						<th>Color</th> 				
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

			$(document).on('click', '.delete-status', function (e){ 

				let external_id = $(e.target).closest('form').find('.status_id').val(); 
  
				$.ajax({
					url: '{!! route('status.checkdelete') !!}',
					type: "POST",
					dataType: 'json',
					cache: false,
					data: {
						_token: "{{ csrf_token() }}", 
						external_id: external_id
					},
					success: function (data) {
						if(data.status === true) {
							Swal.fire({ 
								text: "Do you want to remove this status ?",
								icon: 'warning',
								showCancelButton: true,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#ccc',
								confirmButtonText: 'Delete'
							}).then((result) => { 
								if (result.isConfirmed) {
									$.ajax({
										url: '{!! route('status.delete') !!}',
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
												'Status deleted successfully!.',
												'success'
											);
											reload_page();
										}
									}) 
								} 
							});
						} else {
							Swal.fire({ 
								text: data.message,
								icon: 'error',
								showconfirmButton: false, 
							})
						}
					}
				})
			})
		});

		// Reload page after 2 seconds
		function reload_page()
		{
			setTimeout(() => {
				location.reload();
			}, 2000);
		}

	    "use strict";
        var KTDatatablesDataSourceAjaxServer = {
            init: function () {
                $("#kt_datatable").DataTable({
					"aaSorting": [],
                    processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    ajax: '{!! route('status.data') !!}',
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
                        {data: 'title', name: 'title'},  
                        {data: 'color', name: 'color'},  
                        
                        @if(Entrust::can('status-update'))
                        { data: 'action', name: 'action', width: "15%", orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
                        @endif 
                    ]
                })
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