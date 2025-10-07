<?php
$hotel = new STHotel();
$query = $hotel->get_relate_rooms($hotel_id, $room_id);
if($query->have_posts()) {
    wp_enqueue_script('owlcarousel');
    wp_enqueue_style('owlcarousel');
    $responsive = [
        '992' => [
            'items' => 3,
        ],
        '768' => [
            'items' => 2,
        ],
        '0' => [
            'items' => 1,
        ]
    ];
?>
<div class="relate-rooms">
    <div class="st-hr"></div>
    <h2 class="st-heading-section"><?php echo esc_html__('Explore other options', 'traveler'); ?></h2>
    <div class="inner">
        <?php
        echo '<div class="owl-carousel st-owl-slider" data-items="3" data-margin="24" data-responsive="'. esc_attr(json_encode($responsive)) .'">';
            while ($query->have_posts()) {
                $query->the_post();

				$price_by_per_person = get_post_meta( get_the_ID(), 'price_by_per_person', true );
				$room_num_search     = (int) STInput::get( 'room_num_search', 1 );
				if ( $room_num_search <= 0 ) {
					$room_num_search = 1;
				}

				$adult_number = STInput::request( 'adult_number', 1 );
				$child_number = STInput::request( 'child_number', '' );

				$current_calendar = TravelHelper::get_current_available_calendar( get_the_ID() );
				$start            = STInput::get( 'start', date( TravelHelper::getDateFormat(), strtotime( $current_calendar ) ) );
				$end              = STInput::get( 'end', date( TravelHelper::getDateFormat(), strtotime( '+ 1 day', strtotime( $current_calendar ) ) ) );
				$start            = TravelHelper::convertDateFormat( $start );
				$end              = TravelHelper::convertDateFormat( $end );
				$sale_price       = STPrice::getRoomPrice( get_the_ID(), strtotime( $start ), strtotime( $end ), $room_num_search, $adult_number, $child_number );

				$numberday    = STDate::dateDiff( $start, $end );
				$total_person = intval( $adult_number ) + intval( $child_number );

                echo '<div class="item-slide">';
                    echo stt_elementorv2()->loadView('services/room/loop/grid', [
						'price_by_per_person' => $price_by_per_person,
						'sale_price'          => $sale_price,
						'numberday'           => $numberday,
						'total_person'        => $total_person,
					]);
                echo '</div>';
            }
            wp_reset_postdata();
        echo '</div>';
        ?>
    </div>
</div>
<?php
}