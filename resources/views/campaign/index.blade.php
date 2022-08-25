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
		<div class="card-title remove-flex">
			<span class="card-icon">
				<i class="flaticon2-percentage text-primary"></i>
			</span>
			<h3 class="card-label">All Campaigns</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('deal-create'))
            <a href="{{ route('campaigns.createEmail') }}" class="btn btn-primary mr-3">Create Email Campaign</a>
            <a href="{{ route('campaigns.createSMS') }}" class="btn btn-primary mr-3">Create SMS Campaign</a>
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
		<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr> 				
					<th>Name</th> 		 				
					<th>Segament</th> 		 				
					<th>Deal Code</th> 		 				
					<th>Validity</th> 		 				
					<th>Start At</th> 		 				
					<th>End At</th> 		 				
					<th>Is Active</th> 		 				
					<th class="action-header">Action</th>
				</tr>
			</thead>
		</table>
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
			$(document).on('click', '.delete-deal', function (e){ 

				let external_id = $(e.target).closest('form').find('.deal_id').val(); 
  
				Swal.fire({ 
					text: "Do you want to archive & inactive this deal?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#ccc',
					confirmButtonText: 'Delete'
				}).then((result) => { 
					if (result.isConfirmed) {
						$.ajax({
							url: '{!! route('deals.archive') !!}',
							type: "POST",
							dataType: 'json',
							cache: false,
							data: {
								_token: "{{ csrf_token() }}", 
								external_id: external_id
							},
							success: function (res) {
								Swal.fire(
									'Archived!',
									'Deal archived successfully!',
									'success'
								);
								reload_page();
							}
						}) 
					} 
				});  
			})
			$(document).on('change', '.toggle-deal-status', function (e){ 

				let external_id = $(e.target).closest('tr').find('.deal_id').val(); 
				
				if($(e.target).prop('checked') == true) {
					var is_active = 1;
				} else {
					var is_active = 0;
				}
   
				$.ajax({
					url: '{!! route('deals.toggleDealStatus') !!}',
					type: "POST",
					dataType: 'json',
					cache: false,
					data: {
						_token: "{{ csrf_token() }}", 
						external_id: external_id,
						is_active: is_active,
					},
					success: function (res) {
						Swal.fire(
							res.title,
							res.message,
							'success'
						);
						reload_page();
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
                    ajax: '{!! route('deals.data') !!}',
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
								columns: [0, 1, 2, 3, 4]
							},
						},
						{
							extend: 'copy',
							exportOptions: {
								columns: [0, 1, 2, 3, 4]
							},
						},
						{
							extend: 'excel',
							exportOptions: {
								columns: [0, 1, 2, 3, 4]
							},
						},
						{
							extend: 'csv',
							exportOptions: {
								columns: [0, 1, 2, 3, 4]
							},
						},
						{
							extend: 'pdf',
							exportOptions: {
								columns: [0, 1, 2, 3, 4]
							},
						},
					],
                    autoWidth: false,
                    columns: [   
                        {data: 'namelink', name: 'namelink'},   
                        {data: 'segament', name: 'segament'},   
                        {data: 'deal_code', name: 'deal_code'},   
                        {data: 'validity', name: 'validity'},   
                        {data: 'start_at', name: 'start_at'},   
                        {data: 'end_at', name: 'end_at'},    
                        {data: 'is_active', name: 'is_active'},    
                        
                        @if(Entrust::can('client-delete'))
                        { data: 'action', name: 'action', width: '15%', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
                        @endif

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