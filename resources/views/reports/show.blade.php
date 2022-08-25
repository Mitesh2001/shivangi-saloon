@extends('layouts.default')
@section('title', $report['name'])
@section('content')
@push('scripts')
    <script>
        $(document).ready(function () { 
            
        });
    </script>
@endpush
 
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-chart text-primary"></i>
            </span>
            <h3 class="card-label">{{ __("View Report : ". $report['name']) }}</h3>
        </div>
        <div class="card-toolbar">
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
						<li class="navi-header font-weight-bolder text-uppercase  d-flex justify-content-around text-primary pb-2">Choose an option:</li>
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
			
            <a href="{{ route('reports.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div> 
    </div>
    <div class="card-body remove-padding-mobile"> 
        <div class="card">  
            <div class="card-body"> 
				<div class="row">
					<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
					<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
				</div>
				<div class="table-responsive">
                    {!! $table !!} 
                </div> 
				<div class="row">
					<div class="col-md-6 py-3 custom-info text-left"></div>
					<div class="col-md-6 py-3 custom-pagination d-flex justify-content-end"></div>
				</div>
            </div>   
        </div>
    </div>
</div>
<!--end::Card-->

@stop
@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection
@section('scripts') 
{{-- vendors --}}
<script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function (){

    var report_data = <?php print_r($result); ?>;
    var error = <?php echo $error; ?>;
    var error_message = "<?php echo $error_message; ?>";
  
    if(error === 1) {
        $("#report-table").html(error_message);
    } else {
        
        let columns = Object.keys(report_data[0]); 

        let head_columns_html = "";
        columns.forEach(function (column, v){ 
            head_columns_html += `<th>${column}</th>`;
        });
        $("#table-head").html(head_columns_html);  
         
        let html = ""
        report_data.forEach(function (data, i) {
            html += "<tr>"; 
            let tmp_data = Object.values(data);
            let td_html = "";
            tmp_data.forEach(function (td, i) {
                td_html += "<td>"+td+"</td>";
            });
            html += td_html;
            html += "</tr>"; 
        });
        $("#report-body").html(html);
        
        "use strict";
		var KTDatatablesDataSourceAjaxServer = {
			init: function () {
				$("#report-table").DataTable({
					"aaSorting": [], 
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], 
					name:'search',
					language: {
						searchPlaceholder: "Type here...",
					}, 
					autoWidth: false, 
					initComplete: (settings, json)=>{
						$('.dataTables_info').appendTo('.custom-info');
						$('.dataTables_paginate').appendTo('.custom-pagination'); 
						$('.dataTables_filter').appendTo('.custom-searchbar'); 
						$('.dataTables_length').appendTo('.custom-records-per-page');  
					},
                    buttons: [
						'colvis',
						{
							extend: 'print', 
						},
						{
							extend: 'copy', 
						},
						{
							extend: 'excel', 
						},
						{
							extend: 'csv', 
						},
						{
							extend: 'pdf',
							customize: function (doc) {
								//Remove the title created by datatTables
								doc.content.splice(0,1);
								//Create a date string that we use in the footer. Format is dd-mm-yyyy
								var now = new Date();
								var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear(); 
								doc['header']=(function() {
									return {
										columns: [  
											{
												// alignment: 'center',
												fontSize: 14, 
												text: document.title,
											}, 
										],
										margin: 20
									}
								}); 
								doc['footer']=(function() {
									return {
										columns: [   
											{
												alignment: 'right',
												fontSize: 8, 
												text: ['Created on: ', { text: jsDate.toString() }]
											}
										],
										margin: 20
									}
								}); 
								// Change dataTable layout (Table styling)
								// To use predefined layouts uncomment the line below and comment the custom lines below
								// doc.content[0].layout = 'lightHorizontalLines'; // noBorders , headerLineOnly
								var objLayout = {};
								objLayout['hLineWidth'] = function(i) { return .5; };
								objLayout['vLineWidth'] = function(i) { return .5; };
								objLayout['hLineColor'] = function(i) { return '#aaa'; };
								objLayout['vLineColor'] = function(i) { return '#aaa'; };
								objLayout['paddingLeft'] = function(i) { return 4; };
								objLayout['paddingRight'] = function(i) { return 4; };
								doc.content[0].layout = objLayout;
							}	
						},
					],
				})
			}
		};
		jQuery(document).ready((function () {
			KTDatatablesDataSourceAjaxServer.init()
		}));

		$('.export-print').click(() => {
			$('#report-table').DataTable().buttons(0,1).trigger()
		})
		$('.export-copy').click(() => {
			$('#report-table').DataTable().buttons(0,2).trigger()
		})
		$('.export-excel').click(() => {
			$('#report-table').DataTable().buttons(0,3).trigger()
		})
		$('.export-csv').click(() => {
			$('#report-table').DataTable().buttons(0,4).trigger()
		})
		$('.export-pdf').click(() => {
			$('#report-table').DataTable().buttons(0,5).trigger()
		})
 
		$( document ).ajaxComplete(function() {
            // Required for Bootstrap tooltips in DataTables
            $('[data-toggle="tooltip"]').tooltip({
                "html": true,
                "delay": {"show": 100, "hide": 0},
            });
        });
    }

});
</script> 
@stop
