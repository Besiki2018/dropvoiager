@php
    $carDateRaw = request()->input('car_date');
    $carDateValue = '';
    $carDateDisplay = __('Select date');
    if ($carDateRaw) {
        try {
            $carDateValue = \Illuminate\Support\Carbon::parse($carDateRaw)->format('Y-m-d');
        } catch (\Exception $exception) {
            $carDateValue = $carDateRaw;
        }
        $timestamp = strtotime($carDateValue);
        if ($timestamp) {
            $carDateDisplay = display_date($timestamp);
        } else {
            $carDateDisplay = $carDateValue;
        }
    }
@endphp
<div class="searchMenu-date form-date-search is_single_picker position-relative item">
    <div class="date-wrapper" data-x-dd-click="searchMenu-date">
        <h4 class="text-15 fw-500 ls-2 lh-16">{{ $field['title'] ?? __('Car Calendar') }}</h4>
        <div class="text-14 text-light-1 ls-2 lh-16">
            <span class="render check-in-render">{{ $carDateDisplay }}</span>
        </div>
    </div>
    <input type="hidden" class="check-in-input" name="car_date" value="{{ $carDateValue }}">
    <input type="hidden" class="check-out-input" value="{{ $carDateValue }}">
    <input type="text" class="check-in-out absolute invisible" value="{{ $carDateValue }}" autocomplete="off">
</div>
