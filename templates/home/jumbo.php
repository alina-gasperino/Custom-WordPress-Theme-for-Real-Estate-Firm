<?php

/**
 * Template Part: Jumbo
 *
 * @since 1.4.4
 */

?>

<div class="jumbo">

	<div class="jumbo-content">

		<div class="container">
			<h1>
				<?php the_field( 'jumbo_title' ); ?>
			</h1>

			<div id="home-search" class="search-form-container">
				<?php get_search_form(); ?>
			</div>

			<a class="mobile-only user-location" href="/map?center=user-location">
				<img src="<?php echo get_template_directory_uri() . '/assets/images/crosshair.png'?>" height="20" width="20" />
				Search near my current location
			</a>

            <div id="jumbo-map-button" class="map-buttons ">
                <a href="/map?center=user-location" class="btn btn-primary">
                    Browse New Condos on Map
                </a>
            </div>
		</div>

	</div>

	<?php if ( have_rows( 'jumbo_slider' ) ) : ?>

	<div class="jumbo-slider" data-slick='{ "autoplay": true, "autoplaySpeed": 10000, "fade": true, "arrows": false }'>
		
		<?php while ( have_rows( 'jumbo_slider' ) ): the_row(); ?>

			<div class="jumbo-slide" data-background-image="<?php the_sub_field( 'jumbo_slide_background' ); ?>">
				<div class="overlay"></div>

				<?php if( get_sub_field( 'jumbo_slide_city' ) && get_sub_field( 'jumbo_slide_project' ) ): ?>
					<div class="jumbo-slide__details">
						<span class="jumbo-slide__details__city">
							<?php the_sub_field( 'jumbo_slide_city' ); ?>
						</span>
						<span class="jumbo-slide__details__project">
							<?php the_sub_field( 'jumbo_slide_project' ); ?>
						</span>
					</div>
				<?php endif; ?>
			</div>

		<?php endwhile; ?>

	</div>

	<?php endif; ?>

</div>
