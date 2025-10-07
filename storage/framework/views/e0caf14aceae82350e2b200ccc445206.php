<?php
$review_score = $row->review_data
?>
<div class="row justify-between items-end">
    <div class="col-auto">
        <h1 class="text-26 fw-600"><?php echo e($translation->title); ?></h1>
        <div class="row x-gap-10 y-gap-20 items-center pt-10">
            <?php if($row->getReviewEnable()): ?>
                <?php if($review_score): ?>
                    <div class="col-auto">
                        <div class="d-flex items-center">
                            <i class="icon-star text-10 text-yellow-1"></i>
                            <div class="text-14 text-light-1 ml-10">
                                <span class="text-15 fw-500 text-dark-1"><?php echo e($review_score['score_total']); ?></span>
                                <?php echo e(__(":number reviews",['number'=>$review_score['total_review']])); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="col-auto">
                <div class="row x-gap-10 items-center">
                    <?php if($translation->address): ?>
                        <div class="col-auto">
                            <div class="d-flex x-gap-5 items-center">
                                <i class="icon-location-2 text-16 text-light-1"></i>
                                <div class="text-15 text-light-1"><?php echo e($translation->address); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-auto">
        <div class="row x-gap-10 y-gap-10">
            <div class="col-auto">
                <div class="dropdown">
                    <button class="button px-15 py-10 -blue-1 dropdown-toggle" type="button" id="dropdownMenuShare" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-share mr-10"></i>
                        <?php echo e(__('Share')); ?>

                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuShare">
                        <a class="dropdown-item facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($row->getDetailUrl()); ?>&amp;title=<?php echo e($translation->title); ?>" target="_blank" rel="noopener" original-title="<?php echo e(__("Facebook")); ?>">
                            <i class="fa fa-facebook"></i> <?php echo e(__('Facebook')); ?>

                        </a>
                        <a class="dropdown-item twitter" href="https://twitter.com/share?url=<?php echo e($row->getDetailUrl()); ?>&amp;title=<?php echo e($translation->title); ?>" target="_blank" rel="noopener" original-title="<?php echo e(__("Twitter")); ?>">
                            <i class="fa fa-twitter"></i> <?php echo e(__('Twitter')); ?>

                        </a>
                    </div>
                </div>
            </div>

            <div class="col-auto">
                <div class="service-wishlist <?php echo e($row->isWishList()); ?>" data-id="<?php echo e($row->id); ?>" data-type="<?php echo e($row->type); ?>">
                    <button class="button px-15 py-10 -blue-1 bg-light-2">
                        <i class="icon-heart mr-10"></i>
                        <?php echo e(__('Save')); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="border-top-light mt-40 mb-40"></div>
<h3 class="text-22 fw-500 mt-30">
    <?php echo e(__('Event snapshot')); ?>

</h3>
<div class="row y-gap-30 justify-between pt-20">

    <div class="col-md-auto col-6">
        <div class="d-flex">
            <i class="icofont-wall-clock text-24 text-blue-1 mr-10"></i>
            <div class="text-15 lh-15">
                <?php echo e(__("Start Time")); ?>:<br> <?php echo e($row->start_time); ?>

            </div>
        </div>
    </div>
    <?php if($row->duration): ?>
        <div class="col-md-auto col-6">
            <div class="d-flex">
                <i class="icofont-infinite text-24 text-blue-1 mr-10"></i>
                <div class="text-15 lh-15">
                    <?php echo e(__("Duration")); ?> <br> <?php echo e(duration_format($row->duration)); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-auto col-6">
        <div class="d-flex">
            <i class="icofont-heart-beat text-24 text-blue-1 mr-10"></i>
            <div class="text-15 lh-15">
                <?php echo e(__("Wishlist")); ?> <br> <?php echo e(__("People interest: :number",['number'=>$row->getNumberWishlistInService()])); ?>

            </div>
        </div>
    </div>
        <?php if(!empty($row->location->name)): ?>
            <?php $location =  $row->location->translate() ?>

            <div class="col-md-auto col-6">
                <div class="d-flex">
                    <i class="icon-route text-24 text-blue-1 mr-10"></i>
                    <div class="text-15 lh-15">
                        <?php echo e(__("Location")); ?> <br> <?php echo e($location->name ?? ''); ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>

</div>
<div class="border-top-light mt-40 mb-40"></div>

<?php if($translation->content): ?>
    <div class="row x-gap-40 y-gap-40 gotrip-overview">
        <div class="col-12">
            <h3 class="text-22 fw-500"><?php echo e(__("Overview")); ?></h3>
            <div class="text-dark-1 text-15 mt-20 content-text">
                <?php echo clean($translation->content); ?>

            </div>
            <span class="d-none btn-showmore pointer text-14 text-blue-1 fw-500 underline mt-10">
                <?php echo e(__("Show More")); ?>

            </span>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Event/Views/frontend/layouts/details/detail.blade.php ENDPATH**/ ?>