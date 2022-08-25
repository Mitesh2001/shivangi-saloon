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
				<i class="flaticon2-location text-primary"></i>
			</span>
			<h3 class="card-label">
				Branches
				@if($is_system_user && $distributor_title)
					<span class='text-muted'>( {!! "Salon : $distributor_title" !!} )</span>
				@endif
			</h3>
		</div>
		<div class="card-toolbar">  

			@if(\Entrust::can('branch-create') && !$allow_view_only && $can_create_branch)
				@if($is_system_user || $is_distributor_user)
					@if($distributor)
						<a href="{{ route('branch.create') .'?distributor='. $distributor->external_id }}" class="btn btn-primary mr-3">Create Branch</a>
					@else 
						<a href="{{ route('branch.create') }}" class="btn btn-primary mr-3">Create Branch</a>
					@endif 
				@else 
					<a href="{{ route('branch.create') }}" class="btn btn-primary mr-3">Create Branch</a> 
				@endif 
			@endif 
			
			@if($back_url) 
				<a href="{{ $back_url }}" class="btn btn-light-primary font-weight-bold">Back</a>
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
						<th>City</th> 		 			
						<th>Contact Person</th> 				
						<th>Primary Number</th> 				
						<th>Primary Email</th> 	 
						@if(Entrust::can('branch-update'))
							<th class="action-header">Action</th>
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
@include('branch.view_branch_detail_modal')
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
					url: '{!! route('branch.detailbyid') !!}',
					type: "POST",
					cache: false,
					data: {
						_token: "{{ csrf_token() }}",
						external_id: external_id
					},
					success: function (res) {   
						$("#name-modal").html(res.name);
						$("#primary-contact-person-modal").html(res.primary_contact_person);
						$("#secondary-contact-person-modal").html(res.secondary_contact_person);
						$("#primary-contact-number-modal").html(res.primary_contact_number);
						$("#secondary-contact-number-modal").html(res.secondary_contact_number);
						$("#primary-email-modal").html(res.primary_email);
						$("#secondary-email-modal").html(res.secondary_email);
						$("#country-modal").html(res.country); 
						$("#state-modal").html(res.state); 
						$("#city-modal").html(res.city);
						$("#address-modal").html(res.address);
						$("#zipcode-modal").html(res.zipcode); 
						$("#is-pirmary-modal").html(res.is_primary); 
					}
				})
			});

			$(document).on('click', '.delete-branch', function (e){ 

				Swal.fire({ 
					text: "Do you want to remove this branch ?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#ccc',
					confirmButtonText: 'Delete'
				}).then((result) => { 
					if (result.isConfirmed) {

						let external_id = $(e.target).closest('form').find('.branch_id').val();
  
						$.ajax({
							url: '{!! route('branch.delete') !!}',
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
									'Branch Deleted Successfully!',
									'success'
								);
								reload_page();
							}
						}) 
					} 
				});
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
		var distributor_filter = <?php echo $distributor_filter; ?>;
		var distributor_id 	   = "<?php echo $distributor_id ?? 0; ?>";
  
        var KTDatatablesDataSourceAjaxServer = {
            init: function () {
                $("#kt_datatable").DataTable({
					"aaSorting": [],
                    processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    ajax: {
						url: '{!! route('branch.data') !!}',
						data: {
							distributor: distributor_id,
						},
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
                    autoWidth: false,
					initComplete: (settings, json)=>{
						$('.dataTables_info').appendTo('.custom-info');
					$('.dataTables_paginate').appendTo('.custom-pagination'); 
					$('.dataTables_filter').appendTo('.custom-searchbar'); 
					$('.dataTables_length').appendTo('.custom-records-per-page');   
					},
                    columns: [   
                        {data: 'distributor', name: 'distributor', visible: false},    
                        {data: 'distributor', name: 'distributor', visible: distributor_filter, width: '10%'},    
                        {data: 'name', name: 'name'},    
                        {data: 'city', name: 'city'},    
                        {data: 'primary_contact_person', name: 'primary_contact_person'},    
                        {data: 'primary_contact_number', name: 'primary_contact_number'},    
                        {data: 'primary_email', name: 'primary_email'},    
                        
                        @if(Entrust::can(['branch-update', 'branch-view']))
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
			
			if (selected == "" || selected == null ) {
				$("#kt_datatable").DataTable().column(0).search('').draw();
			} else { 
				$("#kt_datatable").DataTable().column(0).search('^' + selected + '$', true, false).draw();
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