<?php
$footerStyle = !empty($row->footer_style) ? $row->footer_style : setting_item('footer_style','normal');
$mailchimp_classes = "bg-dark-2";
$button_classes = "bg-blue-1 text-white";
if($footerStyle == "style_6"){
    $mailchimp_classes = "bg-blue-1";
    $button_classes = "bg-yellow-1 text-dark-1";
}
?>
<section class="layout-pt-md layout-pb-md mailchimp {{ $mailchimp_classes }} @if((!empty($row) && !empty($row->disable_subscribe_default) ) || !empty($disable_subscribe_default)) d-none @endif">
    <div class="container">
        <div class="row y-gap-30 justify-between items-center">
            <div class="col-auto">
                <div class="row y-gap-20  flex-wrap items-center">
                    <div class="col-auto">
                        <div class="icon-newsletter text-60 sm:text-40 text-white"></div>
                    </div>

                    <div class="col-auto">
                        <h4 class="text-26 text-white fw-600">{{ __('Your Travel Journey Starts Here') }}</h4>
                        <div class="text-white">{{ __("Sign up and we'll send the best deals to you") }}</div>
                    </div>
                </div>
            </div>

            <div class="col-auto">
                <form action="{{route('newsletter.subscribe')}}" class="subcribe-form bravo-subscribe-form bravo-form single-field -w-410 d-flex x-gap-10 y-gap-20">
                    @csrf
                    <div>
                        <input class="bg-white h-60 email-input" type="text" name="email" placeholder="{{__('Your Email')}}">
                    </div>
                    <div>
                        <button class="button -md h-60 {{ $button_classes }}">
                            {{__('Subscribe')}} <i class="fa fa-spinner fa-pulse fa-fw"></i>
                        </button>
                    </div>
                    <div class="form-mess"></div>
                </form>
            </div>
        </div>
    </div>
</section>



@include('Layout::parts.footer-style.index')
@include('Layout::parts.login-register-modal')
@include('Popup::frontend.popup')
@if(Auth::id())
    @include('Media::browser')
@endif

@if(request()->get('app') === 'true')
    <section class="mobile-app-footer">
        <a href="/" class="footer-item">
            <img src="/icons/home.svg" alt="მთავარი">
            <span>მთავარი</span>
        </a>
        <a href="/bookings" class="footer-item">
            <img src="/icons/calendar.svg" alt="ჯავშნები">
            <span>ჯავშნები</span>
        </a>
        <a href="/profile" class="footer-item">
            <img src="/icons/user.svg" alt="იუზერი">
            <span>იუზერი</span>
        </a>
        <a href="/menu" class="footer-item">
            <img src="/icons/menu.svg" alt="მენიუ">
            <span>მენიუ</span>
        </a>
    </section>
@endif



<script>
(function () {
  function markInvalid(input, msgEl) {
    input.classList.add('is-invalid');
    if (msgEl) msgEl.textContent = msgEl.dataset.msg || 'Required field';
  }
  function clearInvalid(input, msgEl) {
    input.classList.remove('is-invalid');
    if (msgEl) msgEl.textContent = '';
  }

  function validateForm(form) {
    let valid = true;

    // იპოვე ყველა required ველი
    var inputs = form.querySelectorAll('input[required]');
    inputs.forEach(function (el) {
      const type = el.getAttribute('type');
      const isCheckbox = type === 'checkbox';
      const empty = isCheckbox ? !el.checked : !el.value.trim();

      // შესაბამისი error span (თუ გაქვს უკვე markup-ში)
      const name = el.getAttribute('name');
      const msgEl =
        form.querySelector('.error-' + name) ||
        el.closest('.col-12')?.querySelector('.invalid-feedback');

      if (empty) {
        markInvalid(el, msgEl);
        valid = false;
      } else {
        // დამატებითი email ველიდაცია
        if (type === 'email') {
          const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value);
          if (!ok) { markInvalid(el, msgEl); valid = false; }
          else { clearInvalid(el, msgEl); }
        } else {
          clearInvalid(el, msgEl);
        }
      }
    });

    return valid;
  }

  // live-სწორება টাইპის/ქლიქისას
  document.addEventListener('input', function (e) {
    if (e.target.matches('input.is-invalid')) {
      const form = e.target.form;
      const name = e.target.getAttribute('name');
      const msgEl =
        form?.querySelector('.error-' + name) ||
        e.target.closest('.col-12')?.querySelector('.invalid-feedback');
      if (e.target.value.trim().length) clearInvalid(e.target, msgEl);
    }
  });
  document.addEventListener('change', function (e) {
    if (e.target.matches('input[type="checkbox"].is-invalid') && e.target.checked) {
      const form = e.target.form;
      const name = e.target.getAttribute('name');
      const msgEl =
        form?.querySelector('.error-' + name) ||
        e.target.closest('.col-12')?.querySelector('.invalid-feedback');
      clearInvalid(e.target, msgEl);
    }
  });

  // მიაბი submit ორივე ფორმას
  ['.bravo-theme-gotrip-login-form', '.bravo-form-register'].forEach(function (sel) {
    var form = document.querySelector(sel);
    if (!form) return;
    form.setAttribute('novalidate', 'novalidate');
    form.addEventListener('submit', function (e) {
      if (!validateForm(form)) e.preventDefault();
    });
  });
})();
</script>



<!-- Custom script for all pages -->
<script src="{{ asset('libs/lodash.min.js') }}"></script>
<script src="{{ asset('libs/jquery-3.6.3.min.js') }}"></script>
<script src="{{ asset('libs/vue/vue'.(!env('APP_DEBUG') ? '.min':'').'.js') }}"></script>
<script type="text/javascript" src="{{asset('themes/gotrip/libs/bs/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('libs/bootbox/bootbox.min.js') }}"></script>
<script type="text/javascript" src="{{asset('themes/gotrip/js/vendors.js')}}"></script>
<script type="text/javascript" src="{{asset('themes/gotrip/js/main.js?_ver='.config('app.asset_version'))}}"></script>

{!! App\Helpers\MapEngine::scripts() !!}
@if(Auth::id())
    <script src="{{ asset('module/media/js/browser.js?_ver='.config('app.version')) }}"></script>
@endif
<script src="{{ asset('libs/carousel-2/owl.carousel.min.js') }}"></script>
<script type="text/javascript" src="{{ asset("libs/daterange/moment.min.js") }}"></script>
<script type="text/javascript" src="{{ asset("libs/daterange/daterangepicker.min.js") }}"></script>
<script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
@if(setting_item('cookie_agreement_type')=='cookie_agreement' and request()->cookie('booking_cookie_agreement_enable') !=1 and !is_api()  and !isset($_COOKIE['booking_cookie_agreement_enable']))
    <div class="booking_cookie_agreement p-3 d-flex fixed-bottom">
        <div class="content-cookie">{!! clean(setting_item_with_lang('cookie_agreement_content')) !!}</div>
        <button class="btn save-cookie">{!! clean(setting_item_with_lang('cookie_agreement_button_text')) !!}</button>
    </div>
    <script>
        var save_cookie_url = '{{route('core.cookie.check')}}';
    </script>
    <script src="{{ asset('js/cookie.js?_ver='.config('app.asset_version')) }}"></script>
@endif
@includeWhen(setting_item('cookie_agreement_type')=='cookie_consent','Layout::parts.cookie-consent-init')

{{-- home.js --}}
<script src="{{ asset('themes/gotrip/dist/frontend/js/gotrip.js?_ver='.config('app.asset_version')) }}"></script>

@if(request('preview'))
    <script src="{{ asset('themes/gotrip/module/template/preview.js?_ver='.config('app.asset_version')) }}"></script>
@endif

@php \App\Helpers\ReCaptchaEngine::scripts() @endphp
@stack('js')
