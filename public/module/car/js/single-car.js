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
            quote_xhr:null,
            is_initialising:true,
            datetime_required_message:'',
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
                if (value && typeof moment !== 'undefined') {
                    var day = moment(value, 'YYYY-MM-DD', true);
                    if (day.isValid()) {
                        this.fetchEvents(day, day);
                    }
                }
                if (!this.is_initialising) {
                    this.refreshTransferQuote();
                }
            },
            transfer_time:function () {
                this.syncTransferDateState();
                if (!this.is_initialising) {
                    this.refreshTransferQuote();
                }
            },
            number:function () {
                var me = this;
                if(parseInt(me.number) > parseInt(me.max_number)){
                    me.number = parseInt(me.max_number);
                }
                if(parseInt(me.number) < 1){
                    me.number = 1
                }
            },
            pickup_location:{
                handler:function () {
                    if (this.is_initialising) {
                        return;
                    }
                    this.refreshTransferQuote();
                },
                deep:true
            },
            dropoff:{
                handler:function () {
                    if (this.is_initialising) {
                        return;
                    }
                    this.refreshTransferQuote();
                },
                deep:true
            }
        },
        computed:{
            priceDetails:function () {
                var quote = this.transfer_quote || {};
                var meta = this.pricing_meta || {};
                var mode = quote.pricing_mode || meta.mode || null;
                if (!mode && !meta.price_per_km && !meta.fixed_price) {
                    return null;
                }

                mode = mode || 'per_km';

                var fromLabel = quote.pickup_label || '';
                if (!fromLabel && quote.pickup && (quote.pickup.address || quote.pickup.name)) {
                    fromLabel = quote.pickup.address || quote.pickup.name;
                }
                if (!fromLabel && this.pickup_location && (this.pickup_location.address || this.pickup_location.name)) {
                    fromLabel = this.pickup_location.address || this.pickup_location.name;
                }

                var toLabel = quote.dropoff_label || '';
                if (!toLabel && quote.dropoff && (quote.dropoff.address || quote.dropoff.name)) {
                    toLabel = quote.dropoff.address || quote.dropoff.name;
                }
                if (!toLabel && this.dropoff && (this.dropoff.address || this.dropoff.name)) {
                    toLabel = this.dropoff.address || this.dropoff.name;
                }

                var distanceValue = (typeof quote.distance_km === 'number') ? quote.distance_km : null;
                var priceValue = (typeof quote.price === 'number') ? quote.price : null;
                var unitPriceValue = (typeof quote.unit_price === 'number') ? quote.unit_price : null;
                var baseFeeValue = (quote.base_fee !== null && typeof quote.base_fee !== 'undefined') ? quote.base_fee : null;
                if ((baseFeeValue === null || baseFeeValue === '') && typeof meta.base_fee === 'number') {
                    baseFeeValue = meta.base_fee;
                }

                var formattedUnit = null;
                var formattedBase = null;
                var formattedTotal = null;

                if (mode === 'fixed') {
                    if (priceValue === null && typeof meta.fixed_price === 'number') {
                        priceValue = meta.fixed_price;
                    }
                    if (priceValue !== null) {
                        formattedTotal = this.formatMoney(priceValue);
                        formattedBase = this.formatMoney(priceValue);
                    }
                } else {
                    if (unitPriceValue === null && typeof meta.price_per_km === 'number') {
                        unitPriceValue = meta.price_per_km;
                    }
                    if (unitPriceValue !== null) {
                        formattedUnit = this.formatMoney(unitPriceValue) + '/km';
                    }
                    if (priceValue !== null) {
                        formattedTotal = this.formatMoney(priceValue);
                    }
                    if (baseFeeValue !== null && baseFeeValue !== '' && !isNaN(parseFloat(baseFeeValue))) {
                        formattedBase = this.formatMoney(parseFloat(baseFeeValue));
                    }
                }

                return {
                    from: fromLabel,
                    to: toLabel,
                    distance: distanceValue !== null ? this.formatDistance(distanceValue) : null,
                    pricePerKm: formattedUnit,
                    baseFee: formattedBase,
                    total: formattedTotal,
                    pricingMode: mode
                };
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
            var quoteUrl = $root.data('quoteUrl');
            if (quoteUrl) {
                this.quote_url = quoteUrl;
            }
            var pricingMeta = $root.data('pricingMeta');
            if (pricingMeta) {
                this.pricing_meta = pricingMeta;
            }
            var initialQuote = $root.data('initialQuote');
            if (initialQuote) {
                this.transfer_quote = initialQuote;
            }
            if (window.BravoTransferForm && typeof window.BravoTransferForm.initAll === 'function') {
                window.BravoTransferForm.initAll($root);
            }
            $root.on('transfer:context-changed', function (event, context) {
                context = context || {};
                if (context.pickup) {
                    me.pickup_location = context.pickup;
                    me.pickup_location_id = context.pickup.id || '';
                }
                if (context.dropoff) {
                    me.dropoff = context.dropoff;
                }
                if (!me.is_initialising) {
                    me.refreshTransferQuote();
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
                this.transfer_date = dateField.val() || '';
            }
            var timeField = $root.find('.js-transfer-time');
            if (timeField.length) {
                this.transfer_time = timeField.val() || '';
            }
            var datetimeMessage = $root.data('datetimeRequired');
            if (datetimeMessage) {
                this.datetime_required_message = datetimeMessage;
            }
            this.syncTransferDateState();
            this.is_initialising = false;
            if (!this.transfer_quote && this.dropoff && this.dropoff.place_id) {
                this.refreshTransferQuote();
            }
        },
        methods:{
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
                                    break;
                                }
                            }
                        }
                        if (me.$refs && me.$refs.start_date) {
                            var drp = $(me.$refs.start_date).data('daterangepicker');
                            if (drp) {
                                drp.allEvents = json;
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
                var value = parseFloat(km);
                if (isNaN(value)) {
                    return null;
                }
                return value.toFixed(2) + ' km';
            },
            refreshTransferQuote:function () {
                if (!this.quote_url) {
                    return;
                }
                var dropoff = this.dropoff || {};
                var dropLat = parseFloat(dropoff.lat);
                var dropLng = parseFloat(dropoff.lng);
                if (!dropoff.place_id || isNaN(dropLat) || isNaN(dropLng)) {
                    this.transfer_quote = null;
                    this.transfer_quote_error = '';
                    if (this.quote_xhr) {
                        this.quote_xhr.abort();
                        this.quote_xhr = null;
                    }
                    this.transfer_quote_loading = false;
                    return;
                }

                var pickup = this.pickup_location || {};
                var pickupLat = parseFloat(pickup.lat);
                var pickupLng = parseFloat(pickup.lng);
                if (isNaN(pickupLat) || isNaN(pickupLng)) {
                    this.transfer_quote = null;
                    this.transfer_quote_error = '';
                    return;
                }

                var $root = $(this.$el);
                var datetimeField = $root.find('.js-transfer-datetime');
                var transferDatetime = datetimeField.length ? datetimeField.val() : '';
                if (!transferDatetime && this.transfer_date && this.transfer_time) {
                    transferDatetime = this.transfer_date + 'T' + this.transfer_time + ':00+04:00';
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
                    transfer_time: this.transfer_time || ''
                };

                this.transfer_quote_loading = true;
                this.transfer_quote_error = '';

                if (this.quote_xhr) {
                    this.quote_xhr.abort();
                    this.quote_xhr = null;
                }

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
            validate(){
                this.syncTransferDateState();
                if(!this.transfer_date || !this.transfer_time)
                {
                    this.message.status = false;
                    this.message.content = this.datetime_required_message || bravo_booking_i18n.no_date_select;
                    return false;
                }
                if(!this.number )
                {
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.no_guest_select;
                    return false;
                }
                var hasPickupCoordinates = this.pickup_location && this.pickup_location.lat && this.pickup_location.lng;
                if(!this.pickup_location_id && !hasPickupCoordinates){
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.pickup_required || 'Please choose a pickup location.';
                    return false;
                }
                var dropLat = parseFloat(this.dropoff && this.dropoff.lat);
                var dropLng = parseFloat(this.dropoff && this.dropoff.lng);
                if(!this.dropoff || isNaN(dropLat) || isNaN(dropLng)){
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.dropoff_required || 'Please choose a drop-off location.';
                    return false;
                }
                if(!this.dropoff.place_id){
                    this.message.status = false;
                    this.message.content = bravo_booking_i18n.dropoff_required || 'Please choose a drop-off location.';
                    return false;
                }

                return true;
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
                if (this.pickup_location_id === '__mylocation__') {
                    this.pickup_location_id = '';
                }
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
                    this.transfer_datetime = transferDate + 'T' + transferTime + ':00+04:00';
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
                        dropoff:this.dropoff,
                        transfer_datetime:this.transfer_datetime,
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
