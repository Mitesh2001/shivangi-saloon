<!-- Modal-->
<div class="modal fade" id="view-enquiry-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Inquiry Details
                    @if($is_system_user)
                        <span class="text-muted"> (Salon : <span id="distributor-modal"></span> )</span>
                    @endif
                </h5>
               
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body detail-parent"> 
                 
                <table class="table gradient-detail-card font-size-14 text-white">
                    <tr>
                        <td width="50%">Client Name</td>
                        <th id="client-name-modal" width="50%"></th>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <th id="client-gender-modal"></th>
                    </tr>
                    <tr>
                        <td>Contact Number</td>
                        <th id="client-contact-number-modal"></th>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <th id="client-email-modal"></th>
                    </tr>
                    <tr>
                        <td>address</td>
                        <th id="client-address-modal"></th>
                    </tr> 
                    <tr>
                        <td>Inquiry for</td>
                        <th id="enquiry-for-modal"></th>
                    </tr> 
                    <tr>
                        <td>Inquiry Type</td>
                        <th id="enquiry-type-modal"></th>
                    </tr> 
                    <tr>
                        <td>Inquiry Response</td>
                        <th id="enquiry-response-modal"></th>
                    </tr> 
                    <tr>
                        <td>Date to Follow</td>
                        <th id="date-to-follow-modal"></th>
                    </tr> 
                    <tr>
                        <td>Source of Inquiry</td>
                        <th id="source-of-enquiry-modal"></th>
                    </tr> 
                    <tr>
                        <td>Lead Representative</td>
                        <th id="lead-representative-modal"></th>
                    </tr> 
                    <tr>
                        <td>Lead Status</td>
                        <td>
                            <select name="status_type" id="lead-status-modal" class="form-control"> 
                                @foreach($statuses as $status)
                                    <option class="table-status-input-option" value="{{$status->id}}">{{$status->title}}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="status_id" id="status_id" value="">
                        </td> 
                    </tr>  
                </table>
                <div class="p-3">
                    <h6>Description :</h6>
                    <div id="enquiry-description-modal" class="container">
                        <!-- html content will be here  -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 

<script>
$(document).ready(function (){
    $(document).on('change', '#lead-status-modal', function (e){

        let external_id = $("#status_id").val();
        let status_id = $(e.target).val();

        $.ajax({
            url: '{!! route('status.updateStatus') !!}',
            type: "POST",
            dataType: 'json',
            cache: false,
            data: {
                _token: "{{ csrf_token() }}", 
                external_id: external_id,
                status_id: status_id,
            },
            success: function (data) { 
                if(data.status == true) {
                    Swal.fire(
                        'Updated!',
                        'Enquiry status updated successfully!',
                        'success'
                    );
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message, 
                    })  
                } 
                reload_page();
            }
        })

        function reload_page()
        {
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        }
    });
});
</script>