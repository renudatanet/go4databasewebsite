<section class="breadcrumb-area breadcrumb-bg navbar-variant-<?php echo e(get_static_option('navbar_variant')); ?>"
    <?php echo render_background_image_markup_by_attachment_id(get_static_option('site_breadcrumb_bg')); ?>

>
    <div class="container

         <?php if(
         (in_array(request()->route()->getName(),['homepage','frontend.dynamic.page'])
         && empty($page_post->breadcrumb_status) )
         ): ?>
             d-none
        <?php endif; ?>

    ">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-inner">
                    <?php if(request()->query('author')!=''): ?>
                        <h1 class="page-title"><?php echo e(request()->query('author')); ?></h1>
                        <ul class="page-list">
                            <li><a href="<?php echo e(url('/')); ?>"><?php echo e(__('Home')); ?></a></li>
                            <li><a href="<?php echo e(url('/blog')); ?>"><?php echo $__env->yieldContent('page-title'); ?></a></li>
                            <li>    Author:  <?php echo e(request()->query('author')); ?></li>
                        </ul>
                      
                    <?php else: ?>   
                        <h1 class="page-title"><?php echo $__env->yieldContent('page-title'); ?></h1>
                        <ul class="page-list">
                            <li><a href="<?php echo e(url('/')); ?>"><?php echo e(__('Home')); ?></a></li>
                            <?php
                            $pages_list = ['list','author','blog','work','service','product','career_with_us','events','knowledgebase','donation','appointment','courses'];
                            ?>
                          
                            <?php $__currentLoopData = $pages_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              
                                <?php if(
    request()->is(get_static_option($page.'_page_slug')) ||
    request()->is(get_static_option($page.'_page_slug').'/*')
): ?>

                                <li><a href="<?php echo e(url('/').'/'.get_static_option($page.'_page_slug')); ?>"><?php echo e(get_static_option($page.'_page_' . $user_select_lang_slug . '_name')); ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo $__env->yieldContent('page-title'); ?></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/partials/breadcrumb.blade.php ENDPATH**/ ?>