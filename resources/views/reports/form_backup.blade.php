<style>
.rules-group-container {
    width: 100% !important;
}

#builder-basic {
    width: 100%;
}
</style>


<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
    <!--begin::Form-->
    <div class="form">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 form-group">
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
                      'inquiry' => 'Inquiry',  
                      'appointment' => 'Appointment',  
                    ],
                    isset($data['module']) ? $data['module'] : null, 
                    ['class' => 'form-control ui search selection top right pointing module-select',
                    'id' => 'module-select', 'required'])
                  !!}
                  @if ($errors->has('module'))
                  <span class="form-text text-danger">{{ $errors->first('module') }}</span>
                  @endif
                </div>
                <div class="col-lg-3 form-group">
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
                <div class="col-lg-3 form-group">
                  {!! Form::label('group_by', __('Group By'). ':', ['class' => '']) !!}
                  {!!
                    Form::select('group_by',
                    [
                      '' => 'Select Column',   
                    ],
                    isset($data['group_by']) ? $data['group_by'] : null, 
                    ['class' => 'form-control ui search selection top right pointing group_by-select',
                    'id' => 'group_by'])
                  !!}
                  @if ($errors->has('group_by'))
                  <span class="form-text text-danger">{{ $errors->first('group_by') }}</span>
                  @endif
                </div>
                <div class="col-lg-3 form-group">
                  {!! Form::label('group_by_2', __('Group by (second field)'). ':', ['class' => '']) !!}
                  {!!
                    Form::select('group_by_2',
                    [
                      '' => 'Select Column',   
                    ],
                    isset($data['group_by_2']) ? $data['group_by_2'] : null, 
                    ['class' => 'form-control ui search selection top right pointing group_by_2-select',
                    'id' => 'group_by_2'])
                  !!}
                  @if ($errors->has('group_by_2'))
                  <span class="form-text text-danger">{{ $errors->first('group_by_2') }}</span>
                  @endif
                </div>
            </div>
            <div class="row">
                <div class="form-group col-lg-12">
                    {!! Form::label('select_columns', __('Select Columns'). ':', ['class' => '']) !!}
                    {!!
                    Form::select('select_columns',
                    [    
                    ],
                    isset($data['select_columns']) ? $data['select_columns'] : null, 
                    ['class' => 'form-control select_columns',
                    'id' => 'select_columns', 'required', 'multiple' => true])
                    !!}
                    @if ($errors->has('select_columns'))
                    <span class="form-text text-danger">{{ $errors->first('select_columns') }}</span>
                    @endif
                </div> 
            </div>
            <div class="row">
                <div id="builder-basic"></div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    {!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitReport'])
                    !!}
                    {!! Form::button("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' =>
                    'resetReport']) !!}
                </div>
            </div>
        </div>
        </duv>
        <!--end::Form-->
    </div>
    <!--end::Card-->

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.5.2/dist/js/query-builder.standalone.min.js"></script> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jQuery-QueryBuilder@2.5.2/dist/css/query-builder.default.min.css">
 
<script>

$(document).ready(function () {

    $(document).on('change', '#module-select', function (e) {

        let option = e.target.value;  
        
        $.ajax({
            url: '{!! route('reports.getModuleRuleSet') !!}',
            type: "POST",
            cache: false,
            data: {
                _token: "{{ csrf_token() }}",
                option: option
            },
            success: function (res) {   
                // Group by columns
                $("#group_by").html(res.group_by_options);
                $("#group_by_2").html(res.group_by_options);
                $("#group_by").val("");
                $("#group_by_2").val("");

                // Select Columns
                $("#select_columns").html(res.columns);
                $("#select_columns").val("").trigger('change');

                // Rule Set
                $('#builder-basic').queryBuilder({ filters : [res.group_by_options]});
            }
        }) 
    });
    
    $("#select_columns").select2({
        placeholder: "Select Columns",
    });
}); 


  var reportQueryBuilder = $('#builder-basic').queryBuilder({
      // plugins: ['bt-tooltip-errors'],

      filters: [{
          id: 'name',
          label: 'Name',
          type: 'string'
      }, {
          id: 'category',
          label: 'Category',
          type: 'integer',
          input: 'select',
          values: {
              1: 'Books',
              2: 'Movies',
              3: 'Music',
              4: 'Tools',
              5: 'Goodies',
              6: 'Clothes'
          },
          operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
      }, {
          id: 'in_stock',
          label: 'In stock',
          type: 'integer',
          input: 'radio',
          values: {
              1: 'Yes',
              0: 'No'
          },
          operators: ['equal']
      }, {
          id: 'price',
          label: 'Price',
          type: 'double',
          validation: {
              min: 0,
              step: 0.01
          }
      }, {
          id: 'id',
          label: 'Identifier',
          type: 'string',
          placeholder: '____-____-____',
          operators: ['equal', 'not_equal'],
          validation: {
              format: /^.{4}-.{4}-.{4}$/
          }
      }],

    //   rules: rules_basic
  });

  $('#btn-reset').on('click', function() {
      $('#builder-basic').queryBuilder('reset');
  });

  $('#btn-set').on('click', function() {
      $('#builder-basic').queryBuilder('setRules', rules_basic);
  });

    $('#resetReport').on('click', function() {
        // var result = reportQueryBuilder.queryBuilder('getSQL', $(this).data('stmt'));
        var result = reportQueryBuilder.queryBuilder({
      // plugins: ['bt-tooltip-errors'], 
                filters: [{
                    id: 'name',
                    label: 'Name',
                    type: 'string'
                }, {
                    id: 'category',
                    label: 'Category',
                    type: 'integer',
                    input: 'select',
                    values: {
                        1: 'Books',
                        2: 'Movies',
                        3: 'Music',
                        4: 'Tools',
                        5: 'Goodies',
                        6: 'Clothes'
                    },
                    operators: ['equal', 'not_equal', 'in', 'not_in', 'is_null', 'is_not_null']
                }] 
            
            }); 
        alert(result.sql);
    });
</script>