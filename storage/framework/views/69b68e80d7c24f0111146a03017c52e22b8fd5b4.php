<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">

 <url>
        <loc><?php echo e(url('/case-study')); ?></loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <!-- Posts -->
    <?php $__currentLoopData = $casestudies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(url('/case-study/'.$post->slug)); ?></loc>
        <lastmod><?php echo e($post->updated_at->toAtomString()); ?></lastmod>
        <priority>0.8</priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php $__currentLoopData = $caseCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <url>
        <loc><?php echo e(url('/case-study/'.$post->slug)); ?></loc>
        <lastmod><?php echo e($post->updated_at->toAtomString()); ?></lastmod>
        <priority>0.8</priority>
    </url>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</urlset><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/sitemap/casestudies.blade.php ENDPATH**/ ?>