<section data-anim-wrap class="masthead -type-6">
    <?php if(!empty($bg_image)): ?>
    <div data-anim-child="fade" class="masthead__bg bg-dark-3">
        <img src="<?php echo e(get_file_url($bg_image,'full')); ?>" alt="background">
    </div>
    <?php endif; ?>

    <div class="container">
        <div class="row justify-center">
            <div class="col-xl-9">
                <div class="text-center">
                    <h1 data-anim-child="slide-up delay-4" class="text-60 lg:text-40 md:text-30 text-white"><?php echo e($title ?? ''); ?></h1>
                    <p data-anim-child="slide-up delay-5" class="text-white mt-5"><?php echo e($sub_title ?? ''); ?></p>
                </div>

                <div class="g-form-control mt-40">
                    <?php echo $__env->make('Event::frontend.layouts.search.form-search',['style' => 'normal'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Event/Views/frontend/blocks/form-search-event/index.blade.php ENDPATH**/ ?>