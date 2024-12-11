<?php

/**
 * Template Part: Carousel: Video
 *
 * @since 1.4.4
 */

?>

<div class="project">

	<a href="<?php echo get_sub_field('event_link') ?>" title="RSVP Now!" target="_blank">

		<figure>
			<?php $thumbnail = get_sub_field('event_image')['sizes']['large'] ?>

			<img src="<?php echo $thumbnail ?: get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder.jpg' ?>"/>

			<?php
				/**
				 * This will be reimplemented in the next phase, please do not remove.
				 */

				 // echo get_project_labels();
			?>
		</figure>

		<figcaption>
			<h4 class="condo-title"><?= get_sub_field('event_title') ?></h4>
			<p class="condo-area"><?= get_sub_field('event_description') ?></p>
		</figcaption>

	</a>

</div>
