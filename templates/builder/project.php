<?php

/**
 * Component Name: Project
 * 
 * @package Templates
 * @subpackage Builder
 */

if( $post = get_sub_field( 'project' ) ) {

	// Variables
	$title    = get_sub_field( 'project_title' );
	$subtitle = get_sub_field( 'project_subtitle' );
	$button   = get_sub_field( 'project_button' );

	// Setup the project post data.
	setup_postdata( $post ); ?>

	<header class="b-section b-project entry-header">

		<header class="project-header" itemscope itemtype="http://schema.org/Place">

			<div class="project-icon">
				<?php the_post_thumbnail( array( 145, 145 ) ); ?>
			</div>

			<div class="project-title">
				<h1 class="title clearfix" itemprop="name">
					<?php if( !$title ): ?>
						<?php echo ( get_field("h1") ) ?: the_title(); ?>
					<?php else: ?>
						<?php echo $title; ?>
					<?php endif; ?>
				</h1>

				<ul class="project-details">
					<?php if( !$subtitle ): ?>
						<li>
							<?php echo str_replace('&', '&<br>', custom_cat_link('developer')) ?>
						</li>
						<?php if ( get_field('address') || custom_cat_link('neighbourhood') || custom_cat_link('city') ): ?>
							<li itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<?php echo implode(', ', array_filter( array(
									'<span itemprop="streetAddress">' . get_field('address') . '</span>',
									'<span>' . custom_cat_link('neighbourhood') . '</span>',
									'<span itemprop="addressLocality">' . custom_cat_link('city') . '</span>',
								))); ?>
							</li>
						<?php endif; ?>
					<?php else: ?>
						<li>
							<?php echo $subtitle; ?>
						</li>
					<?php endif; ?>
				</ul>

				<div class="leadpages__button leadpages__button--main project-header__leadpages">
					<?php echo leadpages_form_button( get_field( 'leadpagesform' ) ) ?>
				</div>
			</div>
			
		</header>

		<div class="project-ratings">
			<?php if (function_exists('the_ratings')) the_ratings(); ?>
		</div>

	</header>

	<?php wp_reset_postdata(); ?>

<?php } ?>
