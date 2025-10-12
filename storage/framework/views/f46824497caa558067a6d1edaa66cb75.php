
<?php $__env->startPush('css'); ?>
    <style type="text/css">
        .bravo_topbar, .footer {
            display: none
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <?php $disable_subscribe_default = true ?>
    <section class="halfMap bravo_search_tour">
        <h1 class="d-none">
            <?php echo e(setting_item_with_lang("tour_page_search_title")); ?>

        </h1>
        <div class="halfMap__content">
            <div class="bravo_form_search_map form-search-all-service">
                <div class="mainSearch bg-white pr-10 py-10 lg:px-20 lg:pt-5 lg:pb-20 bg-light-2 rounded-4">
                    <?php echo $__env->make('Tour::frontend.layouts.search.form-search', ['style' => 'map'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <div class="bravo_filter_search_map" id="advance_filters">
                <?php echo $__env->make('Tour::frontend.layouts.search-map.filter-search-map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="row y-gap-10 justify-between items-center pt-20">
                <div class="col-auto">
                    <div class="text-18 fw-500 result-count">
                        <?php if($rows->total() > 1): ?>
                            <?php echo e(__(":count tours found",['count'=>$rows->total()])); ?>

                        <?php else: ?>
                            <?php echo e(__(":count tour found",['count'=>$rows->total()])); ?>

                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-auto bc-form-order">
                    <?php echo $__env->make('Layout::global.search.orderby',['routeName'=>'tour.search','hidden_map_button'=>1], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <div class="ajax-search-result">
                <?php echo $__env->make('Tour::frontend.ajax.search-result-map', ["layout" => "normal"], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="halfMap__map position-relative">
            <div class="results_map">
                <div class="map-loading d-none">
                    <div class="st-loader"></div>
                </div>
                <div id="bravo_results_map" class="results_map_inner"></div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <?php echo App\Helpers\MapEngine::scripts(); ?>

    <script>
        var bravo_map_data = {
            markers:<?php echo json_encode($markers); ?>,
            map_lat_default:<?php echo e(setting_item('tour_map_lat_default','0')); ?>,
            map_lng_default:<?php echo e(setting_item('tour_map_lng_default','0')); ?>,
            map_zoom_default:<?php echo e(setting_item('tour_map_zoom_default','6')); ?>,
        };
    </script>
    <script type="text/javascript" src="<?php echo e(asset("libs/ion_rangeslider/js/ion.rangeSlider.min.js")); ?>"></script>
    <script type="text/javascript" src="<?php echo e(asset('themes/gotrip/dist/frontend/js/filter-map.js?_ver='.config('app.asset_version'))); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app',['container_class'=>'container-fluid','header_right_menu'=>true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/besikiekseulidze/web-development/dropvoyage/themes/GoTrip/Tour/Views/frontend/search-map.blade.php ENDPATH**/ ?>