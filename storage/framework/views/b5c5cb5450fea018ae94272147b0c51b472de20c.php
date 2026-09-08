<?php echo $__env->make('frontend.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('frontend.partials.navbar-variant.navbar-'.get_static_option('navbar_variant'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php if(!request()->is('author*') && !request()->is('list/*')): ?>
   <?php echo $__env->make('frontend.partials.breadcrumb', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->yieldContent('content'); ?>
<?php echo $__env->make('frontend.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/frontend-page-master.blade.php ENDPATH**/ ?>