<?php

/**
 * Template Part: Interviews
 *
 * @since 1.4.4
 */

if( have_rows( 'projects' ) ): ?>

	<div class="condos-group" data-animation="fadeIn">

		<header>
			<h1 class='heading'>Our Top Picks</h1>
		</header>

		<div id="condos-slider--picks" class="project-carousel condos-slider condos-slider--large gscroll" data-animation="fadeIn">

			<div class="condos-slider__scroller">

				<?php while( have_rows( 'projects' ) ): the_row();

					$index       = get_row_index();
					$image       = get_sub_field( 'projects_item_thumbnail' );
					$description = get_sub_field( 'projects_item_description' );

					$post = get_sub_field( 'projects_item' );

					setup_postdata( $post );

					/**
					 * Videos
					 */
					$videos = get_field('gallery_videos');
					if( is_array($videos) && !empty($videos) ) {
						$video = $videos[0]['gallery_video'];
					} ?>

					<div class="project project--picks">

						<a href="<?php the_permalink(); ?>">

							<figure>
								<?php echo wp_get_attachment_image( $image, 'full' ); ?>
							</figure>

							<figcaption>

								<h4 class="condo-title">
									<?php the_title() ?>
									<?php if( $city = custom_cat_text('city') ): ?>
										<small>
											<?php echo $city; ?>
										</small>
									<?php endif; ?>
								</h4>

								<?php if( $description ): ?>
									<div class="condo-excerpt">
										<p>
											<?php echo $description; ?>
										</p>
									</div>
								<?php endif; ?>

								<footer class="condo-footer">
									<ul class="list-inline">
										<li>
											<a href="<?php the_permalink(); ?>floorplans" class="btn btn-default btn--featured">
												<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="15" viewBox="0 0 16 15"><defs><path id="u5eba" d="M45.55 1506.47l-1 .52 1.1.53-6.01 3.13-5.93-3.13 1.15-.51-1-.53-1.38.61a.43.43 0 0 0-.25.4c0 .09.02.17.06.24.05.08.1.13.17.17l6.95 3.67a.58.58 0 0 0 .23.06l.1-.02c.04 0 .08-.02.12-.04l7.02-3.67c.07-.04.13-.1.18-.17a.47.47 0 0 0 .06-.24.42.42 0 0 0-.07-.23.46.46 0 0 0-.19-.17zm-.08-3.23l-1 .53 1.18.58-6.01 3.13-5.93-3.13 1.24-.61-1-.53-1.47.71a.43.43 0 0 0-.25.4c0 .08.02.16.06.24.05.07.1.13.17.17l6.95 3.67a.58.58 0 0 0 .23.05l.1-.01.12-.04 7.02-3.67c.07-.04.13-.1.18-.17a.47.47 0 0 0 .06-.24.42.42 0 0 0-.07-.23.46.46 0 0 0-.19-.17zm.25-2.22l-6 3.13-5.94-3.13 6-2.93zm-6.23 4.05a3.5 3.5 0 0 1 .22.04h.12l.1-.04 7.03-3.67a.47.47 0 0 0 .23-.41.44.44 0 0 0-.24-.4l-6.94-3.4a.43.43 0 0 0-.43 0l-7.01 3.4a.48.48 0 0 0-.2.17.4.4 0 0 0-.07.23c0 .09.02.17.07.24.04.08.1.13.18.17z"/></defs><g><g transform="translate(-32 -1497)"><use fill="#fff" xlink:href="#u5eba"/></g></g></svg>
												<?php esc_html_e( 'Browse Floor Plans', 'talkcondo' ); ?>
											</a>
										</li>
										<?php if( isset( $video ) ) { ?>
											<li>
												<a href="<?php echo $video; ?>" class="btn btn-default popup-video">
													<i class="fa fa-play-circle-o"></i><?php esc_html_e( 'Watch Video', 'talkcondo' ); ?>
												</a>
											</li>
										<?php } ?>
										<li>
											<a href="<?php the_permalink(); ?>" class="btn btn-default">
												<i class="far fa-building"></i><?php esc_html_e( 'Project Details', 'talkcondo' ); ?>
											</a>
										</li>
									</ul>
								</footer>

							</figcaption>

						</a>

					</div>

					<?php wp_reset_postdata() ?>

				<?php endwhile; ?>

			</div>

		</div>

	</div>

<?php endif;
