<?php

/**
 * Scripts
 *
 * Enqueue script files used by the theme.
 */

if( !function_exists( 'talk_enqueue_scripts' ) ):

function talk_enqueue_scripts() {

	$theme = get_stylesheet_directory_uri();

	$ver = is_live() ? '2020031702' : time();

	wp_enqueue_script( 'lazy',  $theme . '/assets/js/lazyload.min.js', [], $ver );

	if (!is_front_page()) {

		wp_enqueue_script( 'leadbox-custom', $theme . "/assets/js/leadbox-custom.js", [], $ver, true );

		wp_enqueue_script( 'magnific-popup', $theme . '/assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js', ['jquery'], $ver, true);
		wp_enqueue_script( 'tablesorter', $theme . '/assets/vendor/tablesorter/dist/js/jquery.tablesorter.js', ['jquery'], $ver, true );
		wp_enqueue_script( 'flexslider', $theme . '/assets/vendor/flexslider/jquery.flexslider-min.js', ['jquery'], $ver );
		wp_enqueue_script( 'fancybox', $theme . '/assets/vendor/fancybox/jquery.fancybox.min.js', ['jquery'], $ver, true );

		wp_enqueue_script( 'googlemapsapi', "https://maps.googleapis.com/maps/api/js?key=" . GOOGLE_MAPS_API_BROWSER_KEY . "&libraries=places", [], $ver, true );
		wp_enqueue_script( 'markerclusterer', $theme . '/assets/js/markerclusterer.js', ['jquery'], $ver, true );
		wp_enqueue_script( 'talkmap', $theme . '/assets/js/talkMap.jquery.js', ['jquery', 'lazy'], $ver, true );

		wp_enqueue_script( 'ez-plus', $theme . '/assets/vendor/ez-plus/jquery.ez-plus.js', ['jquery'], $ver, true );
		wp_enqueue_script( 'waypoints', $theme . '/assets/vendor/waypoints/lib/noframework.waypoints.min.js', [], $ver, true );
		wp_enqueue_script( 'wp-mediaelement' );

		if (is_live()) {
			wp_deregister_script( 'select2' );
			wp_enqueue_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js', ['jquery'], null, true );
		} else {
			wp_deregister_script( 'select2' );
			wp_enqueue_script( 'select2', $theme . '/assets/vendor/select2/js/select2.min.js', ['jquery'], null, true );
		}

		if (!is_singular('project')) {
			// wp_enqueue_script( 'avia-compat', $theme . '/assets/js/avia-compat.js', ['jquery'], $ver, false );
			wp_enqueue_script( 'sticky', $theme . '/assets/js/sticky.min.js', ['jquery'], $ver, true );
			wp_enqueue_script( 'typeahead', $theme . '/assets/js/typeahead.bundle.min.js', ['jquery'], $ver, true );
		}

	}

	wp_enqueue_script( 'gscroll-js', $theme . '/assets/vendor/google-scrolling-carousel/jquery.gScrollingCarousel.js', ['jquery'], $ver, true );
	wp_enqueue_script( 'nouislider', $theme . '/assets/vendor/nouislider/nouislider.js', [], $ver, true );

	wp_enqueue_script( 'avia-default', $theme . '/assets/js/avia.js', ['jquery'], $ver, true );

	// Enqueue plugin libraries.
	wp_enqueue_script( 'talk-plugins', $theme . '/assets/js/plugins.min.js', ['jquery'], $ver, true );

	// Enqueue the main scripts file.
	wp_enqueue_script( 'talk-scripts', $theme . '/assets/js/scripts.min.js', ['jquery', 'lazy'], $ver, true );
	wp_localize_script( 'talk-scripts', 'global', [
		'theme_url' => $theme
	]);

}

endif;

add_action( 'wp_enqueue_scripts', 'talk_enqueue_scripts' );


/**
 * Webfonts
 *
 * Include the webfonts before anything else in the head.
 */

if( !function_exists( 'talk_webfonts' ) ):

	function talk_webfonts() { ?>

		<script>
			var WebFontConfig = {
				google: {
					families: [
						'Roboto:300,400,700,800'
					]
				},
				active: function() {
					sessionStorage.fonts = true;
				}
			};

			(function() {
				var wf = document.createElement('script');
				wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
				wf.type = 'text/javascript';
				wf.async = 'true';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(wf, s);
			})();
		</script>

	<?php }

endif;

add_action( 'wp_enqueue_scripts', 'talk_webfonts' );


/**
 * Live Only
 *
 * The follow scripts are only loaded on a live site, non-localhost.
 */

function talk_live_scripts() {

	if ( !is_local() ){ ?>

		<div id="fb-root"></div>

		<script>
			(function() {
				var _fbq = window._fbq || (window._fbq = []);
				if (!_fbq.loaded) {
					var fbds = document.createElement('script');
					fbds.async = true;
					fbds.src = '//connect.facebook.net/en_US/fbds.js';
					var s = document.getElementsByTagName('script')[0];
					s.parentNode.insertBefore(fbds, s);
					_fbq.loaded = true;
				}
				_fbq.push(['addPixelId', '704628952986871']);
			})();
			window._fbq = window._fbq || [];
			window._fbq.push(['track', 'PixelInitialized', {}]);
		</script>

		<noscript>
			<img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=704628952986871&amp;ev=PixelInitialized" />
		</noscript>

		<script type="text/javascript">
			//<![CDATA[
				var google_conversion_id = 970584102;
				var google_custom_params = window.google_tag_params;
				var google_remarketing_only = true;
			//]]>
		</script>

		<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>

		<noscript>
			<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/970584102/?value=0&amp;guid=ON&amp;script=0"/>
			</div>
		</noscript>

	<?php }

}

add_action( 'wp_footer', 'talk_live_scripts' );
