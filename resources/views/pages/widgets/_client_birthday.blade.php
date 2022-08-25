<div class="card card-custom {{ @$class }}">
    <div class="card-header">
        <div class="card-toolbar">
            <ul class="nav nav-bold nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_7_1">Clients Birthdays</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_7_2">Clients Anniversary</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="kt_tab_pane_7_1" role="tabpanel"
                aria-labelledby="kt_tab_pane_7_1">
                <span class="text-muted mt-3 font-weight-bold font-size-sm">Clients birthdays within 15 days.</span>
                @if(count($birthdays) > 0)

                @foreach($birthdays as $client)
                {{-- Item --}}
                <div class="d-flex align-items-center mb-10">
                    {{-- Symbol --}}
                    <div class="symbol symbol-40 symbol-light-success mr-5">
                        <span class="symbol-label">
                            <img src="{{ asset('media/svg/avatars/009-boy-4.svg') }}" class="h-75 align-self-end" />
                        </span>
                    </div>

                    {{-- Text --}}
                    <div class="d-flex flex-column flex-grow-1 font-weight-bold">
                        <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">{{ $client->name }}</a>
                        <span class="text-muted">{{ date('d-m-Y', strtotime($client->date_of_birth)) }}</span>
                    </div>

                    {{-- Dropdown --}}
                    <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="View client details">
                        <a href="{{ url('admin/clients/' . $client->external_id ."?ref=dashboard") }}"
                            class="btn btn-hover-light-primary btn-sm btn-icon">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @endforeach

                @else
                <div class="align-items-center bg-light py-3">
                    <p class="text-center">No Birthdays</p>
                </div>
                @endif
            </div>
            <div class="tab-pane fade" id="kt_tab_pane_7_2" role="tabpanel" aria-labelledby="kt_tab_pane_7_2">
                <span class="text-muted mt-3 font-weight-bold font-size-sm">Clients anniversary within 15 days.</span>
                @if(count($anniverssaries) > 0)

                    @foreach($anniverssaries as $client)
                        {{-- Item --}}
                        <div class="d-flex align-items-center mb-10">
                            {{-- Symbol --}}
                            <div class="symbol symbol-40 symbol-light-success mr-5">
                                <span class="symbol-label">
                                    <img src="{{ asset('media/svg/avatars/009-boy-4.svg') }}" class="h-75 align-self-end"/>
                                </span>
                            </div>

                            {{-- Text --}}
                            <div class="d-flex flex-column flex-grow-1 font-weight-bold">
                                <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">{{ $client->name }}</a> 
                                <span class="text-muted">{{ date('d-m-Y', strtotime($client->anniversary)) }}</span>
                            </div>

                            {{-- Dropdown --}}
                            <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="View client details">
                                <a href="{{ url('admin/clients/' . $client->external_id ."?ref=dashboard") }}" class="btn btn-hover-light-primary btn-sm btn-icon">
                                    <i class="fas fa-eye"></i>
                                </a> 
                            </div>
                        </div>
                    @endforeach

                @else  
                    <div class="align-items-center bg-light py-3">  
                        <p class="text-center">No Anniversaries</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>