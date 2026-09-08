
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Search For: ')); ?> <?php echo e($search_term); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <section class="blog-content-area padding-top-100 padding-bottom-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div id="blog-content">
                        <?php if(count($all_blogs) < 1): ?>
                            <div class="alert alert-danger">
                                <?php echo e(__('Nothing found related to').' '.$search_term); ?>

                            </div>
                        <?php endif; ?>
                        <?php $__currentLoopData = $all_blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.frontend.blog.grid','data' => ['blog' => $data,'margin' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('frontend.blog.grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['blog' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($data),'margin' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="pagination-wrapper" aria-label="Page navigation ">
                        <?php echo e($all_blogs->links('vendor.pagination.default')); ?>

                    </div>
                </div>
                </div>
                <div class="col-lg-4">
                   <?php echo $__env->make('frontend.pages.blog.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </section>
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.pagination a.page-link', function(e) {
        e.preventDefault();

        // Get the page number from the clicked link's data-page attribute
        const page = $(this).data('page');
        if (!page) return;

        // Get current URL query params (e.g. search=b2b)
        const urlParams = new URLSearchParams(window.location.search);

        // Set or update the 'page' param to the clicked page number
        urlParams.set('page', page);

        // Compose the AJAX URL with full query string including search, page, etc.
        const ajaxUrl = window.location.pathname + '?' + urlParams.toString();

        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            success: function(data) {
                // Replace the blog content and pagination with the new page's content
                const newContent = $(data).find('#blog-content').html();
                $('#blog-content').html(newContent);
                //window.scrollTo({ top: 0, behavior: 'smooth' });
            },
            error: function() {
                alert('Failed to load page.');
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.frontend-page-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/pages/blog/blog-search.blade.php ENDPATH**/ ?>