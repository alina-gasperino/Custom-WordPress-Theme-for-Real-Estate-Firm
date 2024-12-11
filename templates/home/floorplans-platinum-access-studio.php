<?php
$floorplans = TalkCondo_Ajax_Floorplan_Query::get_results([
	'salesstatus' => ['platinum-access'],
	'max_beds' => 0.1,
	'min_beds' => 0,
	'per_page' => 30,
	'sort' => 'size',
	'sort_dir' => 'asc',
]);
?>

<div class="condos-group" data-animation="fadeIn">

	<header>
		<h3 class="heading">Platinum Access Studio</h3>
	</header>

<!--    <pre style="display:none">-->
<!--    --><?php
//    $res = $floorplans[0]->results;
//
//    echo json_encode($res);
//
//    ?>
<!--    </pre>-->


	<?php if ( $floorplans ): ?>

    <?php $options = array("show_0_beds" => true); ?>

	<div id="condos-slider--new" class="project-carousel condos-slider gscroll">
		<div class="condos-slider__scroller">
			<?php foreach ($floorplans[0]->results as $n => $floorplan): ?>
				<?php include locate_template( 'templates/home/carousel-floorplan.php' ) ?>
			<?php endforeach ?>
		</div>
	</div>

	<?php endif ?>

</div>
