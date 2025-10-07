<?php
$post_id = get_the_ID();
$post_translated = TravelHelper::post_translated($post_id);
$thumbnail_id = get_post_thumbnail_id($post_translated);
$price = get_post_meta(get_the_ID(), 'price', true);
$class_image = 'image-feature';
$url = get_the_permalink();

$get_data                    = [];
$get_data['start']           = STInput::request( 'check_in' );
$get_data['end']             = STInput::request( 'check_out' );
$get_data['date']            = STInput::request( 'date' );
$get_data['room_num_search'] = STInput::request( 'room_num_search' );
$get_data['adult_number']    = STInput::request( 'adult_number' );
$get_data['child_number']    = STInput::request( 'child_number' );

$link_with_params = add_query_arg( $get_data, get_the_permalink() );

$adult_number = STInput::request( 'adult_number', 1 );
$child_number = STInput::request( 'child_number', '' );
$current_calendar        = TravelHelper::get_current_available_calendar( get_the_ID() );
$current_calendar_reverb = date( 'm/d/Y', strtotime( $current_calendar ) );
$room_num_search = (int) STInput::get( 'room_num_search', 1 );
if ( $room_num_search <= 0 ) {
	$room_num_search = 1;
}
$start           = STInput::get( 'start', date( TravelHelper::getDateFormat(), strtotime( $current_calendar ) ) );
$end             = STInput::get( 'end', date( TravelHelper::getDateFormat(), strtotime( '+ 1 day', strtotime( $current_calendar ) ) ) );
$start               = TravelHelper::convertDateFormat( $start );
$end                 = TravelHelper::convertDateFormat( $end );
$price_by_per_person = get_post_meta( $post_translated, 'price_by_per_person', true );
$sale_price          = STPrice::getRoomPrice( $post_translated, strtotime( $start ), strtotime( $end ), $room_num_search, $adult_number, $child_number );
$numberday    = STDate::dateDiff( $start, $end );
$total_person = intval( $adult_number ) + intval( $child_number );

?>
<div class="services-item grid item-elementor" itemscope itemtype="https://schema.org/Room" data-id="<?php echo esc_attr($post_id); ?>">
    <div class="item service-border st-border-radius">
        <div class="featured-image">

            <a href="<?php echo esc_url($link_with_params); ?>">
                <img itemprop="photo" src="<?php echo wp_get_attachment_image_url($thumbnail_id, array(450, 300)); ?>" alt="<?php echo get_the_title(); ?>" class="<?php echo esc_attr($class_image); ?> st-hover-grow" />
            </a>
            <div class="price-wrapper d-flex align-items-center" itemprop="priceRange">
                <!-- <span class="price"><?php echo TravelHelper::format_money($price) ?> </span>
                <span class="unit"><?php echo esc_html__('/ night', 'traveler-layout-essential-for-elementor') ?></span> -->

				<?php
				if ($price_by_per_person == 'on') :
					echo sprintf('<span class="price">%s</span>', TravelHelper::format_money($sale_price));
					echo '<span class="unit">';
					echo sprintf(_n('/person', '/%d persons', $total_person, 'traveler-layout-essential-for-elementor'), $total_person);
					echo sprintf(_n('/night', '/%d nights', $numberday, 'traveler-layout-essential-for-elementor'), $numberday);
					echo '</span>';
				else:
					echo sprintf('<span class="price">%s</span>', TravelHelper::format_money($sale_price));
					echo '<span class="unit">';
					echo sprintf(_n('/night', '/ %d nights', $numberday, 'traveler-layout-essential-for-elementor'), $numberday);
					echo '</span>';
				endif; ?>

            </div>
        </div>
        <div class="content-item">
            <div class="content-inner has-matchHeight">

                <h3 class="title" itemprop="name">
                    <a href="<?php echo esc_url($link_with_params); ?>" class="st-link c-main"><?php echo get_the_title($post_translated) ?></a>
                </h3>

                <div class="descrition">
                    <?php the_excerpt() ?>
                </div>
            </div>
            <div class="section-footer">
                <div class="room-featured-items">
                    <div class="item" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-toggle="tooltip" data-placement="top" title="<?php echo __('No. Beds', 'traveler-layout-essential-for-elementor') ?>">
                        <span class="stt-icon stt-icon-bed"></span>
                        <?php echo get_post_meta(get_the_ID(), 'bed_number', true) ?>
                    </div>
                    <div class="item" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-toggle="tooltip" data-placement="top" title="<?php echo __('No. Adults', 'traveler-layout-essential-for-elementor') ?>">
                        <span class="stt-icon stt-icon-adult"></span>
                        <?php echo get_post_meta(get_the_ID(), 'adult_number', true) ?>
                    </div>
                    <div class="item" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-toggle="tooltip" data-placement="top" title="<?php echo __('No. Children', 'traveler-layout-essential-for-elementor') ?>">
                        <span class="stt-icon stt-icon-baby"></span>
                        <?php echo  get_post_meta(get_the_ID(), 'children_number', true) ?>
                    </div>
                    <div class="item" data-bs-html="true" data-bs-toggle="tooltip" data-bs-placement="top" data-toggle="tooltip" data-placement="top" title="<?php echo __('Room Footage', 'traveler-layout-essential-for-elementor') ?>">
                        <span class="stt-icon stt-icon-area"></span>
                        <?php echo get_post_meta(get_the_ID(), 'room_footage', true) ?><?php echo __('m<sup>2</sup>', 'traveler-layout-essential-for-elementor') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>