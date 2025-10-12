<?php
    $translation = $row->translate();
    $layout_style = $layout_style ?? '';
    $previewImageId = $row->banner_image_id ?: $row->image_id;
    $previewImageUrl = $row->banner_image_url ?: $row->image_url;
?>
<div class="border-top-light pt-30 <?php echo e($wrap_class ?? ''); ?>">
    <div class="row x-gap-20 y-gap-20">
        <div class="col-md-auto">
            <div class="has-skeleton">
            <div class="cardImage ratio ratio-5:4 w-250 md:w-1/1 rounded-4">
                <div class="cardImage__content">
                    <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl()); ?>" >
                    <?php if($previewImageUrl): ?>
                        <?php if(!empty($disable_lazyload)): ?>
                            <img  src="<?php echo e($previewImageUrl); ?>" class="img-responsive rounded-4 col-12 js-lazy" alt="">
                        <?php else: ?>
                            <?php echo get_image_tag($previewImageId,'medium',['class'=>'img-responsive rounded-4 col-12 js-lazy','alt'=>$translation->title]); ?>

                        <?php endif; ?>
                    <?php endif; ?>
                    </a>
                </div>
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
        </div>
        <div class="col-md">
            <div class="row x-gap-10 items-center ">
                <div class="col-auto">
                    <p class="text-14 lh-14 mb-5 has-skeleton"><?php echo e(duration_format($row->duration,true)); ?></p>
                </div>
                <div class="col-auto">
                    <div class="size-3 rounded-full bg-light-1 mb-5 has-skeleton"></div>
                </div>
                <div class="col-auto">
                    <p class="text-14 lh-14 mb-5 has-skeleton"><?php echo e(__(":number% of guests recommend",['number'=>$row->recommend_percent])); ?></p>
                </div>
            </div>
            <h3 class="text-18 lh-16 fw-500 has-skeleton"><a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl()); ?>" ><?php echo e($translation->title); ?></a></h3>
            <?php if(!empty($row->location->name)): ?>
                <?php $location =  $row->location->translate() ?>
                <p class="text-14 lh-14 mt-5 has-skeleton"><?php echo e($location->name); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-auto text-right md:text-left">
            <div class="has-skeleton">
                <?php if(setting_item('tour_enable_review')): ?>
                    <div class="d-flex justify-content-md-end">
                        <?php $reviewData = $row->getScoreReview(); $score_total = $reviewData['score_total'];?>
                        <?php echo $__env->make('Layout::common.rating',['score_total'=>$score_total], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="text-14 lh-14 text-light-1 mt-10">
                        <?php if($reviewData['total_review'] > 1): ?>
                            <?php echo e(__(":number Reviews",["number"=>$reviewData['total_review'] ])); ?>

                        <?php else: ?>
                            <?php echo e(__(":number Review",["number"=>$reviewData['total_review'] ])); ?>

                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="text-14 text-light-1 mt-20 md:mt-20"><?php echo e(__('Starting from')); ?></div>
                <div class="d-flex justify-content-md-end align-baseline mt-5">
                    <div class="text-16 text-red-1 line-through mr-5"><?php echo e($row->display_sale_price); ?></div>
                    <div class="text-22 lh-12 fw-600"><?php echo e($row->display_price); ?></div>
                </div>
                <a <?php if(!empty($blank)): ?> target="_blank" <?php endif; ?> href="<?php echo e($row->getDetailUrl()); ?>" class="button -md -dark-1 bg-blue-1 text-white mt-24">
                    <?php echo e(__('View Detail')); ?> <div class="icon-arrow-top-right ml-15"></div>
                </a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/layouts/search/loop-list.blade.php ENDPATH**/ ?>