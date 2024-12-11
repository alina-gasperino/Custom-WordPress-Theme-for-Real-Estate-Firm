<?php

/**
 * Template Part: Developer Project
 *
 * @since 1.4.4
 */

?>

<div class="project">

	<a href="<?php the_permalink() ?>">

		<figure>
			<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'project_small' ) ?>
			<?php if ( $thumbnail && $thumbnail[0] ) : ?>
				<img src="<?= $thumbnail[0] ?>">
			<?php else: ?>
				<img src="<?= get_template_directory_uri() . '/assets/images/layout/condo-placeholder.jpg' ?>">
			<?php endif ?>
		</figure>

		<figcaption>

			<h4 class="condo-title">
				<?php the_title() ?>
			</h4>

			<p class="condo-area">
				<?php
				$neighbourhood = custom_cat_text('neighbourhood');
				$city = custom_cat_text('city');

				if( isset( $neighbourhood, $city ) ){
					echo $neighbourhood . ', ' . $city;
				} else {
					echo ( $neighbourhood ) ? $neighbourhood : '';
					echo ( $city ) ? $city : '';
				} ?>
			</p>

			<?php $available = project_available_floorplans(); ?>

			<p>
				<span class="condo-floorplan-availability">
					<?php if ( get_field('platinum_access') ):
						esc_html_e( 'Floor Plans Not Public', 'talkcondo' );
					elseif ( $count = count( $available ) ):
						printf( esc_html( _n( '%d Floor Plan', '%d Floor Plans', $count, 'talkcondo' ) ), $count );
					elseif ( $sold_out = project_soldout_floorplans() ):
						esc_html_e( 'Floor Plans Sold Out', 'talkcondo' );
					else :
						esc_html_e( 'Floor Plans Coming Soon', 'talkcondo' );
					endif; ?>
				</span>
				<?php $avg_price = project_avg_price_per_sqft(); ?>
				<?php if ( $avg_price && get_field('platinum_access') == false ): ?>
					&middot;
					<span>$<?php echo $avg_price; ?>/ft Avg.</span>
				<?php endif; ?>
			</p>

		</figcaption>

	</a>

</div>
