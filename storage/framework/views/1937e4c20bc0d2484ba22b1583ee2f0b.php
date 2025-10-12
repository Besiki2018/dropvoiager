<?php if(is_default_lang()): ?>
    <div class="form-group mt-3">
        <label class="" ><?php echo e(__("Map Background in Button Show Map")); ?></label>
        <div class="form-controls form-group-image">
            <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('hotel_map_image',setting_item("hotel_map_image")); ?>

        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Hotel/Views/admin/setting-after-map.blade.php ENDPATH**/ ?>