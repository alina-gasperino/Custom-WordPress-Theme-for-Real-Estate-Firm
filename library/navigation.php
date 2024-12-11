<?php

/**
 * Navigation
 *
 * Register the navigation menus.
 */

if( !function_exists( 'talk_navigation' ) ):

function talk_navigation(){

	register_nav_menus( array( 
		'avia'  => esc_html__( 'Main Menu'     , 'talkcondo' ),
        'avia2' => esc_html__( 'Secondary Menu', 'talkcondo' ),
        'avia3' => esc_html__( 'Footer Menu'   , 'talkcondo' )
    ) );
	
}

endif;

add_action( 'after_setup_theme', 'talk_navigation' );


/**
 * Brand Markup
 *
 * Markup the logo with Bootstrap.
 */

if ( !function_exists( 'talk_logo_class' ) ):

    function talk_logo_class($html){

		$html = str_replace( 'custom-logo-link', 'navbar-brand', $html );
        $html = str_replace( 'custom-logo', 'navbar-brand__logo', $html );

        return $html;

    }

endif;

add_filter( 'get_custom_logo', 'talk_logo_class' );
