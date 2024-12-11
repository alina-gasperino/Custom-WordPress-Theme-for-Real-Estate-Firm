<?php $videos = []; ?>

<?php while (have_rows('gallery_videos')): the_row(); ?>
	<?php $videos[] = parseYoutubeUrl(get_sub_field('gallery_video')); ?>
<?php endwhile; ?>

<?php $images = get_field('gallery'); ?>

<?php 

/**
 * If we have any images or videos.
 */

if ( $images || !empty( $videos ) ): ?>

	<div class="project-gallery" data-animation="fadeIn">
		
		<?php $index = 1; foreach( $images as $image ): ?>

			<?php if( $videos && $index == 2 ): ?>
				<?php foreach ($videos as $video): ?>
					<div class="slick-slide--photo slick-slide--iframe">
						<a href="https://www.youtube.com/watch?v=<?php echo $video ?>" itemprop="image" class="popup-youtube">
							<img src="https://img.youtube.com/vi/<?php echo $video ?>/hqdefault.jpg" alt="<?php echo $image['alt']; ?>" />
						</a>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if ($images) : ?> 

				<div class="slick-slide--photo">
					<a href="<?php echo $image['sizes']['large']; ?>" itemprop="image">
						<img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" />
					</a>
				</div>

			<?php else : 
				
				$uploads = wp_upload_dir();
				$uploads['baseurl'] . '/2018/04/gallery-placeholder.jpg';
			
			endif; ?>

		<?php $index++; endforeach; ?>
		
	</div>

<?php else: ?>
	<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder.jpg' ?>" width="1030" class="gallery-placeholder img-responsive" alt="<?php esc_html_e( 'Images Coming Soon', 'talkcondo' ); ?>" data-animation="fadeIn" />

<?php endif; ?>
