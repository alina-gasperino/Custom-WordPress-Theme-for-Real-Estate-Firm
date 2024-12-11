<?php

wp_enqueue_script( 'facebook', "" );

$folder = get_post_meta( get_the_ID(), 'floorplansfolder', true );

if ( strpos( $folder, 'drive.google.com' ) !== false ) {
	parse_str( parse_url( $folder , PHP_URL_QUERY ), $b );
	$id = $b['id'];
}

?>

<div class="container">

	<main class="template-page site-content <?php avia_layout_class( 'content' ); ?> units">

		<article>

			<?php
			/**
			 * Header
			 */
			?>
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

				<div class="responder-header__button">
					<a href="https://talkcondo.leadpages.co/leadbox/1440d0df3f72a2%3A17f97ed63b46dc/5656123633827840/" target="_blank" class="orange-button">
						<?php esc_html_e( 'Request Call Back', 'talkcondo' ); ?>
					</a>
				</div>

			</header>

			<?php
			/**
			 * Downloads
			 */
			?>
			<section class="responder-section responder-section--downloads">
				<h2 class="responder-section__title">
					<?php esc_html_e( 'Downloads', 'talkcondo' ); ?>
				</h2>
				<div class="responder-section__content responder-section__content--downloads grid-view">
					<iframe src="https://drive.google.com/embeddedfolderview?id=<?php echo $id ?>#grid" width="100%" frameborder="0" style="min-height: 500px;"></iframe>
				</div>

				<div class="responder-section__content responder-section__content--downloads list-view">
					<iframe src="https://drive.google.com/embeddedfolderview?id=<?php echo $id ?>#none" width="100%" frameborder="0" style="min-height: 500px;"></iframe>
				</div>
			</section>

			<?php
			/**
			 * Floor Plan Browser
			 */
			?>
			<section class="responder-section">
				<!-- <h2 class="responder-section__title">
					<?php esc_html_e( 'Floor Plans & Prices', 'talkcondo' ); ?>
				</h2> -->
				<div class="responder-section__content">
					<?php get_template_part( 'templates/project/card-floorplans' ); ?>
				</div>
			</section>

			<div class="responder-content">

				<script data-leadbox="1440d0df3f72a2:17f97ed63b46dc" data-url="https://talkcondo.leadpages.co/leadbox/1440d0df3f72a2%3A17f97ed63b46dc/5656123633827840/" data-config="%7B%7D" type="text/javascript" src="https://talkcondo.leadpages.co/leadbox-893.js"></script>

				<?php include get_stylesheet_directory() . '/library/legacy/includes/facebook-chat.php' ?>

			</div>

		</article>

	</main>

</div>

<?php get_footer(); ?>
