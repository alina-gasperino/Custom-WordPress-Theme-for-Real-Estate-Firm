<?php

/**
 * Template Part: Platinum Access
 *
 * @since 1.4.4
 */

$query = get_transient( 'condos_query_new' );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type' => 'project',
		'posts_per_page' => 12,
		'tax_query' => array(
			array(
				'taxonomy' => 'salesstatus',
				'field' => 'slug',
				'terms' => 'platinum-access',
			)
		),
	) );

	set_transient( 'condos_query_new', $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h3 class="heading">
				<?php esc_html_e( 'Platinum Access Condos', 'talkcondo' ); ?>
			</h3>
		</header>

		<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php while ( $query->have_posts() ): $query->the_post(); ?>
					<?php get_template_part( 'templates/home/carousel' ) ?>
				<?php endwhile; ?>
			</div>
		</div>

	</div>

	<?php wp_reset_query() ?>

<?php endif; ?>
