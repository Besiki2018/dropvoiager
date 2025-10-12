<?php
    $user = Auth::user();
    $languages = \Modules\Language\Models\Language::getActive();
?>

<?php if(!empty($languages) && setting_item('site_enable_multi_lang') && setting_item('site_locale')): ?>
    <div class="btn-group" role="group" aria-label="Basic example">
        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a
                class="btn  <?php if(request()->lang == $language->locale or (!request()->lang && $language->locale == setting_item('site_locale'))): ?> btn-primary <?php else: ?> btn-default <?php endif; ?>"
                href="<?php echo e(add_query_arg(['lang'=>$language->locale])); ?>"
            >
                <?php if($language->flag): ?>
                    <span class="flag-icon flag-icon-<?php echo e($language->flag); ?>"></span>
                <?php endif; ?>
                <?php echo e($language->name); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if(request()->query('lang')): ?>
        <input type="hidden" name="lang" value="<?php echo e(request()->query('lang')); ?>">
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/modules/Template/Views/admin/live/parts/lang_nav.blade.php ENDPATH**/ ?>