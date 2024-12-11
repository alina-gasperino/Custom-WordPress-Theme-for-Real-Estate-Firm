<?php

/**
 * Template Part: Carousel: Video
 *
 * @since 1.4.4
 */

?>
<div class="project">

	<a href="<?php the_permalink(); ?>">

		<figure>
			<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), array( 400, 350, true ) )[0]; ?>

			<img src="<?= $thumbnail && !is_local() ? $thumbnail : get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder.jpg' ?>"/>

			<?php
				/**
				 * This will be reimplemented in the next phase, please do not remove.
				 */

				 // echo get_project_labels();
			?>
		</figure>

		<figcaption>

			<h4 class="condo-title">
				<?php the_title() ?>
			</h4>

			<div class="condo-area">
				<?php if( custom_cat_text('neighbourhood') && custom_cat_text('city') ) {
					echo custom_cat_text('neighbourhood') . ', ' . custom_cat_text('city');
				} elseif( custom_cat_text('neighbourhood') ) {
					echo custom_cat_text('neighbourhood');
				} elseif( custom_cat_text('city') ) {
					echo custom_cat_text('city');
				} ?>
			</div>

			<?php $available = project_available_floorplans(); ?>

		</figcaption>

	</a>

</div>
