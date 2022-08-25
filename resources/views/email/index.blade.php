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
				<i class="flaticon2-email text-primary"></i>
			</span>
			<h3 class="form_title">Emails Template</h3>
		</div> 
		<div class="card-toolbar">
			@if(\Entrust::can('email-template-create') && !$allow_view_only)
				<a href="{{ route('emails.create') }}" class="btn btn-primary mr-3">Create Email Temaplate</a>
			@endif
		</div>
	</div>
	<div class="card-body"> 
	 <div class="row">
			<div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
			<div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
		</div>
	 	<div class="table-responsive remove-padding-mobile">
		<!--begin: Datatable-->
		<table class="table table-hover table-list" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<tr>
                    <th>Salon</th> 				
					<th>
						<select name="distributor_filter" id="distributor_filter" style="width:100%">
							<option value="" data-searchid>Select Salon</option>
							@foreach($distributors as $distributor) 
								<option value="{{ $distributor->name }}" data-searchid="{{ $distributor->id }}">{{ $distributor->name }}</option>
							@endforeach
						</select>
					</th> 
                    <th>Name</th>
					<th>Subject</th>
					<th>Event</th>
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

        $(document).ready(function (){
            
			$('#distributor_filter').select2()

            $('#distributor_filter').on('select2:select', function (e) {
                var return_option = $(this).select2().find(":selected")[0];  
                distributor_search_id = $(return_option).attr('data-searchid'); 
                $("#branch_filter").val("").trigger('change');
                $("#client_filter").val("").trigger('change');
            });
        });
 
		"use strict";
		var is_system_user = "<?php echo $is_system_user ?? 0; ?>";
 
        var KTDatatablesDataSourceAjaxServer = {
            init: function () {
                $("#kt_datatable").DataTable({
					"aaSorting": [],
                    processing: true,
					serverSide: true,
					"lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    ajax: '{!! route('emails.data') !!}',
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
					    {data: 'distributor', name: 'distributor', visible: is_system_user, width: '10%'},
                        {data: 'name', name: 'name', width: '20%'},   
                        {data: 'subject', name: 'subject'},   
                        {data: 'event', name: 'event'},   
                        
                        @if(\Entrust::can('email-template-update'))
                            { data: 'action', name: 'action', width: '10%', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
                        @else 
						{ data: 'action', name: 'action', visible: false},
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