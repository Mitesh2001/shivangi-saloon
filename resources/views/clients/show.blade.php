@extends('layouts.default')
@section('content') 
@include('layouts.alert')
<div class="card card-custom mb-5">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-menu text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Client Details') }}</h3>
        </div> 
        <div class="mt-3">
            @if($ref == "dashboard")	
            	<a href="{{ route('dashboard') }}" class="btn btn-light-primary font-weight-bold">Back</a>
            @else
            	<a href="{{ route('clients.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
            @endif
        </div>
    </div>
    <div class="card-body detail-parent">
		<div class="card rounded gradient-detail-card mb-6 shadow-sm">
			<div class="card-body">
				<div class="row"> 
					<div class="col-md-6">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Name</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->name }}</h3>
						</div>
					</div>    
					<div class="col-md-6 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Email</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->primaryContact->email }}</h3>
						</div>
					</div>  
				</div> 
				<div class="row mb-lg-4 mb-md-2">
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Contact Number</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->primaryContact->primary_number }}</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">WhatsApp Number</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->primaryContact->secondary_number }}</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Birthday</span> 
							@if(!empty($client->date_of_birth) && $client->date_of_birth != "0000-00-00") 
								<h3 class="text-white font-size-17 font-weight-bold">{{ date('d-m-Y',strtotime($client->date_of_birth)) }}</h3>
							@else 
								<h3></h3>
							@endif 
						</div>
					</div> 
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Anniversary</span> 
							@if(!empty($client->anniversary) && $client->anniversary != "0000-00-00") 
								<h3 class="text-white font-size-17 font-weight-bold">{{ date('d-m-Y',strtotime($client->anniversary)) }}</h3>
							@else 
								<h3></h3>
							@endif 
						</div>
					</div> 
				</div>  
				<div class="row mb-lg-4 mb-md-2">
					<!--begin::Info-->  
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Country</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->getCountry->name ?? "" }}</h3>
						</div>
					</div>  
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">State</span>
							@if($client->state_id == 0)
								<h3 class="text-white font-size-17 font-weight-bold">{{ $client->state_name ?? "" }}</h3>
							@else
								<h3 class="text-white font-size-17 font-weight-bold">{{ $client->getState->name ?? "" }}</h3>
							@endif
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">City</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->city }}</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Zip Code</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->zipcode }}</h3>
						</div>
					</div>  
				</div> 
				<div class="row mb-lg-4 mb-md-2">
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Gender</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->gender }}</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Client Type</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->client_type }}</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Notes</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->notes }}</h3>
						</div>
					</div> 
					<!--begin::Info-->  
					<div class="col-md-6 col-sm-12">
						<div class="mb-8 d-flex flex-column">
							<span class="text-light font-size-14 mb-3">Address</span>
							<h3 class="text-white font-size-17 font-weight-bold">{{ $client->address }}</h3>
						</div>
					</div>   
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="card card-custom mb-4 bg-diagonal bg-diagonal-info shadow-sm">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between p-4 flex-lg-wrap flex-xl-nowrap">
							<div class="d-flex align-items-center justify-content-between  mr-5">
								<i class="far fa-calendar-alt text-white icon-4x"></i>
								<p class="font-weight-bolder font-size-h1 ml-5 mt-5 text-white">{{ $appointment_count }}</p>
							</div>
							<div class="ml-6 ml-lg-0 ml-xxl-6 flex-shrink-0">
								@if(!$allow_view_only)
									<a href="{{ route('appointments.create') . '?client='. $client->external_id }}" target="_blank" class="btn font-weight-bolder text-uppercase btn-pink shadow py-4 px-6"> <i class="fas fa-plus text-white"></i> Appointment</a>
								@endif 
							</div>
						</div>
					</div>
				</div> 
			</div>
			<div class="col-md-4">
				<div class="card card-custom mb-6 bg-diagonal bg-diagonal-info shadow-sm">
					<div class="card-body">
						<div class="d-flex align-items-center justify-content-between p-4 flex-lg-wrap flex-xl-nowrap">
							<div class="d-flex align-items-center justify-content-between  mr-5">
								<i class="fas fa-phone text-white icon-4x"></i>
								<p class="font-weight-bolder font-size-h1 ml-5 mt-5 text-white">{{ $enquiry_count }}</p>
							</div>
							<div class="ml-6 ml-lg-0 ml-xxl-6 flex-shrink-0">
								@if(!$allow_view_only)
									<a href="{{ route('leads.create') . '?client='. $client->external_id }}" target="_blank" class="btn font-weight-bolder text-uppercase btn-pink shadow py-4 px-6"> <i class="fas fa-plus text-white"></i> Inquiry &nbsp; &nbsp; &nbsp; &nbsp;</a>
								@endif 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
    </div>
</div> 
 
<div class="card card-custom mb-5">
    <div class="card-header">
		<div class="card-title">
			<span class="card-icon">
				<i class="flaticon2-supermarket text-primary"></i>
			</span>
			<h3 class="card-label">Orders</h3> 
		</div>
		<div class="card-toolbar">
            @if(\Entrust::can('product-create') && !$allow_view_only)
                <a href="{{ route('orders.create', ['client_id' => $client_id]) }}" class="btn btn-primary mr-3">Add Order</a> 
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
                    <th>Order ID</th>
                    <th>Products/Services</th>
                    <th> 
                    {!!
                        Form::select('payment_mode',
                        $payment_modes,
                        isset($data['payment_mode']) ? $data['payment_mode'] : null, 
                        ['class' => 'form-control ui search selection top right pointing payment_mode-select',
                        'id' => 'payment_mode-select','required'])
                    !!}
                    </th>
                    <th class="hidden-heading">Payment Mode</th>
                    <th>Amount</th>
                    <th> 
						<!-- <select name="payment_pending" class="form-control payment-pending" id="payment-select">
							<option value=""><i class="flaticon-clock-2 text-light"></i> Payment Pending</option>
							<option value="Yes">Yes</option>
							<option value="No">No</option>
						</select>  -->
						<select name="payment_pending" id="payment-select" class="selectpicker form-control">
							<option value="" data-icon="fas fa-clock"></option>
							<option value="yes">Yes</option>
							<option value="no">No</option>
						</select> 
                    </th>
                    <th class="hidden-heading">Payment Pending</th>
                    <th width="20%">Date of Order</th>  
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

<div class="card card-custom mb-5">
    <div class="card-header">
		<div class="card-title">
			<span class="card-icon">
				<i class="flaticon2-supermarket text-primary"></i>
			</span>
			<h3 class="card-label">Appointment Images</h3> 
		</div>
		<div class="card-toolbar"> 
			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-3"></div> 
		</div>
	</div>
    <div class="card-body overflow-auto">   
		<div class="timeline timeline-1">
			<div class="timeline-sep bg-primary-opacity-20"></div>
			@if(!empty($appointments))
				@foreach($appointments as $appointment)
					<div class="timeline-item pr-5">
						<div class="timeline-label">{{ date('d-m-Y', strtotime($appointment->date)) }}</div>
						<div class="timeline-badge">
							<i class="flaticon2-image-file text-primary "></i>
						</div>
						<div class="timeline-content text-muted font-weight-normal">
							@php
								$services = $appointment->services->pluck('name')->toArray();
								$appointment_for = implode(', ',$services)
							@endphp 
							<h6><b>Appointment For :</b> {{ $appointment_for }}</h6>  
							<div class="timeline-image-grid" style="display: flex; flex-wrap: wrap;">
								@foreach($appointment->getImages as $image)
								<div class="image-form d-flex flex-column mt-5 mr-6">
									<a href="{{ asset($image->image) }}" target="_blank" id="timeline_image_link_{{ $image->id }}">
										<img src="{{ asset($image->image) }}" class="timeline-image" id="timeline_image_{{ $image->id }}" height="75">
									</a> 
									@if(!$allow_view_only)
									<div class="button-grid d-flex">
										<form action="{{ route('appointments.deleteImage') }}" method="post">
											@csrf
											<input type="hidden" id="delete_image_id_{{ $image->id }}" name="image_id" value="{{ $image->id }}">
											<button class="btn btn-icon btn-secondary btn-sm mr-2 mt-3">
												<i class="flaticon2-rubbish-bin"></i>
											</button> 
										</form> 
										<form id="update_image_{{ $image->id }}" action="#">
											@csrf
											<input type="hidden" name="old_image" value="{{ $image->image }}">
											<input type="hidden" name="appointment_id" value="{{ $image->appointment_id }}">
											<input type="hidden" name="image_id" class="image_id image_id_{{ $image->id }}" id="image_id_{{ $image->id }}" value="{{ $image->id }}">
											<label class="btn btn-icon btn-primary btn-sm mt-3">
												<i class="fas fa-pen"></i> <input type="file" name="new_image" onChange="updateImage({{ $image->id }})" hidden>
											</label>  
										</form>
									</div>
									@endif 
								</div> 
								@endforeach 
							</div>
						</div>
					</div> 
				@endforeach
			@endif 
		</div>
	</div>
</div> 
@stop
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('scripts')
    {{-- vendors --}}
    <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

    {{-- page scripts --}}
    <script>  
	
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

		function updateImage(id)
		{ 
			let form = document.getElementById(`update_image_${id}`);
			let form_data = new FormData(form);  
			
			$.ajax({
				url: '{!! route('appointments.updateImage') !!}',
				type: "POST",
				cache: false,
				processData: false,
    			contentType: false,
				data: form_data,
				success: function (data) {
					if(data.status == true) {
						Swal.fire({ 
							icon: 'success',
							title: 'Image successfully updated!', 
						});
						$(`#timeline_image_${id}`).attr('src', data.image_src);
						$(`#timeline_image_link_${id}`).attr('href', data.image_src);
						$(`#delete_image_id_${id}`).val(data.uploaded_id);
						$(`#image_id_${id}`).val(data.uploaded_id);
					} else {
						Swal.fire({ 
							icon: 'error',
							text: data.error, 
						});
					}
				}
			})
		}
 
		$('#payment_mode-select').change(function () { 
			var selected = $("#payment_mode-select").val();// option:selected
			console.log(selected);
			if (selected == "" || selected == null ) {
				$('#kt_datatable').DataTable().column(2).search('').draw();
			} else { 
				$('#kt_datatable').DataTable().column(2).search('^' + selected + '$', true, false).draw();
			}
		}); 
 
		$('#payment-select').change(function () { 
			var selected = $("#payment-select").val();// option:selected
			console.log(selected);
			if (selected == "" || selected == null ) {
				$('#kt_datatable').DataTable().column(5).search('').draw();
			} else { 
				$('#kt_datatable').DataTable().column(5).search('^' + selected + '$', true, false).draw();
			}
		}); 

		var client_id = "{{$client_id}}";
 
		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
		var exportTitle = "<?php echo $export_title ?? "ND Salon Software"; ?>";
		
		var KTDatatablesDataSourceAjaxServer = {
			init: function () { 

				$("#kt_datatable").DataTable({
					"aaSorting": [],
					processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					ajax: '{!! route('orders.data') !!}?client_id='+client_id,
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
							title: exportTitle,
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7],
							},
						},
						{
							extend: 'copy',
							title: exportTitle,
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7],
							},
						},
						{
							extend: 'excel',
							title: exportTitle,
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7],
							},
						},
						{
							extend: 'csv',
							title: exportTitle,
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7],
							},
						},
						{
							extend: 'pdf',
							title: exportTitle,
							exportOptions: {
								columns: [0, 1, 3, 4, 6, 7],
							},
						},
					],
					autoWidth: false,
					initComplete: (settings, json)=>{
					$('.dataTables_info').appendTo('.custom-info');
					$('.dataTables_paginate').appendTo('.custom-pagination'); 
					$('.dataTables_filter').appendTo('.custom-searchbar'); 
					$('.dataTables_length').appendTo('.custom-records-per-page'); 
				},
					initComplete: (settings, json)=>{
											$('.dataTables_info').appendTo('.custom-info');
					$('.dataTables_paginate').appendTo('.custom-pagination'); 
					$('.dataTables_filter').appendTo('.custom-searchbar'); 
					$('.dataTables_length').appendTo('.custom-records-per-page'); 
					},
					columns: [
						{data: 'order_uid', name: 'order_uid'},
						{data: 'products', name: 'products'},
						{data: 'payment_mode', name: 'payment_mode', orderable: false, width: "14%"},
						{data: 'payment_mode', name: 'payment_mode', visible: false},
						{data: 'final_amount', name: 'final_amount'},
						{data: 'is_payment_pending', name: 'is_payment_pending', orderable: false, width: "5%"},
						{data: 'is_payment_pending', name: 'is_payment_pending', visible: false},
						{data: 'created', name: 'created'}, 	 
						{data: 'action', name: 'action'}, 	 
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
 
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection