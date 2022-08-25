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
				<i class="flaticon2-open-text-book text-primary"></i>
			</span>
			<h3 class="card-label">Day Book</h3>
		</div>
		<div class="card-toolbar">
            @if(\Entrust::can('daybook-cash-in-entry') && !$allow_view_only)
                <a href="#" class="btn btn-primary mr-3" data-toggle="modal" data-target="#cash-in-modal">Cash In</a>
            @endif
            @if(\Entrust::can('daybook-cash-out-entry') && !$allow_view_only)
            <a href="#" class="btn btn-primary mr-3" data-toggle="modal" data-target="#cash-out-modal">Cash Out</a>
            @endif

			<!--begin::Dropdown-->
			<div class="dropdown dropdown-inline mr-2">
 
			</div>
			<!--end::Dropdown-->
			
		</div>
	</div>
	<div class="card-body">
        <div class="row"> 
            @if($is_system_user == 0)
                <div class="col-md-4">
                @if(isset($branch))
                    <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $branch->distributor_id }}">
                @else
                    <div class="form-group form-group-error"> 
                        {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
                        <select name="distributor_id" id="distributor_id" class="form-control">
                            @if(isset($selected_distributor))
                                <option value="{{ $selected_distributor->id }}">{{ $selected_distributor->name }}</option>
                            @endif
                        </select>
                        @if ($errors->has('distributor_id'))  
                            <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                        @endif
                    </div> 
                @endif
                </div>
            @endif  
            <div class="col-md-4">
                <div class="form-group">
                    {{Form::label('Branch')}} :  
                    {{Form::select('branch_id', $selected_branch ?? [], null,['class'=>'form-control', 'id' => 'branch_id', 'required'])}}
                    @if ($errors->has('branch_id')) 
                        <span class="form-text text-danger">{{ $errors->first('branch_id') }}</span>
                    @endif
                </div> 
            </div>
            <div class="col-md-4 <?php echo $is_system_user != 0 ? "offset-md-4" : ''; ?>">
                {{Form::label('Date')}} * 
                <div class="form-group d-flex"> 
                    <input class="form-control" type="date" name="date_search" id="date_search" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                    &nbsp; &nbsp; &nbsp; &nbsp; <button class="btn btn-primary" id="search-button">Search</button>
                </div> 
            </div>
        </div>
		<!--begin: Datatable--> 
			
		<table class="table table-bordered table-hover" id="kt_datatable" style="margin-top: 13px !important">
			<thead>
				<th width="30%">Tender Type</th> 
				<th width="30%">Cash In</th>
				<th width="30%">Cash Out</th> 
				<th width="10%">Details</th> 
			</thead>
            <tbody id="daybook-entries">
                <!-- entries here  -->
            </tbody>
		</table>
		<!--end: Datatable-->
		 
		<!-- <table class="table table-bordered"> 
			<tr>
				<td>Opening Balance</td>
				<td><b id="total_opening_balance"></b> </td>
				<td>Closing Balance</td>
				<td><b id="total_closing_balance"></b></td>
			</tr>
		</table>  -->
	</div>
</div>
<!--end::Card-->
	</div>
</div>
@include('daybook.view_daybook_modal')
@include('daybook.cash_in_modal')
@include('daybook.cash_out_modal')
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

            $(document).on('click', '.view-in-modal', function (e) {

                let branch_id = $(e.target).closest('tr').find('.branch_id').val();
                let payment_method = $(e.target).closest('tr').find('.payment_method').val();
                let entry_type = $(e.target).closest('tr').find('.entry_type').val();
                let entry_type_string = $(e.target).closest('tr').find('.entry_type_string').val();
                let date = $(e.target).closest('tr').find('.date').val();

                let dynamic_title = payment_method +" ("+ entry_type_string + ")";
                $("#daybook-details-title").html(dynamic_title);
                
                $.ajax({
                    url: '{!! route('daybook.getEntryDetails') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        branch_id: branch_id, 
                        payment_method: payment_method, 
                        entry_type: entry_type, 
                        date: date, 
                    },
                    beforeSend: function() { 
                        $("#detail-table").html("<tr><td colspan='3' class='text-center'>loading...</td></tr>");
                    },
                    success: function (res) {

                        if(res.status == true) {
                            $("#detail-table").html(res.table);
                        } else {
                            $("#detail-table").html("<tr><td colspan='2' class='text-center'>Something went wrong!</td></tr>");
                        }

                        // $("#daybook-entries").html(data.entries);
                        // $("#total_opening_balance").html(parseInt(data.total_opening_balance));
                        // $("#total_closing_balance").html(parseInt(data.total_closing_balance) + parseInt(data.total_opening_balance));
                    }
                });
            });

            <?php if($is_system_user == 0): ?>

                $('#distributor_id').select2({
                    placeholder: "Select Salon",
                    allowClear: true,
                    ajax: {
                        url: '{!! route('salons.byname') !!}',
                        dataType: 'json', 
                        processResults: function (data, param) {  
                            return {
                                results: $.map(data, function (item) { 
                                    return {
                                        text: item.name, 
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                })
                $(document).on('change', '#distributor_id', function (e) { 
                    $("#branch_id").val("").trigger('change'); 
                });

                $('#distributor_id_cashin').select2({
                    placeholder: "Select Salon",
                    allowClear: true,
                    ajax: {
                        url: '{!! route('salons.byname') !!}',
                        dataType: 'json', 
                        processResults: function (data, param) {  
                            return {
                                results: $.map(data, function (item) { 
                                    return {
                                        text: item.name, 
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                })
 
                $(document).on('change', '#distributor_id_cashin', function (e) { 
                    $("#branch_id_cashin").val("").trigger('change'); 
                });

                $('#branch_id_cashin').select2({ // selection only for master admin
                    placeholder: "Select Branch", 
                    ajax: {
                        url: '{!! route('branch.getBranchByName') !!}',
                        dataType: 'json', 
                        data: function (params) { 
                            ultimaConsulta = params.term;
                            var distributor_id = $("#distributor_id_cashin").val();
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
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });

                $('#distributor_id_cashout').select2({
                    placeholder: "Select Salon",
                    allowClear: true,
                    ajax: {
                        url: '{!! route('salons.byname') !!}',
                        dataType: 'json', 
                        processResults: function (data, param) {  
                            return {
                                results: $.map(data, function (item) { 
                                    return {
                                        text: item.name, 
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                })
 
                $(document).on('change', '#distributor_id_cashout', function (e) { 
                    $("#branch_id_cashout").val("").trigger('change'); 
                });

                $('#branch_id_cashout').select2({ // selection only for master admin
                    placeholder: "Select Branch",
                    allowClear: true,
                    ajax: {
                        url: '{!! route('branch.getBranchByName') !!}',
                        dataType: 'json', 
                        data: function (params) { 
                            ultimaConsulta = params.term;
                            var distributor_id = $("#distributor_id_cashout").val();
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
                                        id: item.id
                                    }
                                })
                            };
                        }
                    }
                });

            <?php endif; ?>

            $('#branch_id').select2({
                placeholder: "Select Branch",
                allowClear: true,
                ajax: {
                    url: '{!! route('branch.getBranchByName') !!}',
                    dataType: 'json', 
                    data: function (params) { 
                        ultimaConsulta = params.term;
                        var distributor_id = $("#distributor_id").val();
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
                                    id: item.id
                                }
                            })
                        };
                    }
                }
            });

            getEntries();

            $(document).on('click', '#search-button', function () {
                getEntries();
            });

            function getEntries()
            {
                var date = $("#date_search").val();

                $.ajax({
                    url: '{!! route('daybook.entriesByDate') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        date: date,
                        distributor_id: $("#distributor_id").val(),
                        branch_id : $("#branch_id").val(),
                    },
                    beforeSend: function() {
                        $("#daybook-entries").html("<tr><td colspan='3' class='text-center'>loading...</td></tr>");
                    },
                    success: function (data) {
                        $("#daybook-entries").html(data.entries);
                        // $("#total_opening_balance").html(parseInt(data.total_opening_balance));
                        // $("#total_closing_balance").html(parseInt(data.total_closing_balance) + parseInt(data.total_opening_balance));
                    }
                });
            }

			$("#cashInForm").validate({
                rules: {
                    amount: {
                        required: true, 
						number: true,
						maxlength: 8,
                    }, 
                    description : {
                        required: true
                    }, 
                    distributor_id : {
                        required: true
                    },
                    branch_id : {
                        required: true
                    }
                },
                messages: { 
                    amount: {
                        required: "Please enter amount!", 
						number: "Please enter valid amount!",
						maxlength: "Please enter valid amount!"
                    },   
                    description : {
                        required: "Please Enter some description !"
                    }, 
                    distributor_id : {
                        required: "Please Select Salon !"
                    },
                    branch_id : {
                        required: "Please Select Branch !"
                    }
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

			$("#cashOutForm").validate({
                rules: {
                    amount: {
                        required: true, 
						number: true,
						maxlength: 8,
                    },  
                    payment_method: {
                        required: true,  
                    },  
                    description : {
                        required: true, 
                    },
                    distributor_id : {
                        required: true, 
                    },
                    branch_id : {
                        required: true, 
                    }
                },
                messages: { 
                    amount: {
                        required: "Please enter amount!", 
						number: "Please enter valid amount!",
						maxlength: "Please enter valid amount!"
                    },   
                    payment_method: {
                        required: "Please select payment method!",  
                    },   
                    description : {
                        required: "Please Enter some description !"
                    },
                    distributor_id : {
                        required: "Please Select Salon !"
                    },
                    branch_id : {
                        required: "Please Select Branch !"
                    }
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
  
		});

		// Reload page after 2 seconds
		function reload_page()
		{
			setTimeout(() => {
				location.reload();
			}, 2000);
		}

	    "use strict";
        // var KTDatatablesDataSourceAjaxServer = {
        //     init: function () {
        //         $("#kt_datatable").DataTable({
        //             processing: true,
        //             				serverSide: true,
				// "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        //             ajax: '{!! route('branch.data') !!}',
        //             name:'search',
					// language: {
					// 	searchPlaceholder: "Type here...",
					// },
        //             drawCallback: function(){
        //                 var length_select = $(".dataTables_length");
        //                 var select = $(".dataTables_length").find("select");
        //                 select.addClass("tablet__select");
        //             },
        //             autoWidth: false,
        //             columns: [   
        //                 {data: 'name', name: 'name'},    
        //                 {data: 'city', name: 'city'},    
        //                 {data: 'primary_contact_person', name: 'primary_contact_person'},    
        //                 {data: 'primary_contact_number', name: 'primary_contact_number'},    
        //                 {data: 'primary_email', name: 'primary_email'},    
                        
        //                 @if(Entrust::can('client-delete'))
        //                 { data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions'},
        //                 @endif

        //             ]
        //         })
        //     }
        // };
        // jQuery(document).ready((function () {
        //     KTDatatablesDataSourceAjaxServer.init()
        // })); 
		
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