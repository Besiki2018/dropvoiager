@extends('admin.layouts.app')

@section ('content')
    @php $services  = []; @endphp
    <div class="container-fluid">
        <div class="d-flex justify-content-between mb20">
            <h1 class="title-bar">{{__("Cars Availability Calendar")}}</h1>
        </div>
        @include('admin.message')
        <div class="panel">
            <div class="panel-body">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        <form method="get" action="" class="filter-form filter-form-left d-flex flex-column flex-sm-row" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by name')}}" class="form-control">
                            <button class="btn-info btn btn-icon btn_search" type="submit">{{__('Search')}}</button>
                        </form>
                    </div>
                    <div class="col-right">
                        @if($rows->total() > 0)
                            <span class="count-string">{{ __("Showing :from - :to of :total cars",["from"=>$rows->firstItem(),"to"=>$rows->lastItem(),"total"=>$rows->total()]) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if(count($rows))
        <div class="panel">
            <div class="panel-title"><strong>{{__('Availability')}}</strong></div>
            <div class="panel-body no-padding" style="background: #f4f6f8;padding: 0px 15px;">
                <div class="row">
                    <div class="col-md-3" style="border-right: 1px solid #dee2e6;">
                        <ul class="nav nav-tabs  flex-column vertical-nav" id="items_tab"  role="tablist">
                            @foreach($rows as $k=>$item)
                                <li class="nav-item event-name ">
                                    <a class="nav-link"
                                       data-id="{{$item->id}}"
                                       data-toggle="tab"
                                       href="#calendar-{{$item->id}}"
                                       title="{{$item->title}}"
                                       data-update-url="{{ route('car.admin.availability.updateSettings', $item->id) }}"
                                       data-service-radius="{{ $item->service_radius_km }}"
                                       data-pricing-mode="{{ $item->pricing_mode }}"
                                       data-price-per-km="{{ $item->price_per_km }}"
                                       data-fixed-price="{{ $item->fixed_price }}"
                                       data-time-start="{{ $item->transfer_time_start }}"
                                       data-time-end="{{ $item->transfer_time_end }}">
                                        #{{$item->id}} - {{$item->title}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9" style="background: white;padding: 15px;">
                        <div class="car-availability-settings mb-3">
                            <h5 class="mb-3">{{ __('transfers.admin.pricing.availability_settings_title') }}</h5>
                            <form id="car-settings-form" class="car-settings-form">
                                @csrf
                                <input type="hidden" name="car_id" value="">
                                <div class="alert alert-info d-none js-settings-alert" role="alert"></div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('transfers.admin.pricing.service_radius') }}</label>
                                            <input type="number" step="0.1" min="0" name="service_radius_km" class="form-control" placeholder="{{ __('transfers.admin.pricing.service_radius_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('transfers.admin.pricing.mode_label') }}</label>
                                            <select name="pricing_mode" class="form-control">
                                                <option value="per_km">{{ __('transfers.admin.pricing.mode_per_km') }}</option>
                                                <option value="fixed">{{ __('transfers.admin.pricing.mode_fixed') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4 js-fixed-price-group">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('transfers.admin.pricing.fixed_price') }}</label>
                                            <input type="number" step="0.01" min="0" name="fixed_price" class="form-control" placeholder="{{ __('transfers.admin.pricing.fixed_price_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4 js-price-per-km-group">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('transfers.admin.pricing.price_per_km') }}</label>
                                            <input type="number" step="0.01" min="0" name="price_per_km" class="form-control" placeholder="{{ __('transfers.admin.pricing.price_per_km_placeholder') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ __('transfers.admin.pricing.time_range_label') }}</label>
                                            <div class="d-flex align-items-center time-range-fields">
                                                <input type="time" name="transfer_time_start" class="form-control mr-2" placeholder="{{ __('transfers.admin.pricing.time_start') }}">
                                                <span class="mx-2">–</span>
                                                <input type="time" name="transfer_time_end" class="form-control" placeholder="{{ __('transfers.admin.pricing.time_end') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('transfers.admin.pricing.save_settings') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div id="dates-calendar" class="dates-calendar"></div>
                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="alert alert-warning">{{__("No cars found")}}</div>
        @endif
        <div class="d-flex justify-content-center">
            {{$rows->appends($request->query())->links()}}
        </div>
    </div>
    <div id="bravo_modal_calendar" class="modal fade">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Date Information')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="row form_modal_calendar form-horizontal" novalidate onsubmit="return false">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >{{__('Date Ranges')}}</label>
                                <input readonly type="text" class="form-control has-daterangepicker">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >{{__('Status')}}</label>
                                <br>
                                <label ><input true-value=1 false-value=0 type="checkbox" v-model="form.active"> {{__('Available for booking?')}}</label>
                            </div>
                        </div>
                        <div class="col-md-12" v-show="form.active">
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pricing.time_range_label') }}</label>
                                <div class="d-flex align-items-center">
                                    <input type="time" class="form-control" v-model="form.available_start">
                                    <span class="mx-2">–</span>
                                    <input type="time" class="form-control" v-model="form.available_end">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" v-show="form.active">
                            <div class="form-group">
                                <label>{{ __('transfers.admin.pricing.available_hours_label') }}</label>
                                <div class="available-hours-list">
                                    <div class="d-flex align-items-center mb-2" v-for="(hour, index) in form.available_hours"
                                         :key="'available-hour-' + index">
                                        <input type="time" class="form-control" v-model="form.available_hours[index]">
                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm ml-2"
                                                @click="removeAvailableHour(index)">
                                            &times;
                                        </button>
                                    </div>
                                    <div class="text-muted" v-if="!form.available_hours || !form.available_hours.length">
                                        {{ __('transfers.admin.pricing.available_hours_empty') }}
                                    </div>
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm mt-2"
                                            @click="addAvailableHour">
                                        {{ __('transfers.admin.pricing.available_hours_add') }}
                                    </button>
                                </div>
                                <small class="form-text text-muted">{{ __('transfers.admin.pricing.available_hours_hint') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6" v-show="form.active">
                            <div class="form-group">
                                <label >{{__('Number')}}</label>
                                <input type="number"  v-model="form.number" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 d-none" v-show="form.active">
                            <div class="form-group">
                                <label >{{__('Instant Booking?')}}</label>
                                <br>
                                <label><input true-value=1 false-value=0  type="checkbox"  v-model="form.is_instant" > {{__("Enable instant booking")}}</label>
                            </div>
                        </div>
                    </form>
                    <div v-if="lastResponse.message">
                        <br>
                        <div  class="alert" :class="!lastResponse.status ? 'alert-danger':'alert-success'">@{{ lastResponse.message }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <button type="button" class="btn btn-primary" @click="saveForm">{{__('Save changes')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{asset('libs/fullcalendar-4.2.0/core/main.css')}}">
    <link rel="stylesheet" href="{{asset('libs/fullcalendar-4.2.0/daygrid/main.css')}}">
    <link rel="stylesheet" href="{{asset('libs/daterange/daterangepicker.css')}}">

    <style>
        .event-name{
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
        #dates-calendar .loading{

        }
    </style>
@endpush

@push('js')
    <script src="{{asset('libs/daterange/moment.min.js')}}"></script>
    <script src="{{asset('libs/daterange/daterangepicker.min.js?_ver='.config('app.asset_version'))}}"></script>
    <script src="{{asset('libs/fullcalendar-4.2.0/core/main.js')}}"></script>
    <script src="{{asset('libs/fullcalendar-4.2.0/interaction/main.js')}}"></script>
    <script src="{{asset('libs/fullcalendar-4.2.0/daygrid/main.js')}}"></script>

    <script>
        var calendarEl, calendar, lastId, formModal;
        var settingsForm = $('#car-settings-form');
        var settingsAlert = settingsForm.find('.js-settings-alert');
        var pricingModeSelect = settingsForm.find('[name="pricing_mode"]');
        var pricePerKmGroup = settingsForm.find('.js-price-per-km-group');
        var fixedPriceGroup = settingsForm.find('.js-fixed-price-group');
        var settingsSubmit = settingsForm.find('button[type="submit"]');
        var currentSettingsLink = null;
        var defaultDailyTimeStart = '';
        var defaultDailyTimeEnd = '';
        var defaultSuccessMessage = '{{ addslashes(__('transfers.admin.pricing.settings_updated')) }}';
        var defaultErrorMessage = '{{ addslashes(__('transfers.admin.pricing.settings_save_failed')) }}';
        var defaultTimeStepMinutes = {{ (int) setting_item('car_transfer_time_step', 30) }};

        function setSettingsMessage(message, type) {
            if (!settingsAlert.length) {
                return;
            }
            settingsAlert.removeClass('d-none alert-info alert-success alert-danger');
            var variant = 'alert-info';
            if (type === 'success') {
                variant = 'alert-success';
            } else if (type === 'error') {
                variant = 'alert-danger';
            }
            settingsAlert.addClass(variant);
            if (message) {
                settingsAlert.text(message);
            } else {
                settingsAlert.text('');
                settingsAlert.addClass('d-none');
            }
        }

        function togglePricingModeFields(mode) {
            if (!pricePerKmGroup.length || !fixedPriceGroup.length) {
                return;
            }
            if (mode === 'fixed') {
                fixedPriceGroup.removeClass('d-none');
                pricePerKmGroup.addClass('d-none');
            } else {
                pricePerKmGroup.removeClass('d-none');
                fixedPriceGroup.addClass('d-none');
            }
        }

        function setFormEnabled(enabled) {
            if (!settingsForm.length) {
                return;
            }
            var inputs = settingsForm.find('input, select, button').not('[name="_token"]');
            inputs.prop('disabled', !enabled);
            if (settingsSubmit.length) {
                settingsSubmit.prop('disabled', !enabled);
            }
        }

        function populateSettingsForm($link) {
            if (!settingsForm.length) {
                return;
            }
            if (settingsForm.length && settingsForm[0]) {
                settingsForm[0].reset();
            }
            var actionUrl = '';
            if ($link && $link.length) {
                currentSettingsLink = $link;
                updateDefaultDailyTimeRangeFromLink($link);
                actionUrl = $link.data('updateUrl') || '';
                settingsForm.attr('data-action', actionUrl);
                settingsForm.find('[name="car_id"]').val($link.data('id') || '');
                var radius = $link.data('serviceRadius');
                settingsForm.find('[name="service_radius_km"]').val(radius !== undefined && radius !== null ? radius : '');
                var pricingMode = $link.data('pricingMode') || 'per_km';
                pricingModeSelect.val(pricingMode);
                var pricePerKm = $link.data('pricePerKm');
                settingsForm.find('[name="price_per_km"]').val(pricePerKm !== undefined && pricePerKm !== null ? pricePerKm : '');
                var fixedPrice = $link.data('fixedPrice');
                settingsForm.find('[name="fixed_price"]').val(fixedPrice !== undefined && fixedPrice !== null ? fixedPrice : '');
                settingsForm.find('[name="transfer_time_start"]').val($link.data('timeStart') || '');
                settingsForm.find('[name="transfer_time_end"]').val($link.data('timeEnd') || '');
                togglePricingModeFields(pricingMode);
                setFormEnabled(!!actionUrl);
            } else {
                currentSettingsLink = null;
                defaultDailyTimeStart = '';
                defaultDailyTimeEnd = '';
                settingsForm.attr('data-action', '');
                setFormEnabled(false);
            }
            setSettingsMessage('', 'info');
        }

        function updateDefaultDailyTimeRangeFromLink($link) {
            if (!$link || !$link.length) {
                return;
            }
            defaultDailyTimeStart = $link.data('timeStart') || '';
            defaultDailyTimeEnd = $link.data('timeEnd') || '';
        }

        function getDefaultDailyTimeRange() {
            var start = defaultDailyTimeStart || (settingsForm.find('[name="transfer_time_start"]').val() || '');
            var end = defaultDailyTimeEnd || (settingsForm.find('[name="transfer_time_end"]').val() || '');
            if (!start) {
                start = '00:00';
            }
            if (!end) {
                end = '23:30';
            }
            return {start: start, end: end};
        }

        function normaliseHourValue(value) {
            if (value === null || typeof value === 'undefined') {
                return null;
            }
            var text = $.trim(String(value));
            if (!text) {
                return null;
            }
            if (typeof moment === 'undefined') {
                return text;
            }
            var parsed = moment(text, 'HH:mm', true);
            if (!parsed.isValid()) {
                parsed = moment(text, 'H:mm', true);
            }
            if (!parsed.isValid()) {
                return null;
            }
            return parsed.format('HH:mm');
        }

        function sanitiseHoursArray(hours) {
            var results = {};
            if (Array.isArray(hours)) {
                for (var i = 0; i < hours.length; i++) {
                    var normalised = normaliseHourValue(hours[i]);
                    if (normalised) {
                        results[normalised] = normalised;
                    }
                }
            }
            var values = Object.keys(results);
            values.sort();
            return values;
        }

        function buildDefaultHoursList(start, end) {
            var startHour = normaliseHourValue(start);
            var endHour = normaliseHourValue(end);
            if (!startHour || !endHour || typeof moment === 'undefined') {
                return [];
            }
            var step = parseInt(defaultTimeStepMinutes, 10);
            if (isNaN(step) || step <= 0) {
                step = 30;
            }
            var startMoment = moment(startHour, 'HH:mm');
            var endMoment = moment(endHour, 'HH:mm');
            if (!startMoment.isValid() || !endMoment.isValid()) {
                return [];
            }
            if (endMoment.isBefore(startMoment)) {
                endMoment = startMoment.clone();
            }
            var list = [];
            var maxIterations = Math.ceil((24 * 60) / Math.max(step, 1)) + 2;
            var iteration = 0;
            var cursor = startMoment.clone();
            while (cursor.isSameOrBefore(endMoment) && iteration <= maxIterations) {
                list.push(cursor.format('HH:mm'));
                cursor.add(step, 'minutes');
                iteration++;
            }
            return list;
        }

        if (pricingModeSelect.length) {
            pricingModeSelect.on('change', function () {
                togglePricingModeFields($(this).val());
            });
        }

        if (settingsForm.length) {
            setFormEnabled(false);
        }

        settingsForm.on('submit', function (event) {
            if (!settingsForm.length) {
                return;
            }
            event.preventDefault();
            var actionUrl = settingsForm.attr('data-action');
            if (!actionUrl || !settingsSubmit.length || settingsSubmit.prop('disabled')) {
                return;
            }
            settingsSubmit.prop('disabled', true);
            setSettingsMessage('', 'info');
            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: settingsForm.serialize(),
            }).done(function (response) {
                var message = (response && response.message) ? response.message : defaultSuccessMessage;
                setSettingsMessage(message, 'success');
                if (response && response.car && currentSettingsLink) {
                    currentSettingsLink.data('serviceRadius', response.car.service_radius_km || '');
                    currentSettingsLink.data('pricingMode', response.car.pricing_mode || 'per_km');
                    currentSettingsLink.data('pricePerKm', response.car.price_per_km);
                    currentSettingsLink.data('fixedPrice', response.car.fixed_price);
                    currentSettingsLink.data('timeStart', response.car.transfer_time_start || '');
                    currentSettingsLink.data('timeEnd', response.car.transfer_time_end || '');
                    settingsForm.find('[name="service_radius_km"]').val(response.car.service_radius_km || '');
                    pricingModeSelect.val(response.car.pricing_mode || 'per_km');
                    togglePricingModeFields(response.car.pricing_mode || 'per_km');
                    settingsForm.find('[name="price_per_km"]').val(response.car.price_per_km || '');
                    settingsForm.find('[name="fixed_price"]').val(response.car.fixed_price || '');
                    settingsForm.find('[name="transfer_time_start"]').val(response.car.transfer_time_start || '');
                    settingsForm.find('[name="transfer_time_end"]').val(response.car.transfer_time_end || '');
                    updateDefaultDailyTimeRangeFromLink(currentSettingsLink);
                    setFormEnabled(true);
                }
            }).fail(function (xhr) {
                var message = defaultErrorMessage;
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                setSettingsMessage(message, 'error');
            }).always(function () {
                settingsSubmit.prop('disabled', false);
            });
        });

        $('#items_tab').on('show.bs.tab',function (e) {
                        var targetLink = $(e.target);
                        populateSettingsForm(targetLink);
                        calendarEl = document.getElementById('dates-calendar');
                        lastId = $(e.target).data('id');
            if(calendar){
                                calendar.destroy();
            }
			calendar = new FullCalendar.Calendar(calendarEl, {
                buttonText:{
                    today:  '{{ __('Today') }}',
                },
				plugins: [ 'dayGrid' ,'interaction'],
				header: {},
				selectable: true,
				selectMirror: false,
				allDay:false,
				editable: false,
				eventLimit: true,
				defaultView: 'dayGridMonth',
                firstDay: daterangepickerLocale.first_day_of_week,
				events:{
                    	url:"{{route('car.admin.availability.loadDates')}}",
						extraParams:{
							id:lastId,
                        }
                },
				loading:function (isLoading) {
					if(!isLoading){
						$(calendarEl).removeClass('loading');
					}else{
						$(calendarEl).addClass('loading');
					}
				},
				select: function(arg) {
                    var defaults = getDefaultDailyTimeRange();
                    formModal.show({
                        start_date:moment(arg.start).format('YYYY-MM-DD'),
                        end_date:moment(arg.end).format('YYYY-MM-DD'),
                        available_start: defaults.start,
                        available_end: defaults.end,
                    });
                                },
                eventClick:function (info) {
					var form = Object.assign({},info.event.extendedProps);
                    form.start_date = moment(info.event.start).format('YYYY-MM-DD');
                    form.end_date = moment(info.event.start).format('YYYY-MM-DD');
                    console.log(form);
                    formModal.show(form);
                },
                eventRender: function (info) {
                    $(info.el).find('.fc-title').html(info.event.title);
                }
			});
			calendar.render();
		});

        $('.event-name:first-child a').trigger('click');

        formModal = new Vue({
            el:'#bravo_modal_calendar',
            data:{
                lastResponse:{
                    status:null,
                    message:''
                },
                form:{
                    id:'',
                    start_date:'',
                    end_date:'',
                    is_instant:'',
                    enable_person:0,
                    min_guests:0,
                    max_guests:0,
                    active:0,
                    number:0,
                    available_start:'',
                    available_end:'',
                    available_hours:[]
                },
                formDefault:{
                    id:'',
                    start_date:'',
                    end_date:'',
                    is_instant:'',
                    enable_person:0,
                    min_guests:0,
                    max_guests:0,
                    active:0,
                    number:0,
                    available_start:'',
                    available_end:'',
                    available_hours:[]
                },
                person_types:[

                ],
                person_type_item:{
                    name:'',
                    desc:'',
                    min:'',
                    max:'',
                    price:'',
                },
                onSubmit:false
            },
            methods:{
                show:function (form) {
                    $(this.$el).modal('show');
                    this.lastResponse.message = '';
                    this.onSubmit = false;

                    if(typeof form !='undefined'){
                        this.form = Object.assign({},form);
                        if(typeof this.form.person_types == 'object'){
                            this.person_types = Object.assign({},this.form.person_types);
                        }

                        if(form.start_date){
                            var drp = $('.has-daterangepicker').data('daterangepicker');
                            drp.setStartDate(moment(form.start_date).format(bookingCore.date_format));
                            drp.setEndDate(moment(form.end_date).format(bookingCore.date_format));

                        }
                    }
                    var defaults = getDefaultDailyTimeRange();
                    if (!this.form.available_start) {
                        this.$set(this.form, 'available_start', defaults.start);
                    }
                    if (!this.form.available_end) {
                        this.$set(this.form, 'available_end', defaults.end);
                    }
                    var existingHours = Array.isArray(this.form.available_hours) ? this.form.available_hours.slice() : [];
                    var normalisedHours = sanitiseHoursArray(existingHours);
                    if (normalisedHours.length) {
                        this.$set(this.form, 'available_hours', normalisedHours);
                    } else {
                        var generated = buildDefaultHoursList(this.form.available_start, this.form.available_end);
                        this.$set(this.form, 'available_hours', generated);
                    }
                },
                hide:function () {
                    $(this.$el).modal('hide');
                    this.form = Object.assign({},this.formDefault);
                    this.person_types = [];
                },
                saveForm:function () {
                    this.form.target_id = lastId;
                    var me = this;
                    me.lastResponse.message = '';
                    if(this.onSubmit) return;

                    if(!this.validateForm()) return;

                    this.onSubmit = true;
                    this.form.person_types = Object.assign({},this.person_types);
                    var sanitizedHours = sanitiseHoursArray(this.form.available_hours);
                    this.$set(this.form, 'available_hours', sanitizedHours);
                    if (sanitizedHours.length) {
                        if (!this.form.available_start) {
                            this.$set(this.form, 'available_start', sanitizedHours[0]);
                        }
                        if (!this.form.available_end || this.form.available_end < sanitizedHours[0]) {
                            this.$set(this.form, 'available_end', sanitizedHours[sanitizedHours.length - 1]);
                        }
                    }
                    $.ajax({
                        url:'{{route('car.admin.availability.store')}}',
                        data:this.form,
                        dataType:'json',
                        method:'post',
                        success:function (json) {
                            if(json.status){
                                if(calendar)
                                calendar.refetchEvents();
                                me.hide();
                            }
                            me.lastResponse = json;
                            me.onSubmit = false;
                        },
                        error:function (e) {
                            me.onSubmit = false;
                        }
                    });
                },
                validateForm:function(){
                    if(!this.form.start_date) return false;
                    if(!this.form.end_date) return false;

                    return true;
                },
                addItem:function () {
                    console.log(this.person_types);
                    this.person_types.push(Object.assign({},this.person_type_item));
                },
                deleteItem:function (index) {
                    this.person_types.splice(index,1);
                },
                addAvailableHour:function () {
                    if (!Array.isArray(this.form.available_hours)) {
                        this.$set(this.form, 'available_hours', []);
                    }
                    this.form.available_hours.push('');
                },
                removeAvailableHour:function (index) {
                    if (!Array.isArray(this.form.available_hours)) {
                        return;
                    }
                    this.form.available_hours.splice(index, 1);
                }
            },
            created:function () {
                var me = this;
                this.$nextTick(function () {
                    $('.has-daterangepicker').daterangepicker({ "locale": {"format": bookingCore.date_format}})
                     .on('apply.daterangepicker',function (e,picker) {
                         console.log(picker);
                         me.form.start_date = picker.startDate.format('YYYY-MM-DD');
                         me.form.end_date = picker.endDate.format('YYYY-MM-DD');
                     });

                    $(me.$el).on('hide.bs.modal',function () {

                        this.form = Object.assign({},this.formDefault);
                        this.person_types = [];

                    });

                })
            },
            mounted:function () {
                // $(this.$el).modal();
            }
        });

    </script>
@endpush
