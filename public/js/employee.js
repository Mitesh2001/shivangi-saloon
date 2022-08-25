// check number is valid or not
$(document).on('input', '.valid-number', function() {
    var value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null;
    $(this).val(value);
});
 
// add employeer data
$(document).on('click','.add-employeer',function(){
    var id = $(this).data('id');
    var i = id;
    $(this).data('id',i+1);
    var employeerData = '<tr>'+
                            '<td class="form-group-error">'+
                                '<input type="text" name="employer_data['+i+'][employeer]" class="form-control" placeholder="Employeer">'+
                            '</td>'+
                            '<td class="form-group-error">'+
                                '<input type="date" name="employer_data['+i+'][from]" class="form-control date-from">'+
                            '</td>'+
                            '<td class="form-group-error">'+
                                '<input type="date" name="employer_data['+i+'][to]" class="form-control date-to">'+
                            '</td>'+
                            '<td class="week-off-th form-group-error">'+
                                '<a href="javascript:void(0)" class="remove-week-off-tr" data-toggle="tooltip" title="Delete Week-off">'+
                                    '<i class="flaticon2-rubbish-bin icon-lg text-danger"></i>'+
                                '</a>'+
                            '</td>'+ 
                        '</tr>';
    $('.employeer-table > tbody > tr:last').after(employeerData);  
    $('[data-toggle="tooltip"]').tooltip();          
});

// remove employeer tr
$(document).on('click','.remove-employeer-tr',function(){
    $('.tooltip').tooltip().remove();
    $(this).parent().parent('tr').remove();
});

$(document).on('click','.add-certificate',function(){
    var id = $(this).data('id');
    var i = $(".certificate-table tr").length;
    $(this).data('id',i+1);
    var certificateData = '<tr>'+
                        '<td class="form-group-error">'+
                            '<input type="text" name="certification_data['+i+'][name]" class="form-control" placeholder="Certificate Name">'+
                        '</td>'+
                        '<td class="form-group-error">'+
                            '<input type="date" name="certification_data['+i+'][from]" class="form-control date-from">'+
                        '</td>'+
                        '<td class="form-group-error">'+
                            '<input type="date" name="certification_data['+i+'][to]" class="form-control date-to">'+
                        '</td>'+
                        '<td class="form-group-error">'+
                            '<input type="file" name="certification_data['+i+'][certification_attachment]" class="form-control">'+
                        '</td>'+
                        '<td class="form-group-error">'+
                            '<a href="javascript:void(0)" class="remove-certificate-tr" data-toggle="tooltip" title="Delete Certificate">'+
                                '<i class="flaticon2-rubbish-bin icon-lg text-danger"></i>'+
                            '</a>'+
                        '</td>'+ 
                    '</tr>';
    $('.certificate-table > tbody > tr:last').after(certificateData);  
    $('[data-toggle="tooltip"]').tooltip();          
});

// remove employeer tr
$(document).on('click','.remove-certificate-tr',function(){
    $('.tooltip').tooltip().remove();
    $(this).parent().parent('tr').remove();
});


// // add week off tr
// $(document).on('click','.add-week-off',function(){
//     var id = $(this).data('id');
//     var i = id;
//     $(this).data('id',i+1);
//     var weekOffData = '<tr class="week-tr week-tr-'+i+'"><td class="week-off-year">'+
//                         '<select name="week_off['+i+'][year]" class="form-control">'+
//                             '<option value="">Year</option>'+
//                             '<option value="2021">2021</option>'+
//                             '<option value="2022">2022</option>'+
//                         '</select>'+
//                         '</td>'+
//                         '<td class="week-off-year">'+
//                             '<select name="week_off['+i+'][month]" class="form-control">';
//                                 weekOffData += '<option value="">Month</option>';
//                                     $.each(monthsData, function(key, value) {   
//                                         weekOffData +=  '<option value="' + key + '">'+value+'</option>';
//                                     });
//                             weekOffData += '</select>'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<input type="date" name="week_off['+i+'][date_1]" class="form-control">'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<input type="date" name="week_off['+i+'][date_2]" class="form-control">'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<input type="date" name="week_off['+i+'][date_3]" class="form-control">'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<input type="date" name="week_off['+i+'][date_4]" class="form-control">'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<input type="date" name="week_off['+i+'][date_5]" class="form-control">'+
//                         '</td>'+
//                         '<td class="week-off-th">'+
//                             '<a href="javascript:void(0)" class="btn btn-primary font-weight-bolder font-size-sm mr-3 remove-week-off-tr" data-id="'+i+'">Remove</a>'+
//                         '</td>'+
//                     '</tr>';
//                 $('.week-off-table > tbody > tr:last').after(weekOffData);
// });

// // remove week off tr
// $(document).on('click','.remove-week-off-tr',function(){
//     $(this).parent().parent('tr').remove();
// });