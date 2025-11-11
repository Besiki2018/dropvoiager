@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('transfers.admin.pickup_locations.title') }}</h1>
            <div class="title-actions">
                <a href="{{ route('car.admin.pickup-locations.create') }}" class="btn btn-primary">{{ __('transfers.admin.pickup_locations.add') }}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between flex-column flex-md-row">
            <div class="col-left mb-3 mb-md-0">
                <form method="post" action="{{ route('car.admin.pickup-locations.bulk') }}" class="filter-form filter-form-left d-flex flex-column flex-sm-row align-items-sm-center">
                    @csrf
                    <select name="action" class="form-control mb-2 mb-sm-0 mr-sm-2">
                        <option value="">{{ __('transfers.admin.pickup_locations.bulk_placeholder') }}</option>
                        <option value="activate">{{ __('transfers.admin.pickup_locations.bulk_activate') }}</option>
                        <option value="deactivate">{{ __('transfers.admin.pickup_locations.bulk_deactivate') }}</option>
                        <option value="delete">{{ __('transfers.admin.pickup_locations.bulk_delete') }}</option>
                    </select>
                    <button data-confirm="{{ __('transfers.admin.pickup_locations.bulk_confirm') }}" class="btn btn-info btn-icon dungdt-apply-form-btn" type="button">{{ __('transfers.admin.pickup_locations.apply') }}</button>
                </form>
            </div>
            <div class="col-left">
                <form method="get" action="{{ route('car.admin.pickup-locations.index') }}" class="filter-form filter-form-right d-flex flex-column flex-sm-row align-items-sm-center" role="search">
                    <input type="text" name="s" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.pickup_locations.search_placeholder') }}" class="form-control mb-2 mb-sm-0 mr-sm-2" />
                    <select name="car_id" class="form-control mb-2 mb-sm-0 mr-sm-2">
                        <option value="">{{ __('transfers.admin.pickup_locations.car_placeholder') }}</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" @selected((string) request('car_id') === (string) $car->id)>{{ $car->title }}</option>
                        @endforeach
                    </select>
                    <select name="vendor_id" class="form-control mb-2 mb-sm-0 mr-sm-2">
                        <option value="">{{ __('transfers.admin.pickup_locations.vendor_placeholder') }}</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected((string) request('vendor_id') === (string) $vendor->id)>{{ $vendor->name ?? ('#' . $vendor->id) }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-control mb-2 mb-sm-0 mr-sm-2">
                        <option value="">{{ __('transfers.admin.pickup_locations.status_placeholder') }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('transfers.admin.pickup_locations.status_active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('transfers.admin.pickup_locations.status_inactive') }}</option>
                    </select>
                    <button class="btn btn-primary" type="submit">{{ __('transfers.admin.pickup_locations.filter') }}</button>
                </form>
            </div>
        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="40"><input type="checkbox" class="check-all"></th>
                            <th>{{ __('transfers.admin.pickup_locations.table_name') }}</th>
                            <th>{{ __('transfers.admin.pickup_locations.table_address') }}</th>
                            <th>{{ __('transfers.admin.pickup_locations.table_coordinates') }}</th>
                            <th>{{ __('transfers.admin.pickup_locations.table_car') }}</th>
                            <th>{{ __('transfers.admin.pickup_locations.table_vendor') }}</th>
                            <th width="120">{{ __('transfers.admin.pickup_locations.table_status') }}</th>
                            <th width="150">{{ __('transfers.admin.pickup_locations.table_updated') }}</th>
                            <th width="100"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td><input type="checkbox" class="check-item" name="ids[]" value="{{ $row->id }}"></td>
                                <td>
                                    <div class="fw-500">{{ $row->display_name ?: $row->name ?: __('transfers.admin.pickup_locations.unnamed', ['id' => $row->id]) }}</div>
                                    @if($row->place_id)
                                        <small class="text-muted">{{ $row->place_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $row->address }}</div>
                                    @if($row->service_center_name)
                                        <small class="text-muted">{{ __('transfers.admin.pickup_locations.service_center_label', ['name' => $row->service_center_name]) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($row->lat && $row->lng)
                                        <div>{{ number_format($row->lat, 6) }}, {{ number_format($row->lng, 6) }}</div>
                                    @else
                                        <span class="text-muted">{{ __('transfers.admin.pickup_locations.no_coordinates') }}</span>
                                    @endif
                                </td>
                                <td>{{ $row->car?->title ?: '—' }}</td>
                                <td>{{ $row->vendor?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $row->is_active ? 'success' : 'secondary' }}">{{ $row->is_active ? __('transfers.admin.pickup_locations.status_active') : __('transfers.admin.pickup_locations.status_inactive') }}</span>
                                </td>
                                <td>{{ display_datetime($row->updated_at) }}</td>
                                <td>
                                    <a href="{{ route('car.admin.pickup-locations.edit', ['id' => $row->id]) }}" class="btn btn-sm btn-primary">{{ __('transfers.admin.pickup_locations.edit_action') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">{{ __('transfers.admin.pickup_locations.empty') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $rows->links() }}
            </div>
        </div>
    </div>
@endsection
