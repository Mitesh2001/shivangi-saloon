@extends('layouts.default')
@section('content') 

<div class="card card-custom">
    <div class="card-header d-flex justify-content-between">
        <div class="card-title remove-flex">
            <span class="card-icon">
                <i class="flaticon2-supermarket text-primary"></i>
            </span>
            <h3 class="card-label">{{ __('Permission management') }} ({{ $role->display_name }})</h3>
        </div>
        <div class="mt-3">
            <a href="{{ route('roles.index') }}" class="btn btn-light-primary font-weight-bold">Back</a>
        </div>
    </div>
    <div class="card-body"> 
        <div class="card card-custom gutter-b example example-compact">  
            <div class="card-body remove-padding-mobile"> 
                <div class="row">
                    <div class="col-lg-12">
                    {!! Form::model($permissions_grouping, [
                        'method' => 'PATCH',
                        'route' => ['roles.update', $role->external_id],
                    ]) !!}
                        @foreach($permissions_grouping as $permissions)
                        <div class="row">
                            @if($permissions->first)
                            <div class="col-md-2">
                                <p class="calm-header">{{ucfirst(__($permissions->first()->grouping))}} </p>
                            </div>
                            @endif
                            <div class="col-md-9 row">
                                @foreach($permissions as $permission) 
                                    <?php 
                                    $isEnabled = !current(
                                        array_filter(
                                            $role->permissions->toArray(),
                                            function ($element) use ($permission) {
                                                return $element['id'] === $permission->id;
                                            }
                                        )
                                    );  
                                    ?>
                                    <div class="col-lg-4">
                                        <label class="option">
                                            <span class="option-control">
                                                <label class="checkbox">
                                                    <input type="checkbox" {{ !$isEnabled ? 'checked' : ''}}
                                                    name="permissions[ {{ $permission->id }} ]" value="1" data-role="{{ $role->id }}" class="form">
                                                    <span></span>
                                                </label>
                                            </span>
                                            <span class="option-label">
                                                <span class="option-head">
                                                    <span class="option-title">{{ $permission->display_name }}</span> 
                                                </span>
                                                <span class="option-body">{{ $permission->description }}</span>
                                            </span>
                                        </label>
                                    </div>  
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        @endforeach 
                            @if($role->name != "distributor")
                                {!! Form::submit( __('Update Role') , ['class' => 'btn btn-primary']) !!} 
                            @else 
                                <p class="text-muted">Can't update this role for security reasons.</p>
                            @endif
                    {!! Form::close(); !!} 
                    </div>
                </div>
            </div>   
        </div>
    </div>
</div>
<!--end::Card-->  
@stop
