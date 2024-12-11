<?php
global $wpdb;

$attachment_id = filter_var($_GET['attachment_id'], FILTER_SANITIZE_NUMBER_INT);
$project_id = $wpdb->get_var( $wpdb->prepare("select post_parent from $wpdb->posts where ID = %d", $attachment_id) );

$floorplan_image = (wp_get_attachment_image_src($attachment_id, 'full')[0]) ?: get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder.jpg';
if (is_local()) $floorplan_image = str_replace(home_url(), 'https://www.talkcondo.com', $floorplan_image);

$floorplans = get_field('floorplans', $project_id, true);
$floorplans = sort_floorplans($floorplans);
$current = [];
foreach ($floorplans as $f) {
	if ($f['image'] == $attachment_id) {
		$current = $f;
		break;
	}
}
if ($current['availability'] == 'Available') {
	$class = 'available';
} elseif ($current['availability'] == 'Sold Out') {
	$class = 'sold-out';
}
?>

<div class="floorplan-quickview__header <?php echo $class ?>">
		<div class="header simpleflex">
			<div class="floorplan-quickview__header-left">
				<span class="floorplan-quickview__close"><i class='fa fa-close'></i></span>
			</div>
			<div class="floorplan-quickview__header-center">
				<h3>
					<span class="floorplan__availability">
						<i class='fa fa-circle'></i>
						<span class="popup"><?php echo get_sub_field('availability') ?></span>
					</span>
					<?php if ($current['size']): ?>
						<?php echo $current['size'] . ' sq.ft ' ?>
					<?php endif; ?>

					<?php if ($current['beds'] && $current['baths']): ?>
						<?php echo $current['beds'] . ' bed, ' . $current['baths'] . ' bath ' ?>
					<?php elseif ($current['beds']): ?>
						<?php echo $current['beds'] . ' bed ' ?>
					<?php elseif ($current['baths']): ?>
						<?php echo $current['baths'] . ' bath ' ?>
					<?php endif ?>

					<?php if ($current['exposure']): ?>
						<?php echo  implode(', ', $current['exposure']) ?>
					<?php endif ?>
				</h3>
				<h4><span><?php echo $current['suite_name'] ?></span> at <span><?php the_title($project_id) ?></span></h4>
			</div>
			<div class="floorplan-quickview__header-right">
				<!-- <span class="next"><i class='fa fa-chevron-right'></i> </span> -->
			</div>
		</div>
</div>

<div class="floorplan-quickview__image">
	<img src="<?php echo $floorplan_image ?>">
</div>

<div class="floorplan-quickview__footer">
	<?php foreach (get_field('floorplans', $project_id) as $f): ?>
		<?php $thumb = wp_get_attachment_image_src($f['image'], 'thumbnail')[0] ?>
		<?php if (is_local()) $thumb = str_replace(home_url(), 'https://www.talkcondo.com', $thumb) ?>
		<?php $active = ($f['image'] == $attachment_id) ? 'active' : '' ?>
		<img class="<?php echo $active ?>" src="<?php echo $thumb ?>">
	<?php endforeach ?>
</div>
