<?php

/**
 * Theme Support
 *
 * Add theme support for various features.
 */

if( !function_exists( 'talk_support' ) ):

function talk_support(){

	// Add custom logo support.
 	add_theme_support( 'custom-logo', array(
		'width'       => 347,
		'height'      => 156,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	// Set the content width.
	$GLOBALS['content_width'] = 1500;

	// Support for Wordpress menus.
	add_theme_support( 'menus' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/**
	 * Add post format options.
	 */ 
	add_theme_support( 'post-formats', array(
		'link', 
		'quote', 
		'gallery',
		'video',
		'image',
		'audio' 
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/**
	 * Miscellaneous
	 */

	// Remove the default shortcode function, we got new ones that are better ;)
	add_theme_support( 'avia-disable-default-shortcodes', true );

	// Compat mode for easier theme switching from one avia framework theme to another
	add_theme_support( 'avia_post_meta_compat' );

	// Make sure that enfold widgets dont use the old slideshow parameter in widgets, but default post thumbnails
	add_theme_support( 'force-post-thumbnails-in-widget' );

}

endif;

add_action( 'after_setup_theme', 'talk_support' );


/**
 * Excerpt More
 * 
 * Remove the default "continue reading" link from post excerpts.
 */

add_filter( 'excerpt_more', '__return_null' );
