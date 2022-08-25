@extends('layouts.default')
@section('title', $report->name)
@section('content')
@include('layouts.alert')
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-chart text-primary"></i>
            </span>
            <h3 class="card-label">
                {{ __('Edit report :report' , ['report' => '(' . $report->name. ')']) }}
                <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
            </h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('reports.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">

        {!! Form::model($report, [
            'method' => 'PATCH',
            'route' => ['reports.update', $report->external_id],
            'id' => 'reportEditForm',
            'files' => true
        ]) !!}

            @include('reports.form', ['submitButtonText' => __('Update Report')])
    
        {!! Form::close() !!}
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
$(document).ready(function () {

    $(document).on('click', '#runReport', function (e) { 
        
        if($("#reportEditForm").valid()) {
            var form_data = $("#reportEditForm").serialize();
            var query = reportQueryBuilder.queryBuilder('getSQL', $(this).data('stmt'));

            var rules_set = reportQueryBuilder.queryBuilder('getRules');

            $.ajax({
                url: '{!! route('reports.runWithoutSave') !!}', 
                type: "POST",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    rules_query: query.sql,
                    form_data: form_data,
                    rules_set: JSON.stringify(rules_set),
                },
                success: function (res) {   
                    $('.dataTables_info').html("");
                    $('.dataTables_paginate').html(""); 
                    $('.dataTables_filter').html(""); 
                    $('.dataTables_length').html("");

                    $('#report-table').DataTable().clear().destroy();
                    $("#reportResult").slideDown(); 
                    $("#dynamic_table").html(res); 
                    initDataTable();
                }
            }) 
        }
    });

    function initDataTable()
    {
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
												alignment: 'left',
												fontSize: 14, 
												text: $("#name").val(),
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
    }

    $("#reportEditForm").submit(function (e) {
        e.preventDefault();
        $("#submitReport").attr('disabled', true);
        if($(this).valid()) {
            var form_data = $("#reportEditForm").serialize();
            var query = reportQueryBuilder.queryBuilder('getSQL', $(this).data('stmt'));

            var rules_set = reportQueryBuilder.queryBuilder('getRules');

            $.ajax({
                url: '{!! route('reports.store') !!}',
                type: "POST",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    rules_query: query.sql,
                    form_data: form_data,
                    rules_set: JSON.stringify(rules_set),
                },
                success: function (res) {  
                    if(res.status == true) {
                        Swal.fire({ 
                            icon: 'success',
                            title: res.message,
                            showConfirmButton: false, 
                        }); 
                        reload_page();
                    } else {
                        alert("Something went to wrong");
                    }
                    
                }
            }) 
        }
        $("#submitReport").attr('disabled', true);
    });
            
    $("#reportEditForm").validate({
        rules: {
            name: {
                required: true, 
                remote: {
                    url: '{!! route('reports.checkname') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: function () {
                            return $("#name").val();
                        },
                        id: function () {
                            return $("#id").val();
                        },
                        distributor_id : function () {
                            return $("#distributor_id").val();
                        },
                    },
                }, 
            },  
            module: {
                required: true,
            },
            "select_columns[]" : {
                required: true,
            },
        },
        messages: { 
            name: {
                required: "Please enter report name!",
                remote: "Report name already exist!",  
            },    
            module: {
                required: "Please select module!",
            },
            "select_columns[]" : {
                required: "Please select columns!",
            },
        },
        normalizer: function( value ) { 
            return $.trim( value );
        },
        errorElement: "span",
        errorClass: "form-text text-danger",
        highlight: function(element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        },
        errorPlacement: function(error, element) {
            $(element).closest('.form-group-error').append(error);
        }
    });
    
    updateWindowTitle()
    $(document).on('change', '#name', function (e){
        updateWindowTitle();
    });

    function updateWindowTitle()
    { 
        let title = $("#name").val();
        let title_prepend = "ND Salon Software | ";
        $("#dynamic-report-name").html(title);
        document.title = title_prepend + title;
    }

    // Reload page after 2 seconds
    function reload_page()
    {
        setTimeout(() => {
            window.location = "{{ route('reports.index') }}";
        }, 2000);
    } 
});
</script>
@stop