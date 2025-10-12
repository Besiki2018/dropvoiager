<?php $headerAlign = '';
    if ($header_align == 'center') $headerAlign = 'justify-center text-center';
    if ($header_align == 'right') $headerAlign = 'justify-content-end text-right';
?>
<?php switch($style):
    case ("style_3"): ?>
        <?php echo $__env->make('News::frontend.blocks.list-news.style_3', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>
    <?php case ('style_4'): ?>
        <?php echo $__env->make('News::frontend.blocks.list-news.style_4', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php break; ?>
    <?php case ('style_5'): ?>
        <?php echo $__env->make('News::frontend.blocks.list-news.style_5', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>
    <?php case ('style_6'): ?>
        <?php echo $__env->make('News::frontend.blocks.list-news.style_6', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php break; ?>
    <?php default: ?>
        <section class="layout-pt-lg layout-pb-md bravo-list-news">
            <div data-anim-wrap class="container">
                <div data-anim-child="slide-up delay-1" class="row <?php echo e($headerAlign); ?>">
                    <div class="col-auto">
                        <div class="sectionTitle -md">
                            <h2 class="sectionTitle__title"> <?php echo e($title); ?></h2>
                            <?php if(!empty($desc)): ?>
                                <p class=" sectionTitle__text mt-5 sm:mt-0"><?php echo e($desc); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row y-gap-30 pt-40">
                    <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div data-anim-child="<?php echo e($style != 'style_2' ? 'slide-left delay-1' : 'slide-up delay-'.($key+3)); ?>" class="<?php echo e($style != 'style_2' ? 'col-lg-4' : 'col-lg-3'); ?> col-sm-6">
                            <?php echo $__env->make('News::frontend.blocks.list-news.loop', ['style' => $style], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </section>

<?php endswitch; ?>

<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/News/Views/frontend/blocks/list-news/index.blade.php ENDPATH**/ ?>