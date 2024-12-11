<?php

/**
 * Translation
 *
 * Make theme available for translation.
 * Translations can be filed in the /library/languages/ directory.
 * If you're building a theme based on Talk Condo, use a find and replace
 * to change 'talk' to the name of your theme in all the template files.
 */

if( !function_exists( 'talk_translation' ) ):

function talk_translation() {

	load_theme_textdomain( 'talk', get_template_directory() . '/library/languages' );

}

endif;

add_action( 'after_setup_theme', 'talk_translation' );
