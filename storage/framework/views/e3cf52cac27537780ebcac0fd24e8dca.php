

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('Layout::parts.bc', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="page-profile-content page-template-content">
        <div class="container">
            <div class="">
                <div class="row">
                    <div class="col-md-3">
                        <?php echo $__env->make('User::frontend.profile.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                    <div class="col-md-9">
                        <?php
                        $reviews = \Modules\Review\Models\Review::query()->where([
                            'vendor_id'=>$user->id,
                            'status'=>'approved'
                        ])
                            ->orderBy('id','desc')
                            ->with('author')
                            ->paginate(10);
                        ?>
                        <?php if($reviews->total()): ?>
                            <div class="bravo-reviews">
                                <h3><?php echo e(__('Reviews from guests')); ?></h3>
                                <div class="review-pag-text mt-10">
                                    <?php echo e(__("Showing :from - :to of :total total",["from"=>$reviews->firstItem(),"to"=>$reviews->lastItem(),"total"=>$reviews->total()])); ?>

                                </div>
                                <div class="review-list">
                                    <?php if($reviews): ?>
                                        <div class="review-list mt-20">
                                            <div class="row y-gap-40">
                                                <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $userInfo = $item->author; $picture = $item->getReviewMetaPicture(); ?>
                                                    <div class="col-12 review-item">
                                                        <div class="row x-gap-20 y-gap-20 items-center">
                                                            <div class="col-auto">
                                                                <?php if($avatar_url = $userInfo->getAvatarUrl()): ?>
                                                                    <img class="avatar w-60 h-60 rounded-full" src="<?php echo e($avatar_url); ?>" alt="<?php echo e($userInfo->getDisplayName()); ?>">
                                                                <?php else: ?>
                                                                    <span class="avatar-text w-60 h-60 rounded-full"><?php echo e(ucfirst($userInfo->getDisplayName()[0])); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-auto">
                                                                <div class="fw-500 lh-15"><?php echo e($userInfo->display_name); ?></div>
                                                                <div class="text-14 text-light-1 lh-15"><?php echo e(display_datetime($item->created_at)); ?></div>
                                                            </div>
                                                        </div>

                                                        <h5 class="fw-500 text-blue-1 mt-20"><?php echo e($item->title); ?></h5>
                                                        <p class="text-15 text-dark-1 mt-10"><?php echo e($item->content); ?></p>
                                                        <?php if($item->rate_number): ?>
                                                            <ul class="review-star d-flex text-blue-1">
                                                                <?php for( $i = 0 ; $i < 5 ; $i++ ): ?>
                                                                    <?php if($i < $item->rate_number): ?>
                                                                        <li class="me-1"><i class="fa fa-star"></i></li>
                                                                    <?php else: ?>
                                                                        <li class="me-1"><i class="fa fa-star-o"></i></li>
                                                                    <?php endif; ?>
                                                                <?php endfor; ?>
                                                            </ul>
                                                        <?php endif; ?>

                                                        <?php if(!empty($picture)): ?>
                                                            <?php $listImages = json_decode($picture->val, true); ?>
                                                            <div class="row x-gap-30 y-gap-30 pt-20">
                                                                <?php $__currentLoopData = $listImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $oneImages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php $imagesData = json_decode($oneImages, true); ?>
                                                                    <div class="col-auto">
                                                                        <img src="<?php echo e($imagesData['download']); ?>" alt="image" class="rounded-4">
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                                <div class="review-pag-wrapper">
                                    <div class="bravo-pagination">
                                        <?php echo e($reviews->appends(request()->query())->links()); ?>

                                    </div>
                                    <div class="review-pag-text mt-20">
                                        <?php echo e(__("Showing :from - :to of :total total",["from"=>$reviews->firstItem(),"to"=>$reviews->lastItem(),"total"=>$reviews->total()])); ?>

                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="review-pag-text"><?php echo e(__("No Review")); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/User/Views/frontend/profile/all-reviews.blade.php ENDPATH**/ ?>