<?php

/**
 * Template Part: Thumbnail
 *
 * @since 1.4.4
 */

?>

<?php if ( has_post_thumbnail() ) : ?>

	<div class="entry-thumb">

		<?php if( is_single() ): ?>

			<?php the_post_thumbnail( 'entry', array( 'itemprop' => 'image' ) ); ?>

		<?php else: ?>

			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'entry', array( 'itemprop' => 'image' ) ); ?>
			</a>
			
		<?php endif; ?>

	</div>
	
<?php endif; ?>
