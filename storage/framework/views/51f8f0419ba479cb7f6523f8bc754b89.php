<?php if($list_item): ?>
<section class="layout-pt-lg layout-pb-lg bg-dark-3">
    <div class="container">
        <div class="row y-gap-60">
            <div class="col-xl-5 col-lg-6">
                <h2 class="text-30 text-white"><?php echo clean($title); ?></h2>
                <p class="text-white mt-20"><?php echo e($subtitle ?? ''); ?></p>

                <div class="row y-gap-30 text-white pt-60 lg:pt-40">
                    <div class="col-sm-5 col-6">
                        <div class="text-30 lh-15 fw-600"><?php echo e($happy_people_number ?? ''); ?></div>
                        <div class="lh-15"><?php echo e($happy_people_text ?? ''); ?></div>
                    </div>

                    <div class="col-sm-5 col-6">
                        <div class="text-30 lh-15 fw-600"><?php echo e($overall_rating_number ?? ''); ?></div>
                        <div class="lh-15"><?php echo e($overall_rating_text ?? ''); ?></div>
                        <div class="d-flex x-gap-5 items-center pt-10">
                            <?php if(!empty($overall_rating_star)): ?>
                                <?php for($i = 1; $i <= $overall_rating_star; $i++): ?>
                                    <div class="icon-star text-white text-10"></div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 offset-xl-2 col-lg-5 offset-lg-1 col-md-10">
                <div class="testimonials-slider-2 js-testimonials-slider-2">
                    <div class="swiper-wrapper">
                        <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $avatar_url = get_file_url($item['avatar'], 'full') ?>
                            <div class="swiper-slide">
                                <div class="testimonials -type-1 bg-white rounded-4 pt-40 pb-30 px-40 shadow-2">
                                    <div class="">
                                        <h4 class="text-16 fw-500 text-blue-1 mb-20"><?php echo e($item['title'] ?? ""); ?></h4>
                                        <p class="testimonials__text lh-18 fw-500 text-dark-1"><?php echo clean( $item['desc']?? ""); ?></p>

                                        <div class="pt-20 mt-28 border-top-light">
                                            <div class="row x-gap-20 y-gap-20 items-center">
                                                <div class="col-auto">
                                                    <img src="#" data-src="<?php echo e($avatar_url); ?>" alt="<?php echo e($item['name'] ?? ""); ?>" class="js-lazy" style="border-radius: 50%">
                                                </div>

                                                <div class="col-auto">
                                                    <div class="text-15 fw-500 lh-14"><?php echo e($item['name']?? ""); ?></div>
                                                    <div class="text-14 lh-14 text-light-1 mt-5"><?php echo e($item['job']?? ""); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>


                    <div class="d-flex x-gap-15 items-center justify-center pt-30">
                        <div class="col-auto">
                            <button class="d-flex items-center text-24 arrow-left-hover text-white js-prev">
                                <i class="icon icon-arrow-left"></i>
                            </button>
                        </div>

                        <div class="col-auto">
                            <div class="pagination -dots text-white-50 js-pagination"></div>
                        </div>

                        <div class="col-auto">
                            <button class="d-flex items-center text-24 arrow-right-hover text-white js-next">
                                <i class="icon icon-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="row justify-center text-center pt-60">
            <div class="col-auto">
                <div class="text-15 lh-1 text-white"><?php echo e($title_trusted ?? ''); ?></div>
            </div>
        </div>

        <div class="px-40 md:px-0">
            <div class="row y-gap-30 justify-between items-center pt-60 lg:pt-40">
                <?php if(!empty($list_trusted)): ?>
                    <?php $__currentLoopData = $list_trusted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $avatar_url = get_file_url($item['avatar'], 'full') ?>
                        <div class="col-md-auto col-sm-6">
                            <div class="d-flex justify-center">
                                <img src="#" data-src="<?php echo e($avatar_url); ?>" class="js-lazy">
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/blocks/testimonial/style_2.blade.php ENDPATH**/ ?>