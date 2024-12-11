<?php
$floorplans = TalkCondo_Ajax_Floorplan_Query::get_results([
	'salesstatus' => ['platinum-access'],
	'max_price' => 600000,
	'per_page' => 30,
	'sort' => 'size',
	'sort_dir' => 'asc',
]);
?>

<div class="condos-group" data-animation="fadeIn">

	<header>
		<h3 class="heading">Platinum Access Below $600,000</h3>
	</header>

	<?php if ( $floorplans ): ?>

	<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
		<div class="condos-slider__scroller">
			<?php foreach ($floorplans[0]->results as $floorplan): ?>
				<?php include locate_template( 'templates/home/carousel-floorplan.php' ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<?php endif ?>

</div>
