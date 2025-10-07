<section class="layout-pt-lg layout-pb-md bravo-list-locations <?php if(!empty($layout)): ?> <?php echo e($layout); ?> <?php endif; ?>">
    <div class="container">
        <div data-anim="slide-up delay-1" class="row y-gap-20 justify-between items-end">
            <div class="col-auto">
                <div class="sectionTitle -md">
                    <h2 class="sectionTitle__title"><?php echo e($title); ?></h2>
                    <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                </div>
            </div>

            <?php if(!empty($view_all_url)): ?>
                <div class="col-auto md:d-none">
                    <a href="<?php echo e($view_all_url); ?>" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                        <?php echo e(__('View All Destinations')); ?> <div class="icon-arrow-top-right ml-15"></div>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="relative pt-40 sm:pt-20 js-section-slider" data-gap="30" data-scrollbar data-slider-cols="base-2 xl-4 lg-3 md-2 sm-2 base-1" data-anim="slide-up delay-2">
            <div class="swiper-wrapper">
                <?php if($rows): ?>
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('Location::frontend.blocks.list-locations.loop', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>


            <button class="section-slider-nav -prev flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-prev">
                <i class="icon icon-chevron-left text-12"></i>
            </button>

            <button class="section-slider-nav -next flex-center button -blue-1 bg-white shadow-1 size-40 rounded-full sm:d-none js-next">
                <i class="icon icon-chevron-right text-12"></i>
            </button>


            <div class="slider-scrollbar bg-light-2 mt-40 sm:d-none js-scrollbar"></div>

            <?php if(!empty($view_all_url)): ?>
                <div class="row pt-20 d-none md:d-block">
                    <div class="col-auto">
                        <div class="d-inline-block">
                            <a href="<?php echo e($view_all_url); ?>" class="button -md -blue-1 bg-blue-1-05 text-blue-1">
                                <?php echo e(__('View All Destinations')); ?> <div class="icon-arrow-top-right ml-15"></div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Location/Views/frontend/blocks/list-locations/default.blade.php ENDPATH**/ ?>