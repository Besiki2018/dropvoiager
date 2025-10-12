
<?php $__env->startPush('css'); ?>
    <link href="<?php echo e(asset('dist/frontend/module/news/css/news.css?_ver='.config('app.asset_version'))); ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/daterange/daterangepicker.css")); ?>" >
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/ion_rangeslider/css/ion.rangeSlider.min.css")); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo e(asset("libs/fotorama/fotorama.css")); ?>" />
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="bravo-news">
    <?php echo $__env->make('News::frontend.layouts.details.news-breadcrumb', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <section data-anim="slide-up" class="layout-pt-md">
        <div class="container">
            <div class="row y-gap-40 justify-center text-center">
                <div class="col-auto">
                    <div class="text-15 fw-500 text-blue-1 mb-8">
                        <?php $category = $row->category; ?>
                        <?php if(!empty($category)): ?>
                            <?php $t = $category->translate(); ?>
                            <?php echo e($t->name ?? ''); ?>

                        <?php endif; ?>
                    </div>
                    <h1 class="text-30 fw-600"><?php echo e($translation->title); ?></h1>
                    <div class="text-15 text-light-1 mt-10"><?php echo e(display_date($row->updated_at)); ?></div>
                </div>
                <div class="col-12">
                    <?php if($row->gallery): ?>
                        <?php echo $__env->make('Layout::common.detail.gallery4',['galleries' => $row->getGallery()], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php elseif($image_url = get_file_url($row->image_id, 'full')): ?>
                        <img src="<?php echo e($image_url); ?>" alt="<?php echo e($translation->title); ?>" class="col-12 rounded-8">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <section data-anim="slide-up" class="layout-pt-md layout-pb-md">
        <div class="container">
            <div class="row y-gap-30 justify-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="">
                        <?php echo $translation->content; ?>

                        <div class="row y-gap-20 justify-between pt-30">
                            <div class="col-auto">
                                <div class="d-flex items-center">
                                    <div class="fw-500 mr-10"><?php echo e(__("Share")); ?></div>
                                    <div class="d-flex items-center">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e($row->getDetailUrl()); ?>&amp;title=<?php echo e($translation->title); ?>" target="_blank" original-title="<?php echo e(__("Facebook")); ?>" class="flex-center size-40 rounded-full"><i class="icon-facebook text-14"></i></a>
                                        <a href="https://twitter.com/share?url=<?php echo e($row->getDetailUrl()); ?>&amp;title=<?php echo e($translation->title); ?>" target="_blank" original-title="<?php echo e(__("Twitter")); ?>" class="flex-center size-40 rounded-full"><i class="icon-twitter text-14"></i></a>
                                        <a href="#" class="flex-center size-40 rounded-full"><i class="icon-instagram text-14"></i></a>
                                        <a href="#" class="flex-center size-40 rounded-full"><i class="icon-linkedin text-14"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="row x-gap-10 y-gap-10">
                                    <?php if(!empty($tags = $row->getTags()) and count($tags) > 0): ?>
                                        <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $t = $tag->translate(); ?>
                                            <div class="col-auto">
                                                <a href="<?php echo e($tag->getDetailUrl(app()->getLocale())); ?>" class="button -blue-1 py-5 px-20 bg-blue-1-05 rounded-100 text-15 fw-500 text-blue-1">
                                                    <?php echo e($t->name ?? ''); ?>

                                                </a>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($row->user)): ?>
                        <div class="border-top-light border-bottom-light py-30 mt-30">
                            <div class="row y-gap-30">
                                <div class="col-auto">
                                    <img src="<?php echo e($row->author->avatar_url); ?>" alt="image" class="size-70 rounded-full">
                                </div>
                                <div class="col-md">
                                    <h3 class="text-18 fw-500"><?php echo e($row->author->getDisplayName()); ?></h3>
                                    <div class="text-14 text-light-1"><?php echo e($row->author->city); ?></div>
                                    <div class="text-15 mt-10"><?php echo $row->author->bio; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        </div>
    </section>
    <?php if(!empty($related)): ?>
        <section class="layout-pt-md layout-pb-lg">
        <div class="container">
            <div class="row justify-center text-center">
                <div class="col-auto">
                    <div class="sectionTitle -md">
                        <h2 class="sectionTitle__title"><?php echo e(__("Related content")); ?></h2>
                        <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e(__("Interdum et malesuada fames")); ?></p>
                    </div>
                </div>
            </div>
            <div class="row y-gap-30 pt-40">
                <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-3 col-sm-6">
                        <?php $item_translation = $item->translate();?>
                        <a href="" class="blogCard -type-2 d-block bg-white rounded-4 shadow-4">
                            <div class="blogCard__image">
                                <div class="ratio ratio-1:1 rounded-4">
                                    <?php echo get_image_tag($item->image_id,'medium',['class'=>'img-ratio rounded-4','alt'=>$item_translation->title,'lazy'=>false]); ?>

                                </div>
                            </div>
                            <div class="px-20 py-20">
                                <h4 class="text-dark-1 text-16 lh-18 fw-500">
                                    <?php echo clean($item_translation->title); ?>

                                </h4>
                                <div class="text-15 lh-16 text-light-1 mt-10 md:mt-5">
                                    <?php echo get_exceprt($translation->content,80,'...'); ?>

                                </div>
                                <div class="text-light-1 text-15 lh-14 mt-10 text-right">
                                    <?php echo e(display_date($item->updated_at)); ?>

                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/News/Views/frontend/detail.blade.php ENDPATH**/ ?>