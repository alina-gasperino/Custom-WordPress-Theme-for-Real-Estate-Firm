<div class="card description">
		<h2 class="card__title">
		<?php if (get_field('h2')): ?>
			<?php the_field('h2'); ?>
		<?php elseif (custom_cat_text('neighbourhood') && custom_cat_text('city')): ?>
			<?php echo 'Condos in ' . custom_cat_text('neighbourhood') . ', ' . custom_cat_text('city') ?>
		<?php elseif (custom_cat_text('neighbourhood')): ?>
			<?php echo 'Condos in ' . custom_cat_text('neighbourhood') ?>
		<?php elseif (custom_cat_text('city')): ?>
			<?php echo 'Condos in ' . custom_cat_text('city') ?>
		<?php else: ?>
			<?php the_title(); ?>
		<?php endif; ?>
		</h2>
	<div class="card__content">
		<?php // echo project_text() ?>
	</div>
</div>

<div class="card overview">

	<div class="card__title">
		<span itemprop="name"><?php the_title() ?></span> Overview
	</div>

	<hr>

	<div class="card__content">
		<div class="card__subtitle">Key Information</div>

		<div class="card__subsection">

			<div class="card__subitem">
				<div class="card__subitemtitle">Location</div>
				<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
					<span itemprop="streetAddress"><?php the_field('address') ?></span><br>
					<span><?php echo custom_cat_link('neighbourhood'); ?></span>,
					<span itemprop="addressLocality"><?php echo custom_cat_link('city'); ?></span><br>
					<span itemprop="addressRegion"><?php echo custom_cat_link('district') ?></span>
				</div>
			</div>

			<?php if( $developer = custom_cat_link('developer') ): ?>
				<div class="card__subitem">
					<meta itemprop="branchof" content="<?php echo strip_tags( $developer ); ?>">
					<div class="card__subitemtitle">Developer</div>
					<?php echo str_replace('&', '&<br>', $developer ) ?>
				</div>
			<?php endif; ?>

			<div class="card__subitem">
				<div class="card__subitemtitle">Completion</div>
				<?php echo (custom_cat_text('occupancy-date')) ?: '-' ?>
			</div>

		</div>

		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Sales Status</div>
				<div itemprop="amenityFeature">
					<?php $salesstatus = str_replace('&', '&<br>', custom_cat_text('salesstatus')) ?>
					<?php echo ($salesstatus) ?: '-' ?>
				</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Price Range</div>
				<div>
					<?php if ( get_field('pricedfrom') && get_field('pricedto') ): ?>
						<?php the_field('pricedfrom') ?> to <br><?php the_field('pricedto') ?>
					<?php elseif ( get_field('pricedfrom') ): ?>
						<?php the_field('pricedfrom') ?>
					<?php else: ?>
						<?php echo '-'; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="card__subitem">
				<div class="card__subitemtitle">Suite Sizes</div>
				<div itemprop="amenityFeature"><?php echo project_suite_size_range( get_the_ID() ) ?></div>
			</div>
		</div>
		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Development Status</div>
				<?php echo (custom_cat_text('status')) ?: '-' ?>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Building Type</div>
				<div itemprop="amenityFeature">
					<?php $type = str_replace('&', '&<br>', custom_cat_text('type')) ?>
					<?php echo ($type) ?: '-'; ?>
				</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Avg. Price per Foot</div>
				<div></div>
			</div>
		</div>

		<hr>

		<div class="card__subtitle">Extras</div>
		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Parking</div>
				<?php echo (get_field('parking')) ?: '-' ?>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Locker Price</div>
				<?php echo (get_field('locker')) ?: '-' ?>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Mt. Fees ($ per sq.ft.)</div>
				<?php echo (get_field('maintenancefeessq.ft')) ?: '-' ?>
			</div>
		</div>
	</div>

	<div class='last-updated'>Data last updated:
		<?php echo get_the_modified_date('F jS, Y'); ?>
	</div>
</div>

<?php get_template_part('templates/project/card-floorplans-preview'); ?>

<?php if (get_field('projecthighlights')): ?>
<div class="card">
	<div class="card__title"><?php the_title() ?> Highlights</div>
	<div class="card__content" itemprop="description">
		<?php echo nl2br(get_field('projecthighlights')) ?>
	</div>
</div>
<?php endif; ?>

<?php if (get_field('amenities')): ?>
<div class="card amenities">
	<div class="card__title">Amenities</div>
	<div class="card__content">
		<?php if (strpos(get_field('amenities'), '-') === 0): ?>
			<?php echo str_replace( '<li>', '<li itemprop="amenityFeature">', column_list( get_field('amenities') ) ); ?>
		<?php else: ?>
			<?php echo str_replace( '<li>', '<li itemprop="amenityFeature">', get_field('amenities') ); ?>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<div class="card additional-informaiton">
	<div class="card__title">Additional Information</div>

	<div class="card__content">
		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Walk Score</div>
				<div>n/a</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Transit Score</div>
				<div>n/a</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">&nbsp;</div>
			</div>
		</div>

		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Architect</div>
				<div>n/a</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Interior Designer</div>
				<div>n/a</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">&nbsp;</div>
			</div>
		</div>

		<hr>

		<div class="card__subtitle">Height & Rank</div>

		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Count</div>
				<div itemprop="amenityFeature">
					<?php echo (get_field('storeys')) ?: '-' ?> Floors<br>
					<?php echo (get_field('suites')) ?: '-' ?> Suites
				</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Height (M)</div>
				<div>n/a m</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Height (Ft)</div>
				<div>n/a ft</div>
			</div>
		</div>

		<div class="card__subsection">
			<div class="card__subitem">
				<div class="card__subitemtitle">Height Rank (city)</div>
				<div># tallest in {city}</div>
			</div>
			<div class="card__subitem">
				<div class="card__subitemtitle">Height Rank (neighbourhood)</div>
				<div># tallest in {neighbourhood}</div>
			</div>
		</div>

	</div>

</div>

<?php if (get_field('featuresfinishes')): ?>
<?php
$maxlines = 5;
$lines = explode(PHP_EOL, get_field('featuresfinishes'));
$showmore = (count($lines) > $maxlines);
?>
<div class="card featuresfinishes <?php echo ($showmore) ? 'showmore' : '' ?>">
	<div class="card__title">Features &amp; Finishes</div>
	<div class="card__content" itemprop="amenityFeature">
		<?php echo nl2br(implode(PHP_EOL, array_slice($lines, 0, $maxlines))); ?>
		<?php if ($showmore): ?>
		<div class="more">
		<?php echo nl2br(implode(PHP_EOL, array_slice($lines, $maxlines))); ?>
		</div>
		<?php endif; ?>
	</div>
	<?php if ($showmore): ?>
	<button class="card__expand">+ Show More</button>
	<?php endif; ?>
</div>
<?php endif; ?>
<script>
jQuery(function($){
	$('.card__expand').on('click', function(e){
		e.preventDefault();
		$this = $(this);
		$card = $this.closest('.card');
		$card.toggleClass('expanded');
		$card.find('.more').slideToggle('fast');
		$this.html( ($card.hasClass('expanded')) ? '- Show Less' : '+ Show More' );
	});
});
</script>

