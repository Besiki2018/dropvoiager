@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-500">{{ $page_title ?? __('transfers.admin.locations.title') }}</h1>
        <a href="{{ route('car.admin.transfer-locations.create') }}" class="btn btn-primary">
            {{ __('transfers.admin.locations.create_button') }}
        </a>
    </div>
    @include('admin.message')
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('transfers.admin.locations.search_label') }}</label>
                    <input type="text" name="s" class="form-control" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.locations.search_placeholder') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('transfers.admin.locations.vendor_label') }}</label>
                    <select name="vendor_id" class="form-select">
                        <option value="">{{ __('transfers.admin.locations.vendor_any') }}</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected(request('vendor_id') == $vendor->id)>{{ $vendor->name ?? $vendor->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('transfers.admin.locations.status_label') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('transfers.admin.locations.status_any') }}</option>
                        <option value="1" @selected(request('status') === '1')>{{ __('transfers.admin.locations.status_active') }}</option>
                        <option value="0" @selected(request('status') === '0')>{{ __('transfers.admin.locations.status_inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">{{ __('transfers.admin.locations.filter_button') }}</button>
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
                        <th>{{ __('transfers.admin.locations.table_name') }}</th>
                        <th>{{ __('transfers.admin.locations.table_address') }}</th>
                        <th class="text-center">{{ __('transfers.admin.locations.table_coordinates') }}</th>
                        <th>{{ __('transfers.admin.locations.table_vendor') }}</th>
                        <th class="text-center">{{ __('transfers.admin.locations.table_status') }}</th>
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
                                    <span class="text-muted">{{ __('transfers.admin.locations.coordinates_missing') }}</span>
                                @endif
                            </td>
                            <td>{{ optional($row->vendor)->name ?? optional($row->vendor)->email ?? '—' }}</td>
                            <td class="text-center">
                                @if($row->is_active)
                                    <span class="badge bg-success">{{ __('transfers.admin.locations.status_active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('transfers.admin.locations.status_inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('car.admin.transfer-locations.edit', $row) }}" class="btn btn-sm btn-outline-primary">{{ __('transfers.admin.locations.edit_button') }}</a>
                                <form action="{{ route('car.admin.transfer-locations.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('{{ __('transfers.admin.locations.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ __('transfers.admin.locations.delete_button') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('transfers.admin.locations.empty_state') }}</td>
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
