<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
 

    
    <?php $__currentLoopData = $sitemaps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $map): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <sitemap>
    <loc><?php echo e($map['loc']); ?></loc>
    <lastmod><?php echo e($map['lastmod']); ?></lastmod>
</sitemap>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  </sitemapindex><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/sitemap/sitemap.blade.php ENDPATH**/ ?>