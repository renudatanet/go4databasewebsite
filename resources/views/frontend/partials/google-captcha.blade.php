@php $site_google_captcha_v3_site_key = get_static_option('site_google_captcha_v3_site_key'); @endphp
@if(!empty($site_google_captcha_v3_site_key))
    <script src="https://www.google.com/recaptcha/api.js?render={{get_static_option('site_google_captcha_v3_site_key')}}"></script>
    <script>
   grecaptcha.ready(function () {

    const form = document.querySelector('.custom-form-builder-form');

    if (!form) return;

    form.addEventListener('submit', function(e) {

        e.preventDefault();

        grecaptcha.execute("{{ get_static_option('site_google_captcha_v3_site_key') }}", {
            action: 'homepage'
        }).then(function(token) {

            document.getElementById('gcaptcha_token').value = token;

            form.submit();

        });

    });

});
    </script>
@endif
