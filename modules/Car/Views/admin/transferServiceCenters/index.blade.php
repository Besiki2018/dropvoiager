@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-500">{{ $page_title ?? __('transfers.admin.service_centers.title') }}</h1>
        <a href="{{ route('car.admin.transfer-service-centers.create') }}" class="btn btn-primary">
            {{ __('transfers.admin.service_centers.create_button') }}
        </a>
    </div>
    @include('admin.message')
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('transfers.admin.service_centers.search_label') }}</label>
                    <input type="text" name="s" class="form-control" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.service_centers.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('transfers.admin.service_centers.location_label') }}</label>
                    <select name="location_id" class="form-select">
                        <option value="">{{ __('transfers.admin.service_centers.location_any') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected(request('location_id') == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('transfers.admin.service_centers.vendor_label') }}</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">{{ __('transfers.admin.service_centers.vendor_any') }}</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>{{ $vendor->name ?? $vendor->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">{{ __('transfers.admin.service_centers.filter_button') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('transfers.admin.service_centers.table_name') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_address') }}</th>
                        <th class="text-center">{{ __('transfers.admin.service_centers.table_coordinates') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_location') }}</th>
                        <th>{{ __('transfers.admin.service_centers.table_vendor') }}</th>
                        <th class="text-end"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td class="fw-500">{{ $row->name }}</td>
                            <td>{{ $row->address }}</td>
                            <td class="text-center">
                                @if(!is_null($row->lat) && !is_null($row->lng))
                                    <span class="badge bg-light text-dark">{{ number_format($row->lat, 6) }}, {{ number_format($row->lng, 6) }}</span>
                                @else
                                    <span class="text-muted">{{ __('transfers.admin.service_centers.coordinates_missing') }}</span>
                                @endif
                            </td>
                            <td>{{ optional($row->location)->name ?? '—' }}</td>
                            <td>{{ optional($row->vendor)->name ?? optional($row->vendor)->email ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('car.admin.transfer-service-centers.edit', $row) }}" class="btn btn-sm btn-outline-primary">{{ __('transfers.admin.service_centers.edit_button') }}</a>
                                <form action="{{ route('car.admin.transfer-service-centers.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('transfers.admin.service_centers.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('transfers.admin.service_centers.delete_button') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('transfers.admin.service_centers.empty_state') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($rows->hasPages())
            <div class="card-footer">{{ $rows->withQueryString()->links() }}</div>
        @endif
    </div>
@endsection
