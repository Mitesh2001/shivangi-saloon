<!-- Modal-->
<div class="modal fade" id="view-enquiry-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"> 
            <div class="modal-header">
                <h5 class="modal-title remove-flex" id="exampleModalLabel">
                    Incoming Stock  
                    <br>
                    <span class="text-muted">(Invoice Number: <span id="invoice_number"></span> )</span> 
                    <br>
                    @if($is_system_user)
                    <span class="text-muted"> (Salon : <span id="distributor-modal"></span> )</span>
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">  
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <td>Product Name</td>
                            <td>SKU Code</td>
                            <td>MRP</td>
                            <td>QTY</td>
                            <td>Cost/Unit</td>
                            <td>GST</td>
                            <td>Total Cost</td>
                            <td>Expiry</td>
                        </tr>
                    </thead> 
                    <tbody class="products-entries-table" id="products-entries-table">
                        <!-- dynamic content here -->
                    </tbody>
                </table>
                <div class="py-3">
                    <h6>Note :</h6>
                    <div id="entry-note" class="container">
                        <!-- html content will be here  -->
                    </div>
                </div>  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button> 
            </div>
        </div>
    </div>
</div>  