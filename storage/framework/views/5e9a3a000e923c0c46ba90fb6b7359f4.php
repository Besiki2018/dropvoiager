<?php $offer_wrap_class = '';
if (!empty($style)){
    switch ($style){
        case ('style1') : $offer_wrap_class = 'layout-pt-lg layout-pb-md'; break;
        default : $offer_wrap_class = 'layout-pt-md layout-pb-md';
    }
} ?>
<div class="bravo-offer <?php echo e($offer_wrap_class); ?>">
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
            <div class="row y-gap-20">
                <?php $stt = 2; ?>
                <?php $__currentLoopData = $list_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div data-anim="slide-up delay-<?php echo e($stt); ?>" class="<?php echo e(($style ?? '' == 'style1') ? 'col-lg-4 col-sm-6' : 'col-md-6'); ?>">

                        <div class="ctaCard -type-1 rounded-4 <?php if(empty($item['offer_overLay'])): ?> -no-overlay <?php endif; ?>">
                            <div class="ctaCard__image ratio <?php echo e(($style ?? '' == 'style1') ? 'ratio-41:35' : 'ratio-63:55'); ?>">
                                <img class="img-ratio js-lazy" src="#" data-src="<?php echo e(get_file_url($item['background_image'],'full') ?? ""); ?>" alt="image">
                            </div>

                            <div class="ctaCard__content <?php echo e(($style ?? '' == 'style1') ? 'py-50 px-50' : 'py-70 px-70'); ?>  lg:py-30 lg:px-30">

                                <?php if(!empty($item['featured_text'])): ?>
                                    <div class="text-15 fw-500 text-white mb-10"><?php echo e($item['featured_text']); ?></div>
                                <?php endif; ?>
                                <h4 class="<?php echo e($style ?? '' == 'style1' ? 'text-30 xl:text-24' : 'text-40 lg:text-26'); ?> text-white"><?php echo clean($item['title']); ?></h4>

                                <div class="d-inline-block mt-30">
                                    <a href="<?php echo e($item['link_more']); ?>" class="button px-48 py-15 -blue-1 -min-180 bg-white text-dark-1"><?php echo e($item['link_title']); ?></a>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php $stt++; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Template/Views/frontend/blocks/offer-block/style1.blade.php ENDPATH**/ ?>