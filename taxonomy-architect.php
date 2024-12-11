<?php
global $avia_config;

get_header();

echo avia_title(array('title' => avia_which_archive()));

// $mapdata = generateMapData($wp_query);
$mapdata = get_map_data('architect', get_queried_object()->slug);
?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

	<div class='container'>
		<?php global $wp_query; ?>
		<?php $total_projects = count($wp_query->posts) ?>
		<?php $total_pre_construction = 0; ?>
		<?php $total_under_construction = 0; ?>
		<?php $total_complete = 0; ?>
		<?php $total_selling = 0; ?>
		<?php $total_registration_phase = 0; ?>
		<?php $total_sold_out = 0; ?>
		<?php $total_resale = 0; ?>
		<?php foreach ($wp_query->posts as $entry): ?>
			<?php if (has_term( 'pre-construction', 'status', $entry->ID )) $total_pre_construction++ ?>
			<?php if (has_term( 'under-construction', 'status', $entry->ID )) $total_under_construction++ ?>
			<?php if (has_term( 'complete', 'status', $entry->ID )) $total_complete++ ?>
			<?php if (has_term( 'selling', 'salesstatus', $entry->ID )) $total_selling++ ?>
			<?php if (has_term( 'registration-phase', 'salesstatus', $entry->ID )) $total_registration_phase++ ?>
			<?php if (has_term( 'developer-sold-out', 'salesstatus', $entry->ID )) $total_sold_out++ ?>
			<?php if (has_term( 'resale', 'salesstatus', $entry->ID )) $total_resale++ ?>
		<?php endforeach ?>

		<div class="developer-header">
			<div class="developer_logo_wrap">
				<?php if ($logo = get_field('logo', get_queried_object())): ?>
				<div class="developer_logo">
					<img src="<?php echo $logo['sizes']['medium'] ?>" alt="<?php echo single_term_title() ?>">
				</div>
				<?php endif; ?>
				<div class="developer_title">
					<span>DEVELOPER</span>
					<h1><?php single_term_title() ?></h1>
				</div>
			</div>

			<?php if ($website = get_field('website', get_queried_object())): ?>
			<div class="developer_link">
				<a target="_blank" href="<?php echo $website ?>" class="btn btn-round btn-gray"><i class="fa fa-arrow-circle-o-right fa-2x"></i> Visit Developers Website</a>
			</div>
			<?php endif; ?>

		</div>

		<p class="project-statuses">
			<a class="btn btn-round btn-selling active" data-taxonomy="selling"><i class="fa fa-circle fa-lg"></i> Selling(<?php echo $total_selling ?>)</a>
			<a class="btn btn-round btn-registration-phase active" data-taxonomy="registration-phase"><i class="fa fa-circle-o fa-lg"></i> Registration Phase(<?php echo $total_registration_phase ?>)</a>
			<a class="btn btn-round btn-sold-out active" data-taxonomy="sold-out"><i class="fa fa-circle fa-lg"></i> Developer Sold Out(<?php echo $total_sold_out ?>)</a>
			<a class="btn btn-round btn-resale active" data-taxonomy="resale"><i class="fa fa-circle fa-lg"></i> Resale(<?php echo $total_resale ?>)</a>
		</p>
	</div>

	<!--googleoff: index-->
	<div class="project-map" data-baseurl="<?php echo home_url() ?>">
		<div class="filters hide">
			<div class="project-filter filter-button salesstatus-filter active filtered">
				<div class="submenu">
					<div class="filter-section salesstatus-options">
						<div class="filter-options">
							<button class="filter-option active filter-selling" data-taxonomy="salesstatus" data-term="selling">Selling</button>
							<button class="filter-option active filter-registration-phase" data-taxonomy="salesstatus" data-term="registration-phase">Registration Phase</button>
							<button class="filter-option  active filter-sold-out" data-taxonomy="salesstatus" data-term="developer-sold-out">Developer Sold Out</button>
							<button class="filter-option  active filter-resale" data-taxonomy="salesstatus" data-term="resale">Resale</button>
						</div>
					</div>
				</div>
			</div>
			<div class="project-filter floorplan-filter filter-button more-options filtered">
				<span class="count"></span>
				<div class="submenu">
					<div class="filter-section">
					<span class="filter-option rounded active" data-taxonomy="developer" data-term="lanterra-developments"></span>
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

	<?php if ($city = get_the_terms( get_the_ID(), 'city' )): ?>
		<?php $city = reset($city); ?>
		<?php $link = get_term_link( $city->slug, $city->taxonomy ); ?>
<!-- 		<div class='browse-all-projects'>
			<a href="<?php echo $link ?>"><i class="fa fa-arrow-circle-right"></i> Browse All Projects on Large Map</a>
		</div> -->
	<?php endif; ?>

	<div class='condos'>
		<main class='template-portfolio site-content  <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content','post_type'=>'portfolio'));?>>
			<div class="entry-content-wrapper clearfix">
				<header>
					<h1 class="heading">
						<?php esc_html_e( 'Condo Projects', 'talkcondo' ); ?>
					</h1>
					<a href="<?php echo site_url('/map?architect='.get_queried_object()->slug);?>" class="btn btn-gray">
						<i class="fa fa-angle-right fa-lg"></i> View All CentreCourt Condos on Map
					</a>
				</header>
				<?php
				// $params = array(
				// 	'linking' 	=> '',
				// 	'columns' 	=> '4',
				// 	'items'		=> '16',
				// 	'contents' 	=> 'title',
				// 	'sort' 		=> 'yes',
				// 	'paginate' 	=> 'yes',
				// 	'taxonomy'  => 'city',
				// 	'set_breadcrumb' => false,
				// );
				// $grid = new talk_project_grid( $params );
				// $grid->use_global_query();
				// echo $grid->html();
				// $query = new WP_Query( array(
				// 	'post_type' => 'project',
				// 	'posts_per_page' => 12,
				// 	'tax_query' => array(
				// 		array(
				// 			'taxonomy' => 'developer',
				// 			'field' => 'slug',
				// 			'terms' => get_query_var('term'),
				// 		)
				// 	),
				// ) );
				while ( $wp_query->have_posts() ): $wp_query->the_post();
					ob_start();
					get_template_part( 'templates/developer/project' );
					if (has_term( 'Selling', 'salesstatus', get_the_ID() )) $selling[]=ob_get_contents();
					if (has_term( 'launching-soon', 'salesstatus', get_the_ID() )) $launching_soon[]=ob_get_contents();
					if (has_term( 'resale', 'salesstatus', get_the_ID() )) $resale[]=ob_get_contents();
					if (has_term( 'registration-phase', 'salesstatus', get_the_ID() )) $registration_phase[]=ob_get_contents();
					if (has_term( 'developer-sold-out', 'salesstatus', get_the_ID() )) $developer_sold_out[]=ob_get_contents();
					ob_get_clean();
				endwhile;

				if ( $wp_query->have_posts() ): ?>
					<?php if(count($selling)>0):?>
					<div class="selling_construction" data-animation="fadeIn">
						<h2>Pre-Construction Condos Currently Selling (<?php echo count($selling);?>)</h2>
						<div id="selling_construction" class="project-carousel condos-slider">
							<div class="condos-slider__scroller">
							<?php foreach($selling as $construction){
								echo $construction;
							}?>

							</div>
						</div>

					</div>
					<?php endif;?>
					<?php if(count($launching_soon)>0):?>
					<div class="launching_soon" data-animation="fadeIn">
					<h2>Pre-Construction Condos Launching Soon (<?php echo count($launching_soon);?>)</h2>
						<div id="launching_soon" class="project-carousel condos-slider">
							<div class="condos-slider__scroller">
							<?php foreach($launching_soon as $construction){
								echo $construction;
							}?>

							</div>
						</div>

					</div>
					<?php endif;?>
					<?php if(count($resale)>0):?>
					<div class="resale_construction" data-animation="fadeIn">
						<h2>Completed Resale Condo Buildings (<?php echo count($resale);?>)</h2>
						<div id="resale_construction" class="project-carousel condos-slider">
							<div class="condos-slider__scroller">
							<?php foreach($resale as $construction){
								echo $construction;
							}?>

							</div>
						</div>

					</div>
					<?php endif;?>
					<?php if(count($registration_phase)>0):?>
					<div class="registration_phase" data-animation="fadeIn">
						<h2>Pre-Construction Condos Registration Phase (<?php echo count($registration_phase);?>)</h2>
						<div id="registration_phase" class="project-carousel condos-slider">
							<div class="condos-slider__scroller">
							<?php foreach($registration_phase as $construction){
								echo $construction;
							}?>

							</div>
						</div>

					</div>
					<?php endif;?>
					<?php if(count($developer_sold_out)>0):?>
					<div class="developer_sold_out" data-animation="fadeIn">
						<h2>Developer Sold Out (<?php echo count($developer_sold_out);?>)</h2>
						<div id="developer_sold_out" class="project-carousel condos-slider">
							<div class="condos-slider__scroller">
							<?php foreach($developer_sold_out as $construction){
								echo $construction;
							}?>

							</div>
						</div>

					</div>
					<?php endif;?>
				<?php endif; ?>
			</div>

			<div id="recently-updated-projects">
				<a name='recently-updated-projects'></a>
				<div class="container">
					<h3 class='title'>Recently Updated Projects by <?php single_term_title() ?></h3>
					<?php $taxonomy = get_query_var('taxonomy'); ?>
					<?php $term = get_query_var($taxnoomy); ?>
					<div class="projects"></div>
					<a class='loadmore' href="<?php echo admin_url('admin-ajax.php') ?>" data-action='project_updates_by_region' data-count='5' data-paged='1' data-taxonomy='<?php echo $taxonomy ?>' data-term='<?php echo $term ?>'>Load More...</a>
				</div>
			</div>

		</main>
	</div>

</div><!-- close default .container_wrap element -->


<?php $mapdata = json_decode( json_encode($mapdata) ) ?>
<?php include 'templates/template-leadpagesforms.php' ?>

<?php get_footer(); ?>

<?php echo '<script>var mapData = ' . json_encode($mapdata) . '</script>' ?>
<?php
// include get_stylesheet_directory() . '/templates/template-leadpagesforms.php';
$args = array('architect' => array(get_queried_object()->slug));?>
<script>
jQuery(function($){
	var takmap = $('.project-map').talkMap({
		mapOptions: {
			scrollwheel: false,
			draggable: true
		},
		markerIconActive: {
			url: '<?php echo get_template_directory_uri(); ?>/assets/images/marker-blue.png',
			size: new google.maps.Size(16, 24),
			scaledSize: new google.maps.Size(16, 24)
		},
		markerIconActiveHover: {
			url: '<?php echo get_template_directory_uri(); ?>/assets/images/marker-orange.png',
			size: new google.maps.Size(16, 24),
			scaledSize: new google.maps.Size(16, 24)
		},
		mapData: mapData,
		projectCard: false,
		zoomThreshold: 0,
		verticalcutoff: 45,
		regions: false,
		regionHover: false,
		regionHoverLabel: false,
		singleProject: true,
		projects: <?php echo json_encode( TalkCondo_Ajax_Map_Query::get_projects( $args ) ); ?>
	});
	$('.project-statuses .btn').click(function(e){
		e.preventDefault();
		var tax = $(this).data('taxonomy');
		$(this).toggleClass('active');
		$('.salesstatus-options .filter-'+tax).trigger('click');
	})
});
</script>