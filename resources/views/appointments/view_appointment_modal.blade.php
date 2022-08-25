<!-- Modal-->
<div class="modal fade" id="view-enquiry-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">
                    Appointment Details
                    @if($is_system_user == 0)
                        <span class="text-muted"> (Salon : <span id="distributor-modal"></span> )</span>
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body detail-parent" id="appointment-modal-body"> 
                 
                <table class="table gradient-detail-card font-size-14 text-white">
                    <tr>
                        <td width="50%">Client Name</td>
                        <th id="name-modal" width="50%"></th>
                    </tr>
                    <tr>
                        <td>Client Name</td>
                        <th id="gender-modal"></th>
                    </tr>
                    <tr>
                        <td>Client Type</td>
                        <th id="type-modal"></th>
                    </tr>
                    <tr>
                        <td>Date of Appointment</td>
                        <th id="date-modal"></th> 
                    </tr>  
                    <tr>
                        <td>Start at</td>
                        <th id="start-time-modal"></th> 
                    </tr>  
                    <tr>
                        <td>End at</td>
                        <th id="end-time-modal"></th> 
                    </tr>  
                    <tr>
                        <td>Contact Number</td>
                        <th id="contact-number-modal"></th>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <th id="email-modal"></th>
                    </tr>
                    <tr>
                        <td>address</td>
                        <th id="address-modal"></th>
                    </tr> 
                    <tr>
                        <td>Appointment For</td>
                        <th id="appointment-for-modal"></th>
                    </tr>
                    <tr>
                        <td>Service Categories</td>
                        <th id="service-category-modal"></th>
                    </tr>
                    <tr>
                        <td>Source of Appointment</td>
                        <th id="appointment-source-modal"></th>
                    </tr>
                    <tr>
                        <td>Representative</td>
                        <th id="representative-modal"></th>
                    </tr>
                    <tr>
                        <td>Branch</td>
                        <th id="appointment-branch-modal"></th>         
                    </tr> 
                    <tr>
                        <td>Status</td> 
                        <!-- <th id="appointment-status-modal"></th> -->
                        <td>
                            {!! Form::open([
                                'route' => 'appointments.updateStatus',
                                'class' => 'ui-form',
                                'id' => 'statusForm',
                                'files' => true
                            ]) !!}
                            <input type="hidden" name="appointment_id" class="appointment_id" value="0">
                            <select name="status_id" id="status_id_appointment" class="form-control" onchange="this.form.submit()">
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status['id'] }}">{{ $status['title'] }}</option>
                                @endforeach
                            </select> 
                           {!! Form::close() !!}
                        </td>
                    </tr> 
                    <tr>
                        <td>Notes</td> 
                        <th id="notes-modal"></th>
                    </tr> 
                </table>
                <div class="p-3">
                    <h6>Description :</h6>
                    <div id="description-modal" class="container">
                        <!-- html content will be here  -->
                    </div>
                </div>
                {!! Form::open([
                    'route' => 'appointments.storeImages',
                    'class' => 'ui-form',
                    'id' => 'imageForm',
                    'files' => true
                ]) !!}
                <div class="row"> 
                    <div class="form-group col-12"> 
                        <input type="hidden" name="appointment_id" class="appointment_id" value="0">
                        <label class="btn btn-light-primary btn-sm mt-3">
                            <i class="flaticon-upload"></i> Upload Images 
                            <input type="file" onchange="this.form.submit()" class="custom-file-input" name="appointment_images[]" id="appointment_images" hidden multiple>
                        </label> 
                        <span class="form-text">Max file size 1MB. (supported types jpeg, jpg, png, gif)</span>
                        <span class="form-text">Use CTR + Click to upload multiple images</span>
                    </div> 
                </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <input type="hidden" id="appointment-external-id" value="">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
            </div> 
        </div>
    </div>
</div> 
 