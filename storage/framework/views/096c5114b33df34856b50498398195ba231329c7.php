<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">


     
    <url>
        <loc><?php echo e(url('/')); ?></loc>
        <lastmod><?php echo e(now()->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>

    <url>
<loc><?php echo e(url('/pricing')); ?></loc>
<lastmod><?php echo e(optional(\App\PricePlan::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>
<url>
<loc><?php echo e(url('/about')); ?></loc>
<lastmod><?php echo e(\Carbon\Carbon::parse(get_static_option('about_page_updated_at'))->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>

<url>
<loc><?php echo e(url('/contact')); ?></loc>
<lastmod><?php echo e(optional(\App\ContactInfoItem::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>

<url>
<loc><?php echo e(url('/faq')); ?></loc>
<lastmod><?php echo e(optional(\App\Faq::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>


<url>
<loc><?php echo e(url('/author')); ?></loc>
<lastmod><?php echo e(optional(\App\Author::latest('updated_at')->first())->updated_at?->toAtomString()
            ?? now()->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>

 <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<url>
<loc><?php echo e(url('/'.$post->slug)); ?></loc>
<lastmod><?php echo e($post->updated_at->toAtomString()); ?></lastmod>
<priority>0.80</priority>
</url>
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</urlset><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/sitemap/pages.blade.php ENDPATH**/ ?>