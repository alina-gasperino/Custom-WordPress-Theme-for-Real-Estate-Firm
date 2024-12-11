<?php

/**
 * Template Part: Penthouse Collection
 *
 * Projects with 1 or more pentouse floor plans.
 *
 * @since 1.4.4
 */

$query = get_transient( 'condos_query_penthouse' );

if( $query === false ) {

	$query = new WP_Query( array(
		'post_type'      => 'project',
		'posts_per_page' => 12,
		'meta_query'     => array(
			array(
				'key'	  => 'floorplans_$_specialty_type',
				'value'	  => 'Penthouse',
			),
			array(
				'key'	  => 'floorplans_$_availability',
				'value'	  => 'Available',
			)
		),
		'orderby'        => 'modified',
		'order'          => 'DESC'
	) );

	set_transient( 'condos_query_penthouse', $query, 12 * HOUR_IN_SECONDS );

}

if ( $query->have_posts() ) { ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>Penthouse Collection</h1>
		</header>

		<div id="condos-slider--penthouse" class="project-carousel condos-slider gscroll">
			<div class="condos-slider__scroller">
				<?php while( $query->have_posts() ) {
					$query->the_post();
					get_template_part( 'templates/home/carousel' );
				} ?>
			</div>
		</div>

	</div>

	<?php wp_reset_postdata();

}
