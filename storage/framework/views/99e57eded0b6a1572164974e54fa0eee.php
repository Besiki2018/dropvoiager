<?php  $translation = $row->translate(request()->query('lang',get_main_lang())); ?>
<input type="hidden" name="gotrip_save_extra" value="1">
<div class="form-group-item">
    <label class="control-label"><?php echo e(__('General info')); ?></label>
    <div class="g-items-header">
        <div class="row">
            <div class="col-md-4"><?php echo e(__('Title')); ?></div>
            <div class="col-md-3"><?php echo e(__("Desc")); ?></div>
            <div class="col-md-3"><?php echo e(__('Content')); ?></div>
            <div class="col-md-1"></div>
        </div>
    </div>
    <div class="g-items">
        <?php if(!empty($translation->general_info)): ?>
            <?php if(!is_array($translation->general_info)) $translation->general_info = json_decode($translation->general_info,true); ?>
            <?php if(count($translation->general_info)): ?>
                <?php $__currentLoopData = $translation->general_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="item" data-number="<?php echo e($key); ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="general_info[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($item['title']); ?>" placeholder="<?php echo e(__("Title:")); ?>">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="general_info[<?php echo e($key); ?>][desc]" class="form-control" value="<?php echo e($item['desc']); ?>" placeholder="<?php echo e(__("Desc:")); ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="general_info[<?php echo e($key); ?>][content]" class="form-control" placeholder="<?php echo e(__("Content:")); ?>" value="<?php echo e($item['content']); ?>">
                            </div>
                            <div class="col-md-1">
                                <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="text-right">
        <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
    </div>
    <div class="g-more hide">
        <div class="item" data-number="__number__">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" __name__="general_info[__number__][title]" class="form-control" placeholder="<?php echo e(__("Title:")); ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" __name__="general_info[__number__][desc]" class="form-control" placeholder="<?php echo e(__("Desc:")); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" __name__="general_info[__number__][content]" class="form-control" placeholder="<?php echo e(__("Content:")); ?>">
                </div>
                <div class="col-md-1">
                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u618310530/domains/dropvoyage.com/public_html/themes/GoTrip/Location/Views/admin/location_extra_info.blade.php ENDPATH**/ ?>