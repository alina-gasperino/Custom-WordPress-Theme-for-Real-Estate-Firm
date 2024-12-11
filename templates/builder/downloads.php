<?php

/**
 * Component Name: Downloads
 * 
 * @package Templates
 * @subpackage Builder
 */

if( $post = get_sub_field( 'downloads_project' ) ) {

	// Setup the project post data.
	setup_postdata( $post );

	// Get the Drive folder.
	$floorplansfolder = get_post_meta( $post->ID, 'floorplansfolder', true );

	// Process various Google Drive variables.
	if ( $googledrive = ( strpos( $floorplansfolder, 'drive.google.com' ) !== false ) ) {
		
		parse_str( parse_url( $floorplansfolder, PHP_URL_QUERY ), $folder ); } ?>

		<section class="b-section b-downloads responder-section--downloads">

			<h3 class="h1 b-section__title">
				<?php the_sub_field( 'downloads_title' ); ?>
			</h3>

			<?php if ( $googledrive && $folder['id'] ): ?>
				<div class="responder-section__content responder-section__content--downloads">
					<iframe src="https://drive.google.com/embeddedfolderview?id=<?php echo $folder['id']; ?>#grid" width="100%" frameborder="0" style="min-height: 500px;"></iframe>
				</div>
			<?php else: ?>
				<h3>
					<?php echo $floorplansfolder; ?>
				</h3>
			<?php endif; ?>

		</section>

	<?php wp_reset_postdata();

} ?>
