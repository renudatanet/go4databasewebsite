<?php if($paginator->hasPages()): ?>
    <ul class="pagination justify-content-center" role="navigation">
        
        <?php if($paginator->onFirstPage()): ?>
            <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                <span class="page-link" aria-hidden="true">&laquo;</span>
            </li>
        <?php else: ?>
            <li class="page-item">
                <a href="javascript:void(0)" class="page-link" data-page="<?php echo e($paginator->currentPage() - 1); ?>" aria-label="Previous">
                    &laquo;
                </a>
            </li>
        <?php endif; ?>

        
        <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <?php if(is_string($element)): ?>
                <li class="page-item disabled" aria-disabled="true"><span class="page-link"><?php echo e($element); ?></span></li>
            <?php endif; ?>

            
            <?php if(is_array($element)): ?>
                <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($page == $paginator->currentPage()): ?>
                        <li class="page-item active" aria-current="page"><span class="page-link"><?php echo e($page); ?></span></li>
                    <?php else: ?>
                        <li class="page-item">
                            <a href="javascript:void(0)" class="page-link" data-page="<?php echo e($page); ?>"><?php echo e($page); ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if($paginator->hasMorePages()): ?>
            <li class="page-item">
                <a href="javascript:void(0)" class="page-link" data-page="<?php echo e($paginator->currentPage() + 1); ?>" aria-label="Next">
                    &raquo;
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                <span class="page-link" aria-hidden="true">&raquo;</span>
            </li>
        <?php endif; ?>
    </ul>
<?php endif; ?>
<?php /**PATH /home/go4database.com/public_html/@core/resources/views/vendor/pagination/default.blade.php ENDPATH**/ ?>