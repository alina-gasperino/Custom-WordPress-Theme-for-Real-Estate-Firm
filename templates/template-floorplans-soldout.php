<?php

$ver = null;

wp_enqueue_script( 'googlemapsapi', "https://maps.googleapis.com/maps/api/js?key=AIzaSyBsOLdqNXdcZ_6zcRKnt_qjeT9mVO5qGy0&libraries=places", $ver, true );
wp_enqueue_script( 'markerclusterer', get_stylesheet_directory_uri() . '/assets/js/markerclusterer.js', ['jquery'], $ver, true );
wp_enqueue_script( 'talkmap', get_stylesheet_directory_uri() . '/assets/js/talkMap.jquery.js', ['jquery'], $ver, true );

$floorplansfolder = get_post_meta( get_the_ID(), 'floorplansfolder', true);
$googledrive = (strpos($floorplansfolder, 'drive.google.com') !== false);

if ($googledrive) {
	$a = parse_url($floorplansfolder, PHP_URL_QUERY);
	parse_str($a, $b);
	$id = $b['id'];
}
?>


<div class="container">

	<main class="template-page site-content <?php avia_layout_class( 'content' ); ?> units">

		<article>

			<header class="responder-header">

				<div class="responder-header__logo">
					<?php if ( get_field('logo') ): ?>
						<?php echo wp_get_attachment_image( get_field('logo'), 'project-logo' ); ?>
					<?php else: ?>
						<div class="orange-icon">
							<i class="far fa-building"></i>
						</div>
					<?php endif; ?>
				</div>

				<div>
					<h2 class="responder-header__title">
						<?php the_title(); ?>
					</h2>

					<h4 class="responder-header__subtitle">
						<?php esc_html_e( 'Floor Plans & Price Lists', 'talkcondo' ); ?>
					</h4>
				</div>

				<div class="responder-header__button disabled">
					<span class="orange-button disabled">
						<?php esc_html_e( 'Sold Out', 'talkcondo' ); ?>
					</span>
				</div>

			</header>

			<p>Thank you for your interest in <?php the_title(); ?>.  Unfortunately <?php the_title(); ?> is completely Sold Out!</p>
			<p>The map below shows some other condos in the area that might tickle your fancy.  Why not have a browse and register for something else?</p>
			<div class="text-center">
				<a href="https://talkcondo.leadpages.co/leadbox/1440d0df3f72a2%3A17f97ed63b46dc/5656123633827840/" target="_blank" class="orange-button">Arrange an appointment with TalkCondo</a>
			</div>

		</article>

	</main>

</div>

<?php get_template_part('templates/project/template-project-map' ) ?>

<?php get_footer(); ?>
