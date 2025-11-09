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
<div class="searchMenu-date item js-transfer-datetime">
    <h4 class="text-15 fw-500 ls-2 lh-16">{{ $field['title'] ?? __('Date & Time') }}</h4>
    <div class="row x-gap-20 y-gap-20">
        <div class="col-12 col-md-6">
            <label class="text-13 text-light-1 lh-16 mb-5" for="transfer-date-input">{{ __('Date') }}</label>
            <input type="date"
                   id="transfer-date-input"
                   name="transfer_date"
                   class="form-control rounded-4 py-15 px-20"
                   value="{{ $transferDate }}"
                   data-role="transfer-date">
        </div>
        <div class="col-12 col-md-6">
            <label class="text-13 text-light-1 lh-16 mb-5" for="transfer-time-input">{{ __('Time') }}</label>
            <input type="time"
                   id="transfer-time-input"
                   name="transfer_time"
                   class="form-control rounded-4 py-15 px-20"
                   value="{{ $transferTime }}"
                   data-role="transfer-time">
        </div>
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
