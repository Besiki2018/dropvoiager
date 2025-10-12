<input type="hidden" name="save_footer_style" value="1">
<div class="panel">
    <div class="panel-title"><strong><?php echo e(__('Header Style')); ?></strong></div>
    <div class="panel-body">
        <select name="header_style" class="form-control" >
            <option value="normal" <?php echo e(( $row->header_style ?? '') == 'normal' ? 'selected' : ''); ?>><?php echo e(__("Normal")); ?></option>
            <option value="normal_white" <?php echo e(( $row->header_style ?? '') == 'normal_white' ? 'selected' : ''); ?>><?php echo e(__('Normal White')); ?></option>
            <option value="transparent" <?php echo e(( $row->header_style ?? '') == 'transparent' ? 'selected' : ''); ?>><?php echo e(__('Transparent')); ?></option>
            <option value="transparent_v2" <?php echo e(( $row->header_style ?? '') == 'transparent_v2' ? 'selected' : ''); ?>><?php echo e(__('Transparent V2')); ?></option>
            <option value="transparent_v3" <?php echo e(( $row->header_style ?? '') == 'transparent_v3' ? 'selected' : ''); ?>><?php echo e(__('Transparent V3')); ?></option>
            <option value="transparent_v4" <?php echo e(( $row->header_style ?? '') == 'transparent_v4' ? 'selected' : ''); ?>><?php echo e(__('Transparent V4')); ?></option>
            <option value="transparent_v5" <?php echo e(( $row->header_style ?? '') == 'transparent_v5' ? 'selected' : ''); ?>><?php echo e(__('Transparent V5')); ?></option>
            <option value="transparent_v6" <?php echo e(( $row->header_style ?? '') == 'transparent_v6' ? 'selected' : ''); ?>><?php echo e(__('Transparent V6')); ?></option>
            <option value="transparent_v7" <?php echo e(( $row->header_style ?? '') == 'transparent_v7' ? 'selected' : ''); ?>><?php echo e(__('Transparent V7')); ?></option>
            <option value="transparent_v8" <?php echo e(( $row->header_style ?? '') == 'transparent_v8' ? 'selected' : ''); ?>><?php echo e(__('Transparent V8')); ?></option>
            <option value="transparent_v9" <?php echo e(( $row->header_style ?? '') == 'transparent_v9' ? 'selected' : ''); ?>><?php echo e(__('Transparent V9')); ?></option>
        </select>
    </div>
</div>
<div class="panel">
    <div class="panel-title"><strong><?php echo e(__('Footer Style')); ?></strong></div>
    <div class="panel-body">
        <select name="footer_style" class="form-control">
            <option value="normal"><?php echo e(__('Normal')); ?></option>
            <option value="style_1" <?php if($row->footer_style == 'style_1'): ?> selected <?php endif; ?>><?php echo e(__('Style 1')); ?></option>
            <option value="style_2" <?php if($row->footer_style == 'style_2'): ?> selected <?php endif; ?>><?php echo e(__('Style 2')); ?></option>
            <option value="style_3" <?php if($row->footer_style == 'style_3'): ?> selected <?php endif; ?>><?php echo e(__('Style 3')); ?></option>
            <option value="style_4" <?php if($row->footer_style == 'style_4'): ?> selected <?php endif; ?>><?php echo e(__('Style 4')); ?></option>
            <option value="style_5" <?php if($row->footer_style == 'style_5'): ?> selected <?php endif; ?>><?php echo e(__('Style 5')); ?></option>
            <option value="style_6" <?php if($row->footer_style == 'style_6'): ?> selected <?php endif; ?>><?php echo e(__('Style 6')); ?></option>
            <option value="style_7" <?php if($row->footer_style == 'style_7'): ?> selected <?php endif; ?>><?php echo e(__('Style 7')); ?></option>
            <option value="style_8" <?php if($row->footer_style == 'style_8'): ?> selected <?php endif; ?>><?php echo e(__('Style 8')); ?></option>
        </select>

        <label class="form-label mt-3">
            <input type="hidden" name="disable_subscribe_default" value="0">
            <input type="checkbox" name="disable_subscribe_default" <?php if(!empty($row->disable_subscribe_default)): ?> checked <?php endif; ?> value="1">
            <?php echo e(__('Disable subscribe default')); ?>

        </label>
    </div>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Page/Views/admin/advanced.blade.php ENDPATH**/ ?>