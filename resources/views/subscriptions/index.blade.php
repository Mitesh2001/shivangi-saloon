{{-- Extends layout --}}
@extends('layouts.default')

@section('title', "Orders list of ". $salon_data->name)

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
		<div class="card-title remove-flex">
			<span class="card-icon">
				<i class="flaticon2-supermarket text-primary"></i>
			</span>
			<h3 class="card-label">Subscriptions</h3>
			@if(!empty($salon_data)) 
				<span class="text-muted">  (Salon Name : {{ isset($salon_data) ? $salon_data->name : null }}) </span>
			@endif 
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('subscription-create'))
			<a href="{{ route('subscriptions.create', ['salon_id' => $salon_id]) }}" class="btn btn-primary mr-3">Add Subscription</a> 
		@endif 
			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-3">
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
			<a href="{{ route('salons.index') }}" class="btn btn-light-primary font-weight-bold">Back</a> 
		</div>
	</div>
	<div class="card-body">
				<div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div>
		<div class="table-responsive">
			<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr> 
					<th>Subscription Id</th>
					<th>Mode of Payment</th>
					<th>Amount</th>
					<th>Payment Pending</th>
					<th>Date of Plan</th>
					<th>Plan Expiry</th>
					<th>Action</th>
				</tr>
			</thead>
		</table>
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
@include('layouts.modal',['modalId'=>'cancel-subscription','content'=>'Are you sure you want to cancel subscription ?','title'=>'Cancel'])
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
 
		$('#payment_mode-select').change(function () { 
			var selected = $("#payment_mode-select").val();// option:selected
			console.log(selected);
			if (selected == "" || selected == null ) {
				$('#kt_datatable').DataTable().column(3).search('').draw();
			} else { 
				$('#kt_datatable').DataTable().column(3).search('^' + selected + '$', true, false).draw();
			}
		}); 
 
		$('#payment-select').change(function () { 
			var selected = $("#payment-select").val();// option:selected
			console.log(selected);
			if (selected == "" || selected == null ) {
				$('#kt_datatable').DataTable().column(6).search('').draw();
			} else { 
				$('#kt_datatable').DataTable().column(6).search('^' + selected + '$', true, false).draw();
			}
		}); 

		var salon_id = "{{$salon_data->id}}";
 
		"use strict";
		var KTDatatablesDataSourceAjaxServer = {
			init: function () {

				$("#kt_datatable").DataTable({
					"aaSorting": [],
					processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					ajax: '{!! route('subscriptions.data') !!}?salon_id='+salon_id,
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
								columns: [0, 1, 3, 4, 6, 7]
							},
						},
						{
							extend: 'copy',
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7]
							},
						},
						{
							extend: 'excel',
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7]
							},
						},
						{
							extend: 'csv',
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7]
							},
						},
						{
							extend: 'pdf',
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7]
							},
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
						{data: 'subscription_id', name: 'subscription_id'}, 
						{data: 'payment_mode', name: 'payment_mode', orderable: false}, 
						{data: 'final_amount', name: 'final_amount'},
						{data: 'is_payment_pending', name: 'is_payment_pending', orderable: false, width: "5%"},
						{data: 'is_payment_pending', name: 'is_payment_pending', visible: false},
						{data: 'subscription_expiry_date', name: 'subscription_expiry_date'}, 	
						{data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
					]
				})
			}
		};
		jQuery(document).ready((function () {
			KTDatatablesDataSourceAjaxServer.init()
		}));

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


		jQuery(document).on('click','.filter-submit',function(){
			var payment_pending = $('.payment-pending').val();
			table.destroy();
			// generateDataTable(payment_pending);
		});
		jQuery(document).on('click','.filter-reset',function(){
			$('.payment-pending').val('0');
			table.destroy();
			// generateDataTable(payment_pending=0);
		});
		jQuery(document).ready((function () {
			// generateDataTable(payment_pending=0);
		}));
		$(document).on('click','.cancel_order',function(){ 
			Swal.fire({
				title: 'Are you sure?',
				text: "Do you want cancel order?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#ccc',
				confirmButtonText: 'remove!'
			}).then((result) => {
				var id = $(this).data('id'); 
				window.location.href = "{{ URL::to('/admin/orders/cancel' )}}/"+id; 
			})  
		});
		// $(document).on('click','.delete-record',function(){
		// 	var id = $(this).data('id');
		// 	window.location.href = "{{URL::to('/rkadmin/subscriptions/cancel')}}/"+id;
		// });

		// Reload page after 2 seconds
		function reload_page()
		{
			setTimeout(() => {
				location.reload();
			}, 2000);
		}
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection