<?php

if ($floorplan['availability'] == 'Available') {
	$availability = 'available';
} elseif ($floorplan['availability'] == 'Sold Out') {
	$availability = 'sold-out';
}

$title = $floorplan['suite_name'];
if ($floorplan['specialty_type']) $title .= " (" . $floorplan['specialty_type'] . ")";
$title = trim_title($title, 16);

$attachment = get_post($floorplan['image']);
$thumbnail = wp_get_attachment_image_src( $floorplan['image'], 'project_small' );
$fullimage = wp_get_attachment_image_src( $floorplan['image'], 'full' );
if (is_local()) $thumbnail[0] = str_replace( home_url(), 'https://www.talkcondo.com', $thumbnail[0]);
if (is_local()) $fullimage[0] = str_replace( home_url(), 'https://www.talkcondo.com', $fullimage[0]);

$data_attributes = [
	'fullimage' => $fullimage[0],
	'thumbnail' => $thumbnail[0],
	'price' => $floorplan['price'],
	'formattedprice' => $floorplan['price'] ? number_format($floorplan['price']) : '',
	'beds' => $floorplan['beds'],
	'baths' => $floorplan['baths'],
	'size' => $floorplan['size'],
	'availability' => $availability,
	'suite-name' => $floorplan['suite_name'],
	'project' => get_the_title(),
	'exposure' => @implode('/', $floorplan['exposure']),
	'floorplan-url' => get_floorplans_link( $floorplan['image'] ),
	'project-url' => get_permalink(),
	'reserve-link' => get_floorplans_link( $floorplan['image'] ) . 'reserve',
];

$ppsqft = round( absint( $floorplan['price'] ) / absint( $floorplan['size'] ) );

$quick_view_url = admin_url('admin-ajax.php?action=floorplan_quick_view&attachment_id=' . $floorplan['image']);
$quick_view_url = $fullimage[0];

?>

<div class="floorplan grid-entry <?= $availability ?>" <?= html_data_attributes($data_attributes) ?>>

	<div class="grid-entry-left">
		<div class="floorplan__thumbnail">
			<a class="quick-view noLightbox" href="<?= $quick_view_url ?>" data-thumb="<?= $thumbnail[0] ?>">
				<img class="floorplan__image lazy" alt="<?= $floorplan['suite_name'] . ' Floor Plan at ' . get_the_title() . ' - ' . $floorplan['size'] . ' sq.ft'; ?>" src="<?= get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder-square.jpg' ?>" data-original="<?= $thumbnail[0] ?>" data-fullsizeimage="<?= $fullimage[0] ?>" data-fullw="<?= $fullimage[1] ?>" data-fullh="<?= $fullimage[2] ?>" />
			</a>
			<div class="floorplan__thumbnail-overlay hidden-xs">
				<a class="quick-view noLightbox" data-thumb="<?= $thumbnail[0] ?>" href="<?= $quick_view_url ?>"><i class='fa fa-fw fa-search-plus'></i><br>Quick View</a>
				<a class="view-all" href="<?= get_floorplans_link() ?>"><i class='fa fa-fw fa-angle-right'></i><br>See All Plans</a>
				<a class="full-info" href="<?= get_floorplans_link( $floorplan['image'] ) ?>"><i class='fa fa-fw fa-angle-right'></i><br>Full Info</a>
			</div>
		</div>

		<?php /*
		<?php if( $availability == 'available' ): ?>
		<div class="simpleflex">
			<a class="floorplan_save"><i class="fa fa-heart-o fa-lg"></i></a>
			<a class="floorplan__latest-pricing visible-xs" href="<?= $data_attributes['floorplan-url']; ?>">
				Buy
			</a>
		</div>
		<?php endif; ?>
		*/ ?>
	</div>

	<div class="grid-entry-right">

		<div class="floorplan__title">

			<?php if ($availability == 'available'): ?>
				<a href="<?= $data_attributes['floorplan-url'] ?>">
			<?php endif; ?>

			<?php /*
			<span class="floorplan__availability hidden-xs">
				<i class='fa fa-circle'></i>
				<span class="popup"><?= $floorplan['availability'] ?></span>
			</span>
			*/ ?>

			<span class="title"><?= $title ?></span>

			<span class="floorplan__availability--label"></span>

			<?php if ($availability == 'available'): ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="floorplan__info">
			<?php if ($floorplan['beds']): ?>
				<span class="floorplan__beds"><?= $floorplan['beds'] ?> Bed</span>
			<?php endif; ?>
			<?php if ($floorplan['baths']): ?>
				<span class="floorplan__baths"><?= $floorplan['baths'] ?> Bath</span>
			<?php endif; ?>
			<?php if ($floorplan['size']): ?>
				<span class="floorplan__size"><?= $floorplan['size'] ?> sq.ft.</span>
			<?php endif; ?>
			<br>
			<?php if( isset( $data_attributes['exposure'] ) ): ?>
				<span class="floorplan__exposure"><?= $data_attributes['exposure']; ?></span>
			<?php endif; ?>
			<span class="floorplan__floors">Floors <?= floorplan_floor_ranges($floorplan) ?></span>
		</div>

		<div class="floorplan__price">
			<?php if(get_field('hide_pricing')){ ?>

				Contact For Pricing
				<?php }else{ ?>
			<?php if( $floorplan['price'] && $availability == 'available' ): ?>
				<?php if( !platinum_access() ): ?>
					$<?= number_format( $floorplan['price'] ); ?>
					<small>
						$<?= $ppsqft; ?>/ft
					</small>
				<?php else: ?>
					<a href="<?= leadpages_form_url() ?>" data-leadbox="<?= leadpages_form_data_id() ?>">
						$
						<img src="<?= get_template_directory_uri(); ?>/assets/images/icon-lock-small.svg" alt="Unlock Price">
						Unlock Price
					</a>
				<?php endif; ?>
			<?php elseif( $availability == 'available' ): ?>
				$TBC
			<?php else: ?>
				Sold Out
			<?php endif; ?>
			<?php } ?>
		</div>

		<?php if( $availability == 'available' ): ?>
			<a class="floorplan__latest-pricing hidden-xs" href="<?= $data_attributes['floorplan-url']; ?>">More Info</a>
		<?php endif; ?>

	</div>

</div>
