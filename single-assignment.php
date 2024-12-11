<?php
	global $avia_config;

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();

	$title  = __('Blog - Latest News', 'avia_framework'); //default blog title
	$t_link = home_url('/');
	$t_sub = "";

	if(avia_get_option('frontpage') && $new = avia_get_option('blogpage'))
	{
		$title 	= get_the_title($new); //if the blog is attached to a page use this title
		$t_link = get_permalink($new);
		$t_sub =  avia_post_meta($new, 'subtitle');
	}

	if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title(array('heading'=>'strong', 'title' => $title, 'link' => $t_link, 'subtitle' => $t_sub));

	the_post();

	$project = get_field('project');

?>


<div class="container">
	<main class="template-page site-content twelve alpha units" itemprop="mainContentOfPage" role="main">

		<div class="post-entry post-entry-type-page">
			<div class="entry-content-wrapper clearfix">
				<section class="project-header av_textblock_section avia_layout_builder clearfix">
					<div class='project-logo av_one_fifth first'>
						<?php echo wp_get_attachment_image( get_field('logo', $project->ID), 'project-logo' ) ?>
					</div>
					<div class="project-title av_four_fifth last">
						<h2 class='clearfix'><span class="title"><?php the_title() ?></span><span class="price"><?php the_field('price') ?></span></h2>
						<?php
							$city = custom_cat_link('city');
							$developer = custom_cat_link('developer');
							$pricedfrom = get_post_meta( get_the_ID(), 'pricedfrom', true);
							$status = custom_cat_link('status');
							$occupancydate = custom_cat_link('occupancy-date');
							$type = custom_cat_link('type');

							$tags = array();
							$tags[] = get_field('price');
							$tags[] = 'Assignment';
							$tags[] = custom_cat_link('city', $project->ID);
							$tags[] = get_field('majorintersection', $project->ID);
							if (get_field('squarefootage')) $tags[] = get_field('squarefootage') . ' sq.ft.';
						?>
						<p class='details'><?php echo implode(' | ', $tags) ?></p>
					</div>
				</section>
			</div>
		</div>
	</main>
</div>

<?php get_template_part( 'templates/project/components/banner' ); ?>

<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>
	<div class="container">
		<main class="template-page content twelve alpha units" itemprop="mainContentOfPage" role="main">
			<div class="post-entry post-entry-type-page">
				<div class="entry-content-wrapper clearfix">

					<div class="flex_column av_three_fifth first  avia-builder-el-4  el_after_av_four_fifth  el_before_av_one_third  ">
						<div style="margin-top: 20px;">

							<?php $images = get_field('gallery'); ?>

							<?php if ($images): ?>
								<div id="project-slider" class="flexslider project-slider avia-gallery">
									<ul class="slides">
										<?php foreach( $images as $image ): ?>
											<li><a class='' href="<?php echo $image['sizes']['large']; ?>"><img src="<?php echo $image['sizes']['flexslider']; ?>" alt="<?php echo $image['alt']; ?>" /></a></li>
										<?php endforeach; ?>
									</ul>
								</div>

								<div id="project-carousel" class="flexslider project-carousel">
									<ul class="slides">
										<?php foreach( $images as $image ): ?>
											<li><img src="<?php echo $image['sizes']['flexsliderthumb']; ?>" alt="<?php echo $image['alt']; ?>" /></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php elseif (has_post_thumbnail()): ?>
								<?php the_post_thumbnail('flexslider') ?>
							<?php else: ?>
								<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/gallery-placeholder.jpg' ?>"/>
							<?php endif; ?>
						</div>
					</div>

					<div class="flex_column av_two_fifth avia-builder-el-6 el_after_av_two_third el_before_av_two_third">
						<section itemtype="https://schema.org/CreativeWork" itemscope="itemscope" class="av_textblock_section">
							<?php $infusionsoftform = get_post_meta( get_the_ID(), 'infusionsoft', true ) ?>
							<?php echo ($infusionsoftform) ?: '' ?>
						</section>
					</div>

					<div class="flex_column av_three_fifth first avia-builder-el-8 el_after_av_one_third el_before_av_one_third">
						<div class="tabcontainer top_tab">
							<div class="tab_titles">
								<div class="tab tab_counter_0 active_tab" data-fake-id="#tab-id-1">About the Area</div>
								<div class="tab tab_counter_1" data-fake-id="#tab-id-2">About the Suite</div>
								<div class="tab tab_counter_2" data-fake-id="#tab-id-3">About the Building</div>
							</div>
							<section class='av_tab_section'>
								<div id="tab-id-1-container" class='tab_content active_tab_content tab_content_table'>
									<table>
										<tbody>
										<tr>
											<td>Project Name</td>
											<td><?php echo $project->post_title ?></td>
										</tr>
										<tr>
											<td>Address</td>
											<td><?php the_field('address', $project->ID) ?></td>
										</tr>
										<tr>
											<td>Suite Number</td>
											<td><?php the_field('suitenumber') ?></td>
										</tr>
										<tr>
											<td>City</td>
											<td><?php echo custom_cat_link('city', $project->ID) ?></td>
										</tr>
										<tr>
											<td>Neighbourhood</td>
											<td><?php echo custom_cat_link('neighbourhood', $project->ID) ?></td>
										</tr>
										<tr>
											<td>Major Intersection</td>
											<td><?php the_field('majorintersection', $project->ID) ?></td>
										</tr>
										</tbody>
									</table>
								</div>
							</section>
							<section class='av_tab_section'>
								<div id="tab-id-2-container" class='tab_content tab_content_table'>
									<table>
										<tbody>
											<?php if (get_field('squarefootage')): ?>
											<tr>
												<td>Square Footage</td>
												<td><?php echo get_field('squarefootage') . 'sq ft.' ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('balcony')): ?>
											<tr>
												<td>Balcony</td>
												<td><?php the_field('balcony') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('exposure')): ?>
											<tr>
												<td>Exposure</td>
												<td><?php the_field('exposure') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('bedrooms')): ?>
											<tr>
												<td># Bedrooms</td>
												<td><?php the_field('bathrooms') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('bathrooms')): ?>
											<tr>
												<td># Bathrooms</td>
												<td><?php the_field('bathrooms') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('depositpaid')): ?>
											<tr>
												<td>Deposit Paid</td>
												<td><?php the_field('depositpaid') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('depositremaining')): ?>
											<tr>
												<td>Deposit Remaining</td>
												<td><?php the_field('depositremaining') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('occupancydate')): ?>
											<tr>
												<td>Occupancy Date</td>
												<td><?php the_field('occupancydate') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('maintenancefees')): ?>
											<tr>
												<td>Maintenance Fees</td>
												<td><?php the_field('maintenancefees') ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('suitefeatures')): ?>
											<tr>
												<td>Suite Features</td>
												<td><?php echo nl2br(get_field('suitefeatures')) ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('bonusincentives')): ?>
											<tr>
												<td>Bonus Incentives</td>
												<td><?php echo nl2br(get_field('bonusincentives')) ?></td>
											</tr>
											<?php endif; ?>
											<?php if (get_field('other')): ?>
											<tr>
												<td>Other</td>
												<td><?php echo nl2br(get_field('other')) ?></td>
											</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</section>
							<section class='av_tab_section'>
								<div id="tab-id-2-container" class='tab_content tab_content_table'>
									<table>
										<tbody>
											<tr>
												<td>Height</td>
												<td><?php echo (get_field('storeys', $project->ID)) ? get_field('storeys', $project->ID) . ' storeys' : '' ?></td>
											</tr>
											<?php if ($suites = get_post_meta( $project->ID, 'suites', true)): ?>
											<tr>
												<td># Suites:</td>
												<td><?php echo $suites ?></td>
											</tr>
											<?php endif; ?>
											<tr>
												<td>Amenities</td>
												<td><?php echo nl2br(get_field('amenities', $project->ID)) ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</section>
						</div>

						<div class='last-updated'>Data last updated: <?php echo get_the_modified_date('F jS, Y'); ?></div>

					</div>

					<div class="flex_column av_two_fifth avia-builder-el-11 el_after_av_two_third avia-builder-el-last column-top-margin">
						<?php dynamic_sidebar('Assignment Sidebar'); ?>
					</div>
				</div>
			</div>
		</main><!-- close content main element -->
	</div>

</div><!-- close default .container_wrap element -->

<?php if ($map_url = get_post_meta( $project->ID, 'map', true )): ?>
<section id='map'><?php echo map_shortcode_from_url($map_url); ?></section>
<?php endif; ?>

<?php get_footer(); ?>
