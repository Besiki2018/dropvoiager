<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 13-11-2018
 * Time: 3:00 PM
 * Since: 1.0.0
 * Updated: 1.0.0
 */

if ( ! isset( $position ) ) {
	$position = '';
}
if ( ! isset( $has_price_filter ) ) {
	$has_price_filter = '';
}
?>
<div class="form-button">
	<div class="advance">
		<div class="form-group form-extra-field dropdown clearfix field-advance">
			<div class="dropdown" data-toggle="dropdown" id="dropdown-advance">
				<?php if ( $position != 'sidebar' ) { ?>
				<label class="hidden-xs"><?php echo __( 'Advance', 'traveler' ); ?></label>
				<div class="render">
					<span class="hidden-xs"><?php echo __( 'More', 'traveler' ); ?> <i class="fa fa-caret-down"></i></span>
					<span class="hidden-lg hidden-md hidden-sm"><?php echo __( 'More options', 'traveler' ); ?> <i
								class="fa fa-caret-down"></i></span>
				</div>
				<?php } else { ?>
					<div class="render">
						<span><?php echo __( 'More options', 'traveler' ); ?> <i class="fa fa-caret-down"></i></span>
					</div>
				<?php } ?>
			</div>
			<div class="dropdown-menu" aria-labelledby="dropdown-advance">
				<div class="row">
					<div class="col-lg-12">
						<div class="advance-item facilities st-icheck">
							<?php
							$tax                = STInput::get( 'taxonomy' );
							$in_facilities      = [];
							$temp_facilities    = '';
							$search_tax_advance = st()->get_option( 'attribute_search_form_rental', 'rental_types' );
							$get_label_tax      = get_taxonomy( $search_tax_advance );
							if ( ! empty( $tax ) ) {
								if ( isset( $tax[ $search_tax_advance ] ) ) {
									if ( ! empty( $tax[ $search_tax_advance ] ) ) {
										$temp_facilities = $tax[ $search_tax_advance ];
										$in_facilities   = explode( ',', $tax[ $search_tax_advance ] );
									}
								}
							}
							?>
							<div class="item-title">
								<?php
								if ( ! empty( $get_label_tax ) ) {
									echo '<h4>' . esc_html( $get_label_tax->label ) . '</h4>';
								}
								?>

							</div>
							<div class="item-content">
								<div class="row">
									<div class="ovscroll" tabindex="1">
										<?php
											$facilities = get_terms(
												[
													'taxonomy'   => $search_tax_advance,
													'hide_empty' => false,
												]
											);
											if ( $facilities && ! is_wp_error( $facilities ) ) {
												foreach ( $facilities as $term ) {
													?>
													<div class="<?php echo ( $position == 'sidebar' ) ? 'col-lg-12' : 'col-lg-4 col-sm-6'; ?>">
														<div class="st-icheck-item">
															<label><?php echo ( $term->name ); ?><input
																		type="checkbox"
																		name="" value="<?php echo ( $term->term_id ); ?>" <?php echo in_array( $term->term_id, $in_facilities ) ? 'checked' : ''; ?>><span
																		class="checkmark fcheckbox"></span>
															</label></div>
													</div>
													<?php
												}
											}
											?>
									</div>
								</div>
							</div>
							<input type="hidden" class="data_taxonomy" name="taxonomy[<?php echo esc_attr( $search_tax_advance ); ?>]" value="<?php echo esc_attr( $temp_facilities ); ?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-primary btn-search" type="submit"><?php echo __( 'Search', 'traveler' ); ?></button>
</div>
