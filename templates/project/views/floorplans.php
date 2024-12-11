<?php
$floorplans = get_field('floorplans');
$floorplans = sort_floorplans($floorplans);

?>

<?php if (platinum_access()): ?>
	<?php get_template_part('templates/project/card-floorplans-platinum-access'); ?>
<?php elseif (have_rows('floorplans')): ?>
	<?php get_template_part('templates/project/card-floorplans-compact'); ?>
<?php else: ?>
	<?php get_template_part('templates/project/card-floorplans-none'); ?>
<?php endif; ?>

<?php //get_template_part('templates/project/card-googledrive-grid'); ?>