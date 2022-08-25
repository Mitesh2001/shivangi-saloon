{{-- Extends layout --}}
@extends('layouts.default')

@section('title', 'Stock level')

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
				<i class="flaticon2-line-chart text-primary"></i>
			</span>
			<h3 class="card-label">Stock Level</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('incoming-stock-create') && !$allow_view_only)
			<a href="{{ route('incoming_inventory.create') }}" class="btn btn-primary mr-3">Add Stock</a>
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
						<select name="distributor_filter" id="distributor_filter" style="width:100%">
							<option value="" data-searchid>All Salons</option>
							@foreach($distributors as $distributor) 
								<option value="{{ $distributor->name }}" data-searchid="{{ $distributor->id }}">{{ $distributor->name }}</option>
							@endforeach
						</select>
					</th> 
					<th>
					{!! Form::select('branch_filter',
						[],
						null,	
						['class' => 'form-control','id' => 'branch_filter', 'style' => "width:100%"])
						!!}
					</th>
					<th class="branch-hidden">Branch</th>
					<th>Product Name</th>
					<th>SKU Code</th>
					<th>QTY</th> 					
					<th>Last Updated at</th> 			
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

@include('inventory.partials.edit_stock_level_modal')

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

			var distributor_search_id = "";

			$('#distributor_filter').select2()

			$('#distributor_filter').on('select2:select', function (e) {
				var return_option = $(this).select2().find(":selected")[0];  
				distributor_search_id = $(return_option).attr('data-searchid'); 
				$("#branch_filter").val("").trigger('change');
				$("#client_filter").val("").trigger('change');
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

			$(document).on('change', '#invoice_number', function (e) {

				let product_id = $('#product_id').val();
				let invoice_number = $(e.target).val();
  
				$.ajax({
					url: '{!! route('incoming_inventory.getInvoiceProduct') !!}',
					type: "POST",
					dataType: 'json',
					cache: false,
					data: {
						_token: "{{ csrf_token() }}", 
						product_id: product_id,
						invoice_number: invoice_number,
					},
					success: function (res) { 
						$("#date").val(res.stock_income_history.date);
						$("#product_branch").val(res.product_branch);
						$("#cost_per_unit").val(res.product.cost_per_unit);
						$("#qty").val(res.product.qty);
						$("#gst").val(res.product.gst_percent);
						$("#mrp").val(res.product.mrp);
						$("#sku_code").val(res.product.sku_code);

						// console.log(res.product);

						$("#old_date").val(res.stock_income_history.date); 
						$("#old_cost_per_unit").val(res.product.cost_per_unit);
						$("#old_qty").val(res.product.qty);
						$("#old_gst_percent").val(res.product.gst);
						$("#old_mrp").val(res.product.mrp);
						$("#branch_id").val(res.product.branch_id);
					}
				})
			});

			$(document).on('click', '.edit-stock-modal', function (e){

				$(".edit-modal-input").val("");

				let product_id = $(e.target).closest('td').find('.product_id').val();
				let product_name = $(e.target).closest('td').find('.product_name').val();
				let sku_code = $(e.target).closest('td').find('.sku_code').val();
				let qty = $(e.target).closest('td').find('.qty').val();
				let branch = $(e.target).closest('td').find('.branch').val();
				let last_updated = $(e.target).closest('td').find('.last_updated').val();
				let distributor = $(e.target).closest('td').find('.distributor').val();
  
				$("#distributor-modal").html(distributor);
				$("#total_qty").val(qty);
				$("#product_id").val(product_id);
				$("#product_name").val(product_name);
				$("#sku_code").val(sku_code);
				$("#branch").val(branch);
  
				$("#invoice_number").select2({
					placeholder: "Select Invoice Number",
					allowClear: true,
					ajax: {
						url: '{!! route('stock_master.invoiceByNumber') !!}',
						dataType: 'json', 
						processResults: function (data, param) {  
							return {
								results: $.map(data, function (item) {
									if(item.product_id == product_id && item.branch_id == branch) {
										return {
											text: item.invoice_number, 
											id: item.invoice_number,
										} 
									}  	
								})
							};
						}
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
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
		
		var KTDatatablesDataSourceAjaxServer = {
			init: function () {

				if(is_system_user) {
					var print_columns = [0, 3, 4, 5, 6, 7];
				} else {
					var print_columns = [3, 4, 5, 6, 7];
				}

				let table = $("#kt_datatable").DataTable({
					"aaSorting": [],
					processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
					ajax: '{!! route('stock_master.data') !!}',
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
								columns: print_columns
							},
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
						{data: 'branch', name: 'branch', width: '15%'},
						{data: 'branch', name: 'branch', visible: false},
						{data: 'name', name: 'name'},   
						{data: 'sku_code', name: 'sku_code'},   
						{data: 'qty', name: 'qty'},      
						{data: 'last_updated', name: 'last_updated'},    
					
						@if(Entrust::can('stock-level-update'))
							{ data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
						@endif 
					]
				})

                $('#branch_filter').change(function () { 
                    var selected = $("#branch_filter").val();// option:selected
                    
                    if (selected == "" || selected == null ) {
                        $("#kt_datatable").DataTable().column(3).search('').draw();
                    } else { 
                        $("#kt_datatable").DataTable().column(3).search('^' + selected + '$', true, false).draw();
                    }
                }); 
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
		$('#branch_filter').change(function () { 
			var selected = $("#branch_filter").val();// option:selected

			if (selected == "all" || selected == null || selected == "") {
				$("#kt_datatable").DataTable().column(2).search('').draw();
			} else { 
				$("#kt_datatable").DataTable().column(2).search(selected, true, false).draw();
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