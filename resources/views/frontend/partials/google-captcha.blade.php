@php $site_google_captcha_v3_site_key = get_static_option('site_google_captcha_v3_site_key'); @endphp
@if(!empty($site_google_captcha_v3_site_key))
{{-- reCAPTCHA v3 for the form-builder forms.
     The 345 KB Google script used to load on every page (twice on pages that
     include this partial as well as the footer) whether or not a form was on
     it. It is now fetched once, and only when a visitor starts using a form:
     first focus, or the submit itself. The token flow is unchanged, so the
     server-side check in FrontendFormController still sees captcha_token. --}}
<script>
(function () {
  if (window.g4dCaptcha) return;           // included twice on some pages
  window.g4dCaptcha = true;
  var KEY = @json($site_google_captcha_v3_site_key);
  var loading = null;

  function loadApi() {
    if (!loading) {
      loading = new Promise(function (resolve, reject) {
        var s = document.createElement('script');
        s.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(KEY);
        s.async = true;
        s.onload = function () { grecaptcha.ready(resolve); };
        s.onerror = reject;
        document.head.appendChild(s);
      });
    }
    return loading;
  }

  function attach(form) {
    form.addEventListener('focusin', loadApi, { once: true });
    // The site's forms are posted by a jQuery handler on document (see
    // inline-script.blade.php). First submit: stop here, fetch a token, then
    // submit again so that handler runs with the token in place.
    form.addEventListener('submit', function (e) {
      if (form.dataset.captchaDone === '1') { form.dataset.captchaDone = '0'; return; }
      e.preventDefault();
      e.stopImmediatePropagation();
      var field = form.querySelector('#gcaptcha_token, [name="captcha_token"]');
      var again = function () {
        form.dataset.captchaDone = '1';
        if (form.requestSubmit) form.requestSubmit(); else form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      };
      loadApi()
        .then(function () { return grecaptcha.execute(KEY, { action: 'homepage' }); })
        .then(function (token) { if (field) field.value = token; again(); })
        .catch(again);   // never trap the visitor: the server still validates the token
    });
  }

  function init() {
    document.querySelectorAll('.custom-form-builder-form').forEach(attach);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
</script>
@endif
