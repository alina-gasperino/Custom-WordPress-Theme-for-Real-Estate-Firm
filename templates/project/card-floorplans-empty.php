<?php

/**
 * Template Part: No Available Floorplans
 *
 * @since 1.4.4
 */

?>

<div class="floorplans-empty">

	<div class="floorplans-empty__icon">
		<i class="fas fa-layer-group"></i> <?= __( 'Sold Out', 'talkcondo' ) ?>
	</div>

	<h3 class="floorplans-empty__title">
		<?= sprintf( __( '%s is Sold Out', 'talkcondo' ), get_the_title() ) ?>
	</h3>

	<div class="floorplans-empty__description">
		<p>
			<?= __( 'Browse the map to find available condos nearby or click below to show all floorplans', 'talkcondo' ); ?>
		</p>
	</div>

	<div class="floorplans-empty__buttons">
		<ul class="list-inline">
			<li>
				<a href="<?php echo project_map_link( get_the_ID() ); ?>" class="btn btn-alt">
					<i class="far fa-building"></i>
					<?= __( 'Nearby Condos on Map', 'talkcondo' ); ?>
				</a>
			</li>
			<li>
				<a href="#" class="btn btn-secondary floorplans-empty__trigger">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/images/icon-mixed-available.svg" width="14" height="14" alt="">
					<?= sprintf( __( 'Show All Floor Plans (%s)', 'talkcondo' ), count(get_field('floorplans')) ) ?>
				</a>
			</li>
			<li>
				<a class="btn btn-secondary" href="<?php echo leadpages_form_url() ?>" data-leadbox="<?php echo leadpages_form_data_id() ?>">
					<i class="fa fa-envelope"></i>
					<?= __( 'Get Expert Advice', 'talkcondo' ); ?>
				</a>
			</li>
		</ul>
	</div>

</div>
