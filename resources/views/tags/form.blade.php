<!--begin::Card-->
<div class="card card-custom gutter-b example example-compact">
	<!--begin::Form-->
	<div class="form">
		<div class="card-body">
			<div class="form-group row"> 
				@if($is_system_user == 0)
					@if(isset($tag))
						<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $tag->distributor_id }}">
					@else
						<div class="col-lg-4 form-group-error"> 
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
				@else 
					<input type="hidden" name="distributor_id" id="distributor_id" value="{{ $is_system_user }}">
				@endif
			</div>
			<div class="form-group row"> 
				<div class="col-lg-4 form-group-error">
					{!! Form::label('name', __('Name'). ': *', ['class' => '']) !!}
					{!! 
						Form::text('name',  
						null, 
						['class' => 'form-control',
						'placeholder' => "name"]) 
					!!}
                    @if ($errors->has('name')) 
                        <span class="form-text text-danger">{{ $errors->first('name') }}</span>
                    @endif  

				</div>   
				<div class="col-lg-4 offset-lg-4 form-group row form-group-error">
					<label class="col-6 col-form-label">Select Type</label>
					<div class="col-6 col-form-label">
						<div class="radio-inline"> 
							@if(isset($tag->type))
								@if($tag->type == "or")
									<label class="radio radio-primary">
									<input type="radio" name="type" value="or" checked="checked">
									<span></span>Or</label>
									&nbsp;&nbsp;&nbsp;
									<label class="radio radio-primary">
									<input type="radio" name="type" value="and">
									<span></span>And</label> 
								@else 
									<label class="radio radio-primary">
									<input type="radio" name="type" value="or">
									<span></span>Or</label>
									&nbsp;&nbsp;&nbsp;
									<label class="radio radio-primary">
									<input type="radio" name="type" value="and" checked="checked">
									<span></span>And</label> 
								@endif
							@else 
								<label class="radio radio-primary">
								<input type="radio" name="type" value="or" checked="checked">
								<span></span>Or</label>
								&nbsp;&nbsp;&nbsp;
								<label class="radio radio-primary">
								<input type="radio" name="type" value="and">
								<span></span>And</label> 
							@endif 
						</div> 
					</div>
				</div>
			</div>  
			<table class="table">
				<thead>
					<tr>
						<th>Select KPI</th>
						<th></th>
						<th></th>
						<th>Remove</th>
					</tr>
				</thead>
				<tbody id="tags-table-body">

					@if(!empty($conditions_arr))
					<?php $i = 1; ?>
						@foreach($conditions_arr as $condition)

							<tr class="kip-row kpi-row-{{ $i }}" data-row="{{ $i }}">
								<td class="form-group-error" width="30%">
									<select name="condition_arr[{{ $i }}][kpi]" id="kpi_{{ $i }}" class="form-control kpi" data-row="{{ $i }}" required>
										<option value="">Select KPI</option> 	
										@foreach($kpi_arr as $key => $value) 
											@if($condition->kpi == $key) 
												<option value="{{ $key }}" selected>{{ $value }}</option>
											@else 
												<option value="{{ $key }}">{{ $value }}</option>
											@endif
										@endforeach
									</select>
								</td>
								<td class="form-group-error" width="30%"> 
									<input type="number" name="condition_arr[{{ $i }}][start_range]" class="form-control start_range row-input d-none" id="start_range_{{ $i }}" placeholder="Start Range" value="{{ $condition->start_range }}" number>
									<input type="date" name="condition_arr[{{ $i }}][date_last_visit]" class="form-control date_last_visit row-input d-none" id="date_last_visit_{{ $i }}" value="{{ $condition->date_last_visit }}" placeholder="Last Visit Date">
									<input type="date" name="condition_arr[{{ $i }}][date_start_range]" class="form-control date_start_range row-input d-none" id="date_start_range_{{ $i }}" value="{{ $condition->date_start_range }}" placeholder="Date Start Range">
									<input type="number" name="condition_arr[{{ $i }}][expiry_days_remain]" class="form-control expiry_days_remain row-input d-none" id="expiry_days_remain_{{ $i }}" value="{{ $condition->expiry_days_remain }}" placeholder="Expiry Days Remain" number>
									<input type="number" name="condition_arr[{{ $i }}][avg_order]" class="form-control avg_order row-input d-none" id="avg_order_{{ $i }}" placeholder="Avg Orders" value="{{ $condition->avg_orders }}" number>
									<select name="condition_arr[{{ $i }}][gender]" class="form-control gender row-input d-none" id="gender_{{ $i }}">
										<option value="">Select Gender</option> 
										@if($condition->gender == 0)
											<option value="0" selected>Male</option>
											<option value="1">Female</option>
										@else 
											<option value="0">Male</option>
											<option value="1" selected>Female</option>
										@endif
									</select>
								</td>
								<td class="form-group-error" width="30%"> 
									<input type="number" name="condition_arr[{{ $i }}][end_range]" class="form-control end_range row-input d-none" id="end_range_{{ $i }}" value="{{ $condition->end_range }}" placeholder="End Range" number>
									<input type="date" name="condition_arr[{{ $i }}][date_end_range]" class="form-control date_end_range row-input d-none" id="date_end_range_{{ $i }}" value="{{ $condition->date_end_range }}" placeholder="Date End Range"> 
								</td>
								<td class="form-group-error" width="10%">
									<input type="hidden" name="condition_arr[{{ $i }}][id]" class="id_{{ $i }} id" value="{{ $condition->id }}">
									<a href="javascript:void(0)" class="remove-condition"  data-condition-id=""  data-toggle="tooltip" title="Remove Condition">
										<i class="flaticon2-rubbish-bin icon-lg text-danger" data-condition-id=""></i>
									</a>
								</td>
							</tr>

						<?php $i++; ?>
						@endforeach

					@else

					<tr class="kip-row kpi-row-1" data-row="1">
						<td class="form-group-error" width="30%">
							<select name="condition_arr[1][kpi]" id="kpi_1" class="form-control kpi" data-row="1" required>
								<option value="">Select KPI</option> 	
								@foreach($kpi_arr as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
						</td>
						<td class="form-group-error" width="30%"> 
							<input type="number" name="condition_arr[1][start_range]" class="form-control start_range row-input d-none" id="start_range_1" placeholder="Start Range" number>
							<input type="date" name="condition_arr[1][date_last_visit]" class="form-control date_last_visit row-input d-none" id="date_last_visit_1" placeholder="Last Visit Date">
							<input type="date" name="condition_arr[1][date_start_range]" class="form-control date_start_range row-input d-none" id="date_start_range_1" placeholder="Date Start Range">
							<input type="number" name="condition_arr[1][expiry_days_remain]" class="form-control expiry_days_remain row-input d-none" id="expiry_days_remain_1" placeholder="Expiry Days Remain" number>
							<input type="number" name="condition_arr[1][avg_order]" class="form-control avg_order row-input d-none" id="avg_order_1" placeholder="Avg Orders" number>
							<select name="condition_arr[1][gender]" class="form-control gender row-input d-none" id="gender_1">
								<option value="">Select Gender</option>
								<option value="0">Male</option>
								<option value="1">Female</option>
							</select>
						</td>
						<td class="form-group-error" width="30%"> 
							<input type="number" name="condition_arr[1][end_range]" class="form-control end_range row-input d-none" id="end_range_1" placeholder="End Range" number>
							<input type="date" name="condition_arr[1][date_end_range]" class="form-control date_end_range row-input d-none" id="date_end_range_1" placeholder="Date End Range"> 
						</td>
						<td class="form-group-error" width="10%">
							<a href="javascript:void(0)" class="remove-condition"  data-condition-id=""  data-toggle="tooltip" title="Remove Condition">
								<i class="flaticon2-rubbish-bin icon-lg text-danger" data-condition-id=""></i>
							</a>
						</td>
					</tr>

					@endif
				</tbody>
			</table>
			<div class="row">
                <div class="col-lg-6">
                    <a href="javascript:void(0)" class="btn btn-primary add-condition" data-toggle="tooltip"
                        title="Add Condition">
                        Add &nbsp; &nbsp;
                        <i class="flaticon-plus icon-lg"></i>
                    </a>
                </div>
            </div>
		</div>
		<div class="card-footer">
			<div class="row">
				<div class="col-lg-6">
					{!! Form::hidden('id', null, ['id' => 'id']) !!}
					{!! Form::submit($submitButtonText, ['class' => 'btn btn-md btn-primary', 'id' => 'submitClient']) !!} 
					{!! Form::reset("Cancel", ['class' => 'btn btn-light-primary font-weight-bold', 'id' => 'submitClient']) !!}
				</div> 
			</div>
		</div>
	</div>
<!--end::Form-->
</div>
<!--end::Card-->  

<script>
$(document).ready(function (){ 

	<?php if(!isset($tag)): ?>
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

	$(document).on('click', 'input:reset', function (){
		location.reload();
	});

	$(document).on('click', '.remove-condition', function (e) {
		let id = $(e.target).closest('td').find('.id').val();

		if(typeof id == 'undefined') {
			$('.tooltip').tooltip().remove(); 
			$(e.target).closest('tr').remove();
			return false;
		}
 
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#ccc',
			confirmButtonText: 'remove!'
		}).then((result) => {
			if (result.isConfirmed) { 
				$.ajax({
					url: '{!! route('tags.removeCondition') !!}',
					type: "POST",
					dataType: 'json',
					cache: false,
					data: {
						_token: "{{ csrf_token() }}", 
						condition_id: id, 
					},
					success: function (res) {
						$('.tooltip').tooltip().remove(); 
						$(e.target).closest('tr').remove();
						Swal.fire({
							title: 'Removed!',
							text: 'Product/service Removed Successfully!',
							icon: 'success',
							timer: 3000
						}); 
					}
				}) 
			} 
		})
	});

	let table_length = $(".kip-row").length;  
    for(let x = 1; x <= table_length; x++) {
		let row_id = x; 
		let option = $(`#kpi_${x}`).val();
		manageRowConditions(row_id, option, false); // 3 param (false = dont remove value from inputs)
    }

	$(document).on('change', '.kpi', function (e) {

        let target_input = $(e.target);
        let id = $(e.target).val() 
        let attr_id = $(e.target).data('row'); 
        $(".kpi").each(function (index, input){
  
            let current_attr_id = $(input).data('row');  
            if(id == $(input).val() && current_attr_id != attr_id) { 
                target_input.val(null).trigger('change');
                alert("Can not add same condition!");
                return false;
            }
        });

		let row_id = $(e.target).data('row'); 
		let option = $(e.target).val();
		manageRowConditions(row_id, option);
	});
	

	// Show hide rows
	// Manage validation & remove values from inpusts
	function manageRowConditions(row_id, option, remove_val = true) {
 
		triggerKpiChange(row_id, remove_val);

		if(is_number_range_option(option)) {
			$(`#start_range_${row_id}`).removeClass('d-none');
			$(`#end_range_${row_id}`).removeClass('d-none');
			
			$(`#start_range_${row_id}`).attr('required', true); 
		} 
 
		if(is_date_range_option(option)) {
			$(`#date_start_range_${row_id}`).removeClass('d-none'); 
			$(`#date_end_range_${row_id}`).removeClass('d-none'); 
						
			$(`#date_start_range_${row_id}`).attr('required', true); 
		} 	
		
		if(option == "last_visit_date") {
			$(`#date_last_visit_${row_id}`).removeClass('d-none'); 
			$(`#date_last_visit_${row_id}`).attr('required', true); 
		} 
		
		if(option == "avg_order") {
			$(`#avg_order_${row_id}`).removeClass('d-none'); 
			$(`#avg_order_${row_id}`).attr('required', true); 
		} 

		if(option == "gender") {
			$(`#gender_${row_id}`).removeClass('d-none'); 
			$(`#gender_${row_id}`).attr('required', true); 
		} 

		if(option == "package_expiry") {
			$(`#expiry_days_remain_${row_id}`).removeClass('d-none');
			$(`#expiry_days_remain_${row_id}`).attr('required', true);  
		} 
	}

	// Set every input blank and add d-none to all inputs
	// letter remove d-none as per needed
	function triggerKpiChange(row_id, remove_val)
	{ 
		if(remove_val == true) {
			$(`.kpi-row-${row_id}`).find('.row-input').val("");
		} 
		$(`.kpi-row-${row_id}`).find('.row-input').addClass('d-none'); 
		$(`.kpi-row-${row_id}`).find('.row-input').removeAttr('required'); 
	}


	function is_number_range_option(input_value) {
		const arr = ['total_amount_paid', 'visits', 'last_visit_range', 'points', 'age'];
		return arr.includes(input_value);
	}

	function is_date_range_option(input_value) {
		const arr = ['birthday', 'anniversary', 'billing_date'];
		return arr.includes(input_value);
	} 
 


    // Add more week off tr
    $(document).on('click', '.add-condition', function () { 

		let table_length = $(".kip-row").length;

		if(table_length >= 13) { 
			alert("Can not add more then 13 rows!");
			return false;
		}
 
		let dynamic_id = $('#tags-table-body tr:last').data('row'); 
		if(typeof dynamic_id == 'undefined') {
			dynamic_id = 1;
		} else {
			dynamic_id++;
		}
 
		let condition_row = `
			<tr class="kip-row kpi-row-${dynamic_id}" data-row="${dynamic_id}">
				<td class="form-group-error" width="30%">
					<select name="condition_arr[${dynamic_id}][kpi]" id="kpi_${dynamic_id}" class="form-control kpi" data-row="${dynamic_id}"  required>
						<option value="">Select KPI</option> 	
						@foreach($kpi_arr as $key => $value)
							<option value="{{ $key }}">{{ $value }}</option>
						@endforeach
					</select>
				</td>
				<td class="form-group-error" width="30%"> 
					<input type="number" name="condition_arr[${dynamic_id}][start_range]" class="form-control start_range row-input d-none" id="start_range_${dynamic_id}" placeholder="Start Range" number>
					<input type="date" name="condition_arr[${dynamic_id}][date_last_visit]" class="form-control date_last_visit row-input d-none" id="date_last_visit_${dynamic_id}" placeholder="Last Visit Date">
					<input type="date" name="condition_arr[${dynamic_id}][date_start_range]" class="form-control date_start_range row-input d-none" id="date_start_range_${dynamic_id}" placeholder="Date Start Range">
					<input type="number" name="condition_arr[${dynamic_id}][expiry_days_remain]" class="form-control expiry_days_remain row-input d-none" id="expiry_days_remain_${dynamic_id}" placeholder="Expiry Days Remain" number>
					<input type="number" name="condition_arr[${dynamic_id}][avg_order]" class="form-control avg_order row-input d-none" id="avg_order_${dynamic_id}" placeholder="Avg Orders" number>
					<select name="condition_arr[${dynamic_id}][gender]" class="form-control gender row-input d-none" id="gender_${dynamic_id}">
						<option value="">Select Gender</option>
						<option value="0">Male</option>
						<option value="1">Female</option>
					</select>
				</td>
				<td class="form-group-error" width="30%"> 
					<input type="number" name="condition_arr[${dynamic_id}][end_range]" class="form-control end_range row-input d-none" id="end_range_${dynamic_id}" placeholder="End Range" number>
					<input type="date" name="condition_arr[${dynamic_id}][date_end_range]" class="form-control date_end_range row-input d-none" id="date_end_range_${dynamic_id}" placeholder="Date End Range"> 
				</td>
				<td class="form-group-error" width="10%">
					<a href="javascript:void(0)" class="remove-condition"  data-condition-id=""  data-toggle="tooltip" title="Remove Condition">
						<i class="flaticon2-rubbish-bin icon-lg text-danger" data-condition-id=""></i>
					</a>
				</td>
			</tr>
		`; 

  
		$("#tags-table-body").append(condition_row);  
		$('[data-toggle="tooltip"]').tooltip();
	}); 

});	
</script>