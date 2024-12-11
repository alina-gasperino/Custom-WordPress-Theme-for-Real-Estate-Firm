<?php

/**
 * Template Part: Meta
 *
 * @since 1.4.4
 */

?>

<ul class="entry-meta list-inline">

	<?php if( !is_day() ): ?>
		<li>
			<time class="entry-time">
				<i class="fa fa-calendar-o"></i>

				<span datetime="<?php echo get_the_time( 'Y-m-j' ); ?>" itemprop="datePublished">
					<?php echo get_the_time( get_option( 'date_format' ) ); ?>
				</span>
			</time>
		</li>
	<?php endif; ?>

	<?php if( !is_author() ): ?>
		<li>
			<span class="entry-author" itemscope itemtype="http://schema.org/Person" itemprop="author">
				<i class="fa fa-user-o"></i>

				<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="entry-author-link" itemprop="url">
					<span class="entry-author-name" itemprop="name">
						<?php the_author(); ?>
					</span>
				</a>
			</span>
		</li>
	<?php endif; ?>

	<?php if( !is_category() ): ?>
		<li>
			<span class="entry-categories" itemprop="keywords">
				<i class="fa fa-folder-o"></i>
				<?php echo get_the_category_list( ', ' ); ?>
			</span>
		</li>
	<?php endif; ?>

</ul>
