<style>
    .rules-group-container {
        width: 100% !important;
    }
    
    #builder-basic {
        width: 100%;
    }
    </style>
    
    
    <!--begin::Card-->
    <div class="card card-custom gutter-b example example-compact mb-5">
        <!--begin::Form-->
        
        <div class="form">
            <div class="card-body">
                <div class="row"> 
                    @if($is_system_user == 0)
                        @if(isset($report))
                            <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $report->distributor_id }}">
                        @else
                            <div class="col-lg-3 form-group form-group-error"> 
                                {!! Form::label('distributor_id', __('Salon'). ': *', ['class' => '']) !!}  
                                <select name="distributor_id" id="distributor_id" class="form-control">
                                </select>
                                @if ($errors->has('distributor_id'))  
                                    <span class="form-text text-danger">{{ $errors->first('distributor_id') }}</span>
                                @endif
                            </div> 
                        @endif
                    @else 
                        <input type="hidden" name="distributor_id" id="distributor_id" value="{{ $is_system_user }}">
                    @endif
                </div>
                <div class="row">
                    <div class="col-lg-3 form-group form-group-error">
                      {!! Form::label('module', __('Module'). ': *', ['class' => '']) !!}
                      {!!
                        Form::select('module',
                        [
                          '' => 'Select Module',
                          'orders' => 'Orders',
                          'clients' => 'Clients',
                          'inventory' => 'Inventory',  
                          'deals_and_discount' => 'Deals & Discounts',  
                          'employee' => 'Employee',
                          'product' => "Product & Service",
                          'inquiry' => 'Inquiry',  
                          'appointment' => 'Appointment', 
                          'branches' => 'Branch'
                        ],
                        isset($data['module']) ? $data['module'] : null, 
                        ['class' => 'form-control ui search selection top right pointing module-select',
                        'id' => 'module-select', 'required' => true])
                      !!}
                      @if ($errors->has('module'))
                      <span class="form-text text-danger">{{ $errors->first('module') }}</span>
                      @endif
                    </div>
                    <div class="col-lg-3 form-group form-group-error">
                        {!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
                        {!!
                        Form::text('name',
                        $data['name'] ?? old('name'),
                        ['class' => 'form-control',
                        'placeholder' => "Report Name"])
                        !!}
                        @if ($errors->has('name'))
                        <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-3 form-group form-group-error">
                      {!! Form::label('group_by', __('Group By'). ':', ['class' => '']) !!}
                      {!!
                        Form::select('group_by',
                        [
                          '' => 'Select Column',   
                        ],
                        $report['group_by'] ?? null, 
                        ['class' => 'form-control ui search selection top right pointing group_by-select',
                        'id' => 'group_by'])
                      !!}
                      @if ($errors->has('group_by'))
                      <span class="form-text text-danger">{{ $errors->first('group_by') }}</span>
                      @endif
                    </div>
                    <div class="col-lg-3 form-group form-group-error">
                      {!! Form::label('group_by_two', __('Group by (second field)'). ':', ['class' => '']) !!}
                      {!!
                        Form::select('group_by_two',
                        [
                          '' => 'Select Column',   
                        ],
                        $report['group_by_two'] ?? null, 
                        ['class' => 'form-control ui search selection top right pointing group_by_two-select',
                        'id' => 'group_by_two'])
                      !!}
                      @if ($errors->has('group_by_2'))
                      <span class="form-text text-danger">{{ $errors->first('group_by_2') }}</span>
                      @endif
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-12 form-group-error">
                        {!! Form::label('select_columns', __('Select Columns'). ': *', ['class' => '']) !!}
                        {!!
                        Form::select('select_columns[]',
                        [    
                        ],
                        isset($data['select_columns']) ? $data['select_columns'] : null, 
                        ['class' => 'form-control select_columns',
                        'id' => 'select_columns', 'multiple' => true])
                        !!}
                        @if ($errors->has('select_columns'))
                        <span class="form-text text-danger">{{ $errors->first('select_columns') }}</span>
                        @endif
                    </div> 
                </div>
                <div class="row" id="builder-row" style="display:none">
                    <div id="builder-basic"></div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-lg-6">
                        {!! Form::hidden('id', $report->id ?? "", ['id' => 'id']) !!}
                        {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitReport'])
                        !!}
                        {!! Form::button("Run", ['class' => 'btn btn-md btn-primary', 'id' =>
                        'runReport']) !!}
                        {!! Form::button("Cancel", ['class' => 'btn btn-light-primary', 'id' =>
                        'cancleReport']) !!}
                    </div>
                </div>
            </div>
        </duv>
            <!--end::Form-->
    </div>
    
    <div class="card card-custom gutter-b example example-compact" style="display:none" id="reportResult">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="flaticon2-chart text-primary"></i>
                </span>
                <h3 class="card-label" id="dynamic-report-name"></h3>
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
            </div> 
        </div>
        <!--begin::Form--> 
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 py-3 custom-records-per-page d-flex text-left"></div>
                <div class="col-md-6 py-3 custom-searchbar d-flex justify-content-end"></div>
            </div>
            <div class="table-responsive pl-6"> 
                <div id="dynamic_table"></div>  
            </div>
            <div class="row">
                <div class="col-md-6 py-3 custom-info text-left"></div>
                <div class="col-md-6 py-3 custom-pagination d-flex justify-content-end"></div>
            </div>
        </div> 
        <!--end::Form-->
    </div>
    </div>
    <!--end::Card-->
    
    <script src="{{ asset('plugins/custom/query_builder/query-builder.standalone.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder/dist/js/query-builder.standalone.min.js"></script>  -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>  -->
     
    <script>
    
    $(document).ready(function () {
    
        $("#cancleReport").click(function (e) {
            location.reload();
        });
    
        $(document).on('change', '#distributor_id', function (){
            $("#module-select").val("").trigger('change');
        })
    
        $(document).on('change', '#module-select', function (){
            var distributor = $("#distributor_id").val();
            
            if(distributor == null) {
                alert("please select salon");
                $("#module-select").val(""); 
            }
        });
    
        showQueryBuilder(); 
        setupQueryBuilder();
        // chengeOptions();
    
        <?php if($is_system_user == 0 && !isset($report)): ?>
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
        <?php endif; ?>
    
        $(document).on('change', '#name', function (e){
            updateWindowTitle(e);
        });
    
        function updateWindowTitle(e)
        {
            let title = e.target.value;
            let title_prepend = "ND Salon Software | ";
            $("#dynamic-report-name").html(title);
            document.title = title_prepend + title;
        }
     
    
        function chengeOptions()
        {
            let option = $("#module-select").val();
    
            var group_by
            if($("#group_by").val().length > 0 || $("#group_by_two").val().length > 0) {
                group_by = true;
            } else {
                group_by = false;
            }
                $.ajax({
                    url: '{!! route('reports.getGroupByOptions') !!}',
                    type: "POST",
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        option: option,
                        group_by: group_by,
                        distributor_id : $("#distributor_id").val(),
                    },
                    success: function (res) {     
        
                        // Select Columns
                        $("#select_columns").html(res);
                        $("#select_columns").val("").trigger('change');
    
                        @if(isset($report))
                            reportQueryBuilder.queryBuilder('setRules', <?php print_r($report['rules_set']) ?>);
                            $('#select_columns').val(<?php print_r($report['select_columns']) ?>).trigger('change');
                        @endif   
                    }
                }) 
            
        }
    
        $("#group_by").on('change', function (e) {
            chengeOptions();
        })
        $("#group_by_two").on('change', function (e) {
            chengeOptions();
        })
    
        function showQueryBuilder()
        {
            let option = $("#module-select").val();
    
            if(option.length > 0) {
                $("#builder-row").slideDown('d-none');
            } else {
                $("#builder-row").slideUp();
            }
        }
    
        function setupQueryBuilder()
        {
            let option = $("#module-select").val();  
            showQueryBuilder();
    
            $.ajax({
                url: '{!! route('reports.getModuleRuleSet') !!}',
                type: "POST",
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    option: option,
                    distributor_id : $("#distributor_id").val(),
                },
                success: function (res) {   
                    // Group by columns
                    $("#group_by").html(res.group_by_options);
                    $("#group_by_two").html(res.group_by_options);
                    $("#group_by").val("");
                    $("#group_by_two").val("");
    
                    // Select Columns
                    $("#select_columns").html(res.columns);
                    $("#select_columns").val("").trigger('change');
    
                    // Rule Set 
                    reportQueryBuilder.queryBuilder('reset');
                    reportQueryBuilder.queryBuilder('setFilters', res.rule_set);
                    
                    @if(isset($report))
                        reportQueryBuilder.queryBuilder('setRules', <?php print_r($report['rules_set']) ?>);
                        $('#select_columns').val(<?php print_r($report['select_columns']) ?>).trigger('change');
                        $('#group_by').val('<?php print_r($report['group_by']) ?>').trigger('change'); 
                        $('#group_by_two').val('<?php print_r($report['group_by_two']) ?>').trigger('change'); 
                    @endif   
                }
            }) 
    
            // $("#resetReport").on('click', function () { 
            //     setupQueryBuilder();
            // });
        }
    
        $(document).on('change', '#module-select', function (e) {
            setupQueryBuilder(); 
        });
    
        $("#group_by").on("change", function (e) {
            validateSameGroupBy(e);
        });
        $("#group_by_two").on("change", function (e) {
            validateSameGroupBy(e);
        });
    
        function validateSameGroupBy(e)
        {   
            if(e.target.value != "") {
                let group_by_one = $("#group_by").val();
                let group_by_two = $("#group_by_two").val();
    
                if(group_by_one == group_by_two) {
                    alert("Sorry! you can not select same columns on both group by condition.")
                    $(e.target).val("");
                    return false;
                }
            }
                 
        }
        
        $("#select_columns").select2({
            placeholder: "Select Columns",
        });
    }); 
    
    
    var reportQueryBuilder = $('#builder-basic').queryBuilder({
         
     // plugins: ['bt-tooltip-errors'],
        filters: [{
            id: 'name',
            label: 'Name',
            type: 'string',
            operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
        }],
    
        //   rules: rules_basic
    });
      
    </script>