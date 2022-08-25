@extends('layouts.default')
@section('content')
@push('scripts')
<script>
$(document).ready(function() {

});
</script>
@endpush

<div class="card card-custom mb-3">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-percentage text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('View Tag') }}</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('tags.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body remove-padding-mobile">
        <div class="card card-custom gutter-b example example-compact p-5">
            <div class="card-body remove-padding-mobile">
            <div class="row">
                <div class="col-lg-6">
                    <h4><b>Name :</b> {{ $tag->name }}</h4>
                </div> 
                <div class="col-lg-6">
                    <h4><b>Type :</b> {{ $tag->type }}</h4>
                </div>  
            </div>
            <br><hr>
            <div class="table-responsive">
            <table class="table rounded gradient-detail-card font-size-17"> 
                <tbody>  
                    @if(!empty($conditions_arr))
                        @foreach($conditions_arr as $condition)   
                            <tr>
                                <td width="40%">{{ $condition['name'] }}</td>
                                <th width="30%">{{ $condition['td_1'] }}</th>
                                <th width="30%">{{ $condition['td_2'] }}</th>
                            </tr>
                        @endforeach
                    @else 
                        <tr class="text-center bg-gray-100">
                            <th colspan="6">No Conditions!</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

            </div>
        </div>
    </div>
</div> 

@stop