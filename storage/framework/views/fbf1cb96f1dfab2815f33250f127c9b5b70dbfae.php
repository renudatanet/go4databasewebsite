<div class="blog-classic-item-01 <?php echo e($margin ? 'margin-bottom-60' : ''); ?>">
    <div class="thumbnail">
        <?php echo render_image_markup_by_attachment_id($blog->image); ?>

    </div>
    <div class="content">
        <ul class="post-meta">
            <li>
          <?php if($blog->authorData && $blog->authorData->slug): ?>
    <a href="<?php echo e(route('frontend.author.single', $blog->authorData->slug)); ?>">
       <i class="fa fa-user"></i>   <?php echo e($blog->authorData->name); ?>

    </a>
<?php else: ?>
    <span><?php echo e($blog->authorData->name ?? 'Unknown Author'); ?></span>
<?php endif; ?>
       
</li>
            <li>
                <a href="<?php echo e(route('frontend.blog.single',$blog->slug)); ?>">Last updated <!-- <i class="far fa-clock"></i> -->      
                    <!-- <?php echo e(date_format($blog->created_at,'d M y')); ?> -->
                    <?php if(isset($blog->updated_at) && $blog->updated_at!=''): ?>
                        <?php echo e(\Carbon\Carbon::parse($blog->updated_at)->format('d M y')); ?>

                    <?php else: ?>
                        <?php echo e(date_format($blog->created_at,'d M y')); ?>

                    <?php endif; ?>
                </a>
            </li>
            <li>
                <div class="cats"><i class="fas fa-microchip"></i>
                
                    <?php echo get_blog_category_by_id($blog->blog_categories_id,'link'); ?>

                </div>
            </li>
            <li>
                <div class="cats">
                    <i class="fas fa-users"></i>
                    <?php
                        $Counting = '327';
                        $randomNumber = random_int(3, 10);
                        //$TotalVisitors = $Counting+$randomNumber;
                        $TotalVisitors = $blog->total_visitors;
                    ?>
                    <?php echo e($TotalVisitors); ?>

                </div>
            </li>
        </ul>
        <h2 class="title"><a href="<?php echo e(route('frontend.blog.single',$blog->slug)); ?>"><?php echo e($blog->title); ?></a></h2>
        <p><?php echo e($blog->excerpt); ?></p>
        <div class="btn-wrapper">
            <a href="<?php echo e(route('frontend.blog.single',$blog->slug)); ?>" class="boxed-btn reverse-color"><?php echo e(get_static_option('blog_page_'.$user_select_lang_slug.'_read_more_btn_text')); ?></a>
        </div>
    </div>
</div><?php /**PATH /home/go4database.com/public_html/@core/resources/views/components/frontend/blog/grid.blade.php ENDPATH**/ ?>