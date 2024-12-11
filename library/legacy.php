<?php

/**
 * Legacy
 *
 * Include the legacy libraries to make the theme work properly.
 */

global $avia_config;

// Create a global var which stores the ids of all posts which are displayed on the current page. It will help us to filter duplicate posts
$avia_config['posts_on_current_page'] = array();

// WPML Multi Site Config
require_once( 'legacy/config/config-wpml/config.php' );

/*
 * These are the available color sets in your backend.
 * If more sets are added users will be able to create additional color schemes for certain areas
 *
 * The array key has to be the class name, the value is only used as tab heading on the styling page
 */

$avia_config['color_sets'] = array(
    'header_color'      => 'Logo Area',
    'main_color'        => 'Main Content',
    'alternate_color'   => 'Alternate Content',
    'footer_color'      => 'Footer',
    'socket_color'      => 'Socket'
 );

// Add support for responsive mega menus
add_theme_support('avia_mega_menu');

// Deactivates the default mega menu and allows us to pass individual menu walkers when calling a menu.
add_filter('avia_mega_menu_walker', '__return_false');

/*
 * Filters for post formats etc
 */

function is_live() {
	return (home_url() == 'https://www.talkcondo.com');
}

function is_local() {
	return strpos(home_url(), 'local') !== false;
}

// AVIA FRAMEWORK by Kriesi

require_once( 'legacy/framework/avia_framework.php' );

// Register the layout classes

$avia_config['layout']['fullsize'] 		= array('content' => 'av-content-full alpha', 'sidebar' => 'hidden', 	  	  'meta' => '','entry' => '');
$avia_config['layout']['sidebar_left'] 	= array('content' => 'av-content-small', 	  'sidebar' => 'alpha' ,'meta' => 'alpha', 'entry' => '');
$avia_config['layout']['sidebar_right'] = array('content' => 'av-content-small alpha','sidebar' => 'alpha', 'meta' => 'alpha', 'entry' => 'alpha');


/*
 * These are some of the font icons used in the theme, defined by the entypo icon font. the font files are included by the new aviaBuilder
 * common icons are stored here for easy retrieval
 */

 $avia_config['font_icons'] = apply_filters('avf_default_icons', array(

    //post formats +  types
    'standard' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue836'),
    'link'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue822'),
    'image'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80f'),
    'audio'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue801'),
    'quote'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue833'),
    'gallery'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80e'),
    'video'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80d'),
    'portfolio'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue849'),
    'product'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue859'),

    //social
    'behance' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue915'),
	'dribbble' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fe'),
	'facebook' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f3'),
	'flickr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ed'),
	'gplus' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f6'),
	'linkedin' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fc'),
	'instagram' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue909'),
	'pinterest' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f8'),
	'skype' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue90d'),
	'tumblr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fa'),
	'twitter' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f1'),
	'vimeo' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ef'),
	'rss' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue853'),
	'youtube'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue921'),
	'xing'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue923'),
	'soundcloud'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue913'),
	'five_100_px'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue91d'),
	'vk'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue926'),
	'reddit'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue927'),
	'digg'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue928'),
	'delicious'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue929'),
	'mail' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue805'),

	//woocomemrce
	'cart' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue859'),
	'details'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue84b'),

	//bbpress
	'supersticky'	     => array( 'font' =>'entypo-fontello', 'icon' => 'ue808'),
	'sticky'		     => array( 'font' =>'entypo-fontello', 'icon' => 'ue809'),
	'one_voice'		     => array( 'font' =>'entypo-fontello', 'icon' => 'ue83b'),
	'multi_voice'	     => array( 'font' =>'entypo-fontello', 'icon' => 'ue83c'),
	'closed'		     => array( 'font' =>'entypo-fontello', 'icon' => 'ue824'),
	'sticky_closed'      => array( 'font' =>'entypo-fontello', 'icon' => 'ue808\ue824'),
	'supersticky_closed' => array( 'font' =>'entypo-fontello', 'icon' => 'ue809\ue824'),

	//navigation, slider & controls
	'play' 			=> array( 'font' => 'entypo-fontello', 'icon' => 'ue897'),
	'pause'			=> array( 'font' => 'entypo-fontello', 'icon' => 'ue899'),
	'next'    		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue879'),
    'prev'    		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue878'),
    'next_big'  	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue87d'),
    'prev_big'  	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue87c'),
	'close'			=> array( 'font' => 'entypo-fontello', 'icon' => 'ue814'),
	'reload'		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue891'),
	'mobile_menu'	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue8a5'),

	//image hover overlays
    'ov_external'	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue832'),
    'ov_image'		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue869'),
    'ov_video'		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue897'),


	//misc
    'search'  		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue803'),
    'info'    		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue81e'),
	'clipboard' 	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue8d1'),
	'scrolltop' 	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue876'),
	'scrolldown' 	=> array( 'font' => 'entypo-fontello', 'icon' => 'ue877'),
	'bitcoin' 		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue92a'),
	'tools' 		=> array( 'font' => 'entypo-fontello', 'icon' => 'ue856'),

));

##################################################################
# Frontend Stuff necessary for the theme:
##################################################################

/*
 * Load some frontend functions in folder include
 */

require_once( 'legacy/includes/admin/register-portfolio.php' );		// register custom post types for portfolio entries
require_once( 'legacy/includes/admin/register-widget-area.php' );		// register sidebar widgets for the sidebar and footer
require_once( 'legacy/includes/loop-comments.php' );					// necessary to display the comments properly
require_once( 'legacy/includes/helper-template-logic.php' ); 			// holds the template logic so the theme knows which tempaltes to use
require_once( 'legacy/includes/helper-social-media.php' ); 			// holds some helper functions necessary for twitter and facebook buttons
require_once( 'legacy/includes/helper-post-format.php' ); 				// holds actions and filter necessary for post formats
require_once( 'legacy/includes/helper-markup.php' ); 					// holds the markup logic (schema.org and html5)
require_once( 'legacy/includes/admin/register-plugins.php');			// register the plugins we need

if( current_theme_supports('avia_conditionals_for_mega_menu') ) {
	require_once( 'legacy/includes/helper-conditional-megamenu.php' );  // holds the walker for the responsive mega menu
}

require_once( 'legacy/includes/helper-responsive-megamenu.php' ); 		// holds the walker for the responsive mega menu



// Adds the plugin initalization scripts that add styles and functions

if( !current_theme_supports('deactivate_layerslider') ) {
	require_once( 'legacy/config/config-layerslider/config.php' );   // Layerslider plugin
}

require_once( 'legacy/config/config-bbpress/config.php' );			  // Compatibility with  bbpress forum plugin
if( !is_admin() ) {
	require_once( 'legacy/config/config-templatebuilder/config.php' );	  // Templatebuilder plugin
}
require_once( 'legacy/config/config-gravityforms/config.php' );	  // Compatibility with gravityforms plugin
require_once( 'legacy/config/config-woocommerce/config.php' );		  // Compatibility with woocommerce plugin
require_once( 'legacy/config/config-wordpress-seo/config.php' );	  // Compatibility with Yoast WordPress SEO plugin
require_once( 'legacy/config/config-events-calendar/config.php' );	  // Compatibility with the Events Calendar plugin


if( is_admin() ) {
	require_once( 'legacy/includes/admin/helper-compat-update.php'); // Include helper functions for new versions
}


/*
 * Dynamic styles for front and backend
 */

if( !function_exists( 'avia_custom_styles' ) ) {

	function avia_custom_styles() {
		require_once( 'legacy/includes/admin/register-dynamic-styles.php' );
		avia_prepare_dynamic_styles();
	}

	add_action('init', 'avia_custom_styles', 20);
	add_action('admin_init', 'avia_custom_styles', 20);

}


/*
 * Register custom functions that are not related
 * to the framework but necessary for the theme to run.
 */

require_once 'legacy/functions-enfold.php';
require_once 'legacy/functions-talk-condo.php';
require_once 'legacy/functions-floorplans.php';
