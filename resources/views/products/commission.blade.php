{{-- Extends layout --}}
@extends('layouts.master')

@section('title', $page_title)

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
				<i class="flaticon2-supermarket text-primary"></i>
			</span>
			<h3 class="card-label">{{ $page_title }}</h3>
		</div>
		<div class="card-toolbar">  
			@if(!$is_profile_view)
			{!! Form::open([
				'route' => 'product.resetCommission',
				'class' => 'ui-form',
				'id' => 'productCreateForm', 
				'onsubmit' => "return confirm('Do you want to reset commission to default?');"
			]) !!}
			<input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
			<button class="btn btn-primary mr-3">Reset Commission</button> 
			{!! Form::close() !!}
			@endif
			<a href="{{ url()->previous() }}" class="btn btn-light-primary font-weight-bold">Back</a>
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
                        <th>Name</th>					
                        <th>SKU Code</th>					
                        <th width="5%">Commission</th>					
                        <th>Sales Price</th>					
                        <th>GST</th>					
                        <th class="action-header">
							Commission Amount <br>	
							<small>After SGST & CGST</small>
						</th>
                        <th class="action-header">
							Commission Amount <br>
							<small>After IGST</small>
						</th>
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
 
	function updateCommission(product_id)
	{ 
		let employee_id = $(`#employee_id_${product_id}`).val();
		let commission = $(`#commission_input_${product_id}`).val();

		if (commission > 100){
			alert("can not set commission more than 100%");
			$(`#commission_input_${product_id}`).val('100');
			return false;
		} 

		$.ajax({
			url: '{!! route('product.updateCommission') !!}',
			type: "POST",
			cache: false,
			data: {
				_token: "{{ csrf_token() }}",
				employee_id: employee_id,
				product_id: product_id,
				commission: commission,
			},
			beforeSend: function() {
				Swal.showLoading()
			},
			success: function (res) {     
				if(res.status == true) {
					Swal.fire({
						icon: 'success',
						text: res.message,
					})
					$(`#commission_inter_state_${product_id}`).html(res.commission_inter_state);
					$(`#commission_other_state_${product_id}`).html(res.commission_other_state);
				} else { 
					Swal.fire({
						icon: 'error',
						text: res.message,
					})
					reload_page()
				}
			}
		})
	}

	// Reload page after 2 seconds
	function reload_page()
	{
		setTimeout(() => {
			location.reload();
		}, 2000);
	}

	var KTDatatablesDataSourceAjaxServer = {
		init: function () { 

			$("#kt_datatable").DataTable({
				"aaSorting": [],
				processing: true,
				serverSide: true,
				"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				ajax: {
					url: '{!! route('product.commissionData') !!}',
					data: { 
						user_id: "{{ $user->id }}", 
						is_profile_view: "{{ $is_profile_view }}", 
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
					$('.dataTables_paginate').appendTo('.custom-pagination'); 
				},
				columns: [ 
					{data: 'namelink', name: 'name'},  
					{data: 'sku_code', name: 'sku_code'},  
					{data: 'commission', name: 'commission'},  
					{data: 'sales_price', name: 'sales_price'},  
					{data: 'gst', name: 'gst'},  
					{data: 'commission_inter_state', name: 'commission_inter_state'},  
					{data: 'commission_other_state', name: 'commission_other_state'},  
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

	$('#type_filter').change(function () { 
		var selected = $("#type_filter").val();// option:selected

		if (selected == "all" || selected == null || selected == "") {
			$("#kt_datatable").DataTable().column(6).search('').draw();
		} else { 
			$("#kt_datatable").DataTable().column(6).search(selected, true, false).draw();
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
  
		// repeating mask
		$(".form-control-solid").inputmask({  
			"mask": "9",
			"repeat": 3,
			"greedy": false
		}); // ~ mask "9" or mask "99" or ... mask "9999999999"
	});
</script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection