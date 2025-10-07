<?php
    $translation = $row->translate();
    $layout_style = $layout_style ?? '';
?>

<?php if($layout_style != 'home_2'): ?>
<div class="item-loop <?php echo e($wrap_class ?? ''); ?>">
<?php endif; ?>
    <div class="hotelsCard -type-1 ">
        <div class="hotelsCard__image">
            <div class="cardImage ratio ratio-1:1">
                <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl()); ?>" >
                    <div class="cardImage__content">
                        <?php if($row->image_url): ?>
                            <?php if(!empty($disable_lazyload)): ?>
                                <img  src="<?php echo e($row->image_url); ?>" class="img-responsive rounded-4 col-12 js-lazy" alt="">
                            <?php else: ?>
                                <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-responsive rounded-4 col-12 js-lazy','alt'=>$translation->title]); ?>

                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </a>
                <div class="service-wishlist <?php echo e($row->isWishList()); ?>" data-id="<?php echo e($row->id); ?>" data-type="<?php echo e($row->type); ?>">
                    <div class="cardImage__wishlist">
                        <button class="button -blue-1 bg-white size-30 rounded-full shadow-2">
                            <i class="icon-heart text-12"></i>
                        </button>
                    </div>
                </div>
                <div class="cardImage__leftBadge">
                    <?php if($row->is_featured == "1"): ?>
                        <div class="py-5 px-15 rounded-right-4 text-12 lh-16 fw-500 uppercase bg-yellow-1 text-dark-1">
                            <?php echo e(__("Featured")); ?>

                        </div>
                    <?php endif; ?>
                    <?php if($row->discount_percent): ?>
                        <div class="py-5 px-15 rounded-right-4 text-12 lh-16 fw-500 uppercase bg-blue-1 text-white mt-5">
                            <?php echo e(__("Sale off :number",['number'=>$row->discount_percent])); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="hotelsCard__content mt-10">
            <h4 class="hotelsCard__title text-dark-1 text-18 lh-16 fw-500">
                <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl()); ?>" class="text-dark-1-i">
                    <span><?php echo e($translation->title); ?></span>
                </a>
            </h4>
            <?php if(!empty($row->location->name)): ?>
                <?php $location =  $row->location->translate() ?>
            <?php endif; ?>
            <p class="text-light-1 lh-14 text-14 mt-5"><?php echo e($location->name ?? ''); ?></p>
            <?php if(setting_item('boat_enable_review')): ?>
                <?php $reviewData = $row->getScoreReview(); $score_total = $reviewData['score_total'];?>
                <div class="d-flex items-center mt-20">
                    <div class="flex-center bg-blue-1 rounded-4 size-30 text-12 fw-600 text-white"><?php echo e($reviewData['score_total']); ?></div>
                    <div class="text-14 text-dark-1 fw-500 ml-10"><?php echo e($reviewData['review_text']); ?></div>
                    <div class="text-14 text-light-1 ml-10">
                        <?php if($reviewData['total_review'] > 1): ?>
                            <?php echo e(__(":number Reviews",["number"=>$reviewData['total_review'] ])); ?>

                        <?php else: ?>
                            <?php echo e(__(":number Review",["number"=>$reviewData['total_review'] ])); ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mt-5">
                <div class="fw-500">
                    <?php echo e(__('Starting from')); ?> <span class="text-blue-1 d-inline-flex"><?php echo e(format_money($row->min_price)); ?></span>
                </div>
            </div>
        </div>
    </div>
<?php if($layout_style != 'home_2'): ?>
</div>
<?php endif; ?>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Boat/Views/frontend/layouts/search/loop-grid.blade.php ENDPATH**/ ?>