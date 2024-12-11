<?php

/**
 * Template Part: Overview
 *
 * @package    Project
 * @subpackage Panel
 */
 $info = get_project_data( get_the_ID() );
?>

<div class="panel panel-default">

	<div class="panel-heading" role="tab" id="collapseOverviewHeading">

		<h2 class="panel-title">
			<a class="collapsed" role="button" data-toggle="collapse" href="#collapseOverview" aria-expanded="true" aria-controls="collapseOverview">
				<span itemprop="name">
					<?php the_title() ?>
				</span> Overview
			</a>
		</h2>

		<button role="button" class="panel-toggle" data-toggle="collapse" href="#collapseOverview" aria-expanded="true" aria-controls="collapseFloorplans">
			<i class="fa fa-caret-up"></i>Hide
		</button>

	</div>

	<div id="collapseOverview" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="collapseOverviewHeading">

		<div class="panel-body">

			<div class="card project-overview" data-animation="fadeIn">

				<ul class="nav nav-tabs project-overview__tabs" role="tablist">

					<li role="presentation" class="active">
						<a href="#tab-overview" aria-controls="tab-overview" role="tab" data-toggle="tab">
							Overview
						</a>
					</li>

					<?php if ( get_field('projecthighlights') ): ?>
						<li role="presentation">
							<a href="#tab-highlights" aria-controls="tab-highlights" role="tab" data-toggle="tab">
								Highlights
							</a>
						</li>
					<?php endif; ?>

					<?php if ( get_field('amenities') ): ?>
						<li role="presentation">
							<a href="#tab-amenities" aria-controls="tab-amenities" role="tab" data-toggle="tab">
								Amenities
							</a>
						</li>
					<?php endif; ?>

					<?php if ( get_field('featuresfinishes') ): ?>
						<li role="presentation">
							<a href="#tab-finishes" aria-controls="tab-finishes" role="tab" data-toggle="tab">
								Finishes
							</a>
						</li>
					<?php endif; ?>

				</ul>

				<div class="tab-content project-overview__content">

					<div role="tabpanel" class="tab-pane fade in active" id="tab-overview">

						<div class="overview">

							<div class="card__content">

								<h3>Key Information</h3>

								<div class="card-table">

									<div class="card__subitem">
										<div class="card__subitemtitle">Location</div>
										<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
											<span itemprop="streetAddress">
												<?php the_field('address') ?></span><br>
											<span>
												<?php echo custom_cat_link('neighbourhood'); ?></span>,
											<span itemprop="addressLocality">
												<?php echo custom_cat_link('city'); ?></span><br>
											<span itemprop="addressRegion">
												<?php echo custom_cat_link('district') ?></span>
										</div>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Developer</div>
										<?php echo str_replace('&', '&<br>', custom_cat_link('developer')) ?>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Completion</div>
										<?php echo (custom_cat_text('occupancy-date')) ?: '-' ?>
									</div>
								</div>

								<div class="card-table">

											<div class="card__subitem">
												<div class="card__subitemtitle">Sales Status</div>
												<div itemprop="amenityFeature">
													<?php $salesstatus = str_replace('&', '&<br>', custom_cat_text('salesstatus')) ?>
													<?php echo ($salesstatus) ?: '-' ?>
												</div>
											</div>

											<div class="card__subitem">
												<div class="card__subitemtitle">Development Status</div>
												<?php echo (custom_cat_text('status')) ?: '-' ?>
											</div>

											<div class="card__subitem">
												<div class="card__subitemtitle">Building Type</div>
												<div itemprop="amenityFeature">
													<?php $type = str_replace('&', '&<br>', custom_cat_text('type')) ?>
													<?php echo ($type) ?: '-'; ?>
												</div>
											</div>

								</div>

								<div class="card-table">

									<div class="card__subitem">
										<div class="card__subitemtitle">Price Range</div>
										<div>
											<?php
											$pricedfrom = $info['price']['min'];
											$pricedto = $info['price']['max'];
											if ($pricedfrom && $pricedto && $pricedfrom != $pricedto) {
												$pricedfrom = number_format($pricedfrom);
												$pricedto = number_format($pricedto);
												echo sprintf("%s to<br>%s", '$'.$pricedfrom, '$'.$pricedto);
											} elseif ($pricedfrom) {
												$pricedfrom = number_format($pricedfrom);
												echo sprintf("%s", '$'.$pricedfrom);
											} else {
												echo '-';
											}
											?>
										</div>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Suite Sizes</div>
										<div itemprop="amenityFeature">
											<?php echo project_suite_size_range( get_the_ID() ) ?>
										</div>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Avg. Price per Foot</div>
										<div>
											<?php $price = $info['pricepersqft'] ?>
											<?php echo ($price) ? "$" . number_format($price) . "/sq.ft" : '-'; ?>
										</div>
									</div>

								</div>

								<div class="card-table">

									<div class="card__subitem">
										<div class="card__subitemtitle">Parking</div>
										<?php echo ( get_field('parking')) ?: '-' ?>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Locker Price</div>
										<?php echo ( get_field('locker')) ?: '-' ?>
									</div>

									<div class="card__subitem">
										<div class="card__subitemtitle">Mt. Fees ($ per sq.ft.)</div>
										<?php echo ( get_field('maintenancefeessq.ft')) ?: '-' ?>
									</div>
								</div>

								<?php if (have_rows('deposit_structure')): ?>
								<div class="card-table">
									<div class="card__subitem">
										<div class="card__subitemtitle">Deposit Structure</div>
										<?php get_template_part( 'templates/project/card-deposit-structure' ); ?>
									</div>
								</div>
								<?php endif ?>

							</div>

						</div>

					</div>

					<?php if ( get_field('projecthighlights') ): ?>
						<div role="tabpanel" class="tab-pane fade" id="tab-highlights">
							<div class="card__content" itemprop="description">
								<?php echo nl2br(get_field('projecthighlights')) ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( get_field('amenities') ): ?>
						<div role="tabpanel" class="tab-pane fade" id="tab-amenities">
							<div class="amenities">
								<div class="card__content">
									<?php if (strpos(get_field('amenities'), '-') === 0): ?>
									<?php echo str_replace( '<li>', '<li itemprop="amenityFeature">', column_list( get_field('amenities') ) ); ?>
									<?php else: ?>
									<?php echo str_replace( '<li>', '<li itemprop="amenityFeature">', get_field('amenities') ); ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( get_field('featuresfinishes') ): ?>
						<div role="tabpanel" class="tab-pane fade" id="tab-finishes">
							<?php
								$maxlines = 5;
								$lines = explode(PHP_EOL, get_field('featuresfinishes'));
								$showmore = (count($lines) > $maxlines);
							?>

							<div class="featuresfinishes <?php echo ($showmore) ? 'showmore' : '' ?>">
								<div class="card__content" itemprop="amenityFeature">
									<?php echo nl2br(implode(PHP_EOL, array_slice($lines, 0, $maxlines))); ?>
									<?php if ($showmore): ?>
									<div class="more">
										<?php echo nl2br(implode(PHP_EOL, array_slice($lines, $maxlines))); ?>
									</div>
									<?php endif; ?>
								</div>
								<?php if ($showmore): ?>
								<button class="card__expand">+ Show More</button>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

				</div>

				<div class="project-overview__additional">

					<h3>Additional Information</h3>

					<div class="card__content">

						<div class="card-table">

							<div class="card__subitem walk_score">
								<div class="card__subitemtitle">Walk Score</div>
									<?php if ( $score = project_walk_score() ): ?>
										<?php echo $score; ?>
									<?php else: ?>
										-
									<?php endif; ?>
							</div>

							<div class="card__subitem transit_score">
								<div class="card__subitemtitle">Transit Score</div>
									<?php if ( $score = project_transit_score() ): ?>
										<?php echo $score; ?>
									<?php else: ?>
										-
									<?php endif; ?>
							</div>

							<div class="card__subitem"></div>
						</div>

						<div class="card-table">
							<div class="card__subitem">
								<div class="card__subitemtitle">Architect</div>
								<div>
									<?php if (get_field('architect')): ?>
										<?php echo get_field('architect') ?>
									<?php else: ?>
										-
									<?php endif; ?>
								</div>
							</div>
							<div class="card__subitem">
								<div class="card__subitemtitle">Interior Designer</div>
								<div>
									<?php if (get_field('interiordesigner')): ?>
										<?php echo get_field('interiordesigner') ?>
									<?php else: ?>
										-
									<?php endif; ?>
								</div>
							</div>
							<div class="card__subitem"></div>

						</div>

						<div class="card-table">

							<div class="card__subitem" itemprop="amenityFeature">
								<div class="card__subitemtitle">
									Count
								</div>
								<?php if( get_field('storeys') || get_field('suites') ): ?>
									<?php echo ( get_field('storeys') ) ?: '-' ?> Floors<br>
									<?php echo ( get_field('suites') ) ?: '-' ?> Suites
								<?php else: ?>
									-
								<?php endif; ?>
							</div>
							<div class="card__subitem">
								<div class="card__subitemtitle">
									Height (M)
								</div>
								<?php if (get_field('heightm')): ?>
									<?php echo get_field('heightm') ?> m
								<?php else: ?>
									-
								<?php endif; ?>
							</div>
							<div class="card__subitem">
								<div class="card__subitemtitle">
									Height (Ft)
								</div>
								<?php if (get_field('heightft')): ?>
									<?php echo get_field('heightft') ?> ft
								<?php else: ?>
									-
								<?php endif; ?>
							</div>

						</div>

						<?php /*
						<div class="card-table">

							<div class="card__subitem">
								<div class="card__subitemtitle">Height Rank (city)</div>
								<?php if ($rank = project_height_rank('city')): ?>
									<div>
										<?php echo '#' . $rank['rank'] . ' in ' . $rank['region'] ?>
									</div>
								<?php else: ?>
								-
								<?php endif; ?>
							</div>

							<div class="card__subitem">
								<div class="card__subitemtitle">Height Rank (neighbourhood)</div>
								<?php if ($rank = project_height_rank('neighbourhood')): ?>
									<div>
										<?php echo '#' . $rank['rank'] . ' in ' . $rank['region'] ?>
									</div>
								<?php else: ?>
									-
								<?php endif; ?>
							</div>
							<div class="card__subitem"></div>

						</div>
						*/ ?>

					</div>

					<div class='last-updated'>Data last updated:
						<?php echo get_the_modified_date('F jS, Y'); ?>
					</div>

				</div>

			</div>

		</div>

	</div>

</div>
