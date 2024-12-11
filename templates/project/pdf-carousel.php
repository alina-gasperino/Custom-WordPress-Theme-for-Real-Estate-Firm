<?php

/**
 * Template Part: Carousel
 *
 * @since 1.4.4
 */

//$project_pdfs;
?>

<div class="project">

	<a href="<?php the_permalink(); ?>">

		<figure>
			<?php
			$thumbnails = wp_get_attachment_image_src( get_post_thumbnail_id(), 'project_small' );
			if ( is_array($thumbnails) && !empty($thumbnails) && !is_local() ) :
				echo '<img src="' . $thumbnails[0] . '">';
			else :
				echo '<img src="' . get_template_directory_uri() . '/assets/images/layout/condo-placeholder.jpg">';
			endif;
			?>
		</figure>

		<figcaption>
			<h4 class="condo-title"><?php the_title() ?></h4>
		</figcaption>

	</a>

</div>
