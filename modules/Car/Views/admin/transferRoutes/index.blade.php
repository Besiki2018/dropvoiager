@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{ __('transfers.admin.routes.title') }}</h1>
            <div class="title-actions">
                <a href="{{ route('car.admin.transfer-routes.create') }}" class="btn btn-primary">{{ __('transfers.admin.routes.add') }}</a>
            </div>
        </div>
        @include('admin.message')
        <div class="filter-div d-flex justify-content-between">
            <div class="col-left">
                <form method="post" action="{{ route('car.admin.transfer-routes.bulkEdit') }}" class="filter-form filter-form-left d-flex justify-content-start">
                    @csrf
                    <select name="action" class="form-control">
                        <option value="">{{ __('transfers.admin.routes.bulk_placeholder') }}</option>
                        <option value="publish">{{ __('transfers.admin.routes.bulk_publish') }}</option>
                        <option value="draft">{{ __('transfers.admin.routes.bulk_draft') }}</option>
                        <option value="delete">{{ __('transfers.admin.routes.bulk_delete') }}</option>
                        <option value="restore">{{ __('transfers.admin.routes.bulk_restore') }}</option>
                    </select>
                    <button data-confirm="{{ __('transfers.admin.routes.bulk_confirm') }}" class="btn-info btn btn-icon dungdt-apply-form-btn" type="button">{{ __('transfers.admin.routes.apply') }}</button>
                </form>
            </div>
            <div class="col-left dropdown">
                <form method="get" action="{{ route('car.admin.transfer-routes.index') }}" class="filter-form filter-form-right d-flex justify-content-end flex-column flex-sm-row" role="search">
                    <input type="text" name="s" value="{{ request('s') }}" placeholder="{{ __('transfers.admin.routes.search_placeholder') }}" class="form-control" />
                    <select name="status" class="form-control ml-2">
                        <option value="">{{ __('transfers.admin.routes.status_placeholder') }}</option>
                        <option value="publish" @selected(request('status') === 'publish')>{{ __('transfers.admin.routes.status_publish') }}</option>
                        <option value="draft" @selected(request('status') === 'draft')>{{ __('transfers.admin.routes.status_draft') }}</option>
                    </select>
                    <button class="btn btn-primary ml-2" type="submit">{{ __('transfers.admin.routes.filter') }}</button>
                </form>
            </div>
        </div>
        <div class="panel">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th width="60px"><input type="checkbox" class="check-all"></th>
                            <th>{{ __('transfers.admin.routes.table_route') }}</th>
                            <th>{{ __('transfers.admin.routes.table_pickup') }}</th>
                            <th>{{ __('transfers.admin.routes.table_dropoff') }}</th>
                            <th width="120px">{{ __('transfers.admin.routes.table_status') }}</th>
                            <th width="150px">{{ __('transfers.admin.routes.table_updated') }}</th>
                            <th width="100px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td><input type="checkbox" class="check-item" name="ids[]" value="{{ $row->id }}"></td>
                                <td>
                                    <div class="fw-500">{{ $row->display_name }}</div>
                                    @if($row->name)
                                        <small class="text-muted">{{ $row->name }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-500">{{ $row->pickup_name }}</div>
                                    @if($row->pickup_address)
                                        <small class="text-muted">{{ $row->pickup_address }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-500">{{ $row->dropoff_name }}</div>
                                    @if($row->dropoff_address)
                                        <small class="text-muted">{{ $row->dropoff_address }}</small>
                                    @endif
                                </td>
                                <td>{{ $row->status }}</td>
                                <td>{{ display_datetime($row->updated_at) }}</td>
                                <td>
                                    <a href="{{ route('car.admin.transfer-routes.edit', ['id' => $row->id]) }}" class="btn btn-sm btn-primary">{{ __('transfers.admin.routes.edit_action') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">{{ __('transfers.admin.routes.empty') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $rows->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
