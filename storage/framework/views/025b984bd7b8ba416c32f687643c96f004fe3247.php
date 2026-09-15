

<?php $__env->startSection('content'); ?>
    <?php
    $page_partial = 'home-'.get_static_option('home_page_variant');
    if (!empty(get_static_option('home_page_page_builder_status'))){
        $page_partial = 'page-builder';
    }
    //echo $page_partial; die;
    ?>
<?php echo $__env->make('frontend.home-pages.'.$page_partial, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.frontend-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/frontend-home.blade.php ENDPATH**/ ?>