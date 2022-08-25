@section('styles')
    <link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css"/>
@endsection

<div class="card card-custom card-collapsed mb-6" id="appointments-card"> 
    <div class="card-header border-0"> 
        <h3 class="card-title">
            <span class="card-label font-weight-bolder text-dark">Today's Appointments</span> 
        </h3>
        <div class="card-toolbar"> 
            <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary mr-1" data-card-tool="toggle" data-toggle="tooltip" data-placement="top" title="Toggle Card">
                <i class="ki ki-arrow-down icon-nm"></i>
            </a>
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
                        <th>
                        {!! Form::select('branch_filter',
                            [],
                            null,	
                            ['class' => 'form-control','id' => 'branch_filter', 'style' => "width:100%"])
                        !!}
                        </th>
                        <th class="branch-hidden">Branche</th>
                        <th style="width: 150px">
                        {!!
                            Form::select('client_external_id',
                            [],
                            old('client_external_id'),
                            ['class' => 'form-control',
                            'id' => 'client_external_id', 'style' => "width:100%"])
                        !!}
                        </th>
                        <th class="client-hidden">Client</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Appointment For</th> 
                        <!-- <th>Representative</th> -->
                        <th>Date</th>
                        <th>Start At</th>
                        <th>End At</th>
                        <th>
                            <select name="status_type" id="filter-status" class="form-control width-auto">
                                <option value="all" selected>{{ __('All Status') }}</option>
                                @foreach($statuses as $status)
                                    <option class="table-status-input-option" value="{{$status->title}}">{{$status->title}}</option>
                                @endforeach
                                <!-- <option value="all">All</option> -->
                            </select>
                        </th>
                        <th class="status-hidden">Status</th> 
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


@include('appointments.view_appointment_modal', ['statuses' => $statuses, 'branches' => $branches])
@include('appointments.reschedule_appointment_modal', ['statuses' => $statusesPluck, 'branches' => $branches])

 
<script>  
$(document).ready(function () { 
 
    "use strict";  

    var card = new KTCard('appointments-card');

    var KTDatatablesDataSourceAjaxServer = {
		init: function () {

        var table = $("#kt_datatable").DataTable({
            "aaSorting": [],
            processing: true,
            serverSide: true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            ajax: '{!! route('appointments.today') !!}',
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
                {data: 'branch', name: 'branch', width: '15%'},
                {data: 'branch', name: 'branch', visible: false},
                {data: 'client_name', name: 'client_name', width: '20%'},
                {data: 'client_name', name: 'client_name', visible: false},
                {data: 'contact', name: 'contact'},
                {data: 'email', name: 'email'},
                {data: 'appointment_for', name: 'appointment_for'}, 
                // {data: 'assigned_user', name: 'assigned_user'}, 
                {data: 'date', name: 'date'},
                {data: 'start_at', name: 'start_at', visible: false},
                {data: 'end_at', name: 'end_at', visible: false},
                {data: 'status', name: 'status', width: "20%"},
                {data: 'status', name: 'status', visible: false},

                
                @if(Entrust::can('appointment-update'))
                    { data: 'action', name: 'action', orderable: false, searchable: false, class:'fit-action-delete-th table-actions', width: "15%"},
                @endif
            ]
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

        var distributor_id = "";

        $('#branch_filter').select2({
            placeholder: "Select Branch",
            allowClear: true,
            ajax: {
                url: '{!! route('branch.getBranchByName') !!}',
                dataType: 'json', 
                data: function (params) {  
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
        
        $('#client_external_id').select2({
            placeholder: "Client Name",
            allowClear: true,
            ajax: {
                url: '{!! route('leads.clientsbyname') !!}',
                dataType: 'json', 
                data: function (params) {  
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

        $('#distributor_filter').change(function () { 
            var selected = $("#distributor_filter").val();// option:selected

            if (selected == "all" || selected == null || selected == "") {
                table.column(0).search('').draw();
            } else { 
                table.column(0).search(selected, true, false).draw();
            }
        });
        $('#branch_filter').change(function () { 
            var selected = $("#branch_filter").val();// option:selected

            if (selected == "all" || selected == null || selected == "") {
                table.column(2).search('').draw();
            } else { 
                table.column(2).search(selected, true, false).draw();
            }
        });
        $('#filter-status').change(function () { 
            var selected = $("#filter-status").val();// option:selected

            if (selected == "all" || selected == null || selected == "") {
                table.column(11).search('').draw();
            } else { 
                table.column(11).search(selected, true, false).draw();
            }
        });
        $('#client_external_id').change(function () { 
            var selected = $("#client_external_id").val();// option:selected

            if (selected == "all" || selected == null || selected == "") {
                table.column(4).search('').draw();
            } else { 
                table.column(4).search('^' + selected + '$', true, false).draw();
            }
        }); 
        }
    };
    jQuery(document).ready((function () {
        KTDatatablesDataSourceAjaxServer.init()
    }));

    
    $(document).on('click', '.edit-in-modal', function (e) { 
        let external_id = $(e.target).data('appointment-id');   
        $.ajax({
            url: '{!! route('appointments.findById') !!}',
            type: "POST",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                external_id: external_id
            },
            success: function (res) {   
                document.getElementById("date-edit").defaultValue = res.date_fomatted;
                $("#appointment_external_id").val(external_id); 
                $("#start-at-edit").timepicker('setTime', res.start_at); 
                $("#end-at-edit").timepicker('setTime', res.end_at); 
                var newOption = new Option(res.representative, res.representative_id, true, true);
                $('#representative-edit').append(newOption).trigger('change');
                $("#distributor-edit-modal").html(res.distributor);

                $("#status_id").val(res.status_id);
                $("#branch-edit-id").val(res.branch);
                $("#distributor-edit-id").val(res.distributor_id);
            }
        })
    });  

    $(document).on('click', '.view-in-modal', function (e) { 
        let external_id = $(e.target).data('appointment-id');  
        $.ajax({
            url: '{!! route('appointments.findById') !!}',
            type: "POST",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                external_id: external_id
            },
            success: function (res) {  
                // console.log(res);
                $(".appointment_id").val(res.id);
                $("#distributor-modal").html(res.distributor);
                $("#name-modal").html(res.client_name);
                $("#gender-modal").html(res.gender);
                $("#type-modal").html(res.client_type);
                $("#contact-number-modal").html(res.contact_number);
                $("#email-modal").html(res.email);
                $("#address-modal").html(res.address);
                $("#service-category-modal").html(res.service_categories);
                $("#appointment-for-modal").html(res.appointment_for);
                $("#appointment-source-modal").html(res.appointment_source);
                $("#representative-modal").html(res.representative);
                // $("#appointment-status-modal").html(res.status_tag);
                $("#status_id_appointment").val(res.status);
                $("#appointment-branch-modal").html(res.branch_name); 				
                $("#date-modal").html(res.date);
                $("#start-time-modal").html(res.start_at);
                $("#end-time-modal").html(res.end_at);
                $("#notes-modal").html(res.client_notes);

                $("#description-modal").html(res.description);
                $('#appointment-external-id').val(external_id);
            }
        })
    });  
}); 
</script>

