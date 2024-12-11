<?php

/**
 * Footer
 *
 * Used to manage the footer of the pages.
 *
 * @link http://codex.wordpress.org/Function_Reference/get_footer
 */

?>

<?php
global $avia_config;
$blank = isset($avia_config['template']) ? $avia_config['template'] : "";

wp_reset_query();

$the_id 				= avia_get_the_ID(); //use avia get the id instead of default get id. prevents notice on 404 pages
$footer 				= get_post_meta($the_id, 'footer', true);
$footer_widget_setting 	= !empty($footer) ? $footer : avia_get_option('display_widgets_socket');
?>

<?php if (!$blank && $footer_widget_setting != 'nofooterarea' ): ?>
	<?php if( $footer_widget_setting != 'nofooterwidgets' ): ?>

		<div class='container_wrap footer_color' id='footer'>

			<div class='container'>

				<div class="row">

					<div class="col-md-4">
						<section id="text-5" class="widget clearfix widget_text">
							<div class="textwidget">
								<div itemscope itemtype="http://schema.org/RealEstateAgent">
									<div>
										<img class="size-full wp-image-29225" src="<?= wp_get_attachment_image_url(29225, 'large') ?>" alt="" width="192" height="146" itemtype="logo">
									</div>

									<meta itemprop="name" content="<?php bloginfo( 'title' ); ?>">
									<meta itemprop="priceRange" content="$100,000">

									<?php if (get_custom_logo()):
										$image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) , 'full' ); ?>
										<meta itemprop="image" content="<?= $image[0] ?>">
									<?php endif ?>

									<br>

									<span itemprop="legalName">SAGE - TalkCondo Ltd., Brokerage</span><br>
									Brokerage Independently Owned &amp; Operated<br>
									<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
										<span itemprop="streetAddress">2010 Yonge Street</span><br>
										<span itemprop="addressLocality">Toronto</span>,
										<span itemprop="addressRegion">Ontario</span>,
										<span itemprop="postalCode">M4S 1Z9</span><br>
										Phone: <span itemprop="telephone">416-800-0985</span><br>
									</address>
									<div itemscope itemtype="http://schema.org/ContactPoint">
										Roy Bhandari (Broker of Record)
									</div>
									<div itemscope itemtype="http://schema.org/ContactPoint">
										Amit Bhandari (Broker)
									</div>
								</div>
							</div>
							<span class="seperator extralight-border"></span>
						</section>
					</div>

					<div class="col-md-4">
						<section id="text-2" class="widget clearfix widget_text">
							<h3 class="widgettitle">DISCLAIMER</h3>
							<div class="textwidget">TalkCondo makes every effort to ensure accurate information, however, TalkCondo is not liable for the use or misuse of
								the site's information. The information on www.talkcondo.com is neither intended to be nor does it take the place of legal,
								tax or accounting advice and users are strongly recommended to obtain independent legal, tax or accounting advice. The
								information displayed on www.talkcondo.com is for reference only.
								<br>
								<br>
								<p>
									© Copyright TalkCondo 2010 - <?= date('Y') ?>
								</p>
							</div>
							<span class="seperator extralight-border"></span>
						</section>
					</div>

					<div class="col-md-4">
						<section id="text-6" class="widget clearfix widget_text">
							<h3 class="widgettitle">TALKCONDO ON SOCIAL MEDIA</h3>
							<div class="textwidget">
								<p>
									<a href="<?php echo get_theme_mod( 'social_twitter' ); ?>" target="_blank" rel="noopener">
										<?php esc_html_e( 'Twitter', 'talkcondo' ); ?>
									</a>
									<br>
									<a href="<?php echo get_theme_mod( 'social_facebook' ); ?>" target="_blank" rel="noopener">
										<?php esc_html_e( 'Facebook', 'talkcondo' ); ?>
									</a>
									<br>
									<a href="<?php echo get_theme_mod( 'social_instagram' ); ?>" target="_blank" rel="noopener">
										<?php esc_html_e( 'Instagram', 'talkcondo' ); ?>
									</a>
									<br>
									<a href="<?php echo get_theme_mod( 'social_pinterest' ); ?>" target="_blank" rel="noopener">
										<?php esc_html_e( 'Pinterest', 'talkcondo' ); ?>
									</a>
								</p>
								<p>
									<a href="https://www.talkcondo.com/about-us/">
										About Us
									</a>
									<br>
									<a href="/insider-club">
										Insider Club
									</a>
									<br>
									<a href="https://www.talkcondo.com/press-center/">
										Press Center
									</a>
									<br>
									<a href="https://www.talkcondo.com/contact-us/">
										Contact Us
									</a>
								</p>
								<p>
									<a href="https://www.talkcondo.com/privacy-policy/">Privacy Policy
										<br>
									</a>
								</p>
							</div>
							<a href="https://www.talkcondo.com/privacy-policy/">
								<span class="seperator extralight-border"></span>
							</a>
						</section>
						<section id="text-8" class="widget clearfix widget_text">
							<a href="https://www.talkcondo.com/privacy-policy/">
								<div class="textwidget">
									<script type="text/javascript">
										var om_load_webfont = false;
									</script>
								</div>
								<span class="seperator extralight-border"></span>
							</a>
						</section>
					</div>

				</div>

				<?php do_action('avia_after_footer_columns') ?>
			</div>
		</div>
	<?php endif; ?>

	<?php
	$copyright = do_shortcode( avia_get_option('copyright', "&copy; ".__('Copyright','avia_framework')."  - <a href='".home_url('/')."'>".get_bloginfo('name')."</a>") );
	$kriesi_at_backlink = kriesi_backlink(get_option(THEMENAMECLEAN."_initial_version"));
	if($copyright && strpos($copyright, '[nolink]') !== false) {
		$kriesi_at_backlink = "";
		$copyright = str_replace("[nolink]","",$copyright);
	}
	?>
	<?php if( $footer_widget_setting != 'nosocket' ): ?>
		<footer class='container_wrap socket_color' itemscope itemtype="http://schema.org/WPFooter" id='socket' <?php avia_markup_helper(array('context' => 'footer')); ?>>
			<div class='container'>
				<?php echo do_shortcode('[content_block id=5364]'); ?>
				<span class='copyright'><?php echo $copyright . $kriesi_at_backlink; ?></span>
				<?php
					if(avia_get_option('footer_social', 'disabled') != "disabled") {
						$social_args 	= array('outside'=>'ul', 'inside'=>'li', 'append' => '');
						echo avia_social_media_icons($social_args, false);
					}

					echo "<nav class='sub_menu_socket' ".avia_markup_helper(array('context' => 'nav', 'echo' => false)).">";
						$avia_theme_location = 'avia3';
						$avia_menu_class = $avia_theme_location . '-menu';

						$args = array(
							'theme_location'=>$avia_theme_location,
							'menu_id' =>$avia_menu_class,
							'container_class' =>$avia_menu_class,
							'fallback_cb' => '',
							'depth'=>1
						);

						wp_nav_menu($args);
					echo "</nav>";
				?>
			</div>
		</footer>
	<?php endif; ?>

<?php endif; ?>

