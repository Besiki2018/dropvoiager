@php
    $defaultDate = now()->timezone('Asia/Tbilisi')->format('Y-m-d');
    $transferDate = request()->input('transfer_date', request()->input('start', $defaultDate));
    $transferTimeRaw = request()->input('transfer_time', '');
    $transferTime = $transferTimeRaw ? substr($transferTimeRaw, 0, 5) : '00:00';
    $isoDatetime = request()->input('datetime');
    if (!$isoDatetime && $transferDate) {
        $isoDatetime = sprintf('%sT%s:00+04:00', $transferDate, $transferTime);
    }
@endphp
<div class="filter-item js-transfer-datetime">
    <div class="form-group">
        <label class="text-13 text-light-1 lh-16 mb-5 d-block" for="transfer-date-map">{{ __('Date') }}</label>
        <input type="date" id="transfer-date-map" name="transfer_date" class="form-control py-10 px-15" value="{{ $transferDate }}" data-role="transfer-date">
    </div>
    <div class="form-group mt-10">
        <label class="text-13 text-light-1 lh-16 mb-5 d-block" for="transfer-time-map">{{ __('Time') }}</label>
        <input type="time" id="transfer-time-map" name="transfer_time" class="form-control py-10 px-15" value="{{ $transferTime }}" data-role="transfer-time">
    </div>
    <input type="hidden" name="datetime" value="{{ $isoDatetime }}" data-role="transfer-datetime">
    <input type="hidden" class="check-in-input" value="{{ $transferDate }}" name="start">
    <input type="hidden" class="check-out-input" value="{{ $transferDate }}" name="end">
    <input type="hidden" name="date" value="{{ $transferDate }} - {{ $transferDate }}">
</div>

@once
    @push('js')
        <script src="{{ asset('module/car/js/transfer-search.js?_v='.config('app.asset_version')) }}"></script>
    @endpush
@endonce
