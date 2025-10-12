<section data-anim-wrap class="masthead -type-2 js-mouse-move-container">
    <div class="masthead__bg bg-dark-3">
        <img src="<?php echo e($bg_image_url); ?>" alt="image" data-src="<?php echo e($bg_image_url); ?>" class="js-lazy">
    </div>
    <div class="container">
        <div data-anim-child="fade delay-1" class="masthead__tabs">
            <div class="row">
                <div class="col-12">
                    <?php if(empty($hide_form_search)): ?>
                        <div class="tabs -bookmark-2 js-tabs">
                            <div class="tabs__controls d-flex items-center js-tabs-controls">
                                <?php if($service_types): ?>
                                    <?php $allServices = get_bookable_services(); $number = 0; ?>
                                    <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            if(empty($allServices[$service_type])) continue;
                                            $service = $allServices[$service_type];
                                        ?>
                                        <div class="">
                                            <button class="tabs__button px-30 py-20 sm:px-20 sm:py-15 rounded-4 fw-600 text-white js-tabs-button <?php if($number == 0): ?> is-tab-el-active <?php endif; ?>" data-tab-target=".-tab-item-<?php echo e($service_type); ?>">
                                                <i class="<?php echo e($icons[$service_type]); ?> text-20 mr-10 sm:mr-5"></i>
                                                <?php echo e($service::getModelName()); ?>

                                            </button>
                                        </div>
                                        <?php $number++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="masthead__content">
            <div class="row y-gap-40">
                <div class="col-xl-5">
                    <h1 data-anim-child="slide-up delay-2" class="z-2 text-60 lg:text-40 md:text-30 text-white pt-80 xl:pt-0">
                        <span class="text-yellow-1"><?php echo e($title ?? ''); ?></span>
                    </h1>
                    <p data-anim-child="slide-up delay-3" class="z-2 text-white mt-20"><?php echo e($sub_title ?? ''); ?></p>
                    <?php if(empty($hide_form_search)): ?>
                        <div data-anim-child="slide-up delay-4" class="mainSearch -w-900 z-2 bg-white pr-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 rounded-4 shadow-1 mt-40">
                            <div class="tabs__content js-tabs-content">
                                <?php $number = 0; ?>
                                <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        if(empty($allServices[$service_type])) continue;
                                    ?>
                                    <div class="tabs__pane -tab-item-<?php echo e($service_type); ?> <?php if($number == 0): ?> is-tab-el-active <?php endif; ?>">
                                        <?php if ($__env->exists(ucfirst($service_type).'::frontend.layouts.search.form-search', ['style' => $style])) echo $__env->make(ucfirst($service_type).'::frontend.layouts.search.form-search', ['style' => $style], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    <?php $number++; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-xl-7">
                    <div class="masthead__images">
                        <?php if(!empty($list_slider)): ?>
                            <?php $__currentLoopData = $list_slider; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $img = get_file_url($item['bg_image'],'full') ?>
                                <div data-anim-child="slide-up delay-6"><img src="<?php echo e($img); ?>" alt="image" class="js-mouse-move" data-move="30"></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Template/Views/frontend/blocks/form-search-all-service/carousel_v2.blade.php ENDPATH**/ ?>