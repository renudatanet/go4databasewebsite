<?php if(request()->routeIs('homepage') || request()->routeIs('frontend.homepage.demo')): ?>
    <meta property="og:title"  content="<?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>" />
    <?php echo render_og_meta_image_by_attachment_id(filter_static_option_value('og_meta_image_for_site',$global_static_field_data)); ?>

    <?php if(filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)!=''): ?>
    
        <title>  <?php echo e(filter_static_option_value('site_meta_'.$user_select_lang_slug.'_tags',$global_static_field_data)); ?></title>
    <?php else: ?>
        <title> <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?> - <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_tag_line',$global_static_field_data)); ?></title>
    <?php endif; ?>
    
    
    <meta name="description" content="<?php echo e(filter_static_option_value('site_meta_'.$user_select_lang_slug.'_description',$global_static_field_data)); ?>">
    
    
        <?php echo get_static_option('site_meta_'.$user_select_lang_slug.'_schema_code'); ?>
   
<?php else: ?> 
<?php
    $user_select_lang_slug = $user_select_lang_slug ?? 'en_US';
?>
    <?php
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    ?>
    <?php if(get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
        <?php if(Request::path()=='about'): ?>
            <title>
                <?php if(get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('about_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
        <?php elseif(Request::path()=='testimonial'): ?>
            <title> 
                <?php if(get_static_option('testimonial_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('testimonial_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
             <?php elseif(Request::path()=='career'): ?>
            <title> 
                <?php if(get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('career_with_us_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
            <?php elseif(isset($job_category) && $job_category): ?>

    
    <title>
        <?php if(!empty($job_category->meta_tags)): ?>
            <?php echo e($job_category->meta_tags); ?>

        <?php elseif(!empty($job_category->title)): ?>
            <?php echo e($job_category->title); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
            <?php elseif(isset($job) && $job): ?>

    
    <title> 
        <?php if(!empty($job->meta_tags)): ?>
            <?php echo e($job->meta_tags); ?>

        <?php elseif(!empty($job->name)): ?>
        
            <?php echo e($job->meta_tags); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
        <?php elseif(Request::path()=='pricing'): ?>
            <title> 
                <?php if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('price_plan_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>  
        <?php elseif(Request::path()=='contact'): ?>
            <title> 
                <?php if(get_static_option('contact_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('contact_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>  
        <?php elseif(Request::path()=='faq'): ?>
            <title> 
                <?php if(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')); ?> 
                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title> 
                 <?php elseif(Request::path()=='author'): ?>
            <title> 
                <?php if(get_static_option('author_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('author_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title> 
          

<?php elseif(isset($blogauthor) && $blogauthor): ?>
    

    <title>
        <?php if(!empty($blogauthor->meta_tags)): ?>
            <?php echo e($blogauthor->meta_tags); ?>

        <?php elseif(!empty($blogauthor->name)): ?>
            <?php echo e($blogauthor->meta_tags); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
        <?php elseif(Request::path()=='blog'): ?>
            <title> 
                <?php if(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                    <?php echo e(get_static_option('blog_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>   
        <?php elseif(strpos($url,'blog') !== false): ?>
            <title>
                 
                <?php if(isset($blog_post->meta_tags) && $blog_post->meta_tags!=''): ?>
                    <?php echo e($blog_post->meta_tags); ?>

                <?php elseif(isset($blog_post->title) && $blog_post->title!=''): ?>
                    <?php echo e($blog_post->title); ?>

                <?php elseif(isset($blogcat->meta_tags) && $blogcat->meta_tags!=''): ?>
                    <?php echo e($blogcat->meta_tags); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title> 
            <?php elseif(Request::path() == 'b2b'): ?>

    <title>
        <?php if(get_static_option('work_page_'.$user_select_lang_slug.'_meta_tags') != ''): ?>
            <?php echo e(get_static_option('work_page_'.$user_select_lang_slug.'_meta_tags')); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>


<?php elseif(isset($work_category) && $work_category): ?>

    
    <title> 
        <?php if(!empty($work_category->meta_tag)): ?>
            <?php echo e($work_category->meta_tag); ?>

        <?php elseif(!empty($work_category->name)): ?>
            <?php echo e($work_category->meta_tags); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
    

<?php elseif(isset($work_item) && $work_item): ?>

    
    <title> 
        <?php if(!empty($work_item->meta_tag)): ?>
            <?php echo e($work_item->meta_tag); ?>

        <?php elseif(!empty($work_item->title)): ?>
            <?php echo e($work_item->title); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
            
            <?php elseif(Request::path()=='case-study'): ?>
            <title> 
              
                <?php if(get_static_option('case_study_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                   <?php echo e(get_static_option('case_study_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>   
        <?php elseif(strpos($url,'case-study') !== false): ?>
            <title> 
                <?php if(isset($work_item->meta_tag) && $work_item->meta_tag!=''): ?>
                    <?php echo e($work_item->meta_tag); ?>

                <?php elseif(isset($work_item->title) && $work_item->title!=''): ?>
                    <?php echo e($work_item->title); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
        <?php elseif(Request::path()=='b2c'): ?>
       
            <title>
              
                <?php if(get_static_option('service_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                   <?php echo e(get_static_option('service_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title> 
            
<?php elseif(isset($service_category) && $service_category): ?>
    
    <title>
        <?php if(!empty($service_category->meta_tag)): ?>
            <?php echo e($service_category->meta_tag); ?> | 
        <?php elseif(!empty($service_category->name)): ?>
            <?php echo e($service_category->meta_tags); ?>

        <?php else: ?>
            <?php echo $__env->yieldContent('site-title'); ?>
            <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> - <?php endif; ?>
            <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title', $global_static_field_data)); ?>

        <?php endif; ?>
    </title>
     <?php elseif(isset($service_item) && $service_item): ?>
            <title>
                <?php if(isset($service_item->meta_tag) && $service_item->meta_tag!=''): ?>
                    <?php echo e($service_item->meta_tag); ?>

                <?php elseif(isset($service_item->title) && $service_item->title!=''): ?>
                    <?php echo e($service_item->title); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
             <?php elseif(Request::path()=='list'): ?>
       
            <title>
                <?php if(get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')!=''): ?>
                   <?php echo e(get_static_option('list_page_'.$user_select_lang_slug.'_meta_tags')); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title> 
             <?php elseif(isset($list_item) && $list_item): ?>
            <title>
                <?php if(isset($list_item->meta_tag) && $list_item->meta_tag!=''): ?>
                 
                    <?php echo e($list_item->meta_tag); ?>

                <?php elseif(isset($list_item->title) && $list_item->title!=''): ?>
                   <?php echo e($list_item->title); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
            </title>
        <?php else: ?>   
            <title>
                <?php if(isset($page_post->meta_tags) && $page_post->meta_tags!=''): ?>
                    <?php echo e($page_post->meta_tags); ?>

                <?php else: ?>
                    <?php echo $__env->yieldContent('site-title'); ?>
                    <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                    <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

                <?php endif; ?>
                
            </title>
        <?php endif; ?>    
    <?php else: ?>
        <title>
            <?php if(isset($page_post->meta_tags) && $page_post->meta_tags!=''): ?>
                <?php echo e($page_post->meta_tags); ?>

            <?php elseif(isset($blog_post->meta_tags) && $blog_post->meta_tags!=''): ?>
                <?php echo e($blog_post->meta_tags); ?>

            <?php else: ?>
                <?php echo $__env->yieldContent('site-title'); ?>
                <?php if (! empty(trim($__env->yieldContent('site-title')))): ?> - <?php else: ?> <?php echo $__env->yieldContent('page-title'); ?> -  <?php endif; ?>
                <?php echo e(filter_static_option_value('site_'.$user_select_lang_slug.'_title',$global_static_field_data)); ?>

            <?php endif; ?>
        </title>
    <?php endif; ?>
    
    <?php echo $__env->yieldContent('page-meta-data'); ?>
    
    <?php echo $__env->yieldContent('og-meta'); ?>

    <?php if(Request::path()=='about'): ?>
        <?php if(get_static_option('about_page_'.$user_select_lang_slug.'_schema_code')!=''): ?>
            <?php echo get_static_option('about_page_'.$user_select_lang_slug.'_schema_code'); ?>
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "<?php echo e(url('/')); ?>"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "<?php echo e(url(Request::path())); ?>"  
  }]
}
</script>
        <?php endif; ?>
    <?php elseif(Request::path()=='testimonial'): ?>
        <?php if(get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code')!=''): ?>
            <?php echo get_static_option('testimonial_page_'.$user_select_lang_slug.'_schema_code'); ?>
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "<?php echo e(url('/')); ?>"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "<?php echo e(url(Request::path())); ?>"  
  }]
}
</script>
        <?php endif; ?>
    <?php elseif(Request::path()=='pricing'): ?>
        <?php if(get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code')!=''): ?>
            <?php echo get_static_option('price_plan_page_'.$user_select_lang_slug.'_schema_code'); ?>
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "<?php echo e(url('/')); ?>"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "<?php echo e(url(Request::path())); ?>"  
  }]
}
</script>
        <?php endif; ?>
    <?php elseif(Request::path()=='contact'): ?>
        <?php if(get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code')!=''): ?>
            <?php echo get_static_option('contact_page_'.$user_select_lang_slug.'_schema_code'); ?>
                
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "<?php echo e(url('/')); ?>"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "Blog",
    "item": "<?php echo e(url(Request::path())); ?>"  
  }]
}
</script>
        <?php endif; ?>
    <?php elseif(Request::path()=='faq'): ?>
        <?php if(get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code')!=''): ?>
            <?php echo get_static_option('faq_page_'.$user_select_lang_slug.'_schema_code'); ?>
       
        <?php endif; ?>
    <?php elseif(Request::path()=='blog'): ?>
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "<?php echo e(url(Request::path())); ?>"  
        }]
    }
  ]
}
</script>

  <?php elseif(strpos($url,'blog') !== false): ?>
     <?php if(isset($blogcat) && !empty($blogcat)): ?>
        <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
  
  {
     "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "<?php echo e(url('/blog')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "<?php echo e($blogcat->name); ?>",
          "item": "<?php echo e(url(Request::path())); ?>"  
        }]
    }
  ]
}
</script>

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($blogcat->name); ?>"
          } 
      </script>
     <?php elseif(isset($blog_post) && !empty($blog_post)): ?>
          <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Blog",
          "item": "<?php echo e(url('blog')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "<?php echo e($blog_post->title); ?> ",
          "item": "<?php echo e(url()->current()); ?>"  
        }]
    },
    {
        "@type": "BlogPosting",
        "mainEntityOfPage": {
          "@type": "WebPage",
          "@id": "<?php echo e(url()->current()); ?>"
        },
        "headline": "<?php echo e($blog_post->title); ?>",
        "description": "<?php echo e(\Illuminate\Support\Str::limit(strip_tags($blog_post->content),150)); ?>",
        "image": "<?php echo e(get_attachment_image_by_id($blog_post->image,'full',true)['img_url'] ?? ''); ?>",  
        "author": {
          "@type": "Person",
          "name": "<?php echo e($blog_post->author); ?>",
          "url": "<?php echo e(url()->current()); ?>"

        },  
        "publisher": {
          "@type": "Organization",
          "name": "<?php echo e($blog_post->author); ?>",
          "logo": {
            "@type": "ImageObject",
            "url": "<?php echo e(url()->current()); ?>"
          }
        },
        "datePublished": "<?php echo e(date('c', strtotime($blog_post->created_at))); ?>",
"dateModified": "<?php echo e(date('c', strtotime($blog_post->updated_at))); ?>"
    },
    <?php if(!empty($blog_post->faqs)): ?>
<?php echo json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($blog_post->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>


<?php endif; ?>
  ]
}
</script>
     


<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($blog_post->title); ?>"
          } 
      </script>
      <?php endif; ?>
     <?php elseif(Request::path()=='b2b'): ?>
   
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    }
  ]
}
</script>
               
    <script type="application/ld+json">
{
  "@context": "https://schema.org/", 
  "@type": "BreadcrumbList", 
  "itemListElement": [{
    "@type": "ListItem", 
    "position": 1, 
    "name": "Home",
    "item": "<?php echo e(url('/')); ?>"  
  },{
    "@type": "ListItem", 
    "position": 2, 
    "name": "B2b",
    "item": "<?php echo e(url(Request::path())); ?>"  
  }]
}
</script>
  <?php elseif(strpos($url,'b2b') !== false): ?>
     <?php if(isset($category_name) && !empty($category_name)): ?>
        
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "b2b",
          "item": "<?php echo e(url('/b2b')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "<?php echo e($category_name); ?>",
          "item": "<?php echo e(url()->current()); ?>"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($category_name); ?>"
          } 
      </script>
     <?php elseif(isset($work_item) && !empty($work_item)): ?>


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },

    {
      "@type": "Product",
      "@id": "<?php echo e(url()->current()); ?>#product",
      "name": "<?php echo e($work_item->title); ?>",
      "image": "<?php echo e(get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? ''); ?>",
      "description": "<?php echo e(\Illuminate\Support\Str::limit(
    trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($work_item->description)))),
    350,
    ''
)); ?>",
      "brand": { "@id": "<?php echo e(url('/')); ?>#organization" },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
    },

    {
      "@type": "BreadcrumbList",
      "@id": "<?php echo e(url()->current()); ?>#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('/')); ?>",
            "url": "<?php echo e(url('/')); ?>",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('b2b')); ?>",
            "url": "<?php echo e(url('b2b')); ?>",
            "name": "B2B"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url()->current()); ?>",
            "url": "<?php echo e(url()->current()); ?>",
            "name": "<?php echo e($work_item->title); ?>"
          }
        }
      ]
    },

    {
      "@type": "WebPage",
      "@id": "<?php echo e(url()->current()); ?>#webpage",
      "url": "<?php echo e(url()->current()); ?>",
      "name": "<?php echo e($work_item->title); ?>",
      "isPartOf": {
        "@id": "<?php echo e(url('/')); ?>#website"
      },
      "breadcrumb": {
        "@id": "<?php echo e(url()->current()); ?>#breadcrumb"
      },
      "mainEntity": {
        "@id": "<?php echo e(url()->current()); ?>#product"
      },
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    }

  ]
}
</script>
 
      <?php endif; ?>
       
    <?php elseif(Request::path()=='case-study'): ?>
   <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
      "itemListElement": [{
        "@type": "ListItem", 
        "position": 1, 
        "name": "Home",
        "item": "<?php echo e(url('/')); ?>"  
      },{
        "@type": "ListItem", 
        "position": 2, 
        "name": "Case Study",
        "item": "<?php echo e(url(Request::path())); ?>"  
      }]
    }
  ]
}
</script>
               
   
  <?php elseif(strpos($url,'case-study') !== false): ?>
     <?php if(isset($all_work_category) && !empty($all_work_category)): ?>
        
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "Case Study",
          "item": "<?php echo e(url('/case-study')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "<?php echo e($all_work_category); ?>",
          "item": "<?php echo e(url()->current()); ?>"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($all_work_category); ?>"
          } 
      </script>
     <?php elseif(isset($work_item) && !empty($work_item)): ?>
     
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
      "@type": "BreadcrumbList", 
      "itemListElement": [{
        "@type": "ListItem", 
        "position": 1, 
        "name": "Home",
        "item": "<?php echo e(url('/')); ?>"  
      },{
        "@type": "ListItem", 
        "position": 2, 
        "name": "Case Study",
        "item": "<?php echo e(url('case-study')); ?>"  
      },{
        "@type": "ListItem", 
        "position": 3, 
        "name": "<?php echo e($work_item->title); ?> ",
        "item": "<?php echo e(url()->current()); ?>"  
      }]
    },
    {
      "@type": "Article",
      "mainEntityOfPage": {
      "@type": "WebPage",
      "@id": "<?php echo e(url()->current()); ?>"
      },
      "headline": "<?php echo e($work_item->title); ?>",
      "description": "<?php echo e(\Illuminate\Support\Str::limit(strip_tags($work_item->description),150)); ?>",
      "image": "<?php echo e(get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? ''); ?>",
      "author": {
      "url": "<?php echo e(url()->current()); ?>",
      "@type": "Organization",
      "name": "Go4Database",
      "logo": {
      "@type": "ImageObject",
      "url": "<?php echo e(get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? ''); ?>"
      }
      },
      "publisher": {
      "@type": "Organization",
      "name": "Go4Database",
      "logo": {
      "@type": "ImageObject",
      "url": "<?php echo e(get_attachment_image_by_id($work_item->image,'full',true)['img_url'] ?? ''); ?>"
      }
      },
      "datePublished": "<?php echo e($work_item->created_at?->utc()->toIso8601String()); ?>",
"dateModified": "<?php echo e($work_item->updated_at?->utc()->toIso8601String()); ?>"
    }
  ]
}
</script>
    




<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($work_item->title); ?>"
          } 
      </script>
      <?php endif; ?>
       
     <?php elseif(Request::path()=='b2c'): ?>
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {

      "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "B2c",
          "item": "<?php echo e(url(Request::path())); ?>"  
        }]
    }
  ]
}
</script>
   
  <?php elseif(strpos($url,'b2c') !== false): ?>
     <?php if(isset($category_name) && !empty($category_name)): ?>
   <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
      "@type": "BreadcrumbList", 
        "itemListElement": [{
          "@type": "ListItem", 
          "position": 1, 
          "name": "Home",
          "item": "<?php echo e(url('/')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 2, 
          "name": "b2c",
          "item": "<?php echo e(url('/b2c')); ?>"  
        },{
          "@type": "ListItem", 
          "position": 3, 
          "name": "<?php echo e($category_name); ?>",
          "item": "<?php echo e(url()->current()); ?>"  
        }]
    }
  ]
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($category_name); ?>"
          } 
      </script>
     <?php elseif(isset($service_item) && !empty($service_item)): ?>
     
 <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },

    {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "<?php echo e(url('/')); ?>/b2b/dentists-mailing-list#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('/')); ?>",
            "url": "<?php echo e(url('/')); ?>",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('b2c')); ?>",
            "url": "<?php echo e(url('b2c')); ?>",
            "name": "B2C"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url()->current()); ?>",
            "url": "<?php echo e(url()->current()); ?>",
            "name": "<?php echo e($service_item->title); ?>"
          }
        }
      ]
    },
    {
    "@type": "Product",
    "name": "<?php echo e($service_item->title); ?>",
    "image": "<?php echo e(get_attachment_image_by_id($service_item->image,'full',true)['img_url'] ?? ''); ?>",
    "description": "<?php echo e(\Illuminate\Support\Str::limit(strip_tags($service_item->description),150)); ?>",
   "brand": { "@id": "https://www.go4database.com#organization" },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
  }
]
}
</script>
     
<?php if(!empty($service_item->faqs)): ?>
<script type="application/ld+json">
<?php echo json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($service_item->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

</script>
<?php endif; ?>

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($service_item->title); ?>"
          } 
      </script>
      <?php endif; ?>


 <?php elseif(strpos($url,'list') !== false): ?>
     <?php if(isset($category_name) && !empty($category_name)): ?>
        
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
   {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
   },
   {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
      "@type": "BreadcrumbList",
      "@id": "<?php echo e(url()->current()); ?>#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('/')); ?>",
            "url": "<?php echo e(url('/')); ?>",
            "name": "Home"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url('list')); ?>",
            "url": "<?php echo e(url('list')); ?>",
            "name": "List"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "WebPage",
            "@id": "<?php echo e(url()->current()); ?>",
            "url": "<?php echo e(url()->current()); ?>",
            "name": "<?php echo e($category_name); ?>"
          }
        }
      ]
    }
   ],
}
</script>
<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($category_name); ?>"
          } 
      </script>
     <?php elseif(isset($service_item) && !empty($service_item)): ?>
     
     
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
   {
       "@type": "Organization",
      "@id": "<?php echo e(url('/')); ?>#organization",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "logo": {
        "@type": "ImageObject",
        "@id": "<?php echo e(url('/')); ?>#logo",
        "url": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "<?php echo e(url('/')); ?>/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "<?php echo e(url('/')); ?>#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
   },
   {
      "@type": "WebSite",
      "@id": "<?php echo e(url('/')); ?>#website",
      "name": "Go4Database",
      "url": "<?php echo e(url('/')); ?>",
      "publisher": {
        "@id": "<?php echo e(url('/')); ?>#organization"
      }
    },
    {
      "@type": "Product",
      "name": "<?php echo e($service_item->title); ?>",
      "image": "<?php echo e(get_attachment_image_by_id($service_item->image,'full',true)['img_url'] ?? ''); ?>",
      "description": "<?php echo e(\Illuminate\Support\Str::limit(strip_tags($service_item->description),150)); ?>",
      "brand": { "@id": "https://www.go4database.com#organization" },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "reviewCount": "39"
      }
    },
    
    {
      "@type": "BreadcrumbList",
      "@id": "<?php echo e(url()->current()); ?>#breadcrumb",
      "itemListElement": [
      {
      "@type": "ListItem", 
      "position": 1, 
      "name": "Home",
      "item": "<?php echo e(url('/')); ?>"  
      },
      {
        "@type": "ListItem", 
        "position": 2, 
        "name": "list",
        "item": "<?php echo e(url('list')); ?>"  
      },
      {
      "@type": "ListItem", 
      "position": 3, 
      "name": "<?php echo e($service_item->title); ?> ",
      "item": "<?php echo e(url()->current()); ?>"  
      }
    ]
    }
  ]
}
</script>

<?php if(!empty($service_item->faqs)): ?>
<script type="application/ld+json">
<?php echo json_encode([
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => collect($service_item->faqs)->map(function ($faq) {
        return [
            "@type" => "Question",
            "name" => $faq['question'],
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => strip_tags($faq['answer'])
            ]
        ];
    })->values()
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>

</script>
<?php endif; ?>

<script type="application/ld+json">
         {         
            "@context": "https://schema.org/",         
            "@type": "WebPage",         
            "@id": "#WebPage",         
            "url": "<?php echo e(url()->current()); ?>",          
            "name": "<?php echo e($service_item->title); ?>"
          } 
      </script>
      <?php endif; ?>

        <?php elseif(Request::path()=='career'): ?>
        
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "https://www.go4database.com#organization",
      "name": "Go4Database",
      "url": "https://www.go4database.com",
      "logo": {
        "@type": "ImageObject",
        "@id": "https://www.go4database.com#logo",
        "url": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "https://www.go4database.com#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [
            {
              "@type": "ListItem", 
              "position": 1, 
              "name": "Home",
              "item": "<?php echo e(url('/')); ?>"  
            },
            {
            "@type": "ListItem", 
            "position": 2, 
            "name": "Career",
            "item": "<?php echo e(url(Request::path())); ?>"  
            } 
       ]
    }
  ]
}
</script>
  
 <?php elseif(strpos($url,'career') !== false): ?>
  
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [

    {
      "@type": "Organization",
      "@id": "https://www.go4database.com#organization",
      "name": "Go4Database",
      "url": "https://www.go4database.com",
      "logo": {
        "@type": "ImageObject",
        "@id": "https://www.go4database.com#logo",
        "url": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "contentUrl": "https://www.go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png",
        "width": 300,
        "height": 300,
        "caption": "Go4Database"
      },
      "image": { "@id": "https://www.go4database.com#logo" },
     
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+1 786 785 2141",
        "contactType": "customer service",
        "areaServed": "US",
        "availableLanguage": "en"
      },
      "sameAs": [
        "https://www.facebook.com/Go4Database",
        "https://twitter.com/go4database",
        "https://www.instagram.com/go4database/",
        "https://www.youtube.com/@Go4Database",
        "https://www.linkedin.com/company/go4database/",
        "https://in.pinterest.com/go4database/"
      ]
    },
    {
       "@type": "BreadcrumbList", 
        "itemListElement": [
            {
              "@type": "ListItem", 
              "position": 1, 
              "name": "Home",
              "item": "<?php echo e(url('/')); ?>"  
            },
            {
            "@type": "ListItem", 
            "position": 2, 
            "name": "Career",
            "item": "<?php echo e(url(Request::path())); ?>"  
            } 
            {
            "@type": "ListItem", 
            "position": 3, 
            "name": "<?php echo e(isset($job) ? $job->title : $category_name); ?>",
            "item": "<?php echo e(url()->current()); ?>"  
            }
       ]
    }
  ]
}
</script>
<?php if(isset($job)): ?>

<?php
function convertToNumber($value)
{
    $value = strtoupper(trim($value));

    if (str_contains($value, 'K')) {
        return (int) str_replace('K', '', $value) * 1000;
    }

    if (str_contains($value, 'M')) {
        return (int) str_replace('M', '', $value) * 1000000;
    }

    return (int) $value;
}

$range = $job->salary;

[$min, $max] = explode('-', $range);

$minValue = convertToNumber($min);
$maxValue = convertToNumber($max);
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "JobPosting",
  "title": "<?php echo e($job->title); ?>",
  "description": "<?php echo e(strip_tags($job->job_context)); ?>",
  "hiringOrganization": {
    "@type": "Organization",
    "name": "Go4Database",
    "sameAs": "https://www.go4database.com/",
    "logo": "https://go4database.com/assets/uploads/media-uploader/go4database-logo1751528079.png"
  },
  "industry": "<?php echo e($category_name); ?>",
  "employmentType": "<?php echo e($job->employment_status); ?>",
  "workHours": "1pm-10pm",
  "datePosted": "<?php echo e($job->created_at->format('Y-m-d')); ?>",
  "validThrough": "<?php echo e($job->deadline); ?>",
  "jobLocation": {
    "@type": "Place",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "<?php echo e($job->job_location); ?>",
      "addressLocality": "Noida",
      "postalCode": "201301",
      "addressCountry": "IN"
    }
  },
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "INR",
    "value": {
      "@type": "QuantitativeValue",
      "minValue": <?php echo e($minValue); ?>,
      "maxValue": <?php echo e($maxValue); ?>,
      "unitText": "MONTH"
    }
  },
  "responsibilities": "<?php echo e(strip_tags($job->job_responsibility)); ?>",
  "qualifications": "<?php echo e(strip_tags($job->education_requirement)); ?>",
  "educationRequirements": "<?php echo e(strip_tags($job->education_requirement)); ?>",
  "experienceRequirements": "<?php echo e(strip_tags($job->experience_requirement)); ?>"
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "WebPage",
  "@id": "#WebPage",
  "url": "<?php echo e(url()->current()); ?>",
  "name": "<?php echo e($job->title); ?>"
}
</script>

<?php else: ?>

<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "CollectionPage",
  "name": "<?php echo e($category_name); ?> Jobs",
  "url": "<?php echo e(url()->current()); ?>"
}
</script>

<?php endif; ?>
    <?php else: ?>   
        <?php if(isset($page_post->schema_code) && $page_post->schema_code!=''): ?>
            <?php echo $page_post->schema_code; ?>
        <?php endif; ?>
    <?php endif; ?>    
<?php endif; ?>
<?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/partials/og-meta.blade.php ENDPATH**/ ?>