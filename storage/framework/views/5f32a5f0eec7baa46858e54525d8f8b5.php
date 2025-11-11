<section class="pt-40">
    <div class="container">
        <div class="row y-gap-30">
            <div class="col-lg-8">
                <div class="row y-gap-20 justify-between items-end">
                    <div class="col-auto">
                        <h1 class="text-30 sm:text-24 fw-600"><?php echo e($translation->title); ?></h1>
                        <div class="row x-gap-10 items-center pt-10">
                            <div class="col-auto">
                                <div class="d-flex x-gap-5 items-center">
                                    <i class="icon-location text-16 text-light-1"></i>
                                    <div class="text-15 text-light-1"><?php echo e($translation->address); ?></div>
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
                <?php if($row->getGallery()): ?>
                    <div class="mt-20">
                        <?php echo $__env->make('Layout::common.detail.gallery5',['galleries' => $row->getGallery()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <?php echo $__env->make('Car::frontend.layouts.details.form-book', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="justify-end mt-30 d-none">
                    <?php echo $__env->make('Layout::common.detail.vendor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="pt-40">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div>
                    <h3 class="text-22 fw-500">
                        <?php echo e(__("Property highlights")); ?>

                    </h3>
                    <div class="row y-gap-30 justify-between pt-20">
                        <div class="col-md-auto col-6">
                            <div class="d-flex">
                                <i class="icon-user-2 text-22 text-dark-1 mr-10"></i>
                                <div class="text-15 lh-15">
                                    <?php echo e(__("Passenger")); ?> <br> <?php echo e($row->passenger); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto col-6">
                            <div class="d-flex">
                                <i class="icon-luggage text-22 text-dark-1 mr-10"></i>
                                <div class="text-15 lh-15">
                                    <?php echo e(__("Baggage")); ?><br><?php echo e($row->baggage); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto col-6">
                            <div class="d-flex">
                                <i class="icon-transmission text-22 text-dark-1 mr-10"></i>
                                <div class="text-15 lh-15">
                                    <?php echo e(__("Gear Shift")); ?><br><?php echo e($row->gear); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-auto col-6">
                            <div class="d-flex">
                                <i class="icon-speedometer text-22 text-dark-1 mr-10"></i>
                                <div class="text-15 lh-15">
                                    <?php echo e(__("Door")); ?><br><?php echo e($row->door); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-top-light mt-40 mb-40"></div>
                <div id="overview" class="col-12 gotrip-overview">
                    <h3 class="text-22 fw-500"><?php echo e(__('Overview')); ?></h3>
                    <div class="text-dark-1 text-15 mt-20 content-text">
                        <?php echo clean($translation->content); ?>

                    </div>
                    <span class="d-none btn-showmore pointer text-14 text-blue-1 fw-500 underline mt-10">
                        <?php echo e(__("Show More")); ?>

                    </span>
                </div>
                <div class="border-top-light mt-40 mb-40"></div>
                <?php echo $__env->make('Car::frontend.layouts.details.attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="border-top-light mt-40"></div>
            </div>
        </div>
    </div>
</section>

<?php if($row->map_lat && $row->map_lng): ?>
    <section class="mt-40">
        <?php echo $__env->make('Layout::map.detail.map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </section>
<?php endif; ?>
<div class="mt-40"></div>
<section>
    <div class="container">
        <?php if($translation->faqs): ?>
            <?php echo $__env->make('Layout::common.detail.faq2',['faqs'=>$translation->faqs], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?>
    </div>
</section>
<div class="container mt-40 mb-40">
    <div class="border-top-light"></div>
</div>
<section>
    <div class="container">
        <?php echo $__env->make('Layout::common.detail.review', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
</section>
<div class="mt-40"></div>

<div class="container">
    <?php echo $__env->make('Car::frontend.layouts.details.related', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<div class="layout-pt-lg"></div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Car/Views/frontend/layouts/details/detail.blade.php ENDPATH**/ ?>