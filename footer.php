<?php

defined( 'ABSPATH' ) || die();

/**
 * Footer
 *
 * Used to manage the footer of the pages.
 *
 * @link http://codex.wordpress.org/Function_Reference/get_footer
 */

?>


				<?php if ( !is_page_template( 'templates/template-map.php' ) && !is_tax( array( 'district', 'city', 'neighbourhood' ) ) ): ?>
					<?php get_template_part( 'templates/template-avia-footer' ); ?>
				<?php endif ?>

			</div>

		</div>

		<?php if(isset($avia_config['fullscreen_image'])): ?>
			<div class='bg_container' style='background-image:url("<?php echo $avia_config['fullscreen_image'] ?>");'></div>
		<?php endif; ?>

		<?php get_template_part( 'templates/floorplan-quickview-modal' ) ?>

		<a href='#top' title='<?php _e('Scroll to top','avia_framework') ?>' id='scroll-top-link' <?php echo av_icon_string( 'scrolltop' ) ?>>
			<span class="avia_hidden_link_text">
				<?php _e('Scroll to top','avia_framework') ?>
			</span>
		</a>

		<?php wp_footer() ?>

	</body>

</html>
