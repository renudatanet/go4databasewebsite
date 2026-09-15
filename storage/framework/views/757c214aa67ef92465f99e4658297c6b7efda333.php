
<?php
  $post_img = null;
  $blog_image = get_attachment_image_by_id($blog_post->image,"full",false);
  $post_img = !empty($blog_image) ? $blog_image['img_url'] : '';
 ?>
 
<?php $__env->startSection('og-meta'); ?>
    <meta property="og:url"  content="<?php echo e(route('frontend.blog.single',$blog_post->slug)); ?>" />
    <meta property="og:type"  content="article" />
    <meta property="og:title"  content="<?php echo e($blog_post->meta_tags); ?>" />
    <meta property="og:image" content="<?php echo e($post_img); ?>" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-meta-data'); ?>
    <meta name="description" content="<?php echo e($blog_post->meta_description); ?>">
    <meta name="tags" content="<?php echo e($blog_post->tags); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-title'); ?>
    <?php if($blog_post->meta_tags!=''): ?>
        <?php echo e($blog_post->meta_tags); ?>

    <?php else: ?>
        <?php echo e($blog_post->title); ?>

    <?php endif; ?>
    <?php
  $author_image = get_attachment_image_by_id($blog_post->authorData->image,"full",false);
   ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
<!-- Font Awesome 5 or 6 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


    <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/css/blog.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e($blog_post->title); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="blog-details-content-area padding-top-100 padding-bottom-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="blog-details-item">
                        <div class="thumb">
                            <?php if(!empty($blog_image)): ?>
                                <img src="<?php echo e($blog_image['img_url']); ?>" alt="<?php echo e(__($blog_post->title)); ?>">
                            <?php endif; ?>
                            <?php if(!empty($blog_post->video_url)): ?>
                            <div class="popup-videos">
                                <a href="<?php echo e($blog_post->video_url); ?>" class="videos-play mfp-iframe" tabindex="0"> <i class="fas fa-play"></i> </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="entry-content">
                            <ul class="post-meta">
                                <li>Last updated <i class="fas fa-calendar-alt"></i> 
                                    <?php if(isset($blog_post->publish_date) && $blog_post->publish_date!=''): ?>
                                        <?php echo e(\Carbon\Carbon::parse($blog_post->updated_at)->format('d M y')); ?>

                                    <?php else: ?>
                                        <?php echo e(date_format($blog_post->created_at,'d M y')); ?>

                                    <?php endif; ?>    
                                </li>
                                <li>
                                    
                                    <?php if($blog_post->authorData && $blog_post->authorData->slug): ?>
    <a href="<?php echo e(route('frontend.author.single', $blog_post->authorData->slug)); ?>">
       
                                        <i class="fas fa-user"></i> <?php echo e($blog_post->authorData->name); ?>

    </a>
<?php else: ?>
    <span> 
                                        <i class="fas fa-user"></i> <?php echo e($blog_post->authorData->name ?? 'Unknown Author'); ?></span>
<?php endif; ?>
                                <li>
                                    <div class="cats">
                                        <i class="fas fa-folder"></i>
                                        <?php echo get_blog_category_by_id($blog_post->blog_categories_id,'link'); ?>

                                    </div>
                                </li>
                                <li>
                                    <div class="cats">
                                        <i class="fas fa-users"></i>
                                        <?php
                                            $Counting = '327';
                                            $randomNumber = random_int(3, 10);
                                            //$TotalVisitors = $Counting+$randomNumber;
                                            $TotalVisitors = $blog_post->total_visitors;
                                        ?>
                                        <?php echo e($TotalVisitors); ?>

                                    </div>
                                </li>
                            </ul>
                            
                           <div class="content-area">
                               <?php echo $toc['content']; ?>

                               

<div class="faq-section">

    <h2 class="faq-title">
        Frequently Asked Questions
    </h2>

    <p class="faq-subtitle">
        You might have these questions in your mind?
    </p>


<div class="faq-wrapper">

    <?php if(!empty($blog_post->faqs)): ?>

        <?php $__currentLoopData = $blog_post->faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="faq-item <?php echo e($key == 0 ? 'active' : ''); ?>">

                <button class="faq-question">

                    <h3>
                        <?php echo e($faq['question']); ?>

                    </h3>

                    <span class="faq-icon">+</span>

                </button>

                <div class="faq-answer">

                    <div class="faq-answer-content">

                        <?php echo e($faq['answer']); ?>


                    </div>

                </div>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php else: ?>

        <p>No FAQ Found</p>

    <?php endif; ?>

</div>
</div>

                                <?php if(!empty($blog_post->faq_content)): ?>
                                <h2 style="margin-bottom:20px;">FAqs</h2>
                              <?php echo $blog_post->faq_content; ?>

                                <?php endif; ?>
                           </div>
                        </div>
                        <div class="blog-details-footer">
                        <?php
                            $all_tags = explode(',',$blog_post->tags);
                        ?>
                        <?php if(count($all_tags) > 1): ?> 
                          
                        <?php endif; ?>
                            <div class="right">
                                <ul class="social-share">
                                    <li class="title"><?php echo e(get_static_option('blog_single_page_'.$user_select_lang_slug.'_share_title')); ?></li>
                                    <?php echo single_post_share(route('frontend.blog.single',$blog_post->slug),$blog_post->title,$post_img); ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if(count($all_related_blog) > 1): ?>
                    <div class="related-post-area margin-top-40">
                        <div class="section-title ">
                            <p class="title "><?php echo e(get_static_option('blog_single_page_'.$user_select_lang_slug.'_related_post_title')); ?></p>
                            <div class="related-news-carousel margin-top-30">
                                <?php $__currentLoopData = $all_related_blog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($data->id === $blog_post->id): ?> <?php continue; ?> <?php endif; ?>
                                    <div class="single-blog-grid-02">
                                        <div class="thumb">
                                            <?php echo render_image_markup_by_attachment_id($data->image,null,'grid'); ?>

                                        </div>
                                        <div class="content">
                                            <p class="title"><a href="<?php echo e(route('frontend.blog.single',$data->slug)); ?>"><?php echo e($data->title); ?></a></p>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!--<div class="disqus-comment-area margin-top-40">-->
                    <!--    <div id="disqus_thread"></div>-->
                    <!--</div>-->
                </div>
             
                <div class="col-lg-4" >
 
<!-- HERO -->
<div class="hero-card">
    <h2>Do you want <br><span>free mailing list?</span></h2>
    <div class="divider"></div>
    <p>Download High-Quality, Decision-Maker Email List to Boost Your Sales Pipeline Today</p>

    <div class="form">
        <input type="text" placeholder="Your Business Email">
        <button>Signup</button>
    </div>
</div>
<!-- Table of Contents -->
<div class="toc">
    
      <?php if(!empty($toc['toc'])): ?>
    <h2>Table of contents</h2>
    <hr>

    <ul>
        <?php $__currentLoopData = $toc['toc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a href="#<?php echo e($item['anchor']); ?>"> <?php echo e($item['title']); ?></a></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
     <?php endif; ?>
</div>

<!-- KEYWORD -->
<div class="keyword-card">
    <h2>Enter a keyword and get insights and suggestions</h2>
    <div class="search-box">
        <input type="text" placeholder="Keyword">
        <button>🔍</button>
    </div>
    <p>Free keyword research tool</p>
</div>

<!-- ABOUT -->
<div class="about-section">
    <h3>About Go4Database</h3>

    <div class="about-box">
        <div class="logo-box"><i class='fas fa-globe'></i></div>
        <p>Go4Database is a mass & global Business Contacts Search Platform. Having its mission to help almost all small companies, freelancers and startups to find their right audience for business generation at very affordable price.</p>
    </div>

    <div class="about-box">
        <div class="logo-box">
           <i class='far fa-edit'></i>
        </div>
        <p><strong>400 Mn records</strong></p>
    </div>

    <div class="about-box">
        <div class="logo-box">
           <i class='fas fa-folder'></i>
        </div>
        <p><strong>845 Category List</strong></p>
    </div>
</div>

<div class="contact"><a href="<?php echo e(url('/contact')); ?>">Contact Us →</a></div>

<!-- CLIENTS -->
<div class="contact-clients">
    <div class="contact-row">
        <div class="icon">👤</div>
        <div>
            <h3>Brand & Partners</h3>
            <p>They trust in us</p>
        </div>
    </div>

    <div class="logos">
        <div class="client-logo"><img src="https://www.go4database.com/assets/uploads/media-uploader/zoho.webp" alt="zoho"></div>
        <div class="client-logo"><img src="https://www.go4database.com/assets/uploads/media-uploader/hostinger.webp"  alt="hostinger"></div>
        <div class="client-logo"><img src="https://www.go4database.com/assets/uploads/media-uploader/google.webp"  alt="google"></div>
        <div class="client-logo"><img src="https://www.go4database.com/assets/uploads/media-uploader/cloudfare.webp"  alt="cloudfare"></div>
    </div>
</div>

<!-- AUTHOR -->
<div class="about-section">
    <h3>About <?php echo e($blog_post->author); ?></h3>

    <div class="about-box">
         <?php if(!empty($author_image)): ?>
                                <img src="<?php echo e($author_image['img_url']); ?>" alt="<?php echo e(__($blog_post->authorData->name)); ?>" width="60">
                                
                            <?php endif; ?>
                            
        <p>Experienced marketing writer with 10+ years expertise.</p>
    </div>
    
<a href="<?php echo e(route('frontend.author.single', $blog_post->authorData->slug)); ?>" class="moreLink"> More About →</a>
</div>

<!-- TABS -->
<div class="card-tabs">

    <div class="tabs">
        <div class="tab active" onclick="showTab('guides', this)">Verified Mailing List</div>
        <div class="tab" onclick="showTab('agency', this)">Industry List</div>
    </div>

    <ul class="list" id="guides">
       <li><a href="#"> Healthcare Industry Mailing List</a></li>
<li><a href="#">Information Technology Industry Mailing List</a></li>
<li><a href="#">Banking Industry Mailing List</a></li>
<li><a href="#">Manufacturing Industry Mailing List</a></li>
<li><a href="#">Real Estate Industry Mailing List</a></li>
<li><a href="#">Retail Industry Mailing List</a></li>
<li><a href="#">E-commerce Industry Mailing List</a></li>
<li><a href="#">Telecommunications Industry Mailing List</a></li>
<li><a href="#">Education Industry Mailing List</a></li>
<li><a href="#">Energy & Utilities Industry Mailing List</a></li>
<li><a href="#">Automotive Industry Mailing List</a></li>
<li><a href="#">Travel & Hospitality Industry Mailing List</a></li>
    </ul>

    <ul class="list" id="agency" style="display:none;">
      <li><a href="#">Hospitals Mailing List</a></li>
<li><a href="#">Pharmaceutical Industry Mailing List</a></li>
<li><a href="#">Medical Devices Industry Mailing List</a></li>
<li><a href="#">Software & SaaS Companies Mailing List</a></li>
<li><a href="#">Financial Services Industry Mailing List</a></li>
<li><a href="#">Insurance Industry Mailing List</a></li>
<li><a href="#">Construction Industry Mailing List</a></li>
<li><a href="#">Logistics & Supply Chain Industry Mailing List</a></li>
    </ul>

</div>


<!-- TABS -->
<div class="card-tabs">

    <div class="tabs">
        <div class="tab active">Decision Makers List</div>
    </div>

    <ul class="list" id="Tools">
       <li><a href="#" target="_blank" rel="noopener">C-Level Executives Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">CEO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">CFO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">CIO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">CTO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">CMO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">COO Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">Director Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">HR Directors Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">Marketing Directors Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">Sales Director Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">Procurement Managers Mailing List</a></li>
<li><a href="#" target="_blank" rel="noopener">IT Managers Mailing List</a></li>
  

    </ul>


</div>


<div class="slider-container">
  <div class="slider-header">
    <h3>CASE STUDIES</h3>
    <div class="nav-buttons">
      <button onclick="prevSlide()">&#10094;</button>
      <button onclick="nextSlide()">&#10095;</button>
    </div>
  </div>
 <?php $__currentLoopData = $all_work; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="slide <?php echo e($loop->first ? 'active' : ''); ?>">
    <div class="logo"><?php echo e($data->name); ?></div>
    <p>
    <?php echo e(\Illuminate\Support\Str::limit(strip_tags($data->description), 180)); ?>

    </p>
  </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  

  <div class="read-more"><a href="<?php echo e(url('/contact')); ?>">Read all case studies →</a></div>
</div>

                
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

    <?php if(!empty(get_static_option('site_disqus_key'))): ?>
    <script>
        var disqus_config = function () {
        this.page.url = "<?php echo e(route('frontend.blog.single',$blog_post->slug)); ?>";
        this.page.identifier = "<?php echo e($blog_post->id); ?>";
        };

        (function() { // DON'T EDIT BELOW THIS LINE
            var d = document, s = d.createElement('script');
            s.src = "https://<?php echo e(get_static_option('site_disqus_key')); ?>.disqus.com/embed.js";
            s.setAttribute('data-timestamp', +new Date());
            (d.head || d.body).appendChild(s);
        })();
    </script>
  <script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const rawHref = this.getAttribute('href');
        const targetId = rawHref.slice(1); // remove the '#'
        const target = document.querySelector('#' + CSS.escape(targetId)); // escape for safety

        if (target) {
            const offset = 200;
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = target.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });

            // Manually update the URL hash after a short delay
            setTimeout(() => {
                history.pushState(null, '', '#' + targetId);
            }, 500); // delay to sync with smooth scroll (adjust if needed)
        }
    });
});
</script>
<script>
function showTab(tabId, element) {
    // Hide all lists
    document.getElementById("guides").style.display = "none";
    document.getElementById("agency").style.display = "none";

    // Show selected list
    document.getElementById(tabId).style.display = "block";

    // Remove active class
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));

    // Add active class to clicked tab
    element.classList.add("active");
}
</script>
<script>

const faqItems =
document.querySelectorAll('.faq-item');

faqItems.forEach(item => {

    const question =
    item.querySelector('.faq-question');

    question.addEventListener('click', () => {

        if(item.classList.contains('active')){

            item.classList.remove('active');

        }else{

            faqItems.forEach(faq => {
                faq.classList.remove('active');
            });

            item.classList.add('active');

        }

    });

});

</script>
<script>
let currentIndex = 0;
const slides = document.querySelectorAll(".slide");

function showSlide(index) {
  slides.forEach(slide => slide.classList.remove("active"));
  slides[index].classList.add("active");
}

function nextSlide() {
  currentIndex = (currentIndex + 1) % slides.length;
  showSlide(currentIndex);
}

function prevSlide() {
  currentIndex = (currentIndex - 1 + slides.length) % slides.length;
  showSlide(currentIndex);
}
</script>

    <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
    
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.frontend-page-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/pages/blog/blog-single.blade.php ENDPATH**/ ?>