@extends('layouts.default')
@section('content')
@push('scripts')
    <script>
        $(document).ready(function () { 
            
        });
    </script>
@endpush 
<div class="card card-custom gutter-b">
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
            <!-- <a href="{{ route('incoming_inventory.create') . '?product_id='. $product->external_id }}" class="btn btn-light-primary font-weight-bold mr-3">Add Stock</a> -->
            <a href="{{ route('product.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body detail-parent">

        <div class="card rounded gradient-detail-card mb-6 shadow-sm">
            <div class="card-body"> 
                <div class="row mb-17">
                    <div class="col-xxl-5 mb-11 mb-xxl-0">
                        <!--begin::Image-->
                        <div class="card card-custom card-stretch" style="background:transparent">
                            <div class="card-body p-0 rounded d-flex align-items-center justify-content-center"> 
                                @if(!empty($product->thumbnail)) 
                                    <img src="{!! asset($product->thumbnail) !!}" class="mw-100 w-200px"/> 
                                @else 
                                    <img src="{!! asset('storage/assets/no_image.png') !!}" class="mw-100 w-200px"/> 
                                @endif
                            </div>
                        </div>
                        <!--end::Image-->
                    </div>
                    <div class="col-xxl-7 pl-xxl-11">
                        <h2 class="font-weight-bolder  mb-7" style="font-size: 32px;">{{ $product->name ?? "" }}</h2>
                        <div class="font-size-h2 mb-7">Sales Price 
                        <span class="font-weight-boldest ml-2">&#8377; {{ $product->sales_price ?? "" }}</span></div>
                        <div class="line-height-xl">{!! $product->description !!}</div>
                    </div>
                </div>
                <div class="row mb-6">
                    <!--begin::Info-->
                    
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">SKU Code</span>
                            <span class="font-weight-bold mb-4 font-size-17">{{ $product->sku_code ?? "" }}</span>
                        </div>
                    </div> 
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">Type</span>
                            <span class=" font-weight-bold mb-4 font-size-17">
                                @if($product->type == 0)
                                    Product
                                @elseif($product->type == 1) 
                                    Service
                                @elseif($product->type == 2) 
                                    Package
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">Purchase Price</span>
                            <span class=" font-weight-bold mb-4 font-size-17">&#8377; {{ $product->sales_price ?? "" }}</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">Category</span>
                            <span class=" font-weight-bold mb-4 font-size-17">{{ implode(", ",$product->categories->pluck('name')->toArray()) }}</span>
                        </div>
                    </div>
                
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">Unit</span>
                            <span class=" font-weight-bold mb-4 font-size-17">{{ $product->unit->name ?? "QTY" }}</span>
                        </div>
                    </div> 
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">IGST</span>
                            <span class=" font-weight-bold mb-4 font-size-17">{{ $product->igst ?? 0 }}%</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">SGST</span>
                            <span class=" font-weight-bold mb-4 font-size-17">{{ $product->sgst ?? 0 }}%</span>
                        </div>
                    </div> 
                    <div class="col-6 col-md-4">
                        <div class="mb-8 d-flex flex-column">
                            <span class="text-light font-size-14 mb-3">CGST</span>
                            <span class=" font-weight-bold mb-4 font-size-17">{{ $product->cgst ?? 0 }}%</span>
                        </div>
                    </div> 
                    <!--end::Info-->
                </div>
                <!--begin::Buttons--> 
                @if($product->type == 0)
                    <div class="d-flex">
                        <a href="{{ route('incoming_inventory.create') . '?product_id='. $product->external_id }}" class="btn btn-primary font-weight-bolder mr-6 px-6 font-size-sm">
                        <span class="svg-icon">
                            <!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/Files/File-plus.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z" fill="#000000" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>New Stock</a> 
                    </div>
                @endif 
                <!--end::Buttons--> 
            </div>
        </div> 

        @if($product->type == 2)
        <div class="mt-5">  
            <h4 class="card-title">Package Products/Services</h4> 
            <table class="table table-bordered">
                <tr>
                    <th width="80%">Product/Service</th>
                    <th width="20%">Qty</th>
                </tr>
                @if(isset($product->packageProducts) && count($product->packageProducts) > 0)
                    @foreach($product->packageProducts as $sub_product) 
                    <tr>
                        <td>{{ $sub_product->name }}</td>
                        <td>{{ $sub_product->pivot->qty }} {{ $product->unit->name ?? "QTY" }}</td>
                    </tr>
                    @endforeach
                @else 
                    <tr>
                        <td colspan="2" class="text-center">No Product/Services</td>
                    </tr>
                @endif
               
            </table>  
        </div>
        @endif

    </div>
</div> 
@stop
