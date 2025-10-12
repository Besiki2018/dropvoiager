<section class="layout-pt-lg layout-pb-lg">
    <div class="container">
        <div class="tabs js-tabs">
            <div class="row y-gap-30">
                <div class="col-lg-3">
                    <div class="px-30 py-30 rounded-4 border-light">
                        <div class="tabs__controls row y-gap-10 js-tabs-controls">
                            <?php if(!empty($list_item)): ?>
                                <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-12">
                                        <button class="tabs__button js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-<?php echo e($key); ?>"><?php echo e($item['title']); ?></button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="tabs__content js-tabs-content">
                        <?php if(!empty($list_item)): ?>
                            <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="tabs__pane -tab-item-<?php echo e($key); ?> <?php if($key == 0): ?> is-tab-el-active <?php endif; ?>">
                                    <h1 class="text-30 fw-600 mb-15"><?php echo e($item['title']); ?></h1>
                                    <div>
                                        <?php echo $item['desc']; ?>

                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Template/Views/frontend/blocks/terms/index.blade.php ENDPATH**/ ?>