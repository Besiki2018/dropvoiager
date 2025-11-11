<?php if(is_default_lang()): ?>
    <div class="form-group">
        <label><?php echo e(__("Logo Dark")); ?></label>
        <div class="form-controls form-group-image">
            <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('logo_id_dark',setting_item('logo_id_dark')); ?>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Core/Views/admin/settings/setting-after-logo.blade.php ENDPATH**/ ?>