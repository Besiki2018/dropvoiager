@extends('layouts.user')

@section ('content')
    <div class="row y-gap-20 justify-between items-end pb-60 lg:pb-40 md:pb-32">
        <div class="col-auto">
            <h1 class="text-30 lh-14 fw-600">   {{ __("Availability Cars") }}</h1>
            <div class="text-15 text-light-1">{{ __('Lorem ipsum dolor sit amet, consectetur.') }}</div>
        </div>
        <div class="col-auto"></div>
    </div>
    <div class="bravo-list-item py-30 px-30 rounded-4 bg-white shadow-3">
        <div class="language-navigation">
            <div class="panel-body">
                <div class="filter-div d-flex justify-content-between ">
                    <div class="col-left">
                        <form method="get" action="" class="filter-form filter-form-left d-flex flex-column flex-sm-row" role="search">
                            <input type="text" name="s" value="{{ Request()->s }}" placeholder="{{__('Search by name')}}" class="form-control">&nbsp;&nbsp;
                            <button class="btn-info btn btn-icon btn_search btn-sm" type="submit">{{__('Search')}}</button>
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
            <div class="user-panel">
                <div class="panel-title"><strong>{{__('Availability')}}</strong></div>
                <div class="panel-body no-padding" style="background: #f4f6f8;padding: 0px 15px;">
                    <div class="row">
                        <div class="col-md-3" style="border-right: 1px solid #dee2e6;">
                            <ul class="nav nav-tabs  flex-column vertical-nav" id="items_tab"  role="tablist">
                                @foreach($rows as $k=>$item)
                                    <li class="nav-item event-name ">
                                        <a class="nav-link" data-id="{{$item->id}}" data-bs-toggle="tab" data-bs-target="#calendar-{{$item->id}}" title="{{$item->title}}" >#{{$item->id}} - {{$item->title}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-9" style="background: white;padding: 15px;">
                            <div id="dates-calendar" class="dates-calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">{{__("No cars found")}}</div>
        @endif
        <div class="bravo-pagination mt-0 mb-0">
            {{$rows->appends(request()->query())->links()}}
        </div>
    </div>

    <div id="bravo_modal_calendar" class="modal fade">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{__('Date Information')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
                                    <div class="d-flex align-items-center mb-2" v-for="(hour, index) in form.available_hours" :key="'available-hour-' + index">
                                        <input type="time" class="form-control" v-model="form.available_hours[index]">
                                        <button type="button" class="btn btn-outline-danger btn-sm ml-2" @click="removeAvailableHour(index)">&times;</button>
                                    </div>
                                    <div class="text-muted" v-if="!form.available_hours || !form.available_hours.length">
                                        {{ __('transfers.admin.pricing.available_hours_empty') }}
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" @click="addAvailableHour">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
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
		var calendarEl,calendar,lastId,formModal;
        $('#items_tab').on('show.bs.tab',function (e) {
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
                    	url:"{{route('car.vendor.availability.loadDates')}}",
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
                    formModal.show({
                        start_date:moment(arg.start).format('YYYY-MM-DD'),
                        end_date:moment(arg.end).format('YYYY-MM-DD'),
                    });
				},
                eventClick:function (info) {
                                        var form = Object.assign({},info.event.extendedProps);
                    form.start_date = moment(info.event.start).format('YYYY-MM-DD');
                    form.end_date = moment(info.event.start).format('YYYY-MM-DD');
                    formModal.show(form);
                },
                eventRender: function (info) {
                    $(info.el).find('.fc-title').html(info.event.title);
                }
			});
			calendar.render();
		});

        $('.event-name:first-child a').trigger('click');

        function createDefaultAvailabilityForm() {
            return {
                id:'',
                start_date:'',
                end_date:'',
                active:0,
                number:0,
                available_start:'',
                available_end:'',
                available_hours:[]
            };
        }

        formModal = new Vue({
            el:'#bravo_modal_calendar',
            data:{
                lastResponse:{
                    status:null,
                    message:''
                },
                form:createDefaultAvailabilityForm(),
                onSubmit:false
            },
            methods:{
                prepareFormData:function (form) {
                    var payload = createDefaultAvailabilityForm();
                    if (form && typeof form === 'object') {
                        for (var key in form) {
                            if (!Object.prototype.hasOwnProperty.call(form, key)) {
                                continue;
                            }
                            if (Object.prototype.hasOwnProperty.call(payload, key)) {
                                payload[key] = form[key];
                            }
                        }
                    }
                    payload.active = payload.active ? 1 : 0;
                    payload.number = payload.number ? parseInt(payload.number, 10) || 0 : 0;
                    payload.available_start = this.normaliseHourValue(payload.available_start) || '';
                    payload.available_end = this.normaliseHourValue(payload.available_end) || '';
                    payload.available_hours = this.sanitiseHours(payload.available_hours);
                    return payload;
                },
                show:function (form) {
                    $(this.$el).modal('show');
                    this.lastResponse.message = '';
                    this.onSubmit = false;
                    this.form = this.prepareFormData(form);
                    if(form && form.start_date){
                        var drp = $('.has-daterangepicker').data('daterangepicker');
                        if (drp) {
                            drp.setStartDate(moment(form.start_date).format(bookingCore.date_format));
                            drp.setEndDate(moment(form.end_date).format(bookingCore.date_format));
                        }
                    }
                },
                hide:function () {
                    $(this.$el).modal('hide');
                    this.form = createDefaultAvailabilityForm();
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
                    this.form.available_hours.splice(index,1);
                },
                normaliseHourValue:function (value) {
                    if (value === null || typeof value === 'undefined') {
                        return '';
                    }
                    var text = $.trim(String(value));
                    if (!text) {
                        return '';
                    }
                    if (typeof moment !== 'undefined') {
                        var parsed = moment(text, 'HH:mm', true);
                        if (!parsed.isValid()) {
                            parsed = moment(text, 'H:mm', true);
                        }
                        if (parsed.isValid()) {
                            return parsed.format('HH:mm');
                        }
                    }
                    if (/^\d{1,2}:\d{2}$/.test(text)) {
                        var parts = text.split(':');
                        var hour = parseInt(parts[0], 10);
                        var minute = parseInt(parts[1], 10);
                        if (!isNaN(hour) && !isNaN(minute) && hour >= 0 && hour <= 23 && minute >= 0 && minute <= 59) {
                            var h = hour < 10 ? '0' + hour : String(hour);
                            var m = minute < 10 ? '0' + minute : String(minute);
                            return h + ':' + m;
                        }
                    }
                    return '';
                },
                sanitiseHours:function (hours) {
                    var results = {};
                    if (Array.isArray(hours)) {
                        for (var i = 0; i < hours.length; i++) {
                            var value = this.normaliseHourValue(hours[i]);
                            if (value) {
                                results[value] = value;
                            }
                        }
                    } else if (typeof hours === 'string' && hours.length) {
                        var pieces = hours.split(',');
                        for (var j = 0; j < pieces.length; j++) {
                            var trimmed = this.normaliseHourValue(pieces[j]);
                            if (trimmed) {
                                results[trimmed] = trimmed;
                            }
                        }
                    }
                    var values = Object.keys(results);
                    values.sort();
                    return values;
                },
                saveForm:function () {
                    var me = this;
                    me.lastResponse.message = '';
                    if(this.onSubmit) return;

                    if(!this.validateForm()) return;

                    this.onSubmit = true;
                    var payload = this.prepareFormData(this.form);
                    payload.target_id = lastId;
                    this.form = this.prepareFormData(payload);

                    $.ajax({
                        url:'{{route('car.vendor.availability.store')}}',
                        data:payload,
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
                        error:function () {
                            me.onSubmit = false;
                        }
                    });
                },
                validateForm:function(){
                    var prepared = this.prepareFormData(this.form);
                    if(!prepared.start_date) return false;
                    if(!prepared.end_date) return false;

                    this.form = prepared;
                    return true;
                }
            },
            created:function () {
                var me = this;
                this.$nextTick(function () {
                    $('.has-daterangepicker').daterangepicker({ "locale": {"format": bookingCore.date_format}})
                     .on('apply.daterangepicker',function (e,picker) {
                        me.form.start_date = picker.startDate.format('YYYY-MM-DD');
                        me.form.end_date = picker.endDate.format('YYYY-MM-DD');
                     });

                    $(me.$el).on('hide.bs.modal',function () {
                        me.form = createDefaultAvailabilityForm();
                    });

                })
            },
            mounted:function () {
                // $(this.$el).modal();
            }
        });

    </script>
@endpush
