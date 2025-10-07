<section class="layout-pt-md layout-pb-md">
    <div class="container">
        <div class="row justify-center text-center">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                </div>
            </div>
        </div>

        <div class="row y-gap-30 pt-40 sm:pt-20">
            <?php if($list_term): ?>
                <?php $__currentLoopData = $list_term; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $translation = $term->translate(); ?>
                    <div class="col-xl col-md-4 col-sm-6">

                        <a href="<?php echo e(route('event.search',['term_id' => $term->id])); ?>" class="tourTypeCard -type-1 d-block rounded-4 bg-white border-light rounded-4">
                            <div class="tourTypeCard__content text-center pt-60 pb-24 px-30">
                                <?php if(!empty($term->icon)): ?>
                                    <i class="<?php echo e($term->icon); ?> text-60 sm:text-40 text-blue-1"></i>
                                <?php endif; ?>
                                <h4 class="text-dark-1 text-18 fw-500 mt-50 md:mt-30"><?php echo e($translation->name ?? ''); ?></h4>
                                <p class="text-light-1 lh-14 text-14 mt-5">
                                    <?php if($term->event->count() > 1): ?>
                                        <?php echo e(__(':number Events',['number' => $term->event->count()])); ?>

                                    <?php else: ?>
                                        <?php echo e(__(':number Event',['number' => $term->event->count()])); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </a>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Event/Views/frontend/blocks/term-featured-box/index.blade.php ENDPATH**/ ?>