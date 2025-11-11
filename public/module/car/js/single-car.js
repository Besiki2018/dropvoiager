(function ($) {
    new Vue({
        el:'#bravo_car_book_app',
        data:{
            id:'',
            extra_price:[],
            buyer_fees:[],
            pickup_location_id:'',
            pickup_location:null,
            dropoff:{},
            transfer_datetime:'',
            transfer_date:'',
            transfer_time:'',
            quote_url:'',
            pricing_meta:null,
            transfer_quote:null,
            transfer_quote_loading:false,
            transfer_quote_error:'',
            transfer_availability_loading:false,
            transfer_availability_error:'',
            transfer_availability_note:'',
            transfer_availability_blocked:false,
            transfer_time_slots:[],
            availability_url:'',
            availability_messages:{},
            availability_timer:null,
            availability_xhr:null,
            quote_xhr:null,
            timezone_offset:'',
            is_initialising:true,
            datetime_required_message:'',
            pending_quote_refresh:false,
            quote_refresh_timer:null,
            pending_availability_refresh:false,
            fieldErrors:{
                pickup:'',
                dropoff:'',
                datetime:'',
                passengers:''
            },
            suppressPassengerWatch:false,
            form_error_message:'',
            message:{
                content:'',
                type:false
            },
            html:'',
            onSubmit:false,
            start_date:'',
            end_date:'',
            start_date_html:'',

            step:1,
            allEvents:[],
            number:0,
            max_number:1,
            total_price_before_fee:0,
            total_price_fee:0,

            is_form_enquiry_and_book:false,
            enquiry_type:'book',
            enquiry_is_submit:false,
            enquiry_name:"",
            enquiry_email:"",
            enquiry_phone:"",
            enquiry_note:"",
        },
        watch:{
            extra_price:{
                handler:function f() {
                    this.step = 1;
                },
                deep:true
            },
            start_date(){
                this.step = 1;
            },
            transfer_date:function (value) {
                this.syncTransferDateState();
                if (value) {
                    this.setFieldError('datetime', '');
                }
                if (this.$el) {
                    $(this.$el).trigger('transfer:update-date', [value || '']);
                }
                if (value && typeof moment !== 'undefined') {
                    var day = moment(value, 'YYYY-MM-DD', true);
                    if (day.isValid()) {
                        this.fetchEvents(day, day);
                    }
                }
                this.handleTransferFieldChange();
                this.handleAvailabilityFieldChange();
            },
            transfer_time:function () {
                this.syncTransferDateState();
                if (this.transfer_date && this.transfer_time) {
                    this.setFieldError('datetime', '');
                }
                this.handleTransferFieldChange();
                if (this.transfer_time && !this.isTimeSlotValid(this.transfer_time)) {
                    this.transfer_time = '';
                }
            },
            number:function (value) {
                if (this.suppressPassengerWatch) {
                    this.suppressPassengerWatch = false;
                    return;
                }
                var normalized = parseInt(value, 10);
                if (isNaN(normalized) || normalized < 1) {
                    normalized = 1;
                }
                var max = parseInt(this.max_number, 10);
                if (!isNaN(max) && max > 0 && normalized > max) {
                    normalized = max;
                }
                if (String(normalized) !== String(value)) {
                    this.suppressPassengerWatch = true;
                    this.number = normalized;
                    return;
                }
                this.setFieldError('passengers', '');
                this.handleTransferFieldChange();
                this.handleAvailabilityFieldChange();
            },
            pickup_location:{
                handler:function () {
                    this.setFieldError('pickup', '');
                    this.handleTransferFieldChange();
                    this.handleAvailabilityFieldChange();
                },
                deep:true
            },
            dropoff:{
                handler:function () {
                    this.setFieldError('dropoff', '');
                    this.handleTransferFieldChange();
                    this.handleAvailabilityFieldChange();
                },
                deep:true
            }
        },
        computed:{
            hasTimeSlots:function () {
                return Array.isArray(this.transfer_time_slots) && this.transfer_time_slots.length > 0;
            },
            priceSummary:function () {
                var quote = this.transfer_quote || null;
                var meta = this.pricing_meta || {};
                var messages = this.availability_messages || {};
                var passengerCount = this.getPassengerCount();
                var totalNumeric = null;
                var totalDisplay = null;
                var distanceNumeric = null;
                var distanceDisplay = null;

                if (quote) {
                    var passengersFromQuote = this.toNumeric(quote.passengers);
                    if (passengersFromQuote && passengersFromQuote > 0) {
                        passengerCount = passengersFromQuote;
                    }
                    var totalCandidate = typeof quote.total_price !== 'undefined' ? quote.total_price : quote.price;
                    var totalCandidateNumeric = this.toNumeric(totalCandidate);
                    var singlePriceNumeric = this.toNumeric(quote.price_single);
                    if (totalCandidateNumeric !== null) {
                        totalNumeric = totalCandidateNumeric;
                    } else if (singlePriceNumeric !== null) {
                        totalNumeric = singlePriceNumeric * passengerCount;
                    }
                    totalDisplay = quote.total_price_formatted || quote.price_formatted || null;
                    distanceNumeric = this.toNumeric(quote.distance_km);
                    if (!distanceDisplay && quote.distance_formatted) {
                        distanceDisplay = quote.distance_formatted;
                    }
                }

                if (totalNumeric === null && totalDisplay === null && meta && meta.mode === 'fixed') {
                    var fixedMetaPrice = this.toNumeric(meta.fixed_price);
                    if (fixedMetaPrice !== null) {
                        var metaBaseFee = this.toNumeric(meta.base_fee);
                        totalNumeric = fixedMetaPrice * passengerCount;
                        if (metaBaseFee !== null) {
                            totalNumeric += metaBaseFee;
                        }
                    }
                }

                if (totalNumeric === null && totalDisplay === null) {
                    return null;
                }

                if (totalDisplay === null && totalNumeric !== null) {
                    totalDisplay = this.formatMoney(totalNumeric);
                }

                if (!distanceDisplay && distanceNumeric !== null) {
                    distanceDisplay = this.formatDistance(distanceNumeric);
                }

                var mode = (quote && quote.pricing_mode) || meta.mode || 'per_km';
                if (mode !== 'fixed') {
                    mode = 'per_km';
                }

                var modeDisplay = '';
                var modeLabel = messages.pricing_mode_label || '';
                var fixedLabel = messages.pricing_mode_fixed || '';
                var perKmLabel = messages.pricing_mode_per_km || '';
                var modeValueLabel = mode === 'fixed' ? (fixedLabel || 'Fixed price') : (perKmLabel || 'Per kilometer');
                if (modeLabel && modeValueLabel) {
                    modeDisplay = modeLabel + ': ' + modeValueLabel;
                } else if (modeValueLabel) {
                    modeDisplay = modeValueLabel;
                }

                var unitPriceLabel = '';
                if (mode === 'fixed') {
                    var fixedValue = this.toNumeric((quote && typeof quote.unit_price !== 'undefined') ? quote.unit_price : meta.fixed_price);
                    if (fixedValue !== null) {
                        var formattedFixed = this.formatMoney(fixedValue);
                        var fixedTemplate = messages.fixed_price_display || '';
                        unitPriceLabel = fixedTemplate ? fixedTemplate.replace(':price', formattedFixed) : formattedFixed;
                    }
                } else {
                    var perKmValue = this.toNumeric((quote && typeof quote.unit_price !== 'undefined') ? quote.unit_price : meta.price_per_km);
                    if (perKmValue !== null) {
                        var formattedPerKm = this.formatMoney(perKmValue);
                        var perKmTemplate = messages.price_per_km_display || '';
                        unitPriceLabel = perKmTemplate ? perKmTemplate.replace(':price', formattedPerKm) : formattedPerKm;
                    }
                }

                var serviceRadiusLabel = '';
                var radiusValue = this.toNumeric(meta.service_radius_km);
                if (radiusValue !== null && radiusValue > 0) {
                    var radiusTemplate = messages.service_radius_display || '';
                    if (radiusTemplate) {
                        serviceRadiusLabel = radiusTemplate.replace(':radius', radiusValue.toFixed(2));
                    } else {
                        serviceRadiusLabel = radiusValue.toFixed(2) + ' km';
                    }
                }

                return {
                    total: totalDisplay,
                    distance: distanceDisplay,
                    mode_display: modeDisplay,
                    unit_price_display: unitPriceLabel,
                    service_radius_display: serviceRadiusLabel
                };
            },
            availableHoursMessage:function () {
                var meta = this.pricing_meta || {};
                var start = meta.available_time_start || '';
                var end = meta.available_time_end || '';
                if (!start || !end) {
                    return '';
                }
                var template = (this.availability_messages && this.availability_messages.available_hours_range) || '';
                if (template) {
                    return template.replace(':start', start).replace(':end', end);
                }
                return start + ' – ' + end;
            },
            total_price:function(){
                var me = this;
                if (me.start_date !== "") {
                    var total_price = 0;
                    var startDate = new Date(me.start_date).getTime();
                    var endDate = new Date(me.end_date).getTime();
                    var isBook = true;
                    for (var ix in me.allEvents) {
                        var item = me.allEvents[ix];
                        var cur_date = new Date(item.start).getTime();
                        if (startDate == endDate) {
                            if (cur_date >= startDate && cur_date <= endDate) {
                                total_price += parseFloat(item.price);
                                if (item.active === 0) {
                                    isBook = false
                                }
                            }
                        } else {
                            if (cur_date >= startDate && cur_date <= endDate) {
                                total_price += parseFloat(item.price);
                                if (item.active === 0) {
                                    isBook = false
                                }
                            }
                        }
                    }

                    total_price = me.number * total_price;

                    var duration_in_day = moment(endDate).diff(moment(startDate), 'days') + 1;
                    for (var ix in me.extra_price) {
                        var item = me.extra_price[ix];
                        if(!item.price) continue;
                        var type_total = 0;
                        if (item.enable == 1) {
                            switch (item.type) {
                                case "one_time":
                                    type_total += parseFloat(item.price) * me.number;
                                    break;
                                case "per_day":
                                        type_total += parseFloat(item.price) * Math.max(1,duration_in_day) * me.number;
                                    break;
                            }
                            total_price += type_total;
                        }
                    }
                    this.total_price_before_fee = total_price;

                    var total_fee = 0;
                    for (var ix in me.buyer_fees) {
                        var item = me.buyer_fees[ix];
                        if(!item.price) continue;

                        //for Fixed
                        var fee_price = parseFloat(item.price);

                        //for Percent
                        if (typeof item.unit !== "undefined" && item.unit === "percent" ) {
                            fee_price = ( total_price / 100 ) * fee_price;
                        }

                        if (typeof item.per_person !== "undefined") {
                            fee_price = fee_price * guests;
                        }
                        total_fee += fee_price;
                    }
                    total_price += total_fee;
                    this.total_price_fee = total_fee;

                    if (isBook === false || me.number === 0) {
                        return 0;
                    } else {
                       return total_price;
                    }
                }
                return 0;
            },
            total_price_html:function(){
                if(!this.total_price) return '';
                return window.bravo_format_money(this.total_price);
            },
            pay_now_price:function(){
                if(this.is_deposit_ready){
                    var total_price_depossit = 0;

                    var tmp_total_price = this.total_price;
                    var deposit_fomular = this.deposit_fomular;
                    if(deposit_fomular === "deposit_and_fee"){
                        tmp_total_price = this.total_price_before_fee;
                    }

                    switch (this.deposit_type) {
                        case "percent":
                            total_price_depossit =  tmp_total_price * this.deposit_amount / 100;
                            break;
                        default:
                            total_price_depossit =  this.deposit_amount;
                    }
                    if(deposit_fomular === "deposit_and_fee"){
                        total_price_depossit = total_price_depossit + this.total_price_fee;
                    }

                    return  total_price_depossit
                }
                return this.total_price;
            },
            pay_now_price_html:function(){
                return window.bravo_format_money(this.pay_now_price);
            },
            daysOfWeekDisabled(){
                var res = [];

                for(var k in this.open_hours)
                {
                    if(typeof this.open_hours[k].enable == 'undefined' || this.open_hours[k].enable !=1 ){

                        if(k == 7){
                            res.push(0);
                        }else{
                            res.push(k);
                        }
                    }
                }

                return res;
            },
            is_deposit_ready:function () {
                if(this.deposit && this.deposit_amount) return true;
                return false;
            },

        },
        created:function(){
            for(var k in bravo_booking_data){
                this[k] = bravo_booking_data[k];
            }
        },
        mounted(){
            var me = this;
            var $root = $(this.$el);
            var quoteUrl = $root.data('quoteUrl') || $root.attr('data-quote-url') || '';
            if (quoteUrl) {
                this.quote_url = quoteUrl;
            }
            var pricingMeta = $root.data('pricingMeta');
            if (!pricingMeta || typeof pricingMeta !== 'object') {
                pricingMeta = this.parseJsonAttribute($root.attr('data-pricing-meta'));
            }
            if (pricingMeta) {
                this.pricing_meta = pricingMeta;
            }
            var initialQuote = $root.data('initialQuote');
            if (!initialQuote || typeof initialQuote !== 'object') {
                initialQuote = this.parseJsonAttribute($root.attr('data-initial-quote'));
            }
            if (initialQuote) {
                this.transfer_quote = initialQuote;
            }
            if (window.BravoTransferForm && typeof window.BravoTransferForm.initAll === 'function') {
                window.BravoTransferForm.initAll($root);
            }
            var availabilityUrl = $root.data('availabilityUrl') || $root.attr('data-availability-url') || '';
            if (availabilityUrl) {
                this.availability_url = availabilityUrl;
            }
            var availabilityMessages = $root.data('availabilityMessages');
            if (!availabilityMessages || typeof availabilityMessages !== 'object') {
                availabilityMessages = this.parseJsonAttribute($root.attr('data-availability-messages')) || {};
            }
            if (availabilityMessages) {
                this.availability_messages = availabilityMessages;
            }
            var timezoneOffset = $root.data('timezoneOffset') || $root.attr('data-timezone-offset') || '';
            if (timezoneOffset) {
                this.timezone_offset = timezoneOffset;
            }
            $root.on('transfer:date-error', function (event, message) {
                me.setFieldError('datetime', message || '');
            });
            $root.on('transfer:form-error', function (event, message) {
                me.form_error_message = message || '';
            });
            $root.on('transfer:context-changed', function (event, context) {
                context = context || {};
                if (context.pickup) {
                    me.pickup_location = context.pickup;
                    me.pickup_location_id = context.pickup.id || '';
                }
                if (context.dropoff) {
                    me.dropoff = context.dropoff;
                }
                me.handleTransferFieldChange();
            });
            $root.on('transfer:date-changed', function (event, isoDate) {
                var value = isoDate || '';
                if (me.transfer_date !== value) {
                    me.transfer_date = value;
                }
            });
            var initialPickupPayload = $root.find('.js-transfer-pickup-payload').val();
            if (initialPickupPayload) {
                try {
                    var parsedPickup = JSON.parse(initialPickupPayload);
                    if (parsedPickup) {
                        me.pickup_location = parsedPickup;
                        me.pickup_location_id = parsedPickup.id || '';
                    }
                } catch (err) {}
            }
            var initialDropoffPayload = $root.find('.js-transfer-dropoff-json').val();
            if (initialDropoffPayload) {
                try {
                    var parsedDropoff = JSON.parse(initialDropoffPayload);
                    if (parsedDropoff) {
                        me.dropoff = parsedDropoff;
                    }
                } catch (err) {}
            }
            var dateField = $root.find('.js-transfer-date');
            if (dateField.length) {
                var initialDateValue = dateField.val() || dateField.attr('value') || '';
                this.transfer_date = initialDateValue;
                dateField.val(initialDateValue);
            }
            var timeField = $root.find('.js-transfer-time');
            if (timeField.length) {
                var initialTimeValue = timeField.val() || timeField.attr('value') || '';
                if (initialTimeValue) {
                    timeField.val(initialTimeValue);
                }
                this.transfer_time = initialTimeValue;
            }
            var datetimeMessage = $root.data('datetimeRequired');
            if (datetimeMessage) {
                this.datetime_required_message = datetimeMessage;
            }
            this.syncTransferDateState();
            if (this.pending_availability_refresh) {
                this.pending_availability_refresh = false;
                this.queueAvailabilityFetch(50);
            } else if (this.transfer_date) {
                this.queueAvailabilityFetch(50);
            } else {
                this.clearAvailabilityState();
            }
            this.is_initialising = false;
            if (this.pending_quote_refresh) {
                this.pending_quote_refresh = false;
                this.queueQuoteRefresh(50);
            } else {
                this.queueQuoteRefresh(50);
            }
        },
        beforeDestroy:function () {
            this.cancelQuoteRequest();
            this.cancelAvailabilityRequest();
        },
        methods:{
            parseJsonAttribute:function (value) {
                if (!value) {
                    return null;
                }
                if (typeof value === 'object') {
                    return value;
                }
                if (typeof value !== 'string') {
                    return null;
                }
                try {
                    return JSON.parse(value);
                } catch (err) {
                    return null;
                }
            },
            handleTransferFieldChange:function () {
                if (this.is_initialising) {
                    this.pending_quote_refresh = true;
                    return;
                }
                this.pending_quote_refresh = false;
                this.queueQuoteRefresh();
            },
            handleAvailabilityFieldChange:function () {
                if (!this.transfer_date) {
                    this.cancelAvailabilityRequest();
                    this.clearAvailabilityState();
                    return;
                }
                if (this.is_initialising) {
                    this.pending_availability_refresh = true;
                    return;
                }
                this.pending_availability_refresh = false;
                this.queueAvailabilityFetch();
            },
            setFieldError:function (field, message) {
                if (!this.fieldErrors || typeof field === 'undefined') {
                    return;
                }
                this.$set(this.fieldErrors, field, message || '');
            },
            clearFieldErrors:function () {
                if (!this.fieldErrors) {
                    return;
                }
                for (var key in this.fieldErrors) {
                    if (!Object.prototype.hasOwnProperty.call(this.fieldErrors, key)) {
                        continue;
                    }
                    this.$set(this.fieldErrors, key, '');
                }
            },
            getPassengerCount:function () {
                var count = parseInt(this.number, 10);
                if (isNaN(count) || count < 1) {
                    count = 1;
                }
                var max = parseInt(this.max_number, 10);
                if (!isNaN(max) && max > 0 && count > max) {
                    count = max;
                }
                return count;
            },
            handleTotalPrice:function() {
            },
            syncTransferDateState:function () {
                var date = this.transfer_date || '';
                this.start_date = date;
                this.end_date = date;
                if (date && typeof moment !== 'undefined') {
                    this.start_date_html = moment(date, 'YYYY-MM-DD').format(bookingCore.date_format);
                } else {
                    this.start_date_html = date ? date : '';
                }
            },
            fetchEvents(start,end){
                var me = this;
                var data = {
                    start: start.format('YYYY-MM-DD'),
                    end: end.format('YYYY-MM-DD'),
                    id:bravo_booking_data.id,
                    for_single:1
                };

                $.ajax({
                    url: bravo_booking_i18n.load_dates_url,
                    dataType:"json",
                    type:'get',
                    data:data,
                    beforeSend: function() {
                        $('.daterangepicker').addClass("loading");
                    },
                    success:function (json) {
                        me.allEvents = json;
                        if (me.transfer_date) {
                            for (var idx = 0; idx < json.length; idx++) {
                                var availability = json[idx];
                                if (availability.start === me.transfer_date && typeof availability.number !== 'undefined') {
                                    me.max_number = availability.number;
                                    var maxNormalized = parseInt(me.max_number, 10);
                                    if (!isNaN(maxNormalized) && maxNormalized > 0 && parseInt(me.number, 10) > maxNormalized) {
                                        me.suppressPassengerWatch = true;
                                        me.number = maxNormalized;
                                        if (!me.is_initialising) {
                                            me.refreshTransferQuote();
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        if (me.$refs && me.$refs.start_date) {
                            var $dateDisplay = $(me.$refs.start_date);
                            $dateDisplay.data('transferEvents', json);
                            var drp = $dateDisplay.data('daterangepicker');
                            if (drp) {
                                drp.allEvents = json;
                                drp.transferEvents = json;
                                drp.renderCalendar('left');
                                if (!drp.singleDatePicker) {
                                    drp.renderCalendar('right');
                                }
                            }
                        }
                        $('.daterangepicker').removeClass("loading");
                    },
                    error:function (e) {
                        console.log(e);
                        console.log("Can not get availability");
                    }
                });
            },
            formatMoney: function (m) {
                return window.bravo_format_money(m);
            },
            formatDistance:function (km) {
                var value = this.toNumeric(km);
                if (value === null) {
                    return null;
                }
                return value.toFixed(2) + ' km';
            },
            shouldRequestQuote:function () {
                var pickup = this.pickup_location || {};
                var dropoff = this.dropoff || {};
                var pickupLat = parseFloat(pickup.lat);
                var pickupLng = parseFloat(pickup.lng);
                var dropLat = parseFloat(dropoff.lat);
                var dropLng = parseFloat(dropoff.lng);
                if (isNaN(pickupLat) || isNaN(pickupLng)) {
                    return false;
                }
                if (!dropoff.place_id || isNaN(dropLat) || isNaN(dropLng)) {
                    return false;
                }
                return true;
            },
            queueQuoteRefresh:function (delay) {
                var me = this;
                var wait = typeof delay === 'number' ? delay : 200;
                if (!this.shouldRequestQuote()) {
                    this.cancelQuoteRequest();
                    this.transfer_quote = null;
                    this.transfer_quote_loading = false;
                    this.transfer_quote_error = '';
                    return;
                }
                if (this.quote_refresh_timer) {
                    window.clearTimeout(this.quote_refresh_timer);
                    this.quote_refresh_timer = null;
                }
                this.quote_refresh_timer = window.setTimeout(function () {
                    me.quote_refresh_timer = null;
                    me.refreshTransferQuote();
                }, wait);
            },
            queueAvailabilityFetch:function (delay) {
                var me = this;
                var wait = typeof delay === 'number' ? delay : 200;
                if (!this.transfer_date) {
                    this.cancelAvailabilityRequest();
                    this.clearAvailabilityState();
                    return;
                }
                if (this.availability_timer) {
                    window.clearTimeout(this.availability_timer);
                    this.availability_timer = null;
                }
                this.availability_timer = window.setTimeout(function () {
                    me.availability_timer = null;
                    me.fetchTransferAvailability();
                }, wait);
            },
            cancelQuoteRequest:function () {
                if (this.quote_refresh_timer) {
                    window.clearTimeout(this.quote_refresh_timer);
                    this.quote_refresh_timer = null;
                }
                if (this.quote_xhr) {
                    this.quote_xhr.abort();
                    this.quote_xhr = null;
                }
            },
            cancelAvailabilityRequest:function () {
                if (this.availability_timer) {
                    window.clearTimeout(this.availability_timer);
                    this.availability_timer = null;
                }
                if (this.availability_xhr) {
                    this.availability_xhr.abort();
                    this.availability_xhr = null;
                }
            },
            clearAvailabilityState:function () {
                this.transfer_availability_loading = false;
                this.transfer_availability_error = '';
                this.transfer_availability_note = '';
                this.transfer_availability_blocked = false;
                this.transfer_time_slots = [];
            },
            fetchTransferAvailability:function () {
                if (!this.availability_url || !this.transfer_date) {
                    this.clearAvailabilityState();
                    return;
                }
                this.cancelAvailabilityRequest();
                this.transfer_availability_loading = true;
                this.transfer_availability_error = '';
                this.transfer_availability_blocked = false;
                this.transfer_availability_note = '';
                var requestData = {
                    date: this.transfer_date,
                    passengers: this.getPassengerCount()
                };
                if (this.pickup_location_id) {
                    requestData.pickup_location_id = this.pickup_location_id;
                }
                var pickup = this.pickup_location || {};
                var pickupLat = parseFloat(pickup.lat);
                var pickupLng = parseFloat(pickup.lng);
                if (!isNaN(pickupLat)) {
                    requestData.pickup_lat = pickupLat;
                }
                if (!isNaN(pickupLng)) {
                    requestData.pickup_lng = pickupLng;
                }
                var dropoff = this.dropoff || {};
                var dropLat = parseFloat(dropoff.lat);
                var dropLng = parseFloat(dropoff.lng);
                var hasDropCoords = !isNaN(dropLat) && !isNaN(dropLng);
                if (hasDropCoords) {
                    requestData.dropoff_lat = dropLat;
                    requestData.dropoff_lng = dropLng;
                }
                if (hasDropCoords && dropoff.place_id) {
                    requestData.dropoff_place_id = dropoff.place_id;
                }
                var me = this;
                this.availability_xhr = $.ajax({
                    url: this.availability_url,
                    method: 'GET',
                    dataType: 'json',
                    data: requestData
                }).done(function (response) {
                    if (!response || !response.status) {
                        var fallbackMessage = me.getAvailabilityMessage('fetch_failed');
                        var responseMessage = response && response.message ? response.message : '';
                        me.transfer_availability_error = responseMessage || fallbackMessage;
                        me.transfer_availability_note = '';
                        me.transfer_availability_blocked = true;
                        me.transfer_time_slots = [];
                        return;
                    }
                    me.applyAvailabilityResult(response);
                }).fail(function (xhr) {
                    if (xhr && xhr.statusText === 'abort') {
                        return;
                    }
                    var message = me.getAvailabilityMessage('fetch_failed');
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    me.transfer_availability_error = message;
                    me.transfer_availability_note = '';
                    me.transfer_availability_blocked = true;
                    me.transfer_time_slots = [];
                }).always(function () {
                    me.transfer_availability_loading = false;
                    me.availability_xhr = null;
                });
            },
            applyAvailabilityResult:function (response) {
                var data = response && response.status && response.data ? response.data : null;
                var dates = data && Array.isArray(data.dates) ? data.dates : [];
                var targetDate = this.transfer_date;
                var match = null;

                for (var i = 0; i < dates.length; i++) {
                    var candidate = dates[i] || {};
                    var candidateDate = this.normalizeAvailabilityDate(candidate.date || candidate.day || candidate.date_iso || candidate.value);
                    if (!candidateDate && candidate.date_time) {
                        candidateDate = this.normalizeAvailabilityDate(candidate.date_time);
                    }
                    if (candidateDate && candidateDate === targetDate) {
                        match = candidate;
                        break;
                    }
                }

                if (!match && data && data.date) {
                    var normalizedDataDate = this.normalizeAvailabilityDate(data.date);
                    if (normalizedDataDate && normalizedDataDate === targetDate) {
                        match = data;
                    }
                }

                if (!match && data && data.current_date) {
                    var normalizedCurrentDate = this.normalizeAvailabilityDate(data.current_date);
                    if (normalizedCurrentDate && normalizedCurrentDate === targetDate) {
                        match = data;
                    }
                }

                if (!match && dates.length === 1) {
                    var fallbackCandidate = dates[0] || {};
                    var fallbackDate = this.normalizeAvailabilityDate(fallbackCandidate.date || fallbackCandidate.day || fallbackCandidate.date_iso || fallbackCandidate.value);
                    if (!targetDate || (fallbackDate && fallbackDate === targetDate)) {
                        match = fallbackCandidate;
                    }
                }

                var availabilityContext = match;
                if (!availabilityContext && data) {
                    if (Array.isArray(data.time_slots) || Array.isArray(data.slots) || Array.isArray(data.available_hours)) {
                        availabilityContext = data;
                    }
                }

                var note = '';
                var slots = [];
                this.transfer_availability_error = '';
                this.transfer_availability_blocked = false;

                if (availabilityContext) {
                    var availableFlag = true;
                    if (typeof availabilityContext.available !== 'undefined') {
                        availableFlag = this.normalizeAvailabilityFlag(availabilityContext.available);
                    } else if (typeof availabilityContext.is_available !== 'undefined') {
                        availableFlag = this.normalizeAvailabilityFlag(availabilityContext.is_available);
                    }

                    var slotSources = [];
                    if (Array.isArray(availabilityContext.time_slots)) {
                        slotSources.push(availabilityContext.time_slots);
                    }
                    if (Array.isArray(availabilityContext.slots)) {
                        slotSources.push(availabilityContext.slots);
                    }
                    if (Array.isArray(availabilityContext.available_hours)) {
                        slotSources.push(availabilityContext.available_hours);
                    }

                    for (var sourceIndex = 0; sourceIndex < slotSources.length; sourceIndex++) {
                        var source = slotSources[sourceIndex];
                        for (var slotIndex = 0; slotIndex < source.length; slotIndex++) {
                            var normalizedSlot = this.normalizeTimeSlot(source[slotIndex]);
                            if (normalizedSlot) {
                                slots.push(normalizedSlot);
                            }
                        }
                    }

                    if (availableFlag) {
                        if (!slots.length) {
                            note = availabilityContext.note || availabilityContext.message || this.getAvailabilityMessage('no_slots');
                            this.transfer_availability_blocked = true;
                        } else if (availabilityContext.note) {
                            note = availabilityContext.note;
                        } else if (availabilityContext.message) {
                            note = availabilityContext.message;
                        }
                    } else {
                        note = availabilityContext.note || availabilityContext.message || this.getAvailabilityMessage('unavailable');
                        this.transfer_availability_blocked = true;
                    }
                } else {
                    note = this.getAvailabilityMessage('invalid_date');
                    this.transfer_availability_blocked = true;
                }

                this.transfer_availability_note = note;
                this.transfer_time_slots = slots;

                var currentTime = this.transfer_time;
                if (currentTime && (!this.isTimeSlotValid(currentTime) || this.transfer_availability_blocked)) {
                    currentTime = '';
                }
                if (!this.transfer_availability_blocked && (!currentTime || !this.isTimeSlotValid(currentTime))) {
                    if (slots.length) {
                        currentTime = slots[0].value;
                    }
                }
                if (currentTime !== this.transfer_time) {
                    this.transfer_time = currentTime;
                }
            },
            normalizeTimeSlot:function (slot) {
                if (!slot && slot !== 0) {
                    return null;
                }
                if (typeof slot === 'string' || typeof slot === 'number') {
                    var value = String(slot);
                    return {value: value, label: value, disabled: false};
                }
                if (typeof slot === 'object' && slot.value) {
                    return {
                        value: String(slot.value),
                        label: slot.label || String(slot.value),
                        disabled: !!slot.disabled
                    };
                }
                return null;
            },
            normalizeAvailabilityDate:function (value) {
                if (value === null || typeof value === 'undefined') {
                    return '';
                }
                if (typeof value === 'string') {
                    var trimmed = value.trim();
                    if (!trimmed) {
                        return '';
                    }
                    if (/^\d{4}-\d{2}-\d{2}$/.test(trimmed)) {
                        return trimmed;
                    }
                    if (trimmed.length >= 10 && /^\d{4}-\d{2}-\d{2}/.test(trimmed)) {
                        return trimmed.substr(0, 10);
                    }
                    if (typeof moment !== 'undefined') {
                        var parsed = moment(trimmed, moment.ISO_8601, true);
                        if (!parsed.isValid()) {
                            parsed = moment(trimmed, 'YYYY-MM-DD', true);
                        }
                        if (parsed.isValid()) {
                            return parsed.format('YYYY-MM-DD');
                        }
                    }
                    return '';
                }
                if (typeof value === 'object') {
                    if (value.date) {
                        return this.normalizeAvailabilityDate(value.date);
                    }
                    if (value.value) {
                        return this.normalizeAvailabilityDate(value.value);
                    }
                }
                if (typeof value === 'number') {
                    var intValue = parseInt(value, 10);
                    if (!isNaN(intValue) && intValue > 0 && intValue < 100000000000) {
                        return this.normalizeAvailabilityDate(String(intValue));
                    }
                }
                return '';
            },
            normalizeAvailabilityFlag:function (value) {
                if (typeof value === 'string') {
                    var normalized = value.toLowerCase();
                    if (normalized === 'false' || normalized === '0' || normalized === 'no') {
                        return false;
                    }
                    if (normalized === 'true' || normalized === '1' || normalized === 'yes') {
                        return true;
                    }
                    return normalized !== '' && normalized !== 'null' && normalized !== 'undefined';
                }
                if (typeof value === 'number') {
                    return value > 0;
                }
                return !!value;
            },
            isTimeSlotValid:function (value) {
                if (!value) {
                    return false;
                }
                for (var i = 0; i < this.transfer_time_slots.length; i++) {
                    var slot = this.transfer_time_slots[i];
                    if (slot && slot.value === value && !slot.disabled) {
                        return true;
                    }
                }
                return false;
            },
            getAvailabilityMessage:function (key) {
                if (!key || !this.availability_messages) {
                    return '';
                }
                var message = this.availability_messages[key];
                return message || '';
            },
            refreshTransferQuote:function () {
                if (!this.quote_url) {
                    return;
                }
                if (!this.shouldRequestQuote()) {
                    this.transfer_quote = null;
                    this.transfer_quote_error = '';
                    this.transfer_quote_loading = false;
                    return;
                }

                var dropoff = this.dropoff || {};
                var dropLat = parseFloat(dropoff.lat);
                var dropLng = parseFloat(dropoff.lng);

                var pickup = this.pickup_location || {};

                var passengerCount = this.getPassengerCount();

                var $root = $(this.$el);
                var datetimeField = $root.find('.js-transfer-datetime');
                var transferDatetime = datetimeField.length ? datetimeField.val() : '';
                if (!transferDatetime && this.transfer_date && this.transfer_time) {
                    transferDatetime = this.buildTransferDatetime(this.transfer_date, this.transfer_time);
                }
                this.transfer_datetime = transferDatetime;

                var requestData = {
                    pickup: JSON.stringify(pickup),
                    dropoff: JSON.stringify({
                        address: dropoff.address || dropoff.name || '',
                        name: dropoff.name || dropoff.address || '',
                        lat: dropLat,
                        lng: dropLng,
                        place_id: dropoff.place_id
                    }),
                    pickup_location_id: this.pickup_location_id || '',
                    transfer_datetime: transferDatetime,
                    transfer_date: this.transfer_date || '',
                    transfer_time: this.transfer_time || '',
                    passengers: passengerCount
                };

                this.transfer_quote_loading = true;
                this.transfer_quote_error = '';

                this.cancelQuoteRequest();

                var headers = {};
                var csrf = $('meta[name="csrf-token"]').attr('content');
                if (csrf) {
                    headers['X-CSRF-TOKEN'] = csrf;
                }

                var me = this;
                this.quote_xhr = $.ajax({
                    url: this.quote_url,
                    method: 'POST',
                    dataType: 'json',
                    data: requestData,
                    headers: headers
                }).done(function (response) {
                    if (response && response.status && response.data && response.data.quote) {
                        me.transfer_quote = response.data.quote;
                        if (response.data.quote.passengers) {
                            var serverPassengers = parseInt(response.data.quote.passengers, 10);
                            if (!isNaN(serverPassengers) && serverPassengers > 0 && serverPassengers !== parseInt(me.number, 10)) {
                                me.suppressPassengerWatch = true;
                                me.number = serverPassengers;
                            }
                        }
                        me.transfer_quote_error = '';
                    } else {
                        me.transfer_quote = null;
                        me.transfer_quote_error = response && response.message ? response.message : '';
                    }
                }).fail(function (xhr) {
                    if (xhr && xhr.statusText === 'abort') {
                        return;
                    }
                    me.transfer_quote = null;
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                        me.transfer_quote_error = xhr.responseJSON.message;
                    } else {
                        me.transfer_quote_error = '';
                    }
                }).always(function () {
                    me.transfer_quote_loading = false;
                    me.quote_xhr = null;
                });
            },
            toNumeric:function (value) {
                if (value === null || typeof value === 'undefined' || value === '') {
                    return null;
                }
                if (typeof value === 'number') {
                    return isFinite(value) ? value : null;
                }
                var parsed = parseFloat(value);
                return isNaN(parsed) ? null : parsed;
            },
            getTimezoneOffset:function () {
                if (this.timezone_offset) {
                    return this.timezone_offset;
                }
                if (window.bookingCore && window.bookingCore.timezone_offset) {
                    this.timezone_offset = window.bookingCore.timezone_offset;
                    return this.timezone_offset;
                }
                var now = new Date();
                var offsetMinutes = now.getTimezoneOffset();
                var sign = offsetMinutes <= 0 ? '+' : '-';
                var absolute = Math.abs(offsetMinutes);
                var hours = Math.floor(absolute / 60);
                var minutes = absolute % 60;
                var pad = function (value) {
                    return value < 10 ? '0' + value : String(value);
                };
                this.timezone_offset = sign + pad(hours) + ':' + pad(minutes);
                return this.timezone_offset;
            },
            buildTransferDatetime:function (date, time) {
                if (!date || !time) {
                    return '';
                }
                var offset = this.getTimezoneOffset() || '+00:00';
                return date + 'T' + time + ':00' + offset;
            },
            validate(){
                this.syncTransferDateState();
                this.message.status = false;
                this.message.content = '';
                this.clearFieldErrors();

                var isValid = true;
                if (!this.transfer_date) {
                    this.setFieldError('datetime', this.datetime_required_message || bravo_booking_i18n.no_date_select);
                    isValid = false;
                } else if (!this.transfer_time) {
                    this.setFieldError('datetime', this.datetime_required_message || bravo_booking_i18n.no_date_select);
                    isValid = false;
                } else if (!this.isTimeSlotValid(this.transfer_time)) {
                    this.setFieldError('datetime', this.getAvailabilityMessage('time_required') || this.datetime_required_message || bravo_booking_i18n.no_date_select);
                    isValid = false;
                }
                if (this.transfer_availability_error) {
                    this.setFieldError('datetime', this.transfer_availability_error);
                    isValid = false;
                } else if (this.transfer_availability_blocked) {
                    var availabilityMessage = this.transfer_availability_note || this.getAvailabilityMessage('time_required') || this.datetime_required_message || bravo_booking_i18n.no_date_select;
                    this.setFieldError('datetime', availabilityMessage);
                    isValid = false;
                }

                var rawPassengers = parseInt(this.number, 10);
                var passengersMessage = bravo_booking_i18n.passengers_invalid || bravo_booking_i18n.no_guest_select;
                if(isNaN(rawPassengers) || rawPassengers < 1) {
                    this.setFieldError('passengers', passengersMessage);
                    isValid = false;
                } else {
                    var max = parseInt(this.max_number, 10);
                    if (!isNaN(max) && max > 0 && rawPassengers > max) {
                        this.setFieldError('passengers', passengersMessage);
                        isValid = false;
                    }
                }

                var hasPickupCoordinates = this.pickup_location && this.pickup_location.lat && this.pickup_location.lng;
                if(!this.pickup_location_id && !hasPickupCoordinates){
                    this.setFieldError('pickup', bravo_booking_i18n.pickup_required || 'Please choose a pickup location.');
                    isValid = false;
                }

                var dropLat = parseFloat(this.dropoff && this.dropoff.lat);
                var dropLng = parseFloat(this.dropoff && this.dropoff.lng);
                if(!this.dropoff || isNaN(dropLat) || isNaN(dropLng) || !this.dropoff.place_id){
                    this.setFieldError('dropoff', bravo_booking_i18n.dropoff_required || 'Please choose a drop-off location.');
                    isValid = false;
                }

                if (this.transfer_quote_error) {
                    this.setFieldError('dropoff', this.transfer_quote_error);
                    isValid = false;
                } else if (!this.transfer_quote && !this.transfer_quote_loading) {
                    var quoteMessage = bravo_booking_i18n.quote_required || '';
                    if (quoteMessage) {
                        this.setFieldError('dropoff', quoteMessage);
                        isValid = false;
                    }
                }

                return isValid;
            },
            addNumberType(){
                var me = this;
                if(parseInt(me.number) < parseInt(me.max_number) || !me.max_number) me.number +=1;
            },
            minusNumberType(){
                var me = this;
                if(me.number > 1) me.number -=1;
            },
            doSubmit:function (e) {
                e.preventDefault();
                if(this.onSubmit) return false;

                var $root = $(this.$el);
                this.pickup_location_id = $root.find('.js-transfer-pickup').val();
                var pickupPayload = $root.find('.js-transfer-pickup-payload').val();
                if (pickupPayload) {
                    try {
                        this.pickup_location = JSON.parse(pickupPayload);
                    } catch (err) {
                        this.pickup_location = null;
                    }
                } else {
                    this.pickup_location = null;
                }
                this.dropoff = {
                    address: $root.find('.js-transfer-dropoff-address').val(),
                    name: $root.find('.js-transfer-dropoff-name').val(),
                    lat: $root.find('.js-transfer-dropoff-lat').val(),
                    lng: $root.find('.js-transfer-dropoff-lng').val(),
                    place_id: $root.find('.js-transfer-dropoff-place-id').val(),
                };
                var transferDate = $root.find('.js-transfer-date').val();
                var transferTime = $root.find('.js-transfer-time').val();
                var transferDatetimeField = $root.find('.js-transfer-datetime');
                this.transfer_datetime = transferDatetimeField.length ? transferDatetimeField.val() : '';
                if (!this.transfer_datetime && transferDate && transferTime) {
                    this.transfer_datetime = this.buildTransferDatetime(transferDate, transferTime);
                }

                if(!this.validate()) return false;

                this.onSubmit = true;
                var me = this;

                this.message.content = '';

                if(this.step == 1){
                    this.html = '';
                }

                $.ajax({
                    url:bookingCore.url+'/booking/addToCart',
                    data:{
                        service_id:this.id,
                        service_type:"car",
                        start_date:this.start_date,
                        end_date:this.end_date,
                        extra_price:this.extra_price,
                        number:this.number,
                        pickup_location_id:this.pickup_location_id,
                        pickup:this.pickup_location ? JSON.stringify(this.pickup_location) : '',
                        dropoff:this.dropoff,
                        transfer_datetime:this.transfer_datetime,
                        transfer_date:this.transfer_date,
                        transfer_time:this.transfer_time,
                    },
                    dataType:'json',
                    type:'post',
                    success:function(res){

                        if(!res.status){
                            me.onSubmit = false;
                        }
                        if(res.message)
                        {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }

                        if(res.step){
                            me.step = res.step;
                        }
                        if(res.html){
                            me.html = res.html
                        }

                        if(res.url){
                            window.location.href = res.url
                        }

                        if(res.errors && typeof res.errors == 'object')
                        {
                            var html = '';
                            for(var i in res.errors){
                                html += res.errors[i]+'<br>';
                            }
                            me.message.content = html;
                        }
                    },
                    error:function (e) {
                        console.log(e);
                        me.onSubmit = false;

                        bravo_handle_error_response(e);

                        if(e.status == 401){
                            $('.bravo_single_book_wrap').modal('hide');
                        }

                        if(e.status != 401 && e.responseJSON){
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;

                        }
                    }
                })
            },
            doEnquirySubmit:function(e){
                e.preventDefault();
                if(this.onSubmit) return false;
                if(!this.validateenquiry()) return false;
                this.onSubmit = true;
                var me = this;
                this.message.content = '';

                $.ajax({
                    url:bookingCore.url+'/booking/addEnquiry',
                    data:{
                        service_id:this.id,
                        service_type:'car',
                        name:this.enquiry_name,
                        email:this.enquiry_email,
                        phone:this.enquiry_phone,
                        note:this.enquiry_note,
                    },
                    dataType:'json',
                    type:'post',
                    success:function(res){
                        if(res.message)
                        {
                            me.message.content = res.message;
                            me.message.type = res.status;
                        }
                        if(res.errors && typeof res.errors == 'object')
                        {
                            var html = '';
                            for(var i in res.errors){
                                html += res.errors[i]+'<br>';
                            }
                            me.message.content = html;
                        }
                        if(res.status){
                            me.enquiry_is_submit = true;
                            me.enquiry_name = "";
                            me.enquiry_email = "";
                            me.enquiry_phone = "";
                            me.enquiry_note = "";
                        }
                        me.onSubmit = false;
                    },
                    error:function (e) {
                        me.onSubmit = false;
                        bravo_handle_error_response(e);
                        if(e.status == 401){
                            $('.bravo_single_book_wrap').modal('hide');
                        }
                        if(e.status != 401 && e.responseJSON){
                            me.message.content = e.responseJSON.message ? e.responseJSON.message : 'Can not booking';
                            me.message.type = false;
                        }
                    }
                })
            },
            validateenquiry(){
                if(!this.enquiry_name)
                {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.name_required;
                    return false;
                }
                if(!this.enquiry_email)
                {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.email_required;
                    return false;
                }
                return true;
            },
        }

    });




    $(window).on("load", function () {
        var urlHash = window.location.href.split("#")[1];
        if (urlHash &&  $('.' + urlHash).length ){
            var offset_other = 70
            if(urlHash === "review-list"){
                offset_other = 330;
            }
            $('html,body').animate({
                scrollTop: $('.' + urlHash).offset().top - offset_other
            }, 1000);
        }
    });

    $(".bravo-button-book-mobile").click(function () {
        $('.bravo_single_book_wrap').modal('show');
    });

    $(".bravo_detail_car .g-faq .item .header").click(function () {
        $(this).parent().toggleClass("active");
    });

})(jQuery);
