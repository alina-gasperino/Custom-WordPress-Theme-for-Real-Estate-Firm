<?php

/**
 * Component Name: Gallery
 * 
 * @package Templates
 * @subpackage Builder
 */

?>

<section class="b-section b-gallery">

	<h3 class="h1 b-section__title">
		Image Gallery
	</h3>

	<?php if ( $images = get_sub_field('gallery_items') ): ?>

		<div class="project-gallery <?php echo count( $images ) == 1 ? 'hidden' : ''; ?>" data-animation="fadeIn">
			
			<?php $index = 1; foreach( $images as $image ): ?>

				<div class="slick-slide--photo">
					<a href="<?php echo $image['sizes']['large']; ?>" itemprop="image">
						<img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />
					</a>
				</div>

			<?php $index++; endforeach; ?>
			
		</div>

	<?php endif; ?>

</section>
