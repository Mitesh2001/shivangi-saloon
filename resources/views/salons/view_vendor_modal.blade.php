<!-- Modal-->
<div class="modal fade" id="view-enquiry-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">Salon Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body"> 
                <div class="mb-4">
                    <img id="logo-modal" src="" alt="" style="max-height:100px">
                </div>      
                <table class="table">
                    <tr>
                        <th width="50%">Name</th>
                        <td width="50%" id="name-modal"></td>
                    </tr>
                    <tr>
                        <th width="50%">GST Number</th>
                        <td width="50%" id="gst-number-modal"></td>
                    </tr>
                    <tr>
                        <th width="50%">PAN Number</th>
                        <td width="50%" id="pan-number-modal"></td>
                    </tr>
                    <tr>
                        <th width="50%">Number of Employees</th>
                        <td width="50%" id="number-of-employees-modal"></td>
                    </tr>
                    <tr>
                        <th width="50%">Primary Number</th>
                        <td width="50%" id="primary-number-modal"></td>
                    </tr>
                    <tr>
                        <th width="50%">Secondary Number</th>
                        <td width="50%" id="secondary-number-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Primary Email</th>
                        <td width="50%" id="primary-email-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Secondary Email</th>
                        <td width="50%" id="secondary-email-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Contact Person</th>
                        <td width="50%" id="contact-person-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Contact Person Number</th>
                        <td width="50%" id="contact-person-number-modal"></td>
                    </tr> 
                    <!-- <tr>
                        <th width="50%">Contact Person Email</th>
                        <td width="50%" id="contact-person-email-modal"></td>
                    </tr>  -->
                    <tr>
                        <th width="50%">Country</th>
                        <td width="50%" id="country-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">State</th>
                        <td width="50%" id="state-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">City</th>
                        <td width="50%" id="city-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Zip Code</th>
                        <td width="50%" id="zipcode-modal"></td>
                    </tr> 
                    <tr>
                        <th width="50%">Address</th>
                        <td width="50%" id="address-modal"></td>
                    </tr>  
                </table> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 