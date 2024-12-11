<?php

/**
 * Sidebars
 *
 * Register the Wordpress widgetized areas.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */

if( !function_exists( 'talk_sidebars' ) ):

	function talk_sidebars() {

		register_sidebar( array(
			'id'            => 'sidebar-blog',
			'name'          => esc_html__( 'Blog Sidebar', 'talk' ),
			'description'   => esc_html__( 'The default Wordpress blog sidebar.', 'talk' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">', 
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">', 
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'id'            => 'sidebar-page',
			'name'          => esc_html__( 'Page Sidebar', 'talk' ),
			'description'   => esc_html__( 'The default Wordpress page sidebar.', 'talk' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">', 
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">', 
			'after_title'   => '</h4>',
		) );
		
		

	}

endif;

add_action( 'widgets_init', 'talk_sidebars' );


/**
 * Widgets
 * 
 * Register the extra WordPress widgets.
 */

if( !function_exists( 'talk_widgets' ) ):

	function talk_widgets() {

		register_widget( 'avia_newsbox' );
		register_widget( 'avia_portfoliobox' );
		register_widget( 'avia_socialcount' );
		register_widget( 'avia_combo_widget' );
		register_widget( 'avia_partner_widget' );
		register_widget( 'avia_google_maps' );
		register_widget( 'avia_fb_likebox' );

	}

endif;

add_action( 'widgets_init', 'talk_widgets' );
