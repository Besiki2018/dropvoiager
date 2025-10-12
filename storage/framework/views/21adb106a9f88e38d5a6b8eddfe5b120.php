<div class="bravo-offer layout-pt-lg layout-pb-md">
    <div data-anim-wrap class="container">
        <?php if(!empty($title)): ?>
            <div data-anim-child="slide-up delay-1" class="row justify-center text-center pb-40">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title"><?php echo e($title ?? ''); ?></h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($subtitle ?? ''); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if(!empty($list_item)): ?>
            <div class="row y-gap-20 pt-40">
                <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div data-anim-child="slide-up delay-<?php echo e($key+2); ?>" class="col-lg-4 col-sm-6">
                        <div class="ctaCard -type-1 rounded-4">
                            <div class="ctaCard__image ratio ratio-41:45">
                                <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($item['background_image'],'full') ?? ""); ?>" alt="image">
                            </div>
                            <div class="ctaCard__content py-50 px-50 lg:py-30 lg:px-30">
                                <?php if(!empty($item['featured_text'])): ?>
                                    <h4 class="text-30 lg:text-24 text-white"><?php echo e($item['featured_text']); ?></h4>
                                <?php endif; ?>
                                <h4 class="text-30 lg:text-24 text-white"><?php echo clean($item['title']); ?></h4>

                                <div class="d-inline-block mt-30">
                                    <a href="<?php echo e($item['link_more']); ?>" class="button px-48 py-15 -blue-1 -min-180 bg-white text-dark-1"><?php echo e($item['link_title']); ?></a>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Template/Views/frontend/blocks/offer-block/style2.blade.php ENDPATH**/ ?>