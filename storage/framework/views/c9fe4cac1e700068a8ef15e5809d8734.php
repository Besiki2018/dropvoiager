<section class="layout-pt-md layout-pb-md">
        <div data-anim-wrap class="container">
                <div data-anim-child="slide-up" class="row y-gap-20 justify-between items-end">
                        <div class="col-auto">
                                <div class="sectionTitle -md">
                                        <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc ?? ''); ?></p>
                                </div>
                        </div>

                        <div class="col-auto">

                                <a href="<?php echo e(route('tour.search')); ?>"
                                   class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                                        <?php echo e(__('More')); ?>

                                        <div class="icon-arrow-top-right ml-15"></div>
                                </a>

                        </div>
                </div>
                <div class="row y-gap-30 pt-40 sm:pt-20">

                        <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-<?php echo e($col ?? 3); ?> col-md-6">
                                        <?php echo $__env->make('Tour::frontend.layouts.search.loop-grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
        </div>
</section><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/blocks/list-tour/style-normal.blade.php ENDPATH**/ ?>