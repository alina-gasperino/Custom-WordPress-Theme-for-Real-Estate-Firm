<?php

/**
 * Template Part: Map
 *
 * @package    Project
 * @subpackage Panel
 */

$cities = get_the_terms( get_the_ID(), 'city' );
$city = reset($cities);
?>

<div class="panel panel-default">

	<div class="panel-heading" role="tab" id="collapseMapHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapseMap" aria-expanded="true" aria-controls="collapseMap">
				<?= get_field('address') ?>, <?= $city->name ?>
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseMap" aria-expanded="true" aria-controls="collapseMap">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>

	<div id="collapseMap" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseMapHeading">

		<div class="panel-body">

			<div class="card" data-animation="fadeIn">

				<div class="card__content">

					<?php
						$data_attributes = array(
							'region'    => $city->slug,
							'projectid' => get_the_ID(),
							'lat'       => get_field('lat'),
							'lng'       => get_field('lng'),
							'zoom'      => 16,
						);
					?>

					<div class="project-map" <?= html_data_attributes( $data_attributes ) ?>>
						<div class="map-inner">
							<div class="map-canvas-container" style='height: 300px;'>
								<div class='map-canvas'></div>
							</div>
						</div>

						<div class="map-nav" style="top:10px">
							<div class="zoom-in"><i class="material-icons">add</i></div>
							<div class="zoom-out"><i class="material-icons">remove</i></div>
							<!-- <div class="toggle location"><i class="material-icons">my_location</i></div> -->
						</div>


						<div class="map-buttons">
							<a href="<?= project_map_jump_to_link( get_the_ID() ) ?>" class="btn btn-primary">
								<i class="far fa-map"></i> View on Map
							</a>
						</div>
					</div>

				</div>

			</div>

		</div>

	</div>

</div>


<script>
jQuery(function($){
	$('.project-map').talkMap({
		regions: false,
		mapOptions: {
			scrollwheel: false,
			draggable: false
		},
	});
});
</script>
