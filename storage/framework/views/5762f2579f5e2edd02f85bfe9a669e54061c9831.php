<?php $__env->startSection('site-title'); ?>
    <?php echo e(get_static_option('faq_page_'.$user_select_lang_slug.'_name') ?: __('Frequently Asked Questions')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(get_static_option('faq_page_'.$user_select_lang_slug.'_name') ?: __('Frequently Asked Questions')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-meta-data'); ?>
    <meta name="description" content="<?php echo e(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_description')); ?>">
    <meta name="tags" content="<?php echo e(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_tags')); ?>">
    <?php echo render_og_meta_image_by_attachment_id(get_static_option('faq_page_'.$user_select_lang_slug.'_meta_image')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('assets/frontend/css/faq.css?v=2')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    /* One list to render from, so categorised and uncategorised questions
       take the same path through the page instead of two near-identical loops. */
    $fq_groups = collect();

    foreach (($all_categories ?? collect()) as $fq_cat) {
        $fq_groups->push([
            'key'  => 'c' . $fq_cat->id,
            'name' => $fq_cat->name,
            'faqs' => $fq_cat->faqs,
        ]);
    }

    if (($uncategorized_faqs ?? collect())->isNotEmpty()) {
        $fq_groups->push([
            'key'  => 'general',
            'name' => __('General'),
            'faqs' => $uncategorized_faqs,
        ]);
    }

    $fq_total = $fq_groups->sum(fn ($g) => $g['faqs']->count());
?>

<div class="fq">

  
  <section class="fq-hero">
    <div class="fq-shell">
      <span class="fq-kicker"><?php echo e(__('Help centre')); ?></span>
      <h1 class="fq-h1"><?php echo e(__('Frequently asked')); ?> <span><?php echo e(__('questions')); ?></span></h1>
      <p class="fq-lede"><?php echo e(__('Answers about your account, our data, orders, billing and privacy. Search below, or pick a topic.')); ?></p>

      <?php if($fq_total): ?>
      <div class="fq-search">
        <i data-lucide="search" class="fq-search-icon"></i>
        <input type="search" id="fq-search" class="fq-search-input" autocomplete="off"
               placeholder="<?php echo e(__('Search all questions...')); ?>"
               aria-label="<?php echo e(__('Search the questions')); ?>">
        <button type="button" id="fq-clear" class="fq-search-clear" hidden aria-label="<?php echo e(__('Clear search')); ?>">
          <i data-lucide="x"></i>
        </button>
      </div>

      <p class="fq-meta">
        <span id="fq-meta-default"><?php echo e($fq_total); ?> <?php echo e($fq_total == 1 ? __('question') : __('questions')); ?> <?php echo e(__('across')); ?> <?php echo e($fq_groups->count()); ?> <?php echo e($fq_groups->count() == 1 ? __('topic') : __('topics')); ?></span>
        <span id="fq-meta-search" hidden></span>
      </p>
      <?php endif; ?>
    </div>
  </section>

  
  <section class="fq-body">
    <div class="fq-shell fq-grid">

      <?php if($fq_groups->count() > 1): ?>
      <aside class="fq-rail" aria-label="<?php echo e(__('Topics')); ?>">
        <div class="fq-rail-inner">
          <span class="fq-rail-label"><?php echo e(__('Topics')); ?></span>
          <nav class="fq-rail-nav">
            <?php $__currentLoopData = $fq_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <a href="#fq-<?php echo e($g['key']); ?>" class="fq-rail-link" data-target="fq-<?php echo e($g['key']); ?>">
                <span><?php echo e($g['name']); ?></span>
                <em><?php echo e($g['faqs']->count()); ?></em>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </nav>
          <div class="fq-rail-help">
            <p><?php echo e(__('Still stuck?')); ?></p>
            <a href="<?php echo e(route('frontend.contact')); ?>" class="fq-rail-cta">
              <?php echo e(__('Talk to our team')); ?> <i data-lucide="arrow-right"></i>
            </a>
          </div>
        </div>
      </aside>
      <?php endif; ?>

      <div class="fq-main">
        <?php $__empty_1 = true; $__currentLoopData = $fq_groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <section class="fq-group" id="fq-<?php echo e($g['key']); ?>">
            <header class="fq-group-head">
              <h2 class="fq-group-title"><?php echo e($g['name']); ?></h2>
              <span class="fq-group-count"><?php echo e($g['faqs']->count()); ?></span>
            </header>

            <div class="fq-list">
              <?php $__currentLoopData = $g['faqs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="fq-item <?php if($data->is_open == 'on'): ?> is-open <?php endif; ?>"
                         itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                  <h3 class="fq-q-wrap">
                    <button type="button" class="fq-q" aria-expanded="<?php echo e($data->is_open == 'on' ? 'true' : 'false'); ?>">
                      <span class="fq-q-tx" itemprop="name"><?php echo e($data->title); ?></span>
                      <span class="fq-q-mark" aria-hidden="true"></span>
                    </button>
                  </h3>
                  <div class="fq-a" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div class="fq-a-inner" itemprop="text"><?php echo $data->description; ?></div>
                  </div>
                </article>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p class="fq-none"><?php echo e(__('No questions have been published yet.')); ?></p>
        <?php endif; ?>

        <div class="fq-empty" id="fq-empty" hidden>
          <i data-lucide="search-x"></i>
          <h3><?php echo e(__('Nothing matched that')); ?></h3>
          <p><?php echo e(__('Try a shorter word, or ask us directly and we will answer it.')); ?></p>
          <a href="<?php echo e(route('frontend.contact')); ?>" class="fq-btn"><?php echo e(__('Ask your question')); ?></a>
        </div>
      </div>
    </div>
  </section>

  
  <section class="fq-cta">
    <div class="fq-shell">
      <div class="fq-cta-card">
        <div>
          <h2><?php echo e(__("Can't find what you're after?")); ?></h2>
          <p><?php echo e(__('Tell us what you need and a real person will get back to you.')); ?></p>
        </div>
        <a href="<?php echo e(route('frontend.contact')); ?>" class="fq-btn fq-btn--solid">
          <i data-lucide="message-circle"></i> <?php echo e(__('Contact us')); ?>

        </a>
      </div>
    </div>
  </section>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="<?php echo e(asset('assets/frontend/js/faq.js?v=2')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend.frontend-page-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/go4database.com/public_html/@core/resources/views/frontend/pages/faq-page.blade.php ENDPATH**/ ?>