<?php $selected = (array) Request::query('attrs',[]); ?>
<?php $__currentLoopData = $attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(empty($item['hide_in_filter_search'])): ?>
        <?php $translate = $item->translate();
                $term_label = $translate->name;

        $selected_attr = $selected[$item->id] ?? [];
        $selected_term = !empty($selected_attr[0]) ? $item->terms->where('id',$selected_attr[0])->first() : null;
        ?>
        <div class="col-auto terms-item">
            <div class="dropdown js-dropdown js-<?php echo e($item->slug); ?>-active">
                <div class="dropdown__button d-flex items-center text-14 rounded-100 border-light px-15 h-34" data-el-toggle=".js-<?php echo e($item->slug); ?>-toggle" data-el-toggle-active=".js-<?php echo e($item->slug); ?>-active">
                    <span class="js-dropdown-title"><?php echo e($selected_term ? $selected_term->name : $translate->name); ?></span>
                    <i class="icon icon-chevron-sm-down text-7 ml-10"></i>
                </div>
                <div class="toggle-element -dropdown js-click-dropdown js-<?php echo e($item->slug); ?>-toggle">
                    <div class="text-15 y-gap-15 js-dropdown-list">
                        <div class="border-bottom border-bottom-light"><a href="#" data-term="" class="d-block js-dropdown-link term-item "><?php echo e($term_label); ?></a></div>
                        <?php $__currentLoopData = $item->terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $translate = $term->translate(); ?>
                            <div><a href="#" data-term="<?php echo e($term->slug); ?>" class="d-block js-dropdown-link term-item"><?php echo e($translate->name); ?></a></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" class="terms" name="attrs[<?php echo e($item->slug); ?>][]" value="">
        </div>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Layout/global/search/filters-map/attrs.blade.php ENDPATH**/ ?>