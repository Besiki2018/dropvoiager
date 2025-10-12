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

                <div class="d-flex x-gap-15 items-center ">
                    <div class="col-auto">
                        <button class="d-flex items-center text-24 arrow-left-hover js-deals-prev">
                            <i class="icon icon-arrow-left"></i>
                        </button>
                    </div>

                    <div class="col-auto">
                        <div class="pagination -dots text-border js-deals-pag"></div>
                    </div>

                    <div class="col-auto">
                        <button class="d-flex items-center text-24 arrow-right-hover js-deals-next">
                            <i class="icon icon-arrow-right"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="row y-gap-30 pt-40">
            <div data-anim-child="slide-up delay-2" class="col-xl-5">

                <div class="ctaCard -type-1 rounded-4 ">
                    <div class="ctaCard__image ratio ratio-63:55">
                        <?php if($book_img): ?>
                            <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($book_img)); ?>" alt="image">
                        <?php endif; ?>
                    </div>

                    <div class="ctaCard__content py-50 px-50 lg:py-30 lg:px-30">

                        <div class="text-15 fw-500 text-white mb-10"><?php echo e($book_title ?? ''); ?></div>


                        <h4 class="text-30 lg:text-24 text-white"><?php echo e($book_desc ?? ''); ?></h4>

                        <?php if($book_url_text): ?>
                        <div class="d-inline-block mt-30">
                            <a href="<?php echo e($book_url); ?>" class="button px-48 py-15 -blue-1 -min-180 bg-white text-dark-1"><?php echo e($book_url_text ?? ''); ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <?php if(!empty($rows)): ?>
                <div data-anim-child="slide-left delay-3" class="col-xl-7">
                    <div class="relative overflow-hidden js-section-slider" data-gap="30" data-scrollbar data-slider-cols="xl-3 lg-3 md-2 sm-2 base-1" data-nav-prev="js-deals-prev" data-pagination="js-deals-pag" data-nav-next="js-deals-next">
                        <div class="swiper-wrapper">
                            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <?php echo $__env->make('Tour::frontend.layouts.search.loop-grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/blocks/tour-deals/index.blade.php ENDPATH**/ ?>