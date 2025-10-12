

<?php $__env->startSection('content'); ?>
    <form
        class="overflow-y-auto d-flex"
        action="<?php echo e(route('tour.admin.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>"
        method="post"
    >
        <?php echo csrf_field(); ?>
        <div class="container-fluid overflow-y-auto d-flex flex-column">
            <div class="d-flex justify-content-between mb20">
                <div class="">
                    <h1 class="title-bar"><?php echo e($row->id ? __('Edit: ').$row->title : __('Add new tour')); ?></h1>
                    <?php if($row->slug): ?>
                        <p class="item-url-demo"><?php echo e(__("Permalink")); ?>: <?php echo e(url(config('tour.tour_route_prefix') )); ?>/<a href="#" class="open-edit-input" data-name="slug"><?php echo e($row->slug); ?></a>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="">
                    <?php if($row->slug): ?>
                        <a class="btn btn-primary btn-sm" href="<?php echo e($row->getDetailUrl(request()->query('lang'))); ?>" target="_blank"><?php echo e(__("View Tour")); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php echo $__env->make('admin.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php if($row->id): ?>
                <?php echo $__env->make('Language::admin.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
            <div class="lang-content-box overflow-y-auto h-100 pr-0 pb-0">
                <div class="d-flex h-100">
                    <div class="col-md-2 p-0">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <strong>
                                    <i class="fa fa-cogs"></i>
                                    <?php echo e(__("Tour Information")); ?></strong>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="#tab_general" class="list-group-item list-group-item-action active" data-toggle="tab"><?php echo e(__('General')); ?></a>
                                <a
                                    href="#tab_location" class="list-group-item list-group-item-action" data-toggle="tab"
                                >
                                    <?php echo e(__('Location')); ?>

                                </a>
                                <a
                                    href="#tab_pricing"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php if(!$row->id): ?> disabled <?php endif; ?>"
                                    data-toggle="tab"
                                >
                                    <?php echo e(__('Pricing')); ?>

                                    <?php if(!$row->id): ?>
                                        <i class="fa fa-lock"></i>
                                    <?php endif; ?>
                                </a>
                                <a
                                    href="#tab_availability"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php if(!$row->id): ?> disabled <?php endif; ?>"
                                    data-toggle="tab"
                                >
                                    <?php echo e(__('Availability')); ?>

                                    <?php if(!$row->id): ?>
                                        <i class="fa fa-lock"></i>
                                    <?php endif; ?>
                                </a>
                                <a href="#tab_status" class="list-group-item list-group-item-action " data-toggle="tab"><?php echo e(__('Status')); ?></a>
                                <a href="#tab_seo" class="list-group-item list-group-item-action " data-toggle="tab"><?php echo e(__('SEO')); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10 overflow-y-auto h-100">
                        <div class="d-flex flex-column overflow-y-auto h-100">
                            <div
                                class="tab-content flex-grow-1 overflow-y-auto mb-3 p-1"
                                data-action="<?php echo e(route('tour.admin.store',['id'=>($row->id) ? $row->id : '-1','lang'=>request()->query('lang')])); ?>"
                            >
                                <div class="tab-pane active" id="tab_general">
                                    <?php echo $__env->make('Tour::admin/tour/tour-content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php echo $__env->make('Hotel::admin.hotel.surrounding', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="tab-pane" id="tab_location">
                                    <?php echo $__env->make('Tour::admin/tour/tour-location', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <?php if($row->id): ?>
                                    <div class="tab-pane" id="tab_pricing">
                                        <?php echo $__env->make('Tour::admin/tour/pricing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                    <div class="tab-pane" id="tab_availability">
                                        <?php echo $__env->make('Tour::admin/tour/availability', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </div>
                                <?php endif; ?>
                                <div class="tab-pane" id="tab_seo">
                                    <?php echo $__env->make('Core::admin/seo-meta/seo-meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="tab-pane" id="tab_status">
                                    <div class="panel">
                                        <div class="panel-title"><strong><?php echo e(__('Publish')); ?></strong></div>
                                        <div class="panel-body">
                                            <?php if(is_default_lang()): ?>
                                                <div>
                                                    <label>
                                                        <input
                                                            <?php if($row->status=='publish'): ?> checked <?php endif; ?> type="radio" name="status" value="publish"
                                                        > <?php echo e(__("Publish")); ?>

                                                    </label>
                                                </div>
                                                <div>
                                                    <label>
                                                        <input
                                                            <?php if($row->status=='draft'): ?> checked <?php endif; ?> type="radio" name="status" value="draft"
                                                        > <?php echo e(__("Draft")); ?>

                                                    </label>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if(is_default_lang()): ?>
                                        <div class="panel">
                                            <div class="panel-title"><strong><?php echo e(__("Author Setting")); ?></strong></div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                        <?php
                                                        $user = $row->author;
                                                        \App\Helpers\AdminForm::select2('author_id', [
                                                            'configs' => [
                                                                'ajax'        => [
                                                                    'url'      => route('user.admin.getForSelect2'),
                                                                    'dataType' => 'json'
                                                                ],
                                                                'allowClear'  => true,
                                                                'placeholder' => __('-- Select User --')
                                                            ]
                                                        ], !empty($user->id) ? [
                                                            $user->id,
                                                            $user->getDisplayName() . ' (#' . $user->id . ')'
                                                        ] : false)
                                                        ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(is_default_lang()): ?>
                                        <div class="panel">
                                            <div class="panel-title"><strong><?php echo e(__("Tour Featured")); ?></strong></div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <input
                                                        type="checkbox" name="is_featured" <?php if($row->is_featured): ?> checked <?php endif; ?> value="1"
                                                    > <?php echo e(__("Enable featured")); ?>

                                                </div>
                                                <div class="form-group">
                                                    <label><?php echo e(__('Default State')); ?></label>
                                                    <br>
                                                    <select name="default_state" class="custom-select">
                                                        <option
                                                            value="1"
                                                            <?php if(old('default_state',$row->default_state ?? -1) == 1): ?> selected <?php endif; ?>><?php echo e(__("Always available")); ?></option>
                                                        <option
                                                            value="0"
                                                            <?php if(old('default_state',$row->default_state ?? -1) == 0): ?> selected <?php endif; ?>><?php echo e(__("Only available on specific dates")); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <?php echo $__env->make('Tour::admin/tour/attributes', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        <div class="panel">
                                            <div class="panel-title"><strong><?php echo e(__('Feature Image')); ?></strong></div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <?php echo \Modules\Media\Helpers\FileHelper::fieldUpload('image_id',$row->image_id); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <?php echo $__env->make('Tour::admin/tour/ical', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0 d-flex justify-content-between pb-3">
                                <div></div>
                                <div>
                                    <div class="text-right">
                                        <button class="btn btn-success" type="submit">
                                            <i class="fa fa-save"></i> <?php echo e(__('Save changes')); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('js'); ?>
    <?php echo App\Helpers\MapEngine::scripts(); ?>

    <script>
        jQuery(function ($) {
            new BravoMapEngine('map_content', {
                disableScripts: true,
                fitBounds: true,
                center: [<?php echo e($row->map_lat ?? setting_item('map_lat_default',51.505 )); ?>, <?php echo e($row->map_lng ?? setting_item('map_lng_default',-0.09 )); ?>],
                zoom:<?php echo e($row->map_zoom ?? "8"); ?>,
                ready: function (engineMap) {
                    <?php if($row->map_lat && $row->map_lng): ?>
                    engineMap.addMarker([<?php echo e($row->map_lat); ?>, <?php echo e($row->map_lng); ?>], {
                        icon_options: {}
                    });
                    <?php endif; ?>
                    engineMap.on('click', function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                    engineMap.on('zoom_changed', function (zoom) {
                        $("input[name=map_zoom]").attr("value", zoom);
                    });
                    if(bookingCore.map_provider === "gmap"){
                        engineMap.searchBox($('#customPlaceAddress'),function (dataLatLng) {
                            engineMap.clearMarkers();
                            engineMap.addMarker(dataLatLng, {
                                icon_options: {}
                            });
                            $("input[name=map_lat]").attr("value", dataLatLng[0]);
                            $("input[name=map_lng]").attr("value", dataLatLng[1]);
                        });
                    }
                    engineMap.searchBox($('.bravo_searchbox'),function (dataLatLng) {
                        engineMap.clearMarkers();
                        engineMap.addMarker(dataLatLng, {
                            icon_options: {}
                        });
                        $("input[name=map_lat]").attr("value", dataLatLng[0]);
                        $("input[name=map_lng]").attr("value", dataLatLng[1]);
                    });
                }
            });
        })
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app',['body_class'=>'resource-edit-page'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/modules/Tour/Views/admin/detail.blade.php ENDPATH**/ ?>