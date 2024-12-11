<?php

/**
 * Stylesheets
 *
 * Enqueue stylesheet files used by the theme.
 */

if( !function_exists( 'talk_enqueue_styles' ) ):

function talk_enqueue_styles() {

	$theme = get_stylesheet_directory_uri();

	$ver = is_live() ? '2020031702' : time();

	if (!is_local()) {
		wp_enqueue_style( 'roboto', '//fonts.googleapis.com/css?family=Roboto', [], null, true );
	}

	if (is_live()) {
		wp_dequeue_style( 'font-awesome' );
		wp_deregister_style( 'font-awesome' );
		wp_enqueue_style('font-awesome', 'https://use.fontawesome.com/releases/v5.11.2/css/all.css', [], null, false);
		wp_enqueue_style('google-material-ui-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', [], null, false);
		wp_deregister_style( 'select2' );
		wp_enqueue_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css', [], null, false );
	} else {
		wp_dequeue_style( 'font-awesome' );
		wp_deregister_style( 'font-awesome' );
		wp_enqueue_style('font-awesome', $theme . '/assets/vendor/fontawesome-free/css/all.css', [], null, false);
		wp_enqueue_style('google-material-ui-icons', $theme . '/assets/vendor/material-design-icons/material-icons.css', [], null, false);
		wp_deregister_style( 'select2' );
		wp_enqueue_style( 'select2', $theme . '/assets/vendor/select2/css/select2.css', [], null, false );
	}

	if (!is_front_page()) {
		wp_enqueue_style( 'magnific-popup', $theme . "/assets/vendor/magnific-popup/dist/magnific-popup.css", [], $ver, 'screen' );
		wp_enqueue_style( 'fancybox', $theme . '/assets/vendor/fancybox/jquery.fancybox.min.css', [], $ver, 'screen' );
		wp_enqueue_style( 'flexslider' ,  $theme . '/assets/vendor/flexslider/flexslider.css', [], $ver );
		wp_enqueue_style( 'avia-media', $theme . '/assets/js/mediaelement/skin-1/mediaelementplayer.css', [], $ver, 'screen' );
	}

	wp_enqueue_style( 'gscroll-css', $theme . '/assets/vendor/google-scrolling-carousel/jquery.gScrollingCarousel.css', [], $ver, 'screen' );
	wp_enqueue_style( 'nouislider', $theme . '/assets/vendor/nouislider/nouislider.css', [], null, false );

	global $avia;
	$safe_name = avia_backend_safe_string($avia->base_data['prefix']);

	if( get_option('avia_stylesheet_exists'.$safe_name) == 'true' )
	{
		$avia_upload_dir = wp_upload_dir();
		if(is_ssl()) $avia_upload_dir['baseurl'] = str_replace("http://", "https://", $avia_upload_dir['baseurl']);

		$avia_dyn_stylesheet_url = $avia_upload_dir['baseurl'] . '/dynamic_avia/'.$safe_name.'.css';
		$version_number = get_option('avia_stylesheet_dynamic_version'.$safe_name);
		if(empty($version_number)) $version_number = '1';

		wp_enqueue_style( 'avia-dynamic', $avia_dyn_stylesheet_url, [], $version_number, 'all' );
	}

	// Enqueue third party stylesheets.
	wp_enqueue_style( 'talk-bootstrap', get_template_directory_uri() . '/assets/css/vendor/bootstrap.css', [], $ver );

	// Enqueue the main stylesheet.
	wp_enqueue_style( 'talk-style', get_template_directory_uri() . '/assets/css/style.css', [], $ver );

}

endif;

add_action( 'wp_enqueue_scripts', 'talk_enqueue_styles' );


/**
 * Registers an editor stylesheet for the theme.
 */

if( !function_exists( 'talk_add_editor_style' ) ):

function talk_add_editor_style() {

	add_editor_style( 'assets/css/editor-style.css' );

}

endif;

add_action( 'admin_init', 'talk_add_editor_style' );
