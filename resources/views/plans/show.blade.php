@extends('layouts.default')
@section('content')
@push('scripts')
    <script>
        $(document).ready(function () { 
            
        });
    </script>
@endpush
 
<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title">
            <span class="card-icon">
                <i class="flaticon2-supermarket text-primary"></i>
            </span>
            <h3 class="card-label">
                {{ __('View Product') }}
                @if($is_system_user == 0)
                    <span class="text-muted">( Salon : {{ $distributor->name }} )</span>
                @endif 
            </h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('product.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body detail-parent"> 
        <div class="card rounded gradient-detail-card mb-6">  
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table font-size-17 text-white">
                            <tr>
                                <td width="50%">Name</td>
                                <th width="50%">{{ $product->name ?? "" }}</th>
                            </tr>
                            <tr>
                                <td width="50%">Purchase Price</td>
                                <th width="50%">&#8377; {{ $product->purchase_price ?? "" }}</th>
                            </tr>
                            <tr>
                                <td width="50%">Sales Price</td>
                                <th width="50%">&#8377; {{ $product->sales_price ?? "" }}</th>
                            </tr>
                            <tr>
                                <td width="50%">Category</td>
                                <th width="50%"> 
                                    {{ implode(", ",$product->categories->pluck('name')->toArray()) }}  
                                </th>
                            </tr>
                            <tr>
                                <td width="50%">Type</td>
                                <th width="50%"> 
                                    @if($product->type == 0)
                                        Product
                                    @else 
                                        Service
                                    @endif
                                </th>
                            </tr>
                            <tr>
                                <td width="50%">Unit</td>
                                <th width="50%">{{ $product->unit->name ?? "QTY" }}</th>
                            </tr>
                            <tr>
                                <td width="50%">Package</td>
                                <th width="50%">{{ $product->package->name ?? "" }}</th>
                            </tr> 
                            <tr>
                                <td width="50%">SKU Code</td>
                                <th width="50%">{{ $product->sku_code ?? "" }}</th>
                            </tr>
                            <tr>
                                <td width="50%">Expiry Reminder</td>
                                <th width="50%">{{ $product->expiry_reminder ?? "" }}</th>
                            </tr>
                            @if($product->other_document) 
                            <tr>
                                <td width="50%">Other Document</th>
                                <th width="50%">
                                    <a href="{{ asset($product->other_document) }}" target="_blank" data-toggle="tooltip" title="View Document">
                                        <i class="flaticon-eye icon-lg text-white"></i>
                                    </a>
                                </th>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-lg-4"> 
                        <h6>Image</h6>
                        <br>
                        <div class="image-input image-input-outline bg-white">
                            <div class="image-input-wrapper" style="background-image: url({!! asset($product->thumbnail) !!});background-size: contain;background-position: center"></div> 
                        </div>  
                    </div>
                </div> 
                <div class="row mt-4"> 
                    <div class="col-lg-12">
                        <h6>Product Description</h6>
                        <br> 
                        {!! $product->description !!}
                    </div>
                </div> 
            </div>   
        </div>
    </div>
</div>
<!--end::Card-->
 

@stop
