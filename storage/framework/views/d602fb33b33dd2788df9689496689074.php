<?php $translation = $row->translate(); ?>

<a href="<?php echo e($row->getDetailUrl()); ?>" class="cruiseCard -type-1 rounded-4 <?php echo e($wrap_class ?? ''); ?>">
    <div class="cruiseCard__image">
        <div class="cardImage ratio ratio-6:5">
            <div class="cardImage__content">
                <?php if($row->image_url): ?>
                    <?php if(!empty($disable_lazyload)): ?>
                        <img  src="<?php echo e($row->image_url); ?>" class="img-responsive rounded-4 col-12 js-lazy" alt="<?php echo e($translation->title); ?>">
                    <?php else: ?>
                        <?php echo get_image_tag($row->image_id,'medium',['class'=>'img-responsive rounded-4 col-12 js-lazy','alt'=>$translation->title]); ?>

                    <?php endif; ?>
                <?php endif; ?>
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

    <div class="cruiseCard__content mt-10">
        <?php if(!empty($row->location->name)): ?>
            <?php $location = $row->location->translate() ?>
            <div class="text-14 lh-14 text-light-1 mb-5"><?php echo e($location->name ?? ''); ?></div>
        <?php endif; ?>

        <h4 class="cruiseCard__title text-dark-1 text-18 lh-16 fw-500">
            <span><?php echo e($translation->title); ?></span>
        </h4>

        <p class="text-light-1 lh-14 text-14 mt-5"></p>

        <div class="row y-gap-10 justify-between items-center mt-2">

            <?php if($row->max_guest): ?>
                <div class="col-auto">
                    <div class="text-25 text-dark-1 fw-500 lh-1" title="<?php echo e(__("Max Guests")); ?>"><i class="icofont-ui-user-group input-icon field-icon"></i></div>
                    <div class="text-14 text-light-1"><?php echo e($row->max_guest); ?></div>
                </div>
            <?php endif; ?>

            <?php if($row->cabin): ?>
                <div class="col-auto">
                    <div class="text-25 text-dark-1 fw-500 lh-1" title="<?php echo e(__("Cabin")); ?>"><i class="input-icon field-icon icofont-sail-boat-alt-2"></i></div>
                    <div class="text-14 text-light-1"><?php echo e($row->cabin); ?></div>
                </div>
            <?php endif; ?>

            <?php if($row->length): ?>
                <div class="col-auto">
                    <div class="text-25 text-dark-1 fw-500 lh-1" title="<?php echo e(__("Length Boat")); ?>"><i class="input-icon field-icon icofont-yacht"></i></div>
                    <div class="text-14 text-light-1"><?php echo e($row->length); ?></div>
                </div>
            <?php endif; ?>

            <?php if($row->speed): ?>
                <div class="col-auto">
                    <div class="text-25 text-dark-1 fw-500 lh-1" title="<?php echo e(__("Speed")); ?>"><i class="input-icon field-icon icofont-speed-meter"></i></div>
                    <div class="text-14 text-light-1"><?php echo e($row->speed); ?></div>
                </div>
            <?php endif; ?>

        </div>

        <div class="row y-gap-20 justify-between items-center pt-5">
            <div class="col-auto">
                <?php if(setting_item('boat_enable_review')): ?>
                    <?php $reviewData = $row->getScoreReview(); $score_total = $reviewData['score_total'];?>
                    <div class="d-flex items-center">
                        <div class="icon-star text-yellow-1 text-10 mr-5"></div>

                        <div class="text-14 text-light-1">
                            <span class="text-15 text-dark-1 fw-500"><?php echo e($reviewData['score_total']); ?></span>
                            <?php if($reviewData['total_review'] > 1): ?>
                                <?php echo e(__(":number reviews",["number"=>$reviewData['total_review'] ])); ?>

                            <?php else: ?>
                                <?php echo e(__(":number review",["number"=>$reviewData['total_review'] ])); ?>

                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="col-auto">
                <div class="text-14 text-light-1">
                    <?php echo e(__('From')); ?>

                    <span class="text-16 fw-500 text-dark-1 d-inline-flex"><?php echo e(format_money($row->min_price)); ?></span>
                </div>
            </div>
        </div>
    </div>
</a>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Boat/Views/frontend/layouts/search/loop-grid-2.blade.php ENDPATH**/ ?>