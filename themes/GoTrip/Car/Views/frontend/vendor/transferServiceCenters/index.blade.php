@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ __('transfers.admin.service_centers.title') }}</h1>
            <div class="text-15 text-light-1">{{ __('transfers.admin.service_centers.search_placeholder') }}</div>
        </div>
        <div class="col-auto">
            @if(Auth::user()->hasPermission('car_create'))
                <a href="{{ route('car.vendor.transfer-service-centers.create') }}" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                    {{ __('transfers.admin.service_centers.create_button') }}
                    <div class="icon-arrow-top-right ml-15"></div>
                </a>
            @endif
        </div>
    </div>
    @include('admin.message')
    <div class="card px-30 py-30 rounded-4 bg-white shadow-3 mb-30">
        <form method="get" class="row y-gap-20">
            <div class="col-md-8">
                <input type="text" class="form-control" name="s" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.service_centers.search_placeholder') }}">
            </div>
            <div class="col-md-3">
                <select name="location_id" class="form-select">
                    <option value="">{{ __('transfers.admin.service_centers.location_any') }}</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="button -dark-1 w-100 h-50 bg-blue-1 text-white">{{ __('transfers.admin.service_centers.filter_button') }}</button>
            </div>
        </form>
    </div>
    <div class="card px-30 py-30 rounded-4 bg-white shadow-3">
        @if($rows->count())
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                    <tr class="text-15 text-uppercase text-light-1">
                        <th>{{ __('transfers.admin.service_centers.table_name') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_address') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_coordinates') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_location') }}</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $row)
                        <tr class="text-15">
                            <td class="fw-500">{{ $row->name }}</td>
                            <td>{{ $row->address }}</td>
                            <td>
                                @if(!is_null($row->lat) && !is_null($row->lng))
                                    <span class="text-13 text-light-1">{{ number_format($row->lat, 6) }}, {{ number_format($row->lng, 6) }}</span>
                                @else
                                    <span class="text-13 text-light-1">{{ __('transfers.admin.service_centers.coordinates_missing') }}</span>
                                @endif
                            </td>
                            <td>{{ optional($row->location)->name ?? '—' }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('car.vendor.transfer-service-centers.edit', $row) }}" class="btn btn-sm btn-outline-primary">{{ __('transfers.admin.service_centers.edit_button') }}</a>
                                    <form action="{{ route('car.vendor.transfer-service-centers.destroy', $row) }}" method="post" onsubmit="return confirm('{{ __('transfers.admin.service_centers.delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">{{ __('transfers.admin.service_centers.delete_button') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bravo-pagination mt-20">
                {{ $rows->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-15 text-light-1">{{ __('transfers.admin.service_centers.empty_state') }}</div>
        @endif
    </div>
@endsection
