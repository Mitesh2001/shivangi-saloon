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
				<i class="flaticon-shopping-basket text-primary"></i>
			</span>
			<h3 class="card-label">Vendors</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('vendor-create') && !$allow_view_only)
            <a href="{{ route('vendors.create') }}" class="btn btn-primary mr-3">Create Vendor</a>
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
						<th>Salon</th> 				
						<th>
							{!! Form::select('distributor_filter',
								[],
								null,	
								['class' => 'form-control','id' => 'distributor_filter', 'style' => "width:100%"])
							!!}
						</th> 		
						<th>Name</th> 		 				
						<th>Contact Person</th> 		 				
						<th>GST Number</th> 		 				
						<th>Primary Number</th> 		 				
						<th>Primary Email</th> 		 				
						<th>Contact P Number</th> 		 				
						<th>city</th> 		 				
						<th class="action-header" style="width:15%">Action</th>
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
@include('vendors.view_vendor_modal')
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

			$('#distributor_filter').select2({
				placeholder: "Salon filter",
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

			$(document).on('click', '.view-in-modal', function (e) { 
				let external_id = $(e.target).data('enquiry-id');
				
				$.ajax({
					url: '{!! route('vendors.detailbyid') !!}',
					type: "POST",
					cache: false,
					data: {
						_token: "{{ csrf_token() }}",
						external_id: external_id
					},
					success: function (res) {   
						$("#distributor-modal").html(res.distributor); 
						$("#name-modal").html(res.name); 
						$("#gst-number-modal").html(res.gst_number); 
						$("#primary-number-modal").html(res.primary_number); 
						$("#secondary-number-modal").html(res.secondary_number); 
						$("#primary-email-modal").html(res.primary_email); 
						$("#secondary-email-modal").html(res.secondary_email); 
						$("#contact-person-modal").html(res.contact_person); 
						$("#contact-person-number-modal").html(res.contact_person_number); 
						$("#contact-person-email-modal").html(res.contact_person_email); 
						$("#city-modal").html(res.city); 
						$("#zipcode-modal").html(res.zipcode); 
						$("#address-modal").html(res.address);  
					}
				})
			});  

			$(document).on('click', '.delete-vendor', function (e){ 

				let external_id = $(e.target).closest('form').find('.vendor_id').val(); 
  
				$.ajax({
					url: '{!! route('vendors.checkdelete') !!}',
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
								text: "Do you want to remove this vendor ?",
								icon: 'warning',
								showCancelButton: true,
								confirmButtonColor: '#3085d6',
								cancelButtonColor: '#ccc',
								confirmButtonText: 'Delete'
							}).then((result) => { 
								if (result.isConfirmed) {
									$.ajax({
										url: '{!! route('vendors.delete') !!}',
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
												'Vendor deleted successfully!',
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
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";

        var KTDatatablesDataSourceAjaxServer = {
            init: function () {
                $("#kt_datatable").DataTable({
					"aaSorting": [],
                    processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    ajax: '{!! route('vendors.data') !!}',
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
						{data: 'distributor', name: 'distributor', visible: false},    
						{data: 'distributor', name: 'distributor', visible: is_system_user},
                        {data: 'name', name: 'name'},   
                        {data: 'contact_person', name: 'contact_person'},       
                        {data: 'gst_number', name: 'gst_number'},       
                        {data: 'primary_number', name: 'primary_number'},       
                        {data: 'primary_email', name: 'primary_email'},       
                        {data: 'contact_person_number', name: 'contact_person_number'},       
                        {data: 'city', name: 'city', width: '15%'},       
                        
                        @if(Entrust::can('vendor-update'))
                        { data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
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