<div class="form-group">
    <label><?php echo e(__("Footer Style")); ?></label>
    <select name="footer_style" class="form-control">
        <option value="normal" <?php if(setting_item('footer_style') == 'normal'): ?> selected <?php endif; ?>><?php echo e(__('Normal')); ?></option>
        <option value="style_1" <?php if(setting_item('footer_style') == 'style_1'): ?> selected <?php endif; ?>><?php echo e(__('Style 1')); ?></option>
        <option value="style_2" <?php if(setting_item('footer_style') == 'style_2'): ?> selected <?php endif; ?>><?php echo e(__('Style 2')); ?></option>
        <option value="style_3" <?php if(setting_item('footer_style') == 'style_3'): ?> selected <?php endif; ?>><?php echo e(__('Style 3')); ?></option>
        <option value="style_4" <?php if(setting_item('footer_style') == 'style_4'): ?> selected <?php endif; ?>><?php echo e(__('Style 4')); ?></option>
        <option value="style_5" <?php if(setting_item('footer_style') == 'style_5'): ?> selected <?php endif; ?>><?php echo e(__('Style 5')); ?></option>
        <option value="style_6" <?php if(setting_item('footer_style') == 'style_6'): ?> selected <?php endif; ?>><?php echo e(__('Style 6')); ?></option>
        <option value="style_7" <?php if(setting_item('footer_style') == 'style_7'): ?> selected <?php endif; ?>><?php echo e(__('Style 7')); ?></option>
        <option value="style_8" <?php if(setting_item('footer_style') == 'style_8'): ?> selected <?php endif; ?>><?php echo e(__('Style 8')); ?></option>
    </select>
</div>

<div class="form-group">
    <label><?php echo e(__("Footer content left")); ?></label>
    <div class="form-controls">
        <div class="form-group-item">
            <div class="form-group-item">
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-5"><?php echo e(__("Title")); ?></div>
                        <div class="col-md-6"><?php echo e(__('Content')); ?></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php $contents = setting_item('footer_content_left');?>
                    <?php if(!empty($contents)): ?>
                        <?php $contents = json_decode($contents); ?>
                        <?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item" data-number="<?php echo e($k); ?>">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" name="footer_content_left[<?php echo e($k); ?>][title]" class="form-control" value="<?php echo e($content->title); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <textarea name="footer_content_left[<?php echo e($k); ?>][content]" class="form-control" rows="5"><?php echo clean($content->content); ?></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
                </div>
                <div class="g-more hide">
                    <div class="item" data-number="__number__">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" __name__="footer_content_left[__number__][title]" class="form-control" value="">
                            </div>
                            <div class="col-md-6">
                                <textarea __name__="footer_content_left[__number__][content]" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <label><?php echo e(__("Footer content right")); ?></label>
    <div class="form-controls">
        <div class="form-group-item">
            <div class="form-group-item">
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-5"><?php echo e(__("Title")); ?></div>
                        <div class="col-md-6"><?php echo e(__('Content')); ?></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php $contents = setting_item('footer_content_right');?>
                    <?php if(!empty($contents)): ?>
                        <?php $contents = json_decode($contents); ?>
                        <?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="item" data-number="<?php echo e($k); ?>">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" name="footer_content_right[<?php echo e($k); ?>][title]" class="form-control" value="<?php echo e($content->title); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <textarea name="footer_content_right[<?php echo e($k); ?>][content]" class="form-control" rows="5"><?php echo clean($content->content); ?></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
                </div>
                <div class="g-more hide">
                    <div class="item" data-number="__number__">
                        <div class="row">
                            <div class="col-md-5">
                                <input type="text" __name__="footer_content_right[__number__][title]" class="form-control" value="">
                            </div>
                            <div class="col-md-6">
                                <textarea __name__="footer_content_right[__number__][content]" class="form-control" rows="5"></textarea>
                            </div>
                            <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Core/Views/admin/settings/setting-after-footer.blade.php ENDPATH**/ ?>