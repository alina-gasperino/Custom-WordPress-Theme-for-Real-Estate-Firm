<?php
$city = reset(get_the_terms( get_the_ID(), 'city' ));
$mapdata = get_map_data('city', $city->slug);

$lat = get_post_meta( get_the_ID(), 'lat', true );
$lng = get_post_meta( get_the_ID(), 'lng', true );
$zoom = 16;
?>

<div class="project-map" data-region='<?= $city->slug ?>' data-lat="<?= $lat ?>" data-lng="<?= $lng ?>" data-zoom='<?= $zoom ?>' data-projectid='<?= get_the_ID() ?>'>
	<div class="map-inner">
		<div class="map-canvas-container" style='height: 500px;'>
			<div class='map-canvas'></div>
		</div>
	</div>
</div>

<?php $link = get_term_link( $city->slug, $city->taxonomy ) ?>
<div class='browse-all-projects'>
	<a href="<?= $link ?>"><i class="fa fa-arrow-circle-right"></i> Browse All Projects on Large Map</a>
</div>

<script>
jQuery(function($) {
	$('.project-map').talkMap({
		mapData: <?= json_encode($mapdata) ?>,
		mapOptions: {
			scrollwheel: false,
			draggable: false
		},
		markerIcon: {
			url: '<?= get_template_directory_uri() ?>/assets/images/marker-gray.png',
			size: new google.maps.Size(16, 24),
			scaledSize: new google.maps.Size(16, 24)
		},
		markerIconHover: {
			url: '<?= get_template_directory_uri() ?>/assets/images/total-marker-white.png',
			size: new google.maps.Size(24, 36),
			scaledSize: new google.maps.Size(24, 36)
		},
		markerIconActive: {
			url: '<?= get_template_directory_uri() ?>/assets/images/marker-orange.png',
			size: new google.maps.Size(16, 24),
			scaledSize: new google.maps.Size(16, 24)
		},
		markerIconActiveHover: {
			url: '<?= get_template_directory_uri() ?>/assets/images/total-marker-orange.png',
			size: new google.maps.Size(24, 36),
			scaledSize: new google.maps.Size(24, 36)
		},
		enableList: false,
		projectCard: false,
		zoomThreshold: 0,
		verticalcutoff: 45
	});
});
</script>

<?php include 'templates/template-leadpagesforms.php' ?>
