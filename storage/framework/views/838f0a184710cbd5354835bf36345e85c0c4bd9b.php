<?php $site_google_captcha_v3_site_key = get_static_option('site_google_captcha_v3_site_key'); ?>
<?php if(!empty($site_google_captcha_v3_site_key)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e(get_static_option('site_google_captcha_v3_site_key')); ?>"></script>
    <script>
   grecaptcha.ready(function () {

    const form = document.querySelector('.custom-form-builder-form');

    if (!form) return;

    form.addEventListener('submit', function(e) {

        e.preventDefault();

        grecaptcha.execute("<?php echo e(get_static_option('site_google_captcha_v3_site_key')); ?>", {
            action: 'homepage'
        }).then(function(token) {

            document.getElementById('gcaptcha_token').value = token;

            form.submit();

        });

    });

});
    </script>
<?php endif; ?>
<?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/partials/google-captcha.blade.php ENDPATH**/ ?>