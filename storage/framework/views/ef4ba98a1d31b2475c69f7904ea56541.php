<section data-anim-wrap="" class="masthead -type-5 animated bravo-tour-service">
    <div data-anim-child="fade" class="masthead__bg is-in-view">
        <img src="<?php echo e(asset('themes/gotrip/images/bg.svg')); ?>" alt="bg">
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                <h1 data-anim-child="slide-up delay-4" class="text-60 lg:text-40 md:text-30 is-in-view"><?php echo e($title ?? ''); ?></h1>

                <p data-anim-child="slide-up delay-5" class="mt-20 is-in-view subtitle"><?php echo e($sub_title ?? ''); ?></p>

                <div class="mt-35 form-search-service-tour">
                    <?php echo $__env->make('Tour::frontend.layouts.search.form-search',['style' => 'normal'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                
            </div>
        </div>
    </div>

    <div data-anim-child="fade" class="masthead__image is-in-view">
        <img src="<?php echo e(get_file_url($bg_image)); ?>" alt="image">
    </div>

</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/blocks/form-search-tour/index.blade.php ENDPATH**/ ?>