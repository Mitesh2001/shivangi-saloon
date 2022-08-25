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
				<i class="flaticon2-tag text-primary"></i>
			</span>
			<h3 class="card-label">Tags</h3>
		</div>
		<div class="card-toolbar">
		@if(\Entrust::can('tag-create'))
            <a href="{{ route('tags.create') }}" class="btn btn-primary mr-3">Create Tag</a>
		@endif


			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">

			</div>
			<!--end::Dropdown-->
			
		</div>
	</div>
	<div class="card-body">
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
					<th>No. Of Rule</th> 		 				
					<th>Date</th> 		 	 					
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

			$(document).on('click', '.delete-tag', function (e){ 

				let external_id = $(e.target).closest('form').find('.tag_id').val(); 
  
				Swal.fire({ 
					text: "Do you want to archive this tag?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#ccc',
					confirmButtonText: 'Delete'
				}).then((result) => { 
					if (result.isConfirmed) {
						$.ajax({
							url: '{!! route('tags.archive') !!}',
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
									'Tag archived successfully!',
									'success'
								);
								reload_page();
							}
						}) 
					} 
				});  
			})

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
                    ajax: '{!! route('tags.data') !!}',
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
                    columns: [   
						{data: 'distributor', name: 'distributor', visible: false},    
						{data: 'distributor', name: 'distributor', visible: is_system_user, width: "20%"},
                        {data: 'name', name: 'name'},   
                        {data: 'number_of_rules', name: 'number_of_rules'},   
                        {data: 'date', name: 'date'},    
                        
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

		});
	</script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script>
@endsection