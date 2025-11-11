<?php if(is_default_lang()): ?>
    <div class="form-group">
        <label><?php echo e(__("Enable Preload")); ?></label>
        <div class="form-controls">
            <label><input type="checkbox" <?php if(setting_item_with_lang('enable_preload',request()->query('lang')) ?? '' == 1): ?> checked <?php endif; ?> name="enable_preload" value="1"><?php echo e(__('Enable')); ?></label>
        </div>
    </div>
    <div class="form-group">
        <label><?php echo e(__("Logo Preload")); ?></label>
        <div class="form-controls form-group-image">
            <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('logo_preload_id',setting_item('logo_preload_id')); ?>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Core/Views/admin/settings/setting-after-homepage.blade.php ENDPATH**/ ?>