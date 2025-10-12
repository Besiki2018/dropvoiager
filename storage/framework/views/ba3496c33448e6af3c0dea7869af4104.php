<?php $key_time = time(); ?>
<section class="layout-pt-md layout-pb-lg">
    <div data-anim-wrap class="container">
        <div class="tabs -pills-2 js-tabs">
            <div data-anim-child="slide-up delay-1" class="row y-gap-20 justify-between items-end">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($sub_title ?? ''); ?></p>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="tabs__controls row x-gap-10 justify-center js-tabs-controls">
                        <?php if(!empty($service_types)): ?>
                            <?php $number = 0; ?>
                            <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $allServices = get_bookable_services();
                                    if(empty($allServices[$service_type]) OR empty($rows[$service_type])) continue;
                                    $module = new $allServices[$service_type];
                                ?>
                                <div class="col-auto">
                                    <button class="tabs__button text-14 fw-500 px-20 py-10 rounded-4 bg-light-2 js-tabs-button <?php if($number == 0): ?> is-tab-el-active <?php endif; ?>" data-tab-target=".-tab-item-<?php echo e($service_type); ?>">
                                        <?php echo e(!empty($modelBlock["title_for_".$service_type]) ? $modelBlock["title_for_".$service_type] : $module->getModelName()); ?>

                                    </button>
                                </div>
                                <?php $number++; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tabs__content pt-40 js-tabs-content">
                <?php if(!empty($service_types)): ?>
                    <?php $number = 0; ?>
                    <?php $__currentLoopData = $service_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $allServices = get_bookable_services();
                            if(empty($allServices[$service_type]) OR empty($rows[$service_type])) continue;
                        ?>
                        <div class="tabs__pane -tab-item-<?php echo e($service_type); ?> <?php if($number == 0): ?> is-tab-el-active <?php endif; ?>">
                            <div class="row y-gap-30">
                                <?php $__currentLoopData = $rows[$service_type]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div data-anim-child="slide-left delay-<?php echo e($number+4); ?>" class="col-xl-3 col-lg-3 col-sm-6">
                                        <?php echo $__env->make(ucfirst($service_type).'::frontend.layouts.search.loop-grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php $number++; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Template/Views/frontend/blocks/list-all-service/style_1.blade.php ENDPATH**/ ?>