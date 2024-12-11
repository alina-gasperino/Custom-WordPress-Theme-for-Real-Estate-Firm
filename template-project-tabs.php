<div class="tabcontainer top_tab">
	<?php $n = 1 ?>
	<section class='av_tab_section'>
		<div class="tab active_tab" data-fake-id="#tab-id-<?php echo $n ?>">Details</div>
		<div id="tab-id-<?php echo $n ?>-container" class='tab_content active_tab_content tab_content_table'>
			<div class="tab_inner_content invers-color">
				<?php include('templates/components/tab-project-details.php'); ?>
			</div>
		</div>
	</section>

	<?php $n++ ?>
	<section class='av_tab_section'>
		<div class="tab" data-fake-id="#tab-id-<?php echo $n ?>">Highlights</div>
		<div id="tab-id-<?php echo $n ?>-container" class='tab_content'>
			<div class="tab_inner_content invers-color" itemprop="description">
				<?php if ($projecthighlights = get_post_meta( get_the_ID(), 'projecthighlights', true)): ?>
					<?php echo nl2br($projecthighlights); ?>
				<?php else: ?>
					<?php echo get_the_title() . " is a new $type project by $developer located in $city. " . get_the_title() . " is currently in $status and will be completed in $occupancydate"; ?>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ($floorplans = get_post_meta( get_the_ID(), 'floorplansgallery', true )): ?>
	<?php $n++ ?>
	<section class='av_tab_section'>
		<div class="tab" data-fake-id="#tab-id-<?php echo $n ?>">Floor Plans</div>
		<div id="tab-id-<?php echo $n ?>-container" class='tab_content'>
			<div class="tab_inner_content invers-color">
				<?php include('templates/components/tab-project-floorplans.php'); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if (get_field('features')): ?>
	<?php $n++ ?>
	<section class='av_tab_section'>
		<div class="tab" data-fake-id="#tab-id-<?php echo $n ?>">Features</div>
		<div id="tab-id-<?php echo $n ?>-container" class='tab_content'>
			<div class="tab_inner_content invers-color">
				<?php echo nl2br(get_field('features')); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ($amenities = get_post_meta( get_the_ID(), 'amenities', true )): ?>
	<?php $n++ ?>
	<section class='av_tab_section'>
		<div class="tab" data-fake-id="#tab-id-<?php echo $n ?>">Amenities</div>
		<div id="tab-id-<?php echo $n ?>-container" class='tab_content'>
			<div class="tab_inner_content invers-color">
				<?php echo nl2br($amenities); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</div>
<div class='last-updated'>Data last updated:
	<?php echo get_the_modified_date('F jS, Y'); ?>
</div>
