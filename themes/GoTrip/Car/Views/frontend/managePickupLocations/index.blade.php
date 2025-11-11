@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ __('transfers.admin.pickup_locations.vendor_title') }}</h1>
            <div class="text-15 text-light-1">{{ __('transfers.admin.pickup_locations.vendor_subtitle') }}</div>
        </div>
        <div class="col-auto">
            <a href="{{ route('car.vendor.pickup-locations.create') }}" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">
                {{ __('transfers.admin.pickup_locations.add') }}
                <div class="icon-arrow-top-right ml-15"></div>
            </a>
        </div>
    </div>
    @include('admin.message')
    <div class="bravo-list-item py-30 px-30 rounded-4 bg-white shadow-3">
        <form class="row y-gap-20 justify-between items-end mb-30" method="get">
            <div class="col-md-6">
                <label class="text-15 fw-500 mb-10">{{ __('transfers.admin.pickup_locations.search_placeholder') }}</label>
                <input type="text" name="s" class="form-control" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.pickup_locations.search_placeholder') }}">
            </div>
            <div class="col-auto">
                <button class="button h-50 px-24 -dark-1 bg-blue-1 text-white" type="submit">{{ __('transfers.admin.pickup_locations.filter') }}</button>
            </div>
        </form>
        @if($rows->total() > 0)
            <div class="table-responsive">
                <table class="table table-borderless">
                    <thead>
                    <tr class="text-15 text-uppercase text-blue-1">
                        <th>{{ __('transfers.admin.pickup_locations.table_name') }}</th>
                        <th>{{ __('transfers.admin.pickup_locations.table_address') }}</th>
                        <th>{{ __('transfers.admin.pickup_locations.table_car') }}</th>
                        <th>{{ __('transfers.admin.pickup_locations.table_status') }}</th>
                        <th class="text-right">{{ __('transfers.admin.pickup_locations.table_updated') }}</th>
                        <th class="text-right"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rows as $row)
                        <tr class="text-15">
                            <td class="fw-500">{{ $row->display_name ?: $row->name }}</td>
                            <td>{{ $row->address ?: '—' }}</td>
                            <td>{{ $row->car?->title ?: '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $row->is_active ? 'success' : 'light' }} text-{{ $row->is_active ? 'white' : 'dark-1' }}">{{ $row->is_active ? __('transfers.admin.pickup_locations.status_active') : __('transfers.admin.pickup_locations.status_inactive') }}</span>
                            </td>
                            <td class="text-right">{{ display_datetime($row->updated_at) }}</td>
                            <td class="text-right">
                                <div class="d-flex justify-end">
                                    <a href="{{ route('car.vendor.pickup-locations.edit', $row->id) }}" class="button -sm -outline-blue-1 text-blue-1 mr-10">{{ __('transfers.admin.pickup_locations.edit_action') }}</a>
                                    <form action="{{ route('car.vendor.pickup-locations.destroy', $row->id) }}" method="post" onsubmit="return confirm('{{ __('transfers.admin.pickup_locations.delete_confirm') }}')">
                                        @csrf
                                        <button class="button -sm -outline-red-1 text-red-1" type="submit">{{ __('transfers.admin.pickup_locations.delete_action') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bravo-pagination mt-30">
                <span class="count-string">{{ __('transfers.admin.pickup_locations.pagination', ['from' => $rows->firstItem(), 'to' => $rows->lastItem(), 'total' => $rows->total()]) }}</span>
                <div class="mt-2">{{ $rows->appends(request()->query())->links() }}</div>
            </div>
        @else
            <div class="text-15 text-light-1">{{ __('transfers.admin.pickup_locations.empty') }}</div>
        @endif
    </div>
@endsection
