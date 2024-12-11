<?php

/**
 * Customizer
 *
 * Implement theme support for custom data.
 */

if( !class_exists( 'talk_customizer' ) ) {

	class talk_customizer {

		// Declare the empty settings array.
		public $sections = array();

		/**
		 * Construct
		 * 
		 * Construct the customizer class.
		 */
		
		public function __construct() {
			
			// Header
			$this->sections[] = array(
				'id'     => 'header',
				'args'   => array(
					'title'       => __( 'Header', 'talk' ),
					'priority'    => 100,
				),
				'fields' => array(
					array(
						'id'      => 'header_sage',
						'type'    => 'WP_Customize_Media_Control',
						'args'    => array(
							'label'       => __( 'Sage Logo', 'talk' ),
						)
					),
					array(
						'id'      => 'header_sage',
						'type'    => 'WP_Customize_Control',
						'args'    => array(
							'label'       => __( 'Sage Link', 'talk' ),
						)
					),
				)
			);

			// Hook to the Wordpress register action.
			add_action( 'customize_register', array( $this, 'initialize' ) );

		}
		
		/**
		 * Initialize
		 * 
		 * Register the settings to the Customizer.
		 */

		public function initialize ( $wp_customize ) {

			$this->register( $wp_customize, $this->sections );

		}

		/**
		 * Register
		 * 
		 * Go through the sections and fields and register to the Customizer.
		 */

		public function register ( $wp_customize, $sections ) {

			foreach( $sections as $section ) {

				// Add the section if the arguments are set.
				if( isset( $section['args'] ) ) {
					$wp_customize->add_section( $section['id'], $section['args'] );
				}

				// Add the fields to the section.
				foreach( $section['fields'] as $field ) {

					// Initialize the setting array if it's undeclared.
					if( !isset( $field['setting'] ) ) {
						$field['setting'] = array();
					}
					
					// Inherit the default value from the field array.
					$field['setting']['default'] = $field['default'];

					// Add the setting to the Customizer.
					$wp_customize->add_setting( 
						$field['id'], 
						$field['setting'] 
					);

					// Adjust the control arguments to assist automation.
					$field['args']['section'] = $section['id'];
					$field['args']['settings'] = $field['id'];

					// Add the control to the Customizer.
					$wp_customize->add_control( new $field['type'] ( 
						$wp_customize, 
						$field['id'] . '_control', 
						$field['args'] ) 
					);

				}

			}

		}

	}

	return new talk_customizer();

}
