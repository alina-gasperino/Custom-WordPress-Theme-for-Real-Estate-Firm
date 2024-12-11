<?php

/**
 * Image Sizes
 *
 * Add image sizes for the media library.
 */

if( !function_exists( 'talk_image_sizes' ) ):

function talk_image_sizes(){

	// Add the image size for loop featured images.
	// add_image_size( 'loop' , 750, 200, true );

	// Add the image size for entry featured images.
	// add_image_size( 'entry', 750, 400, true );

	// Add the custom image sizes.
	add_image_size( 'widget', 36, 36 );					  // Small preview pics eg sidebar news
	add_image_size( 'square', 180, 180 );		          // Small image for blogs
	add_image_size( 'featured', 1500, 430 );			  // Images for fullsize pages and fullsize slider
	add_image_size( 'featured_large', 1500, 630 );		  // Images for fullsize pages and fullsize slider
	add_image_size( 'extra_large', 1500, 1500 );	      // Images for fullscrren slider
	add_image_size( 'portfolio', 495, 400, true );		  // Images for portfolio entries (2,3 column)
	add_image_size( 'portfolio_small', 260, 185, true );  // Images for portfolio 4 columns
	add_image_size( 'portfolio_map', 260, 260, true );	  // Images for the map projects.
	add_image_size( 'project_small', 200, 200, true );    // Images for portfolio 4 columns
	add_image_size( 'project_icon', 145, 145, true );	  // Images for portfolio 4 columns
	add_image_size( 'gallery', 710, 575, true );		  // Images for portfolio entries (2,3 column)
	add_image_size( 'entry_with_sidebar', 710, 270 );	  // Big images for blog and page entries
	add_image_size( 'entry_without_sidebar', 1030, 360 ); // Images for fullsize pages and fullsize slider

}

endif;

add_action( 'after_setup_theme', 'talk_image_sizes' );


/**
 * Register the selectable image sizes.
 */
 
function talk_selectable_sizes( $sizes ) {

    return array_merge( $sizes, array(
        'square' 				=> __( 'Square', 'talkcondo' ),
		'featured'  			=> __( 'Featured Thin', 'talkcondo' ),
		'featured_large'  		=> __( 'Featured Large', 'talkcondo' ),
		'portfolio' 			=> __( 'Portfolio', 'talkcondo' ),
		'gallery' 				=> __( 'Gallery', 'talkcondo' ),
		'entry_with_sidebar' 	=> __( 'Entry with Sidebar', 'talkcondo' ),
		'entry_without_sidebar'	=> __( 'Entry without Sidebar', 'talkcondo' ),
		'extra_large' 			=> __( 'Fullscreen Sections/Sliders', 'talkcondo' ),
	) );
	
}

add_filter( 'image_size_names_choose', 'talk_selectable_sizes' );