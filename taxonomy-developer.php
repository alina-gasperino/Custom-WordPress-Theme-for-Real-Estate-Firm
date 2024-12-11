<?php
global $avia_config;

get_header();

$developer = get_queried_object();
$projects = TalkCondo_Ajax_Map_Query::get_projects([
	'developer' => [$developer->slug],
	'salesstatus' => ['selling', 'registration-phase', 'developer-sold-out', 'resale'],
]);

foreach ($projects as $project) {
	if ($project['coords'][0] != 0 && $project['coords'][1] != 0) {
		$coords[] = [
			'lat' => $project['coords'][1],
			'lng' => $project['coords'][0],
		];
	}
}

if ($coords) {
	$lat = array_sum(array_column($coords, 'lat')) / count($coords);
	$lng = array_sum(array_column($coords, 'lng')) / count($coords);
}

global $wp_query;

$total_projects = count($wp_query->posts);
$total_pre_construction = 0;
$total_under_construction = 0;
$total_complete = 0;
$total_selling = 0;
$total_registration_phase = 0;
$total_sold_out = 0;
$total_resale = 0;
foreach ($wp_query->posts as $entry) {
	if (has_term( 'pre-construction', 'status', $entry->ID )) $total_pre_construction++;
	if (has_term( 'under-construction', 'status', $entry->ID )) $total_under_construction++;
	if (has_term( 'complete', 'status', $entry->ID )) $total_complete++;
	if (has_term( 'selling', 'salesstatus', $entry->ID )) $total_selling++;
	if (has_term( 'registration-phase', 'salesstatus', $entry->ID )) $total_registration_phase++;
	if (has_term( 'developer-sold-out', 'salesstatus', $entry->ID )) $total_sold_out++;
	if (has_term( 'resale', 'salesstatus', $entry->ID )) $total_resale++;
}
?>

<div class='container_wrap container_wrap_first main_color'>

	<div class='container'>

		<div class="developer-header">
			<div class="developer_logo_wrap">
				<?php if ($logo = get_field('logo', get_queried_object())): ?>
				<div class="developer_logo">
					<img src="<?= $logo['sizes']['medium'] ?>" alt="<?= single_term_title() ?>">
				</div>
				<?php endif ?>
				<div class="developer_title">
					<span>DEVELOPER</span>
					<h1><?php single_term_title() ?></h1>
				</div>
			</div>

			<?php if ($website = get_field('website', get_queried_object())): ?>
			<div class="developer_link">
				<a target="_blank" href="<?= $website ?>" class="btn btn-round btn-gray"><i class="fa fa-arrow-circle-o-right fa-2x"></i> Visit Developers Website</a>
			</div>
			<?php endif ?>

		</div>

		<p class="project-statuses">
			<a class="btn btn-round btn-selling active" data-taxonomy="selling"><i class="fa fa-circle fa-lg"></i> Selling (<?= $total_selling ?>)</a>
			<a class="btn btn-round btn-registration-phase active" data-taxonomy="registration-phase"><i class="fa fa-circle fa-lg"></i> Registration Phase (<?= $total_registration_phase ?>)</a>
			<a class="btn btn-round btn-sold-out active" data-taxonomy="sold-out"><i class="fa fa-circle fa-lg"></i> Developer Sold Out (<?= $total_sold_out ?>)</a>
			<a class="btn btn-round btn-resale active" data-taxonomy="resale"><i class="fa fa-circle fa-lg"></i> Resale (<?= $total_resale ?>)</a>
		</p>
	</div>

	<?php
	$atts = [
		'lat' => $lat,
		'lng' => $lng,
		'term' => get_queried_object()->slug,
		'taxonomy' => get_queried_object()->taxonomy,
		'baseurl' => home_url(),
	];
	?>

	<!--googleoff: index-->
	<div class="project-map" <?= html_data_attributes($atts) ?>>
		<div class="filters hide">
			<div class="project-filter filter-button salesstatus-filter active filtered">
				<div class="submenu">
					<div class="filter-section salesstatus-options">
						<div class="filter-options">
							<button class="filter-option active filter-selling" data-taxonomy="salesstatus" data-term="selling">Selling</button>
							<button class="filter-option active filter-registration-phase" data-taxonomy="salesstatus" data-term="registration-phase">Registration Phase</button>
							<button class="filter-option active filter-sold-out" data-taxonomy="salesstatus" data-term="developer-sold-out">Developer Sold Out</button>
							<button class="filter-option active filter-resale" data-taxonomy="salesstatus" data-term="resale">Resale</button>
						</div>
					</div>
				</div>
			</div>
			<div class="project-filter floorplan-filter filter-button more-options filtered">
				<span class="count"></span>
				<div class="submenu">
					<div class="filter-section">
					<span class="filter-option rounded active" data-taxonomy="developer" data-term="<?= $developer->slug ?>"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="map-inner">
			<div class="map-canvas-container" style="height: 500px;">
				<div class='map-canvas'></div>
			</div>

			<div class="region-hover"></div>
			<div class="project-hover"></div>
			<div class="project-infocard"></div>
		</div>
	</div>
	<!--googleon: index-->

	<?php
	if ($city = get_the_terms( get_the_ID(), 'city' )) {
		$city = reset($city);
		$link = get_term_link( $city->slug, $city->taxonomy );
	}
	?>

	<div class='condos'>
		<main class='template-portfolio site-content'>
			<div class="entry-content-wrapper clearfix">
				<header>
					<h1 class="heading">
						<?php esc_html_e( 'Condo Projects', 'talkcondo' ) ?>
					</h1>
					<a href="<?= site_url('/map?developer='.$developer->slug) ?>" class="btn btn-gray hidden-xs hidden-sm">
						<i class="fa fa-angle-right fa-lg"></i> View All <?= str_replace('Developments', '', single_term_title('', false)) ?> Condos on Map
					</a>
					<a href="<?= site_url('/map?developer='.$developer->slug) ?>" class="btn btn-gray hidden-md hidden-lg">
						<i class="fa fa-angle-right fa-lg"></i> View All on Map
					</a>
				</header>
				<?php
				while ( $wp_query->have_posts() ): $wp_query->the_post();
					ob_start();
					get_template_part( 'templates/developer/project' );
					if (has_term( 'resale', 'salesstatus', get_the_ID() )) $resale[] = ob_get_contents();
					if (has_term( 'selling', 'salesstatus', get_the_ID() )) $selling[] = ob_get_contents();
					if (has_term( 'launching-soon', 'salesstatus', get_the_ID() )) $launching_soon[] = ob_get_contents();
					if (has_term( 'registration-phase', 'salesstatus', get_the_ID() )) $registration_phase[] = ob_get_contents();
					if (has_term( 'developer-sold-out', 'salesstatus', get_the_ID() )) $developer_sold_out[] = ob_get_contents();
					ob_get_clean();
				endwhile;

				if ( $wp_query->have_posts() ): ?>

					<?php if( $selling && count($selling)>0):?>
					<div class="condos-group selling_construction" data-animation="fadeIn">
						<h2 class="heading">Pre-Construction Condos Currently Selling (<?= count($selling) ?>)</h2>
						<div id="selling_construction" class="project-carousel condos-slider gscroll">
							<div class="condos-slider__scroller">
							<?php foreach($selling as $construction){
								echo $construction;
							}?>
							</div>
						</div>
					</div>
					<?php endif ?>

					<?php if ($launching_soon && count($launching_soon)>0):?>
					<div class="condos-group launching_soon" data-animation="fadeIn">
					<h2 class="heading">Pre-Construction Condos Launching Soon (<?= count($launching_soon) ?>)</h2>
						<div id="launching_soon" class="project-carousel condos-slider gscroll">
							<div class="condos-slider__scroller">
							<?php foreach($launching_soon as $construction){
								echo $construction;
							}?>
							</div>
						</div>
					</div>
					<?php endif ?>

					<?php if ($resale && count($resale)>0): ?>
					<div class="condos-group resale_construction" data-animation="fadeIn">
						<h2 class="heading">Completed Resale Condo Buildings (<?= count($resale) ?>)</h2>
						<div id="resale_construction" class="project-carousel condos-slider gscroll">
							<div class="condos-slider__scroller">
							<?php foreach($resale as $construction){
								echo $construction;
							}?>
							</div>
						</div>
					</div>
					<?php endif ?>

					<?php if ($registration_phase && count($registration_phase)>0): ?>
					<div class="condos-group registration_phase" data-animation="fadeIn">
						<h2 class="heading">Pre-Construction Condos Registration Phase (<?= count($registration_phase) ?>)</h2>
						<div id="registration_phase" class="project-carousel condos-slider gscroll">
							<div class="condos-slider__scroller">
							<?php foreach($registration_phase as $construction){
								echo $construction;
							}?>
							</div>
						</div>
					</div>
					<?php endif ?>

					<?php if ($developer_sold_out && count($developer_sold_out)>0): ?>
					<div class="condos-group developer_sold_out" data-animation="fadeIn">
						<h2 class="heading">Developer Sold Out (<?= count($developer_sold_out) ?>)</h2>
						<div id="developer_sold_out" class="project-carousel condos-slider gscroll">
							<div class="condos-slider__scroller">
							<?php foreach($developer_sold_out as $construction){
								echo $construction;
							}?>
							</div>
						</div>

					</div>
					<?php endif ?>
				<?php endif ?>
			</div>

			<div id="recently-updated-projects">
				<a name='recently-updated-projects'></a>
				<div class="container">
					<h3 class='title'>Recently Updated Projects by <?php single_term_title() ?></h3>
					<?php $taxonomy = get_query_var('taxonomy') ?>
					<?php $term = get_query_var($taxnoomy) ?>
					<div class="projects"></div>
					<a class='loadmore' href="<?= admin_url('admin-ajax.php') ?>" data-action='project_updates_by_region' data-count='5' data-paged='1' data-taxonomy='<?= $taxonomy ?>' data-term='<?= $term ?>'>Load More...</a>
				</div>
			</div>

		</main>
	</div>

</div><!-- close default .container_wrap element -->


<?php get_footer() ?>

<script>
jQuery(function($){
	var talkmap = $('.project-map').talkMap({
		mapOptions: {
			scrollwheel: false,
			draggable: true
		},
		projectCard: false,
		cluster: false,
		zoomThreshold: 0,
		verticalcutoff: 45,
		regions: false,
		regionHover: false,
		regionHoverLabel: false,
		singleProject: false,
		projects: <?= json_encode($projects) ?>
	});
	$('.project-statuses .btn').click(function(e){
		e.preventDefault();
		var $this = $(this);
		var tax = $this.data('taxonomy');
		$this.toggleClass('active');
		$('.salesstatus-options .filter-'+tax).trigger('click');
	})
});
</script>
