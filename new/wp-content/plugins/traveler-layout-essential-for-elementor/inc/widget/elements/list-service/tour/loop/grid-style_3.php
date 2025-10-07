<?php
global $post;
$info_price = STTour::get_info_price();
$location_name = STInput::request('location_name');
$location_id = STInput::request('location_id');
$main_color = st()->get_option( 'main_color', '#ec927e' );
if (isset($_REQUEST['start']) && strlen($_REQUEST['start']) > 0) {
    $_REQUEST['check_in'] = $_REQUEST['check_out'] = $_REQUEST['end'] = $_REQUEST['start'];
}
$url=st_get_link_with_search(get_permalink(), array('start','date','duration','people'), $_REQUEST);
?>

<div class="services-item item-elementor grid-2" itemscope itemtype="https://schema.org/Trip">
    <div class="item service-border st-border-radius">
        <div class="featured-image">
            <a href="<?php echo esc_url($url); ?>">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail(array(450, 300), array('alt' => TravelHelper::get_alt_image(), 'itemprop' => 'image', 'class' => 'img-responsive'));
            } else {
                echo '<img itemprop="image" src="'. get_template_directory_uri() . '/img/no-image.png' .'" alt="Default Thumbnail" class="img-responsive" />';
            }
            ?>
            </a>
            <?php
            $list_country = get_post_meta(get_the_ID(), 'multi_location', true);

            if (!empty($list_country)) {
                $list_country = preg_replace("/(\_)/", "", $list_country);
                $list_country = explode(",", $list_country);
                if (!empty($location_name && !empty($location_id))) {
                    if (in_array($location_id, $list_country)) {
                        $color_location = get_post_meta($location_id, 'color', true);
                        ?>
                        <span class="ml5 f14 address st-location--style4" style="background:<?php echo esc_attr($color_location) ?>"><?php echo esc_html(get_the_title($location_id)); ?></span>
                    <?php
                    } else {
                        $color_location = get_post_meta($list_country[0], 'color', true);
                        ?>
                        <span class="ml5 f14 address st-location--style4" style="background:<?php echo esc_attr($color_location) ?>"><?php echo esc_html(get_the_title($list_country[0])); ?></span>
                    <?php
                    }
                } else {
                    $color_location = get_post_meta($list_country[0], 'color', true);
                    ?>
                    <span class="ml5 f14 address st-location--style4" style="background:<?php echo esc_attr($color_location) ?>"><?php echo esc_html(get_the_title($list_country[0])); ?></span>
            <?php
                }
            }
            ?>

			<?php if (is_user_logged_in()) { ?>
                <?php $data = STUser_f::get_icon_wishlist(); ?>
                <div class="service-add-wishlist login <?php echo ($data['status']) ? 'added' : ''; ?>"
                     data-id="<?php echo get_the_ID(); ?>" data-type="<?php echo get_post_type(get_the_ID()); ?>"
                     title="<?php echo ($data['status']) ? __('Remove from wishlist', 'traveler') : __('Add to wishlist', 'traveler'); ?>">
                    <?php echo TravelHelper::getNewIconV2('wishlist');?>
                    <div class="lds-dual-ring"></div>
                </div>
            <?php } else { ?>
                <a href="#" class="login" data-bs-toggle="modal" data-bs-target="#st-login-form">
                    <div class="service-add-wishlist" title="<?php echo __('Add to wishlist', 'traveler'); ?>">
                        <?php echo TravelHelper::getNewIconV2('wishlist');?>
                        <div class="lds-dual-ring"></div>
                    </div>
                </a>
            <?php } ?>
        </div>
        <div class="content-item">

            <h3 class="title" itemprop="name">
                <a href="<?php echo esc_url($url); ?>" class="c-main"><?php echo get_the_title() ?></a>
            </h3>
            <?php
            $description_tour = get_post(get_the_ID());
            if (!empty($description_tour)) {
                ?>
                <div class="st-tour--description"><?php the_excerpt() ?></div>
            <?php
            }
            ?>
             <div class="fixed-bottoms">
                <div class="st-tour--feature st-tour--tablet">
                    <div class="st-tour__item">
                        <div class="item__icon">
                            <?php echo TravelHelper::getNewIcon('icon-calendar-tour-solo', $main_color, '24px', '24px'); ?>
                        </div>
                        <div class="item__info">
                            <h4 class="info__name"><?php echo esc_html__('Duration', 'traveler-layout-essential-for-elementor'); ?></h4>
                            <p class="info__value">
                                <?php
                                $duration = get_post_meta(get_the_ID(), 'duration_day', true);
                                echo esc_html($duration);
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="st-tour__item">
                        <div class="item__icon">
                            <?php echo TravelHelper::getNewIcon('icon-service-tour-solo', $main_color, '24px', '24px'); ?>
                        </div>
                        <div class="item__info">
                            <h4 class="info__name"><?php echo esc_html__('Group Size', 'traveler-layout-essential-for-elementor'); ?></h4>
                            <p class="info__value">
                                <?php
                                $max_people = get_post_meta(get_the_ID(), 'max_people', true);
                                if (empty($max_people) or $max_people == 0 or $max_people < 0) {
                                    echo esc_html__('Unlimited', 'traveler-layout-essential-for-elementor');
                                } else {
                                    if ($max_people == 1) {
                                        echo sprintf(esc_html__('%s person', 'traveler-layout-essential-for-elementor'), $max_people);
                                    } else { echo sprintf(esc_html__('%s people', 'traveler-layout-essential-for-elementor'), $max_people);
                                    }
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-footer">
                <div class="st-flex space-between st-price__wrapper">

                    <div class="right">

                        <span class=" price--tour">
                            <?php echo sprintf(esc_html__('%s', 'traveler-layout-essential-for-elementor'), STTour::get_price_html(get_the_ID())); ?>
                        </span>
                    </div>
                    <div class="st-btn--book">
                        <a href="<?php echo esc_attr(get_permalink(get_the_ID())); ?>"><?php echo esc_html__('BOOK NOW', 'traveler-layout-essential-for-elementor'); ?></a>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>