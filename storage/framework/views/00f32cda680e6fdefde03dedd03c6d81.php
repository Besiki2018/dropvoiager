<section data-anim-wrap class="bravo-form-search-space masthead -type-7">
    <div class="masthead-slider js-masthead-slider-7">
        <div class="swiper-wrapper">
            <?php if(!empty($list_slider)): ?>
                <?php $__currentLoopData = $list_slider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <div class="row justify-center text-center">
                            <div class="col-auto">
                                <div class="masthead__content">
                                    <div class="masthead__bg">
                                        <img src="<?php echo e(get_file_url($item['bg_image'],'full')); ?>" alt="image">
                                    </div>

                                    <div data-anim-child="slide-up delay-1" class="text-white">
                                        <?php echo e($item['sub_title'] ?? ''); ?>

                                    </div>

                                    <h1 data-anim-child="slide-up delay-2" class="text-60 lg:text-40 md:text-30 text-white">
                                        <?php echo $item['title'] ?? ''; ?>

                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>

        <div class="masthead-slider__nav -prev js-prev">
            <button class="button -outline-white text-white size-50 rounded-full">
                <i class="icon-arrow-left"></i>
            </button>
        </div>

        <div class="masthead-slider__nav -next js-next">
            <button class="button -outline-white text-white size-50 rounded-full">
                <i class="icon-arrow-right"></i>
            </button>
        </div>
    </div>
    <?php echo $__env->make('Space::frontend.layouts.search.form-search',['style' => 'space_form_search'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Space/Views/frontend/blocks/form-search-space/index.blade.php ENDPATH**/ ?>