<?php
global $project_floorplan;
$floorplans = get_field('floorplans', false);
$floorplans = sort_floorplans($floorplans);

foreach ($floorplans as $key => $f) {
	if ($project_floorplan->ID == $f['image']) {
		$floorplan = $f;
		break;
	}
}

$total_floorplans = count($floorplans);
$available_floorplans = count(project_available_floorplans());
$sold_floorplans = $total_floorplans - $available_floorplans;
$default_layout = 'list';


$info = get_project_data( get_the_ID() );
$city_price = project_priceavg_tax( 'city', $info['city']);
$neighbourhood_price = project_priceavg_tax( 'neighbourhood', $info['neighbourhood']);

if ($floorplan['availability'] == 'Available') {
	$availability = 'available';
} elseif ($floorplan['availability'] == 'Sold Out') {
	$availability = 'sold-out';
}

?>

<div class="flex-row">
	<div class="flex-col first">

		<div class="floorplan__navigation <?= $availability ?> simpleflex">
			<div>
				<div class='condo_title'><?php the_title();?></div>
				<div class="floorplan__availability floorplan_title"><i class="fa fa-circle"></i> <?= $floorplan['suite_name'].' Floor Plan'; ?></div>
			</div>
			<div>
				<?php if ($floorplan['availability'] == 'Sold Out'): ?>
					<div class="floorplan_price">Sold Out</div>
				<?php elseif (get_field('hide_pricing')): ?>
					<div class="floorplan_price">Contact For Pricing</div>
				<?php else: ?>
					<div class="floorplan_price"><small>From</small> <?= '$'.number_format($floorplan['price'] ?: 0);?></div>
					<?php $price = floorplan_price_per_sqft($floorplan) ?>
					<div><?= $price > 0 ? '$' . number_format($price) . '/sq.ft' : '-'; ?></div>
				<?php endif ?>
			</div>
		</div>

		<div class="floorplan__image">
			<a class="noLightbox" href="<?= wp_get_attachment_image_url( $floorplan['image'], 'fullimage' ); ?>" data-fancybox="floorplan">
				<img src="<?= wp_get_attachment_image_url( $floorplan['image'], 'fullimage' ); ?>" alt="<?= floorplan_alt_text($floorplan, $post) ?>">
				<i class="fa fa-search-plus fa-2x"></i>
			</a>
		</div>

		<div class="floorplan_card">

			<div class="card_item">
				<div class="card__subitemtitle">Sq.ft.</div>
				<div><?= $floorplan['size'] ?> sq.ft.</div>
			</div>
			<div class="card_item">
				<div class="card__subitemtitle">Type</div>
				<div>
					<?= $floorplan['beds'] ?> Bed, <?= $floorplan['baths'] ?> Bath
				</div>
			</div>
			<div class="card_item">
				<div class="card__subitemtitle">Exposure</div>
				<div><?= implode('/', $floorplan['exposure']) ?></div>
			</div>
			<div class="card_item">
				<div class="card__subitemtitle">Floor Range</div>
				<div><?= floorplan_floor_ranges($floorplan) ?></div>
			</div>
		</div>

		<div class="project-panels">

			<div class="panel panel-default">

				<div class="panel-heading" role="tab" id="collapsePPSHeading">

					<h2 class="panel-title">
						<a class="collapsed" role="button" data-toggle="collapse" href="#collapsePPS" aria-expanded="true" aria-controls="collapsePPS" itemprop="name">Price Per Square Foot</a>
					</h2>

					<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapsePPS" aria-expanded="true" aria-controls="collapsePPS">
						<i class="fa fa-angle-up"></i>Hide
					</button>

				</div>

				<div id="collapsePPS" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseIntroHeading">

					<div class="panel-body">

						<div class="card description animated fadeIn" data-animation="fadeIn">

							<div class="psf_details">
								<div class="detail_item">
									<div>THIS FLOOR PLAN</div>
									<div class="detail_psf">
									<?php if (get_field('hide_pricing')): ?>
										N/A
									<?php else: ?>
										$<?= $price ?><small>/sq.ft</small>
									<?php endif ?>
									</div>
								</div>
								<div class="detail_item">
									<div style="text-transform: uppercase;"><?php the_title() ?> AVERAGE</div>
									<div class="detail_psf">
										$<?= number_format($info['pricepersqft'], 0, '.', '');?><small>/sq.ft</small>
									</div>
								</div>
								<div class="detail_item">
									<div>NEIGHBOURHOOD AVERAGE</div>
									<div class="detail_psf">
										$<?= number_format($neighbourhood_price, 0, '.', '');?><small>/sq.ft</small>
									</div>
								</div>
							</div>

							<div class="floorplan_details">
								<div class="floorplan_detail suite_details">
									<h2>Suite Details</h2>
									<p>Suite Name: <span><?= $floorplan['suite_name'] ?></span></p>
									<p>Beds: <span><?= $floorplan['beds'] ?> Bed</span></p>
									<p>Baths: <span><?= $floorplan['baths'] ?> Bath</span></p>
									<p>View: <span><?= implode('/', $floorplan['exposure']) ?></span></p>
									<p>Interior Size: <span><?= $floorplan['size'] ?> sq.ft.</span></p>
									<p>Floor Range: <span><?= floorplan_floor_ranges($floorplan) ?></span></p>
								</div>
								<div class="floorplan_detail suite_details">
									<h2>Prices</h2>

									<p>Price (From): <span>
										<?php if ($floorplan['availability'] == 'Sold Out'): ?>
											Sold Out
										<?php elseif (get_field('hide_pricing')): ?>
											Contact For Pricing
										<?php else: ?>
											$<?= number_format((double)$floorplan['price']) ?>
										<?php endif ?>
										</span>
									</p>
									<p>Price Per Sq.Ft.: <span>
										<?php if ($floorplan['availability'] == 'Sold Out'): ?>
											Sold Out
										<?php elseif (get_field('hide_pricing')): ?>
											Contact For Pricing
										<?php else: ?>
											<?= $price ? '$' . number_format($price) . '/sq.ft' : '-' ?>
										<?php endif ?>
										</span>
									</p>
									<p>Mt. Fees per Month: <span>
										<?= get_field('maintenancefeessq.ft') ? '$'.$floorplan['size'] * floatval(str_replace('$', '', get_field('maintenancefeessq.ft'))).' ('.get_field('maintenancefeessq.ft').'/sq.ft)': '-' ?></span>
									</p>
									<p>Parking: <span><?= get_field('parking') ?: '-' ?></span></p>
									<p>Locker: <span><?= get_field('locker') ?: '-' ?></span></p>
								</div>

								<?php if (have_rows('deposit_structure')): ?>
								<div class="floorplan_detail suite_details">
									<h2>Deposit Structure</h2>
									<?php get_template_part( 'templates/project/card-deposit-structure' ); ?>
								</div>
								<?php endif ?>

							</div>

							<?php if ($floorplan['price_history'] && is_user_logged_in()): ?>
							<div class="floorplan_details">
								<div class="floorplan_detail">
									<h2>Price History</h2>
									<?php foreach ($floorplan['price_history'] as $i => $row): ?>
										<p>
											<span class="price">$<?= number_format($row['history_price']) ?></span> - <span class="date"><?= (new DateTime($row['history_date']))->format('j F Y') ?></span>
											<?php if ($i > 0): ?>
												<?php $diff = ($floorplan['price_history'][$i]['history_price']) - ($floorplan['price_history'][$i-1]['history_price']) ?>
												<?php if ($diff < 0): ?>
													(<span class='diff negative' style="color: red;">- $<?= number_format(abs($diff)) ?></span>)
												<?php else: ?>
													(<span class='diff positive' style="color: green;">+ $<?= number_format(abs($diff)) ?></span>)
												<?php endif ?>
											<?php endif ?>
										</p>
									<?php endforeach ?>
								</div>
							</div>
							<?php endif ?>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>

	<div class="flex-col project__sidebar">
		<div class="floorplan_reserve">
			<h2>Reserve Online <i class="fa fa-shopping-cart pull-right"></i></h2>
			<a href="<?= $_SERVER['REQUEST_URI'] . 'reserve/' ?>" class="btn btn-blue btn-block btn-round">Reserve This Condo</a>
		</div>

		<div class="floorplan_purchase_request_form">
			<?= do_shortcode('[gravityforms id=10 ajax=true]') ?>
			<?php /*
			<script type='text/javascript' src='https://vt167.infusionsoft.com/app/form/iframe/3364fc805f749b4f8e860d3ef09888e7'></script>
			*/ ?>
		</div>

	</div>
</div>

<div class="browse-more-floorplans project-content">
	<h4 class="title">Browse more <?php the_title() ?> Floor Plans</h4>
	<div class="card floorplans" data-animation="fadeIn">
		<div class="row">
			<div class="col-md-6">
				<div class="dropdown availability-dropdown">
					<button class="dropdown-toggle" type="button" data-filter="available" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<i class="fa fa-circle fa--available"></i>
						Show Available Only <span class="floorplan-count available"> (<?= $available_floorplans ?>)</span>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li class="active">
							<a href="#" data-filter="available"><i class="fa fa-circle fa-fw fa--available"></i>
								Show Available Only <span class="floorplan-count available"> (<?= $available_floorplans ?>)</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="sold-out">
								<i class="fa fa-circle fa-fw fa--sold"></i>
								Show Sold Only <span class="floorplan-count sold">(<?= $sold_floorplans ?>)</span>
							</a>
						</li>
						<li>
							<a href="#" data-filter="all">
								<img src="<?= get_template_directory_uri(); ?>/assets/images/icon-mixed-available.svg" width="14" height="14" alt="">
	                            Show Available & Sold <span class="floorplan-count all">(<?= $total_floorplans ?>)</span>
							</a>
						</li>
	                    <?php if ( is_user_logged_in() ):?>
	                        <li>
	                            <a href="#" data-filter="saved">
	                                <i class="fa fa-heart fa-fw fa--saved"></i>
	                                Show Only Saved <span class="floorplan-count saved">(0)</span>
	                            </a>
	                        </li>
	                    <?php endif;?>
						<li>
							<label class="availability-sort" for="available-first">
								<input type="checkbox" id="available-first">
								Sort Available First
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-md-6">
				<div class="floorplans__layout-toggle">
					<button class="floorplans_more-filters-toggle"><i class="fa fa-filter fa-lg"></i> Filter Floor Plans <i class="fa fa-caret-down"></i></button>
<!--					<a class="desktop-only floorplans__layout active" data-layout="list"><i class="fa fa-fw fa-list fa-lg"></i></a>-->
<!--					<a class="desktop-only floorplans__layout" data-layout="grid"><i class="fa fa-fw fa-th fa-lg"></i></a>-->
				</div>
				<div class="floorplans__filter-sliders">
					<div class="floorplans__filter-slider hide">
						<label>Price</label>
						<?php $chunk = 50000; ?>
						<div id="priceslider"
							data-min="<?= floor(floorplans_bounds('price', 'min') / $chunk) * $chunk ?>"
							data-max="<?= ceil(floorplans_bounds('price', 'max') / $chunk) * $chunk ?>"></div>
					</div>

					<div class="hide">
						<button class="floorplans_more-filters-toggle">Filter by # Beds, # Baths, Sq. Ft. <i class="fa fa-caret-down"></i></button>
					</div>

					<div class="floorplans_more-filters" style="display: none;">
						<div class="floorplans__filter-slider">
							<label>BEDS</label>
							<div id="bedslider" data-min="<?= floorplans_bounds('beds', 'min') ?>" data-max="<?= floorplans_bounds('beds', 'max') ?>"></div>
						</div>
						<div class="floorplans__filter-slider">
							<label>BATHS</label>
							<div id="bathslider" data-min="<?= floorplans_bounds('baths', 'min') ?>" data-max="<?= floorplans_bounds('baths', 'max') ?>"></div>
						</div>
						<div class="floorplans__filter-slider">
							<label>SQ.FT</label>
							<div id="sizeslider" data-min="<?= floorplans_bounds('size', 'min') ?>" data-max="<?= floorplans_bounds('size', 'max') ?>"></div>
						</div>

						<a class="btn btn-link floorplans_reset-filters">RESET</a>
					</div>
				</div>
			</div>
		</div>

		<div class="floorplans grid quickview-gallery" style="<?= ($default_layout != 'grid') ? 'display: none;' : ''; ?> ">
			<?php if ($floorplans): ?>
				<?php foreach ($floorplans as $floorplan): ?>
					<?php include get_stylesheet_directory() . '/templates/project/floorplan-grid-entry.php'; ?>
				<?php endforeach ?>
			<?php endif ?>
		</div>

		<table class="floorplans list quickview-gallery" style="<?= ($default_layout != 'list') ? 'display: none;' : ''; ?>">
		<thead>
			<?php include get_template_directory() . '/templates/project/floorplan-list-entry-headers.php'; ?>
		</thead>
			<tbody>
			<?php if ($floorplans): ?>
				<?php foreach ($floorplans as $floorplan): ?>
					<?php include get_template_directory() . '/templates/project/floorplan-list-compact-entry.php'; ?>
				<?php endforeach ?>
			<?php endif ?>
			</tbody>
		</table>

		<?php if ($available_floorplans == 0) get_template_part('templates/project/card-floorplans-empty'); ?>

		<p class="text-center">All Prices, availability, figures and materials are preliminary and are subject to change without notice. E&OE <?= date('Y'); ?> Floor Premiums apply, please speak to sales representative for further information.</p>
	</div>
</div>
