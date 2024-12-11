<?php

$limit = 30;

$projects = TalkCondo_Ajax_Map_Query::get_projects([
	'include_floorplans' => true,
	// 'salesstatus' => ['platinum-access'],
	'max_beds' => 2,
	'min_beds' => 2,
	'exposure' => ['South', 'South West', 'South East'],
	'max_size' => 2000,
	'city' => ['toronto'],
]);

$floorplans = [];
foreach ($projects as $project) {
	foreach ($project->floorplans as $floorplan) {
		$p = $project;
		unset($p->floorplans);
		$floorplan->project = $p;
		$floorplans[] = $floorplan;
	}
}

usort($floorplans, function($a, $b) {
	return $a['price'] <= $b['price'] ? -1 : 1;
});

?>

<div class="condos-group" data-animation="fadeIn">

	<header>
		<h3 class="heading">TEST: one bedroom, under 1000 sq.ft, south exposure, in toronto</h3>
	</header>

	<?php if ( $floorplans ): ?>
	<?php $i = 1 ?>

	<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
		<div class="condos-slider__scroller">
			<?php foreach ($floorplans as $n => $floorplan): ?>
				<?php if ($n > $limit) break; ?>
				<?php include locate_template( 'templates/home/carousel-floorplan.php' ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<?php else:  ?>

		<div>No results</div>

	<?php endif ?>

</div>

<?php

$projects = TalkCondo_Ajax_Map_Query::get_projects([
	'include_floorplans' => true,
	'salesstatus' => ['platinum-access'],
	'max_price' => 500000,
]);

$floorplans = [];
foreach ($projects as $project) {
	foreach ($project->floorplans as $floorplan) {
		$p = $project;
		unset($p->floorplans);
		$floorplan->project = $p;
		$floorplans[] = $floorplan;
	}
}

usort($floorplans, function($a, $b) {
	return $a['price'] <= $b['price'] ? -1 : 1;
});

?>

<div class="condos-group" data-animation="fadeIn">

	<header>
		<h3 class="heading">TEST: platinum access, under $500,000</h3>
	</header>

	<?php if ( $floorplans ): ?>
	<?php $i = 1 ?>

	<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
		<div class="condos-slider__scroller">
			<?php foreach ($floorplans as $n => $floorplan): ?>
				<?php if ($n > $limit) break; ?>
				<?php include locate_template( 'templates/home/carousel-floorplan.php' ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<?php else:  ?>

		<div>No results</div>

	<?php endif ?>

</div>

<?php

$projects = TalkCondo_Ajax_Map_Query::get_projects([
	'include_floorplans' => true,
	'salesstatus' => ['platinum-access'],
	'min_beds' => 2,
	'max_beds' => 2,
]);

$floorplans = [];
foreach ($projects as $project) {
	foreach ($project->floorplans as $floorplan) {
		$p = $project;
		unset($p->floorplans);
		$floorplan->project = $p;
		$floorplans[] = $floorplan;
	}
}

usort($floorplans, function($a, $b) {
	return $a['price'] <= $b['price'] ? -1 : 1;
});

?>

<div class="condos-group" data-animation="fadeIn">

	<header>
		<h3 class="heading">TEST: platinum access, two bedroom</h3>
	</header>

	<?php if ( $floorplans ): ?>
	<?php $i = 1 ?>

	<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
		<div class="condos-slider__scroller">
			<?php foreach ($floorplans as $n => $floorplan): ?>
				<?php if ($n > $limit) break; ?>
				<?php include locate_template( 'templates/home/carousel-floorplan.php' ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<?php else:  ?>

		<div>No results</div>

	<?php endif ?>

</div>
