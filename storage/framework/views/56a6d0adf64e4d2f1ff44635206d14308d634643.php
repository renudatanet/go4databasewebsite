

<?php $__env->startSection('page-meta-data'); ?>
    <meta name="description" content="<?php echo e($work_item->meta_description); ?>">
    <meta name="tags" content="<?php echo e($work_item->meta_tag); ?>">
<?php $__env->stopSection(); ?> 
<?php $__env->startSection('og-meta'); ?>
    <meta property="og:url"  content="<?php echo e(route('frontend.work.single',$work_item->slug)); ?>" />
    <meta property="og:type"  content="article" />
    <meta property="og:title"  content="<?php echo e($work_item->title); ?>" />
    <?php echo render_og_meta_image_by_attachment_id($work_item->image); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-title'); ?>
    <?php if($work_item->meta_tag!=''): ?>
        <?php echo e($work_item->meta_tag); ?>

    <?php else: ?>
        <?php echo e($work_item->title); ?> - <?php echo e(get_static_option('work_page_'.$user_select_lang_slug.'_name')); ?>

    <?php endif; ?>    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
     <?php echo e($work_item->title); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .boxed-btn{
        background-color: var(--main-color-one);
    color: #fff;
    display: inline-block;
    padding: 12px 40px;
    border-radius: 25px;
    min-width: 160px;
    text-align: center;
    -webkit-transition: all .3s ease-in;
    -o-transition: all .3s ease-in;
    transition: all .3s ease-in;
    font-weight: 600;
    border: none;
    }
    .post-description table{
         width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
    }
</style>
    <div class="work-details-content-area padding-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="portfolio-details-item">
                        <div class="thumb">
                          <?php echo render_image_markup_by_attachment_id($work_item->image); ?>

                        </div>
                        <div class="post-description">
                            <?php echo $work_item->description; ?> 
                        </div>

                        <?php $gallery_item = $work_item->gallery ? explode('|',$work_item->gallery) : []; ?>
                        <?php if(!empty($gallery_item)): ?>
                        
                        <div class="case-study-gallery-wrapper">
                            <h2 class="main-title"><?php echo e(get_static_option('case_study_'.$user_select_lang_slug.'_gallery_title')); ?></h2>
                            <div class="case-study-gallery-carousel global-carousel-init"
                                 data-loop="true"
                                 data-desktopitem="1"
                                 data-mobileitem="1"
                                 data-tabletitem="1"
                                 data-nav="true"
                                 data-autoplay="true"
                                 data-margin="0"
                            >
                                <?php $__currentLoopData = $gallery_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gall): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="single-gallery-item">
                                    <?php echo render_image_markup_by_attachment_id($gall); ?>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="project-widget">
                        <div class="widget-nav-menu margin-bottom-30">
                          
                        </div>
                        <?php echo App\WidgetsBuilder\WidgetBuilderSetup::render_frontend_sidebar('case_study',['column' => false]); ?>

                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="related-work-area padding-top-100">
                        <div class="section-title margin-bottom-30">
                            <h2 class="title"><?php echo e(get_static_option('case_study_'.$user_select_lang_slug.'_related_title')); ?></h2>
                        </div>
                            <div class="related-case-study-carousel global-carousel-init"
                                 data-loop="true"
                                 data-desktopitem="3"
                                 data-mobileitem="1"
                                 data-tabletitem="1"
                                 data-nav="true"
                                 data-autoplay="true"
                                 data-margin="40"
                            >
                            <?php $__currentLoopData = $related_works; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="single-related-case-study-item">
                                    <div class="thumb">
                                        <?php echo render_image_markup_by_attachment_id($data->image); ?>

                                    </div>
                                    <div class="content">
                                        <p class="title"><a href="<?php echo e(route('frontend.work.single',$data->slug)); ?>"> <?php echo e($data->title); ?></a></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.frontend-page-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/pages/work/work-single.blade.php ENDPATH**/ ?>