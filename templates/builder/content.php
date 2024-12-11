<?php

/**
 * Component Name: Content
 * 
 * @package Templates
 * @subpackage Builder
 */

?>

<section class="b-section b-content">

	<?php if( have_rows( 'content_row' ) ): ?>

		<?php while( have_rows( 'content_row' ) ): the_row(); ?>

			<?php if( $title = get_sub_field( 'content_title' ) ): ?>
				<h3 class="h1 b-section__title">
					<?php echo $title; ?>
				</h3>
			<?php endif; ?>

			<div class="row">
				
				<?php while( have_rows( 'content_column' ) ): the_row(); ?>

					<div class="col-md-<?php the_sub_field( 'content_layout' ); ?> b-content__area">

						<?php the_sub_field( 'content_editor' ); ?>

					</div>

				<?php endwhile; ?>

			</div>

		<?php endwhile; ?>

	<?php endif; ?>

</section>
