<?php

/*
 * Template Name: Map
 */

global $avia_config;
global $wp_query;
global $post;

add_filter( 'body_class', 'add_body_class' );

function add_body_class( $classes ) {
	$classes[] = 'fullpagemap';
	return $classes;
}

$obj = get_queried_object();
$mapdata = get_map_data();

$show_floorplans = isset($_GET['include_floorplans']) && !$_GET['include_floorplans'] ? false : true;

$lat = '';
$lng = '';
$zoom = 17;

if ($map_url = get_field('google_map_url', "{$obj->taxonomy}_{$obj->term_id}")) {
	list( $lat, $lng, $zoom ) = array_values(parse_google_map_url($map_url));
}

$url_zoom = intval($_GET['zoom']);
if($url_zoom > 0 && $url_zoom < 25)
    $zoom = $url_zoom;

if (isset($_GET['lat']) && isset($_GET['lng']) ) {
	$lat = filter_var($_GET['lat'], FILTER_VALIDATE_FLOAT) ?: $lat;
	$lng = filter_var($_GET['lng'], FILTER_VALIDATE_FLOAT) ?: $lng;
}
$center = (isset($_GET['center']) && $_GET['center']) ? filter_var($_GET['center'], FILTER_SANITIZE_STRING) : '';
$projectid = (isset($_GET['projectid']) && $_GET['projectid']) ? filter_var($_GET['projectid'], FILTER_VALIDATE_INT) : '';

$project_qry_args = [];
// if ($obj->taxonomy == 'city') $project_qry_args[$obj->taxonomy] = $obj->slug;

if ($show_floorplans) $project_qry_args['include_floorplans'] = true;
// $projects = TalkCondo_Ajax_Map_Query::get_projects( $project_qry_args );

get_header();

?>

<!--googleoff: index-->
<script>
    console.log("<?= $map_url ?>")
    console.log("<?= $zoom ?>")
    console.log("<?= $obj->taxonomy.'_'.$obj->term_id ?>")
</script>
<div class="project-map quickview-gallery fullpage sidebar-open-desktop" data-lat='<?= $lat ?>' data-lng='<?= $lng ?>' data-zoom='<?= $zoom ?>' data-taxonomy="<?= $obj->taxonomy ?>" data-term="<?= $obj->slug ?>" data-center="<?= $center ?>" data-projectid="<?= $projectid ?>" data-baseurl="<?= home_url() ?>">

	<div class="filters">

		<div class="mobile-header mobile-tablet">
			<h4><i class='fa fa-sliders-h'></i> Refine Results</h4>
			<button class="btn btn-link reset-filters">Reset</button>
			<button class="btn btn-link close-filters">Done</button>
		</div>

		<div class="project-filter floorplan-filter filter-box neighbourhood-filter" style="display: none;">
			<i class='fa fa-map-marker-alt'></i>
			<select name="neighbourhoods[]" multiple size='1' id="regions" class='regions filter' data-placeholder="Jump to Location or Project" placeholder="Jump to Location or Project" data-taxonomy='neighbourhood' style='min-width: 240px;'>
				<?php foreach (get_terms('neighbourhood') as $term): ?>
					<option value="<?= $term->term_id ?>" <?php if ($obj->taxonomy == 'neighbourhood' && $obj->slug == $term->slug) echo 'selected="selected"' ?> data-term="<?= $term->slug ?>"><?= $term->name ?></option>
				<?php endforeach ?>
			</select>
		</div>

        <?php
        $extra_filters = '';

        $extra_labels = [];
        /*
        if ( $mapdata->taxonomies->salesstatus ):

            // This needs to be done beforehand so that labels can be set
	        $labels = $extra_labels = [];
	        $html = '';

	        foreach ($mapdata->taxonomies->salesstatus as $term) {
		        $active = term_selected( $term );

		        $_html = sprintf('<button class="filter-option %s" data-taxonomy="%s" data-term="%s">%s</button>', $active, 'salesstatus', $term->slug, $term->name );

		        if ( 'resale' == $term->slug || 'developer-sold-out' == $term->slug ) {
			        $extra_filters .= $_html;

			        if( $active ) $extra_labels[] = $term->name;
		        } else {
			        $html .= $_html;

			        if( $active ) $labels[] = $term->name;
		        }
	        }
	        ?>
			<div class="project-filter floorplan-filter filter-button salesstatus-filter <?= $labels ? 'filtered' : '' ?>">
				<button class='toggle'>
					<span class='title' data-placeholder='Sales Status'>
                        <?= $labels ? implode( ', ', $labels ) : 'Sales Status' ?>
                    </span>
					<span class="count"></span>
					<i class="fa fa-fw fa-caret-down"></i>
				</button>
				<div class="submenu">
				<div class="filter-section salesstatus-options">
					<h4 class="heading">Sales Status</h4>
						<div class="filter-options"><?= $html ?></div>
					</div>
				</div>
			</div>
			<?php endif */ ?>
		<?php
		$sliders = array(
			'price'        => array( 'label' => 'Price Range', 'type' => [ 'project', 'floorplan' ]  ),
            'pricepersqft' => array( 'label' => 'Price per sq.ft.', 'type' => [ 'project', 'floorplan'] ),
			'size'         => array( 'label' => 'Suite Size', 'type' => [ 'project', 'floorplan' ]  ),
			'beds'         => array( 'label' => 'Beds', 'data' => 'float', 'type' => [ 'project', 'floorplan' ]  ),
			'baths'        => array( 'label' => 'Baths', 'data' => 'float', 'type' => [ 'project', 'floorplan' ]  ),
//			'deposit'      => array( 'label' => 'Deposit', 'type' => [ 'project', 'floorplan' ] ),
		);

		foreach ( $sliders as $slider => $args ) {
			filter_slider( $slider, $args );
        }

		$labels = [];
		$html   = '';

		$taxonomies = [
			[ 'slug' => 'type', 'label' => 'Type', 'icon' => '<i class="far fa-building"></i>' ],
			[ 'slug' => 'status', 'label' => 'Development Status', 'icon' => '' ],
			[ 'slug'  => 'occupancy_date', 'label' => 'Occupancy Year', 'icon'  => '<i class="fa fa-check-circle"></i>' ],
		];

		foreach ( $taxonomies as $tax ) {
			if ( $mapdata->taxonomies->{$tax['slug']} ) {
				$html .= '<div class="filter-section">';
				$html .= sprintf( '<h4 class="heading">%s %s</h4>', $tax['icon'], $tax['label'] );
				$html .= '<div class="filter-options">';

                $occupancyHtml = "";
				foreach ( $mapdata->taxonomies->{$tax['slug']} as $term ) {
					$active = term_selected( $term );

                    $btnHtml = sprintf( '<button class="filter-option %s" data-taxonomy="%s" data-term="%s">%s</button>',
                        $active, $tax['slug'], $term->slug, $term->name );

                    if("occupancy_date" == $tax['slug']) {
                        $occupancyHtml = $btnHtml . $occupancyHtml;
                    } else {
                        $html .= $btnHtml;
                    }

					if ( $active ) {
						$labels[] = $term->name;
					}
				}

                if("occupancy_date" == $tax['slug']) {
                    $html .= $occupancyHtml;
                }

				if ( 'status' == $tax['slug'] ) {
					$html .= $extra_filters;
				}
				if ( 'salesstatus' == $tax['slug'] ) {
					$labels = array_merge( $labels, $extra_labels );
				}

				$html .= '</div></div>';
			}
		}
		?>

		<div class="project-filter floorplan-filter filter-button more-options <?= $labels ? 'filtered' : '' ?>">
			<button class="toggle">
				<span class="title" data-placeholder="More Filters">
                    <?= $labels ? implode( ', ', $labels ) : 'More Filters' ?>
                </span>
				<span class="count"></span>
				<i class="fa fa-fw fa-caret-down"></i>
			</button>
			<div class="submenu">
				<div class="filter-section">
					<h4 class='heading'>
						<span data-av_iconfont="entypo-fontello" data-av_icon="" aria-hidden="true" class="label iconfont"></span>
						Developers
					</h4>
					<input type='text' name='developers' id='developers' class='filter' placeholder='Search for Developers'/>
 					<?php if(isset($_GET['developer'])) :?>
					<?php $developer = get_term_by( 'slug', $_GET['developer'], 'developer' ) ?>
					<span class="filter-option rounded active" data-taxonomy="developer" data-term="<?= $developer->slug ?>"><?= $developer->name ?><i class="fa fa-fw fa-times-circle fa-lg close"></i></span>
					<?php endif ?>
				</div>
                <?= $html ?>
			</div>

		</div>

		<div class="share-toggle filter-button" style="display: none">
			<button class="toggle">
				<i class="fas fa-share-alt"></i>
				<span>Share</span>
			</button>
			<div class="submenu">
				<h4 class="heading">Share</h4>
				<p>This link will re-create this exact map (location, filters, etc)</p>
				<input class="form-control">
				<p style="text-align: right;"><button class="btn btn-alt copy-share-link"><i class="fa fa-copy"></i> Copy Link</button></p>
			</div>
		</div>
		<button class="btn btn-secondary project-filter floorplan-filter reset-filters">
			<i class="fa fa-history"></i>
			<span>Reset Filters</span>
		</button>

	</div>

	<div class="map-inner">


		<div class="map-canvas-container">

			<button class="map-sidebar__toggle">
				<i class="fa fa-caret-left"></i>
			</button>

<!--                <div class="overlay standard hidden">&nbsp</div>-->
                <div class="overlay standard hidden"></div>
                <div class='map-canvas'></div>
<!--			<div class='map-canvas'></div>-->

            <div class="neighbourhood-filter">
                <div id="map-search">
                    <?php get_search_form() ?>
                </div>
            </div>


            <!--			<div class="view-toggle dropdown">-->
<!--				<button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">-->
<!--					<i class="fa fa-layer-group"></i> --><?//= $show_floorplans ? 'Viewing Available Suites' : 'Viewing Condo Buildings' ?><!-- <span class="caret"></span>-->
<!--				</button>-->
<!--				<ul class="dropdown-menu">-->
<!--					<li data-view="floorplans"><a><i class="fa fa-layer-group"></i> Viewing Available Suites</a></li>-->
<!--					<li class="selected" data-view="projects"><a><i class="far fa-building"></i> Viewing Condo Buildings</a></li>-->
<!--				</ul>-->
<!--			</div>-->

			<div class="pin-data-toggle dropup floorplan-view-only">
				<button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<span class="marker-icon"></span> <span class="pin-data__label">Price Range</span> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li class="selected" data-view="price-range" data-label="Price Range"><a>Price Range</a></li>
					<li data-view="avg-price" data-label="Avg Price per sq. ft."><a>Avg Price per sq. ft.</a></li>
					<li data-view="suites" data-label="# Suites Available"><a># Suites Available</a></li>
					<li data-view="suite-size-range" data-label="Suite Size Range"><a>Suite Size Range</a></li>
				</ul>
			</div>

			<div class="map-nav">
				<div class="zoom-in"><i class="material-icons">add</i></div>
				<div class="zoom-out"><i class="material-icons">remove</i></div>
				<div class="toggle location"><i class="material-icons">my_location</i></div>
			</div>
		</div>

		<div class="map-sidebar">
			<ul class="nav nav-tabs map-sidebar__tabs" role="tablist" style="display: none;">
				<li role="presentation" <?= $show_floorplans ? '' : 'class="active"'?>>
					<a href="#tab-condos" class="tab-condos" aria-controls="tab-condos" role="tab" data-toggle="tab">
						Buildings
					</a>
				</li>
				<li role="presentation" <?= $show_floorplans ? 'class="active"' : ''?>>
					<a href="#tab-floorplans" class="tab-floorplans" aria-controls="tab-floorplans" role="tab" data-toggle="tab">
						Available Suites
					</a>
				</li>
			</ul>

			<div class="tab-content map-sidebar__content">
				<div role="tabpanel" class="tab-pane fade <?= $show_floorplans ? '' : 'in active'?>" id="tab-condos">
					<div class="project-sidebar">
						<header>
							<h3>Condo Projects on Visible Map</h3>
							<span class="count"></span>
							<div class="loading"><i class="fa fa-fw fa-spin fa-cog"></i> Loading...</div>
							<div class="project-sidebar__sort" style="left:1rem; top: 4.5rem">
								<div class="dropdown">
									<button class="btn btn-sm dropdown-toggle project-sidebar__sort__toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										Sort by Featured <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li data-sort="featured"><a>Featured</a></li>
										<li data-sort="updated"><a>Recently Updated</a></li>
										<li data-sort="pricepersqft"><a>Price per sq.ft</a></li>
									</ul>
								</div>
								<button class="btn btn-sm project-sidebar__sort__dir" data-direction="desc">
									<i class="fa fa-sort-amount-down"></i></button>
								</button>
							</div>

                            <div class="view-toggle" style="position:absolute; top: 4.5rem; right: 1rem;">
                                <button class="btn btn-sm btn-light group" type="button" >
                                    Floor Plan View
                                </button>
                            </div>

						</header>
						<div class="projects"></div>
					</div>

				</div>
				<div role="tabpanel" class="tab-pane fade <?= $show_floorplans ? 'in active' : ''?>" id="tab-floorplans">
					<div class="floorplan-sidebar">
						<header>
							<h3>Available Suites on Visible Map</h3>
							<span class="count"></span>
							<div class="loading"><i class="fa fa-fw fa-spin fa-cog"></i> Loading...</div>

							<div class="project-sidebar__sort" style="left:1rem; top: 4.5rem">

								<div class="btn-group project-sidebar__layout" data-toggle="buttons" role="group" aria-label="..." style="display: none;">
									<label class="btn btn-sm active"><input type="radio" name="floorplan-layout" value="list" autocomplete="off" data-layout="list" checked><i class="fa fa-list"></i></label>
									<label class="btn btn-sm"><input type="radio" name="floorplan-layout" value="grid" autocomplete="off" data-layout="grid"><i class="fa fa-th"></i></label>
								</div>

								<div class="dropdown">
									<button class="btn btn-sm dropdown-toggle project-sidebar__sort__toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
										Sort by Price <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li data-sort="price"><a>Price</a></li>
										<li data-sort="featured"><a>Featured</a></li>
										<li data-sort="size"><a>Suite Size</a></li>
										<li data-sort="beds"><a>Beds</a></li>
										<li data-sort="pricepersqft"><a>Price per sq.ft</a></li>
										<li data-sort="projectname"><a>Project Name</a></li>
									</ul>
								</div>

								<button class="project-sidebar__sort__dir" data-direction="asc">
									<i class="fa fa-sort-amount-up"></i>
								</button>

							</div>
                            <div class="view-toggle" style="position:absolute; top: 4.5rem; right: 1rem;">
                                <button class="btn btn-sm btn-light group" type="button" >
                                    Project View
                                </button>
                            </div>
							<div class="mobile-tablet">
								<button class="floorplans_more-filters-toggle">Filter Floor Plans by Price, Size, # Beds or # Baths <i class="fa fa-caret-down"></i></button>
							</div>
						</header>

						<div class="list-entry-headers" style="display: none;">
							<div class="floorplan__thumbnail"></div>
							<div class="floorplan__title">Model Name</div>
							<div class="floorplan__project" data-sort="projectname">Building <i class="fa fa-sort"></i></div>
							<div class="floorplan__type" data-sort="beds">Type <i class="fa fa-sort"></i></div>
							<div class="floorplan__size" data-sort="size">Size <i class="fa fa-sort"></i></div>
							<div class="floorplan__view">View</div>
							<div class="floorplan__range">Floors</div>
							<div class="floorplan__view-all" data-sort="price">Price <i class="fa fa-sort"></i></div>
						</div>
						<div class="floorplans"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="region-hover"></div>
		<div class="project-hover"></div>
		<div class="project-infocard"></div>
	</div>

	<div class="toggle-buttons mobile-tablet">
		<button class='toggle toggle-filters'><i class='fa fa-filter'></i> Filters</button>
		<button class='toggle toggle-map active'><i class='far fa-map active'></i> Map</button>
		<button class='toggle toggle-projects' style="<?= $show_floorplans ? 'display: none;' : ''; ?>"><i class='far fa-building'></i> List View</button>
		<button class='toggle toggle-floorplans' style="<?= $show_floorplans ? '' : 'display: none;'; ?>"><i class='fa fa-layer-group'></i> List View</button>
	</div>

</div>

<?php get_footer() ?>

<?php include get_stylesheet_directory() . '/templates/template-leadpagesforms.php' ?>

<script>
jQuery(function($){
	$('.project-map').talkMap({
		mapData: mapdata,
		projectCard: true,
		zoomThreshold: 0,
		verticalcutoff: 45,
		regions: true,
		regionsOnlySelected: true,
		regionsHighlights: true,
		regionHover: false,
		regionHoverLabel: false,
		singleProject: true,
        lazyLoadImages: false,

        //initView: <?//= $show_floorplans ? '"floorplans"' : '"projects"' ?>//,
        initView: <?=  '"projects"' ?>,

		projects: <?= false ? json_encode($projects) : 'null' ?>
	});
	$(".map-sidebar .floorplans_more-filters-toggle").click(function(){
		$(".toggle-buttons .toggle-filters").trigger('click');
	})
});
var mapdata = <?= json_encode($mapdata) ?>;
</script>


<?php if ($show_floorplans): ?>
<script>
	// jQuery(document).ready(function($) {
        // var $filtersContainer = $('.project-map .filters');
		// var $salesFilters = $filtersContainer.find('.salesstatus-filter .salesstatus-options').children();
		// $salesFilters.detach().appendTo($filtersContainer.find('.more-options .salesstatus-options'));
	// });

    window.history.forward();
    function noBack() { window.history.forward(); }

	jQuery(document).ready(function($){
		$(".toggle-buttons.mobile-tablet button").click(function(e){
			e.preventDefault();
			history.pushState({page: 1}, "title 1", location.href);
		})
	})

	window.onpopstate = function(event) {
	  jQuery('.toggle-buttons.mobile-tablet .toggle-map').click();
	};

</script>
<?php endif ?>
