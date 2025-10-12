<?php if(!empty($list_item)): ?>
<section class="section-bg rounded-4 overflow-hidden">
    <div class="section-bg__item -left-100 -right-100 bg-blue-2"></div>

    <div class="section-bg__item col-4 -right-100 lg:d-none">
        <img src="<?php echo e(get_file_url($testimonial_bg,'full')); ?>" alt="image">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-8">
                <div class="pt-120 pb-120 lg:pt-80 lg:pb-80">
                    <h2 class="text-30 fw-600"><?php echo e($title ?? ''); ?></h2>
                    <p class="mt-5"><?php echo e($subtitle ?? ''); ?></p>

                    <div class="overflow-hidden pt-60 lg:pt-40 js-section-slider" data-slider-cols="base-1">
                        <div class="swiper-wrapper">

                            <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="swiper-slide">
                                    <div class="testimonials -type-2">
                                        <img src="<?php echo e(asset('themes/gotrip/images/quote-1.svg')); ?>" alt="quote" class="mb-35">
                                        <div class="text-22 md:text-18 fw-600 text-dark-1"><?php echo e($item['desc']); ?></div>

                                        <div class="d-flex items-center mt-35">
                                            <img src="<?php echo e(get_file_url($item['avatar'])); ?>" alt="<?php echo e($item['name']); ?>" class="size-70">
                                            <div class="ml-20">
                                                <h5 class="text-15 lh-11 fw-500"><?php echo e($item['name']); ?></h5>
                                                <div class="text-14 lh-11 mt-5"><?php echo e($item['job']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Tour/Views/frontend/blocks/testimonial/style_6.blade.php ENDPATH**/ ?>