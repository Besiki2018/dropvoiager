@extends('layouts.user')
@section('content')
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">{{ __("Manage Coupon") }}</h1>
            <div class="text-15 text-light-1">{{ __("Lorem ipsum dolor sit amet, consectetur.") }}</div>
        </div>
        <div class="col-auto">
            @if(Auth::user()->hasPermission('coupon_create') && empty($recovery))
                <a href="{{ route("coupon.vendor.create") }}" class="button h-50 px-24 -dark-1 bg-blue-1 text-white">{{__("Add Coupon")}} <div class="icon-arrow-top-right ml-15"></div></a>
            @endif
        </div>
    </div>
    @include('admin.message')

    <div class="py-30 px-30 rounded-4 bg-white shadow-3">
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        <form action="" class="bravo-form-item">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Code')}}</th>
                                            <th> {{ __('Name')}}</th>
                                            <th> {{ __('Amount')}}</th>
                                            <th> {{ __('Discount Type')}}</th>
                                            <th> {{ __('End Date')}}</th>
                                            <th width="70px"> {{ __('Status')}}</th>
                                            <th width="200px"> {{ __("Action") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($rows->total() > 0)
                                            @foreach($rows as $row)
                                                <tr class="{{$row->status}}">
                                                    <td class="title">
                                                        <strong>{{$row->code}}</strong>
                                                    </td>
                                                    <td>{{$row->name}}</td>
                                                    <td>{{$row->amount}}</td>
                                                    <td>{{$row->discount_type == 'percent' ? __("Percent") : __("Amount")}}</td>
                                                    <td>{{ ($row->end_date) }}</td>
                                                    <td><span class="badge badge-{{ $row->status }}">{{ $row->status }}</span></td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <a href="{{route('coupon.vendor.edit',['id'=>$row->id])}}" class="btn btn-sm btn-primary btn-info-booking mt-1 mr-1">{{__('Edit')}}</a>
                                                            <a href="{{route('coupon.vendor.delete',['id'=>$row->id])}}" class="btn btn-sm btn-secondary btn-info-booking mt-1">{{__('Delete')}}</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6">{{__("No data")}}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <div class="bravo-pagination">
                            <span class="count-string mb-2">{{ __("Showing :from - :to of :total coupon",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                            {{$rows->appends(request()->query())->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
