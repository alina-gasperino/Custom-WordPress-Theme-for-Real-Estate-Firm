<?php

/**
 * Engine
 *
 * Create the bridge between the theme and Ascripta.
 */

// The current theme version.
define( 'THEME_CURRENT_VERSION', '1.4.4' );

// Declare support for the engine.
add_theme_support( 'ascripta' );

// Declare that this is a custom theme.
add_theme_support( 'ascripta-custom-theme' );


/**
 * Check for Issues
 * 
 * Require the Ascripta plugin before loading the theme and make sure the theme is built.
 */ 

if( ( !class_exists( 'AE_Plugin' ) || !file_exists( get_template_directory() . '/assets/css/style.css' ) ) && is_user_logged_in() && current_user_can( 'edit_plugins' ) && !is_admin() ){ ?>

	<div class="error">

		<p class="error-back">
			<a href="<?php echo admin_url(); ?>">
				&larr; <?php esc_html_e( 'Back to Admin', 'talk' ); ?>
			</a>
		</p>

		<?php if( !class_exists( 'AE_Plugin' ) ): ?>

			<h1 class="error-title">
				<?php esc_html_e( 'The current theme requires the Ascripta plugin in order to work properly.', 'talk' ); ?>
			</h1>

			<div class="error-content">
				<p>
					<?php printf( esc_html__( 'Please go to the %1$splugins%4$s view in the dashboard to install and activate it. If you cannot find the plugins in the admin area, you can %2$sdownload it for free%4$s from the official website and %3$sinstall it manually%4$s.', 'talk' ), '<a href="' . admin_url( 'plugins.php' ) . '">', '<a href="//ascripta.com" target="_blank">', '<a href="https://codex.wordpress.org/Managing_Plugins#Installing_Plugins" target="_blank">', '</a>' ); ?>
				</p>
				<p>
					<?php printf( esc_html__( 'If you need further assistance, please %1$sget in touch%2$s with the theme author.', 'talk' ), '<a href="' . wp_get_theme()->get( 'AuthorURI' ) . '" target="_blank">', '</a>' ); ?>
				</p>
			</div>

		<?php elseif( !file_exists( get_template_directory() . '/assets/css/style.css' ) ): ?>

			<h1 class="error-title">
				<?php esc_html_e( 'The current theme must be compiled before it can be viewed properly.', 'talk' ); ?>
			</h1>

			<div class="error-content">
				<p>
					<?php printf( esc_html__( 'Navigate to the theme directory and run the %1$snpm install%2$s command on it. That will automatically run the %1$sgrunt%2$s command which compiles the theme.', 'talk' ), '<code>', '</code>' ); ?>
				</p>
				<p>
					<?php printf( esc_html__( 'If you need further assistance, learn more about %1$sNPM%4$s and %2$sGrunt.js%4$s, or %3$sget in touch %4$s with the theme author.', 'talk' ), '<a href="https://docs.npmjs.com/cli/install" target="_blank">', '<a href="https://gruntjs.com/getting-started" target="_blank">', '<a href="' . wp_get_theme()->get( 'AuthorURI' ) . '" target="_blank">', '</a>' ); ?>
				</p>
			</div>

		<?php endif; ?>

	</div>

	<style>
		.error {
			color: #333;
			margin: 0 auto;
			padding: 1.5rem;
			width: 40rem;
			max-width: 100%;
			font-family: sans-serif;
			box-sizing: border-box;
		}
		.error-back a {
			text-decoration: none;
			font-style: italic;
			color: #666;
		}
		.error-back a:hover,
		.error-back a:focus {
			color: #CE422D;
		}
		.error-title {
			font-size: 1.75rem;
			margin: 1.5rem 0;
			line-height: 1.4;
		}
		.error-content {
			line-height: 1.8;
		}
		.error-content a {
			color: #CE422D;
			font-weight: bold;
			border-bottom: 1px solid #CE422D;
			text-decoration: none;
			padding-bottom: 0.25em;
		}
		.error-content a:hover,
		.error-content a:focus {
			color: #666;
			border-color: #666;
		}
	</style>

	<?php exit(); ?>

<?php }
