<?php
$tour_program_style = get_post_meta( get_the_ID(), 'tours_program_style', true );
if ( empty( $tour_program_style ) ) {
	$tour_program_style = 'style1';
}

switch ( $tour_program_style ) {
	case 'style1':
		$tour_programs = get_post_meta( get_the_ID(), 'tours_program', true );
		break;
	case 'style2':
		$tour_programs = get_post_meta( get_the_ID(), 'tours_program_bgr', true );
		break;
	case 'style3':
		$tour_programs = get_post_meta( get_the_ID(), 'tours_program', true );
		break;
	case 'style4':
		$tour_programs = get_post_meta( get_the_ID(), 'tours_program_style4', true );
		break;
	default:
		$tour_programs = get_post_meta( get_the_ID(), 'tours_program', true );
		break;
}


if ( ! empty( $tour_programs ) ) {
	if ( $tour_program_style == 'style1' ) :
	?>
	<div class="st-hr"></div>
	<h2 class="st-heading-section" id="st-itinerary">
		<?php echo __( 'Itinerary', 'traveler' ); ?>
	</h2>
	<div class="st-program-list">
		<?php
		$i = 0;
		foreach ( $tour_programs as $k => $v ) {
			$section_id = trim( st_convert_characers_to_slug( esc_html( $v['title'] ) ) );
			?>
			<div class="accordion faq st-program style1" id="accordion_<?php echo esc_attr( $section_id ); ?>">
				<div class="accordion-item">
					<h2 class="accordion-header" id="heading_<?php echo esc_attr( $section_id ); ?>">
						<button class="accordion-button<?php echo ( $i == 0 ) ? '' : ' collapsed' ?> "
							type="button"
							data-bs-toggle="collapse"
							data-bs-target="#st-<?php echo esc_attr( $section_id ) ?>"
							aria-expanded="true"
							aria-controls="<?php echo esc_attr( $section_id ) ?>"
						>
						<?php echo balanceTags( $v['title'] ); ?>
						</button>
					</h2>
					<div id="st-<?php echo esc_attr( $section_id ) ?>" class="accordion-collapse collapse <?php echo ( $i == 0 ) ? 'show' : '' ?>"
						aria-labelledby="heading_<?php echo esc_attr( $section_id ); ?>"
						data-bs-parent="#accordion_<?php echo esc_attr( $section_id ); ?>"
					>
						<div class="accordion-body">
							<?php
							if ( isset( $v['image'] ) && ! empty( $v['image'] ) ) {
								$img = $v['image'];
								?>
									<div class="row">
										<div class="col-lg-4">
											<img src="<?php echo esc_url( $v['image'] ) ?>" alt="<?php echo esc_attr( $v['title'] ) ?>" class="img-fluid"/>
										</div>
										<div class="col-lg-8">
											<p class="content-itinerary">
												<?php echo do_shortcode( wpautop( $v['desc'] ) ); ?>
											</p>
										</div>
									</div>
								<?php
							} else {
								echo '<p class="content-itinerary">';
								echo do_shortcode( wpautop( $v['desc'] ) );
								echo '</p>';
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<?php
			++$i;
		}
		?>
	</div>

	<?php else : ?>
		<div class="st-hr"></div>
		<div class="st-program--wrapper st-program-parent">
			<?php echo stt_elementorv2()->loadView('services/tour/single/item/style-2/itenirary', ['post_id' => get_the_ID()]) ?>
		</div>
	<?php endif; ?>

	<?php
}
?>
