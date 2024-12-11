<header class="project-header" itemscope itemtype="http://schema.org/Place" id="project-header">
<?php if ( !get_query_var("floorplan") ) :?>
	<meta itemprop="url" content="<?php the_permalink(); ?>">
	<meta itemprop="telephone" content="<?php esc_html_e( 'Request Info', 'talkcondo' ); ?>">
	<?php if( get_field('lat') && get_field('lng') ): ?>
		<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
			<meta itemprop="latitude" content="<?php the_field('lat'); ?>">
			<meta itemprop="longitude" content="<?php the_field('lng'); ?>">
		</div>
	<?php endif; ?>

    <?php
        $isLaunchingSoon = custom_cat_slug('salesstatus') == 'launching-soon';
    ?>

	<?php get_template_part( 'templates/project/components/tabs' ); ?>

    <?php if($isLaunchingSoon) get_template_part( 'templates/project/template-project-launching-soon' ); ?>

	<div class="project-icon">

		<?php if ( has_post_thumbnail() ) : ?>
			<a href="#" rel="nofollow" data-popup="true">
				<?php the_post_thumbnail( array( 145, 145 ) ); ?>
			</a>
		<?php else : ?>
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/project-placeholder.jpg" width="145" height="145" alt="<?php the_title(); ?>">
		<?php endif; ?>

		<meta itemprop="image" content="<?php the_post_thumbnail_url(); ?>">
		<?php if ( $logo = get_field('logo') ): ?>
			<meta itemprop="logo" content="<?php echo wp_get_attachment_url( $logo, 'project-logo' ) ?>">
		<?php endif; ?>
	</div>

	<div class="project-title">

		<h1 class="title clearfix" itemprop="name">
			<?php echo ( get_field("h1") ) ?: the_title(); ?>
		</h1>

		<ul class="project-details">

			<?php if ( get_field('address') || custom_cat_link('neighbourhood') || custom_cat_link('city') ): ?>
				<li itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
					<?php echo implode(', ', array_filter( array(
						'<span itemprop="streetAddress"><a href="'. project_map_jump_to_link( get_the_id() ) .'">' . get_field('address') . '</a></span>',
						'<span>' . custom_cat_link('neighbourhood') . '</span>',
						'<span itemprop="addressLocality">' . custom_cat_link('city') . '</span>',
						'<span>(<a href="'. project_map_jump_to_link( get_the_id() ) .'"><i class="far fa-map"></i> Go to Map</a>)</span>',
					))); ?>
				</li>
			<?php endif; ?>

			<li>

				<?php $terms = get_the_terms( get_the_id(), 'developer'); ?>
				<?php foreach ($terms as $i => $term): ?>
					<?= $i > 0 ? ' & ' : 'By ' ?>
					<a href="<?= get_term_link($term, 'developer') ?>"><?= $term->name ?> <?= $term->count ? "($term->count)" : '' ?></a>
				<?php endforeach ?>

				<?php if ($date = custom_cat_text('occupancy-date')): ?>
					<?php printf( esc_html__( '&middot; Completed in %s', 'talkcondo' ), $date ); ?>
				<?php endif; ?>

			</li>

		</ul>

        <?php if(!$isLaunchingSoon):?>
		<div class="leadpages__button leadpages__button--main project-header__leadpages">
			<?php echo leadpages_form_button( get_field( 'leadpagesform' ) ) ?>
		</div>
        <?php endif; ?>

	</div>

	<?php $pricedfrom = project_pricedfrom(); ?>
	<?php $pricedto = project_pricedto(); ?>
	<?php if ( custom_cat_text('salesstatus') || $pricedfrom ): $status = custom_cat_text('salesstatus') ?>
		<div class="project-header-right">
			<?php if ($pricedfrom): ?>
				<div class="project__price">
					<meta itemprop="pricerange" content="<?php echo $pricedfrom; ?>">
					<?php
						if ( $pricedfrom && $pricedto && $pricedfrom != $pricedto ) {
							printf(
								"%s <span>-</span> %s",
								'<span>' . $pricedfrom . '</span>',
								'<span>' . $pricedto . '</span>'
							);
						} elseif ($pricedfrom) {
							printf(
								"%s",
								'<span>' . $pricedfrom . '</span>'
							);
						}
					?>
				</div>

			<?php elseif( $status ) : ?>
				<meta itemprop="pricerange" content="<?php echo $status; ?>">
			<?php endif; ?>

			<?php if ( $status ): ?>
				<?php if ( $pricedfrom ): ?>
					<div class="project__status">
                        <?php echo $status; ?>
					</div>
				<?php elseif( !$isLaunchingSoon ): ?>

					<div class="project__price">
						<?php echo $status; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

		</div>
	<?php endif; ?>

	<div class='leadpages'>
		<p class="leadpages__tagline">Floor Plans & Pricing delivered to Your Inbox</p>
		<div class="leadpages__button">
			<?php echo leadpages_form_button(get_field('leadpagesform')) ?>
		</div>
	</div>
<?php endif;?>
</header>

<?php if ( !get_query_var('floorplan') && !get_query_var('floorplans') ) :?>
<div class="project-ratings">
	<?php if (function_exists('the_ratings')) the_ratings(); ?>
</div>

<div class="leadpages__button leadpages__button--main project-header__leadpages project-mobile-only">
	<?php echo leadpages_form_button( get_field( 'leadpagesform' ) ) ?>
</div>
<?php endif;?>

<div class="project-header-right project-mobile-only">
	<?php if ( $status ): ?>
		<?php if ( $pricedfrom ): ?>
			<!-- <div class="project__status"> -->
				<?php echo $status; ?>
			<!-- </div> -->
		<?php else: ?>
			<!-- <div class="project__price"> -->
				<?php echo $status; ?>
			<!-- </div> -->
		<?php endif; ?>
	<?php endif; ?>
	<?php if ($pricedfrom): ?>
		<!-- <div class="project__price"> -->
			 ·
			<!-- <meta itemprop="pricerange" content="<?php echo $pricedfrom; ?>"> -->
			<?php
				if ( $pricedfrom && $pricedto && $pricedfrom != $pricedto ) {
					printf(
						"%s <span>-</span> %s",
						'<span>' . $pricedfrom . '</span>',
						'<span>' . $pricedto . '</span>'
					);
				} elseif ($pricedfrom) {
					printf(
						"%s",
						'<span>' . $pricedfrom . '</span>'
					);
				}
			?>
		<!-- </div> -->

	<?php elseif( $status ) : ?>
		<meta itemprop="pricerange" content="<?php echo $status; ?>">
	<?php endif; ?>
</div>
