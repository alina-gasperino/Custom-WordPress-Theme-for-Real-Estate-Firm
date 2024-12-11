<?php

/**
 * Template Part: Condos Soon
 *
 * @since 1.4.4
 */

$query = get_transient( 'condos_query_soon' );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type' => 'project',
		'posts_per_page' => 12,
		'tax_query' => array(
			array(
				'taxonomy' => 'salesstatus',
				'field' => 'slug',
				'terms' => 'launching-soon',
			)
		)
	) );

	set_transient( 'condos_query_soon', $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class="heading">Condos Launching Soon   (templates/home/condos/condos-soon.php)</h1>
		</header>

		<div id="condos-slider--soon" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php while ( $query->have_posts() ): $query->the_post(); ?>
					<?php get_template_part( 'templates/home/carousel' ); ?>
				<?php endwhile; ?>
			</div>
		</div>

	</div>

	<?php wp_reset_query(); ?>

<?php endif; ?>
