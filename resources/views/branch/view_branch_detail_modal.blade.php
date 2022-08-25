<!-- Modal-->
<div class="modal fade" id="view-enquiry-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">Branch Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body remove-padding-mobile"> 
                 
                <table class="table gradient-detail-card font-size-14 text-white">
                    <tr>
                        <th>name</th>
                        <td id="name-modal"></td>
                    </tr>
                    <tr>
                        <th>Primary Contact Person</th>
                        <td id="primary-contact-person-modal"></td>
                    </tr>
                    <tr>
                        <th>Secondary Contact Person</th>
                        <td id="secondary-contact-person-modal"></td>
                    </tr>
                    <tr>
                        <th>Primary Number</th>
                        <td id="primary-contact-number-modal"></td>
                    </tr> 
                    <tr>
                        <th>Secondary Number</th>
                        <td id="secondary-contact-number-modal"></td>
                    </tr> 
                    <tr>
                        <th>Primary Email</th>
                        <td id="primary-email-modal"></td>
                    </tr> 
                    <tr>
                        <th>Secondary Email</th>
                        <td id="secondary-email-modal"></td>
                    </tr> 
                    </tr>
                    <tr>
                        <th>Country</th>
                        <td id="country-modal"></td>
                    </tr> 
                    <tr>
                        <th>State</th>
                        <td id="state-modal"></td>
                    </tr> 
                    <tr>
                        <th>City</th>
                        <td id="city-modal"></td>
                    </tr> 
                    <tr>
                        <th>Address</th>
                        <td id="address-modal"></td>
                    </tr> 
                    <tr>
                        <th>Zipcode</th>
                        <td id="zipcode-modal"></td>
                    </tr>  
                    <tr>
                        <th>Is Primary Branch</th>
                        <td id="is-pirmary-modal"></td>
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