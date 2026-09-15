
<?php
  $author_image = get_attachment_image_by_id($blogauthor->image,"full",false);
   ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Author:')); ?> <?php echo e(' '.$author_name); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-title'); ?>
    <?php if($blogauthor->meta_tags!=''): ?>
        <?php echo e($blogauthor->meta_tags); ?>

    <?php else: ?>
        Category: <?php echo e(' '.$author_name); ?>

    <?php endif; ?>
<?php $__env->stopSection(); ?> 
<?php $__env->startSection('page-meta-data'); ?>

    <meta name="description" content="<?php echo e($blogauthor->meta_description); ?>">
    <meta name="tags" content="<?php echo e($blogauthor->meta_tags); ?>">
    <?php echo render_og_meta_image_by_attachment_id(get_static_option('author_page_'.$user_select_lang_slug.'_meta_image')); ?>

<?php $__env->stopSection(); ?>
<style>
  
    .hero {
       
    background: #e7fde7;
        color: #fff;
        padding: 60px 80px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .left {
        max-width: 70%;
    }

    .left h4 {
        font-size: 26px;
        font-weight: 300;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .left h1 {
        font-size: 64px;
        font-weight: 700;
        margin-bottom: 20px;
    }

    .left h2 {
        font-size: 28px;
        font-weight: 400;
        margin-bottom: 20px;
    }

    .left h2 span {
        text-decoration: underline;
    }

    .left p {
        font-size: 16px;
        line-height: 1.7;
        opacity: 0.95;
        margin-bottom: 30px;
        
    color: #000000;
    }

 .stats-follow {
    display: flex;
    align-items: center;
    gap: 20px; 
}

.stats-follow .follow {
    margin-left: auto;
}

.stats {
    background: #9494946e;
    padding: 20px 30px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 20px;
}

    .stats .number {
        font-size: 32px;
        font-weight: bold;
        background: rgba(255,255,255,0.2);
        padding: 10px 18px;
        border-radius: 6px;
        color:#000;
    }

    .stats .text {
        font-size: 16px;
        color:#000;
    }

    .follow {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .follow span {
        font-size: 18px;
        
    color: #000;
    }

    .author-social-icons {
        display: flex;
        gap: 10px;
    }

    .author-social-icons a {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
           background: #000000;
    color: #ffffff;
        text-decoration: none;
        font-size: 18px;
        transition: all 0.3s ease;
    }

    .author-social-icons a:hover {
        background: #000;
        color: #fff;
        transform: translateY(-3px);
    }

    .right img {
        width: 280px;
        height: 280px;
        object-fit: cover;
        border-radius: 8px;
    }

    @media(max-width: 900px) {
        .hero {
            flex-direction: column;
            text-align: center;
        }

        .left {
            max-width: 100%;
        }

        .stats-follow {
            flex-direction: column;
            gap: 20px;
        }

        .right {
            margin-top: 30px;
        }
    }
/* SECTION */
.section {
    padding: 60px 80px;
}

.section h1 {
    text-align: center;
    font-size: 42px;
    margin-bottom: 10px;
}
.team-section h1 {
    margin-bottom: 50px;
}
.underline {
    width: 220px;
    height: 4px;
    background: #f15a2b;
    margin: 10px auto 50px;
    border-radius: 10px;
}

/* CARDS */
.cards {
    display: flex;
    gap: 40px;
    justify-content: center;
    flex-wrap: wrap;
    padding-bottom:50px;
}

.card {
    width: 380px;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-8px);
}

/* IMAGE */
.card-img {
    position: relative;
}

.card-img img {
    width: 100%;
    /*height: 240px;*/
    object-fit: cover;
}

/* NEW RIBBON */
.ribbon {
    position: absolute;
    top: 15px;
    left: -10px;
    background: #f15a2b;
    color: #fff;
    padding: 6px 20px;
    font-size: 12px;
    transform: rotate(-45deg);
}

/* CONTENT */
.card-content {
   
    padding: 20px 20px 10px;
}

.meta {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #777;
    margin-bottom: 10px;
}

.card h3 {
    font-size: 22px;
    margin-bottom: 10px;
    color: #222;
}

.card p {
    font-size: 15px;
    color: #555;
    line-height: 1.6;
}

/* FOOTER */
.footer {
    margin-top: 20px;
    border-top: 1px solid #eee;
    padding-top: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.footer : hover {
    
    border-top: 1px solid #6fd943;
}
.author {
    display: flex;
    align-items: center;
    gap: 10px;
}

.author img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.author-info {
    font-size: 14px;
}

.author-info strong {
    display: block;
    color: #000;
}

.date {
    font-size: 14px;
    color: #777;
    
    display: inline-grid;
    text-align: center;
}
.date i{
    color:#6fd943;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px 30px;
}

/* AUTHOR ITEM */
.author {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    text-align: left;
    transition: 0.3s;
}

.author:hover {
    transform: translateY(-5px);
}

.grid .author img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 4px;
    filter: grayscale(100%);
}

/* TEXT */
.author-content h3 {
    font-size: 16px;
    color: #000009;
    margin-bottom: 5px;
    cursor: pointer;
}

.author-content h3:hover {
    text-decoration: underline;
}

.author-content p {
    font-size: 15px;
    color: #333;
    line-height: 1.5;
}
span i {
    color:#6fd943;
}
/* RESPONSIVE */
@media(max-width: 900px) {
    .section {
        padding: 40px 20px;
    }
}
/* RESPONSIVE */
@media (max-width: 1024px) {
    .grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .section {
        padding: 40px 20px;
    }

    .grid {
        grid-template-columns: 1fr;
    }

    .author {
        align-items: center;
    }
}
.bg-grey{
    background-color: #f2f2f2;
}
</style>

<?php $__env->startSection('page-meta-data'); ?>
    <meta name="description" content="<?php echo e($blogauthor->meta_description); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


<section class="hero">
    <div class="left">
        <h4>Hi, I'm</h4>
        <h1><?php echo e($blogauthor->name); ?> </h1>
        <h2><?php echo e($blogauthor->position); ?> </h2>
        <?php echo $blogauthor->content; ?>   

        <div class="stats-follow">
            <div class="stats">
                <div class="number"><?php echo e($blogauthor->blogs->count()); ?></div>
                <div class="text">Total articles<br>since <?php echo e($blogauthor->since_date); ?></div>
            </div>

           <div class="follow">
                <span>Follow the expert:</span>

                <div class="author-social-icons">
                <?php if(!empty($blogauthor->linkedin) && $blogauthor->linkedin != '#'): ?>
                <a href="<?php echo e(Str::startsWith($blogauthor->linkedin, ['http://', 'https://']) 
                ? $blogauthor->linkedin 
                : 'https://' . $blogauthor->linkedin); ?>" target="_blank">
                <i class="fab fa-linkedin-in"></i>
                </a>
                <?php endif; ?>
                
                <?php if(!empty($blogauthor->twitter) && $blogauthor->twitter != '#'): ?>
                <a href="<?php echo e(Str::startsWith($blogauthor->twitter, ['http://', 'https://']) 
                ? $blogauthor->twitter 
                : 'https://' . $blogauthor->twitter); ?>" target="_blank">
                <i class="fab fa-twitter"></i>
                </a>

                <?php endif; ?>
                
                <?php if(!empty($blogauthor->facebook) && $blogauthor->facebook != '#'): ?>
                <a href="<?php echo e(Str::startsWith($blogauthor->facebook, ['http://', 'https://']) 
                ? $blogauthor->facebook 
                : 'https://' . $blogauthor->facebook); ?>" target="_blank">
                <i class="fab fa-facebook"></i>
                </a>
                
                <?php endif; ?>
                
                <?php if(!empty($blogauthor->instagram) && $blogauthor->instagram != '#'): ?>
                <a href="<?php echo e(Str::startsWith($blogauthor->instagram, ['http://', 'https://']) 
                ? $blogauthor->instagram 
                : 'https://' . $blogauthor->instagram); ?>" target="_blank">
                <i class="fab fa-instagram"></i>
                </a>
                
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="right">
         <?php if(!empty($author_image)): ?>
                                <img src="<?php echo e($author_image['img_url']); ?>" alt="<?php echo e(__($blogauthor->name)); ?>">
                                
                            <?php endif; ?>
    </div>
</section>

<section class="section">
    <h1>Recent Articles From <?php echo e($blogauthor->name); ?> </h1>
    <div class="underline"></div>
    <div id="blog-content">
    <div class="cards">
  <?php if(count($all_blogs) > 0): ?>
        <!-- CARD 1 -->
         <?php $__currentLoopData = $all_blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($data->id === $blogauthor->id): ?> <?php continue; ?> <?php endif; ?>
        <div class="card">
            <div class="card-img">
                <!--<span class="ribbon">NEW</span>-->
              
                 <a href="<?php echo e(route('frontend.blog.single',$data->slug)); ?>">  <?php echo render_image_by_attachment_id($data->image,null,'grid'); ?></a>
            </div>

            <div class="card-content">
                <div class="meta">
                    <span><i class="fas fa-folder"></i> <?php echo get_blog_category_by_id($data->blog_categories_id,'link'); ?></span>
                    <span>                    <i class="fas fa-users"></i> <?php
                        $Counting = '327';
                        $randomNumber = random_int(3, 10);
                        //$TotalVisitors = $Counting+$randomNumber;
                        $TotalVisitors = $data->total_visitors;
                    ?>
                    <?php echo e($TotalVisitors); ?></span>
                </div>

                <h3><a href="<?php echo e(route('frontend.blog.single',$data->slug)); ?>"><?php echo e($data->title); ?></a></h3>

                <p>                  <?php echo e(\Illuminate\Support\Str::words(
    trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($data->content)))),
    10,
    '...'
)); ?>

                </p>

                <div class="footer"> 
                    <div class="author">
                         <?php if(!empty($author_image)): ?>
                                <img src="<?php echo e($author_image['img_url']); ?>" alt="<?php echo e(__($blogauthor->name)); ?>">
                                
                            <?php endif; ?>
                        <div class="author-info">
                            <strong><?php echo e(__($blogauthor->name)); ?></strong>
                          <?php echo e(__($blogauthor->position)); ?>

                        </div>
                    </div>
                    <div class="date"><i class='far fa-calendar-alt'></i> <?php echo e($data->updated_at->format('M d, y')); ?></div>
                </div>
            </div>
        </div>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 <?php endif; ?>
   
        </div>
<nav class="pagination-wrapper" aria-label="Page navigation "> 
            <?php if(request()->query('author')!=''): ?>
                <?php echo e($all_blogs->appends(['author' => request()->query('author')])->links()); ?>

            <?php else: ?>
            <?php echo e($all_blogs->links('vendor.pagination.default')); ?>

                <!--<?php echo e($all_blogs->links()); ?> -->
            <?php endif; ?>
        </nav>
        <!-- CARD 2 -->
    
    </div>
</section>
<section class="section bg-grey">
    <div class="team-section">
    <h1>Explore Our Other Authors</h1>
</div>
    <div class="grid">
<?php $__currentLoopData = $all_author; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <!-- AUTHOR 1 -->
        <div class="author">
            <?php echo render_image_markup_by_attachment_id($data->image,null,'grid'); ?>

            <!--<img src="https://www.go4database.com/assets/uploads/media-uploader/about-image-min1616328572.jpg" alt="">-->
            <div class="author-content">
                <a href="<?php echo e(route('frontend.author.single',$data->slug)); ?>">
                <h3> <?php echo e(__($data->name)); ?></h3>
                <p>  <?php echo e(\Illuminate\Support\Str::words(
    trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($data->content)))),
    10,
    '...'
)); ?> </p>
                </a>
            </div>
        </div>

         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </div>
</section>
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.pagination a.page-link', function(e) {
        e.preventDefault();

        const page = $(this).data('page');
        if (!page) return;

        $.ajax({
            url: window.location.pathname + '?page=' + page,
            type: 'GET',
            success: function(data) {
                const newContent = $(data).find('#blog-content').html();
                $('#blog-content').html(newContent);
               // window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            error: function() {
                alert('Failed to load page.');
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.frontend-page-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/pages/blog/blog-author.blade.php ENDPATH**/ ?>