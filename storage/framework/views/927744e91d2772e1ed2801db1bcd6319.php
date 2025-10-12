<section data-anim-wrap class="masthead -type-8">
    <div data-anim-child="fade" class="masthead__bg">
        <img src="<?php echo e($bg_image_url); ?>" alt="image" class="col-12 h-full object-cover">
    </div>

    <div class="container">
        <div class="row justify-center">
            <div class="col-xl-10">
                <div class="text-center">
                    <h1 data-anim-child="slide-up delay-5" class="text-60 lg:text-40 md:text-30 text-dark-1"><?php echo e($title ?? ''); ?></h1>
                    <p data-anim-child="slide-up delay-6" class="text-dark-1 mt-5"><?php echo e($sub_title ?? ''); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div data-anim-child="slide-up delay-7" class="masthead-bottom mt-80 md:mt-40">

        <!--Search Form-->
        <?php echo $__env->make('Car::frontend.layouts.search.form-search',['style' => 'car_carousel'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--End Search Form-->

        <?php if(!empty($style) and $style == "carousel" and !empty($list_slider)): ?>
            <div class="masthead-slider overflow-hidden js-masthead-slider-8">
                <div class="swiper-wrapper">

                    <?php $__currentLoopData = $list_slider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!empty($item['bg_image'])): ?>
                            <?php $img_url = get_file_url($item['bg_image'],'full') ?>
                            <div class="swiper-slide">
                                <img src="<?php echo e($img_url); ?>" alt="image">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>

                <div class="masthead-slider__nav -prev">
                    <button class="button -outline-white size-50 flex-center text-white rounded-full js-prev">
                        <i class="icon-arrow-sm-left text-12"></i>
                    </button>
                </div>

                <div class="masthead-slider__nav -next">
                    <button class="button -outline-white size-50 flex-center text-white rounded-full js-next">
                        <i class="icon-arrow-sm-right text-12"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Car/Views/frontend/blocks/form-search-car/index.blade.php ENDPATH**/ ?>