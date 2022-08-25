{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Salon List')

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
			<h3 class="card-label">Salons</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('salon-create'))
            <a href="{{ route('salons.create') }}" class="btn btn-primary mr-3">Create Salons</a>
		@endif

			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">

			</div>
			<!--end::Dropdown-->
			@if($back_url) 
				<a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
			@endif 
		</div>
	</div>
	<div class="card-body">
				<div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div>
		<div class="table-responsive">
			<!--begin: Datatable-->
			<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important; min-height:200px !important">
				<thead>
					<tr> 				
						@if(!$is_distributor_user === true)
						<th>Distributor</th>
						@endif
						<th>Name</th> 		 				
						<th>Contact Person</th> 		 				
						<th>GST Number</th> 		 				
						<th>Primary Number</th> 		 				
						<th>Primary Email</th> 		 				
						<th>Contact P Number</th> 		 				
						<th>city</th> 		 			
						@if(Entrust::can('salon-update'))	
							<th class="action-header" style="width:15%">Action</th>
						@endif
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
@include('salons.view_vendor_modal')
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

			$(document).on('click', '.view-in-modal', function (e) { 
				let external_id = $(e.target).data('enquiry-id');
				
				$.ajax({
					url: '{!! route('salons.detailbyid') !!}',
					type: "POST",
					cache: false,
					data: {
						_token: "{{ csrf_token() }}",
						external_id: external_id
					},
					success: function (res) {  
						
						$("#logo-modal").attr('src', res.logo); 
						$("#name-modal").html(res.name); 
						$("#number-of-employees-modal").html(res.number_of_employees); 
						$("#gst-number-modal").html(res.gst_number); 
						$("#pan-number-modal").html(res.pan_number); 
						$("#primary-number-modal").html(res.primary_number); 
						$("#secondary-number-modal").html(res.secondary_number); 
						$("#primary-email-modal").html(res.primary_email); 
						$("#secondary-email-modal").html(res.secondary_email); 
						$("#contact-person-modal").html(res.contact_person); 
						$("#contact-person-number-modal").html(res.contact_person_number); 
						// $("#contact-person-email-modal").html(res.contact_person_email); 
						$("#country-modal").html(res.country); 
						$("#state-modal").html(res.state); 
						$("#city-modal").html(res.city); 
						$("#zipcode-modal").html(res.zipcode); 
						$("#address-modal").html(res.address); 
					}
				})
			});  

			$(document).on('click', '.delete-distributor', function (e){ 

				let external_id = $(e.target).closest('form').find('.distributor_id').val(); 
				  
				$.ajax({
					url: '{!! route('salons.checkdelete') !!}',
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
								text: "Do you want to remove this distributor ?",
								icon: 'warning',
								showCancelButton: true,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#ccc',
								confirmButtonText: 'Delete'
							}).then((result) => { 
								if (result.isConfirmed) {
									$.ajax({
										url: '{!! route('salons.delete') !!}',
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
												'Salons deleted successfully!',
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
                    ajax: '{!! route('salons.data') !!}',
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
						$('.dataTables_paginate').appendTo('.custom-pagination'); 
					},
                    columns: [   
						@if(!$is_distributor_user === true)
						{data: 'created_by', name: 'created_by'}, 
						@endif
                        {data: 'name', name: 'name'},   
                        {data: 'contact_person', name: 'contact_person'},       
                        {data: 'gst_number', name: 'gst_number'},       
                        {data: 'primary_number', name: 'primary_number'},       
                        {data: 'primary_email', name: 'primary_email'},       
                        {data: 'contact_person_number', name: 'contact_person_number'},       
                        {data: 'city', name: 'city'},       
                        
                        @if(Entrust::can('salon-update'))
                        { data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
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