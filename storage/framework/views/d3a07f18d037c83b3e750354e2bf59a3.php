<div class="form-group">
    <label><?php echo e(__("List Contact")); ?></label>
    <div class="form-controls">
        <div class="form-group-item">
            <div class="form-group-item">
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-4"><?php echo e(__("Title")); ?></div>
                        <div class="col-md-7"><?php echo e(__('Info Contact')); ?></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php
                    $page_contact_lists = setting_item_with_lang('page_contact_lists',request()->query('lang'));
                    if(!empty($page_contact_lists)) $page_contact_lists = json_decode($page_contact_lists,true);
                    if(empty($page_contact_lists) or !is_array($page_contact_lists))
                        $page_contact_lists = [];
                    ?>
                    <?php $__currentLoopData = $page_contact_lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item" data-number="<?php echo e($key); ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for=""><?php echo e(__("Title")); ?></label>
                                    <input type="text" name="page_contact_lists[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($item['title'] ?? ""); ?>">
                                </div>
                                <div class="col-md-7">
                                    <label for=""><?php echo e(__("Content")); ?></label>
                                    <textarea name="page_contact_lists[<?php echo e($key); ?>][content]" class="form-control"><?php echo e($item['content'] ?? ""); ?></textarea>
                                </div>
                                <div class="col-md-1">
                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="text-right">
                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
                </div>
                <div class="g-more hide">
                    <div class="item" data-number="__number__">
                        <div class="row">
                            <div class="col-md-4">
                                <label for=""><?php echo e(__("Title")); ?></label>
                                <input type="text" __name__="page_contact_lists[__number__][title]" class="form-control" value="">
                            </div>
                            <div class="col-md-7">
                                <label for=""><?php echo e(__("Content")); ?></label>
                                <textarea __name__="page_contact_lists[__number__][content]" class="form-control"></textarea>
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
    <label class=""><?php echo e(__("Iframe google map")); ?></label>
    <div class="form-controls">
        <input type="text" class="form-control" name="page_contact_iframe_google_map" value="<?php echo e(setting_item_with_lang('page_contact_iframe_google_map',request()->query('lang'))); ?>">
    </div>
</div>

<div class="form-group mt-4">
    <h4 class="form-group-title"><?php echo e(__("Why Choose Us")); ?></h4>
    <div class="row">
       <div class="col-md-6">
           <label class=""><?php echo e(__("Block: Title")); ?></label>
           <div class="form-controls">
               <input type="text" class="form-control" name="page_contact_why_choose_us_title" value="<?php echo e(setting_item_with_lang('page_contact_why_choose_us_title',request()->query('lang'))); ?>">
           </div>
       </div>
       <div class="col-md-6">
           <label class=""><?php echo e(__("Block: Desc")); ?></label>
           <div class="form-controls">
               <input type="text" class="form-control" name="page_contact_why_choose_us_desc" value="<?php echo e(setting_item_with_lang('page_contact_why_choose_us_desc',request()->query('lang'))); ?>">
           </div>
       </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo e(__("List Items")); ?></label>
    <div class="form-controls">
        <div class="form-group-item">
            <div class="form-group-item">
                <div class="g-items-header">
                    <div class="row">
                        <div class="col-md-4"><?php echo e(__("Image")); ?></div>
                        <div class="col-md-7"><?php echo e(__("Title/Desc")); ?></div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="g-items">
                    <?php
                    $page_contact_why_choose_us = setting_item_with_lang('page_contact_why_choose_us',request()->query('lang'));
                    if(!empty($page_contact_why_choose_us)) $page_contact_why_choose_us = json_decode($page_contact_why_choose_us,true);
                    if(empty($page_contact_why_choose_us) or !is_array($page_contact_why_choose_us))
                        $page_contact_why_choose_us = [];
                    ?>
                    <?php $__currentLoopData = $page_contact_why_choose_us; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item" data-number="<?php echo e($key); ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('page_contact_why_choose_us['.$key.'][image_id]',$item['image_id']); ?>

                                </div>
                                <div class="col-md-7">
                                    <label for=""><?php echo e(__("Title")); ?></label>
                                    <input type="text" name="page_contact_why_choose_us[<?php echo e($key); ?>][title]" class="form-control" value="<?php echo e($item['title'] ?? ""); ?>">
                                    <label for=""><?php echo e(__("Desc")); ?></label>
                                    <input type="text" name="page_contact_why_choose_us[<?php echo e($key); ?>][desc]" class="form-control" value="<?php echo e($item['desc'] ?? ""); ?>">
                                </div>
                                <div class="col-md-1">
                                    <span class="btn btn-danger btn-sm btn-remove-item"><i class="fa fa-trash"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="text-right">
                    <span class="btn btn-info btn-sm btn-add-item"><i class="icon ion-ios-add-circle-outline"></i> <?php echo e(__('Add item')); ?></span>
                </div>
                <div class="g-more hide">
                    <div class="item" data-number="__number__">
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('page_contact_why_choose_us[__number__][image_id]','','__name__'); ?>

                            </div>
                            <div class="col-md-7">
                                <label for=""><?php echo e(__("Title")); ?></label>
                                <input type="text" __name__="page_contact_why_choose_us[__number__][title]" class="form-control" value="">
                                <label for=""><?php echo e(__("Desc")); ?></label>
                                <input type="text" __name__="page_contact_why_choose_us[__number__][desc]" class="form-control" value="">
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
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Core/Views/admin/settings/setting-after-contact.blade.php ENDPATH**/ ?>