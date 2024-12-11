<?php

defined( 'ABSPATH' ) || die();

global $avia_config;

$blank       = isset($avia_config['template']) ? $avia_config['template'] : "";
$responsive  = avia_get_option('responsive_active') != "disabled" ? "responsive" : "fixed_layout";
$av_lightbox = avia_get_option('lightbox_active') != "disabled" ? 'av-default-lightbox' : 'av-custom-lightbox'; ?>

<!DOCTYPE html>

<html <?php language_attributes() ?> class="<?php echo $responsive." ".$av_lightbox." ".avia_header_class_string() ?> ">

<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
    <script src="https://embed.lpcontent.net/leadboxes/current/embed.js" async defer></script>

    <?php wp_head(); ?>

    <?php if(is_page( array( 'register') ) ): ?>
        <meta name="robots" content="noindex,nofollow">
    <?php endif; ?>

	<meta name="google-site-verification" content="kcod3KG7hJGWzRWWdNQrsnU2cYw7PtwKynBNadM7jiQ" />

	<?php if (is_live()) include "snippets/facebook-pixel.php" ?>

</head>

<body id="top" <?php body_class( $blank ); ?> itemscope itemtype="http://schema.org/WebPage">

	<script>
		var home_url = '<?= home_url() ?>';
		var ajax_url = '<?= admin_url( 'admin-ajax.php' ) ?>';
		var theme_url = '<?= get_stylesheet_directory_uri() ?>';
		var map_data_url = '<?= get_stylesheet_directory_uri() . "/library/legacy/gsheet_import/data/mapData.json" ?>';
	</script>

	<div id="wrap_all">

		<?php if (!$blank): ?>
		<header id='header' class='header_color'>
			<a id="advanced_menu_toggle" href="#" <?= av_icon_string('mobile_menu') ?>></a>
			<a id="advanced_menu_hide" href="#" <?= av_icon_string('close') ?>></a>
			<div id='header_main' class='container_wrap container_wrap_logo'>
				<div class='container-fluid'>
					<?php the_custom_logo() ?>
					<div id="header-search">
						<?php get_search_form() ?>
				 	</div>
					<nav class='main_menu'>
						<?php wp_nav_menu([
							'theme_location'  => 'avia',
							'menu_id'         => 'avia-menu',
							'menu_class'      => 'menu av-main-nav',
							'container_class' => 'avia-menu av-main-nav-wrap',
							'fallback_cb'     => 'avia_fallback_menu',
							'walker'          => new avia_responsive_mega_menu()
						]) ?>
					</nav>
					<a href="https://talkcondo.lpages.co/leadbox/147764973f72a2%3A17f97ed63b46dc/5638830484881408/" target="_blank" id="insider-access" class="btn btn-alt desktop-only">Get Insider Access</a>
					<script data-leadbox="147764973f72a2:17f97ed63b46dc" data-url="https://talkcondo.lpages.co/leadbox/147764973f72a2%3A17f97ed63b46dc/5638830484881408/" data-config="%7B%7D" type="text/javascript" src="https://talkcondo.lpages.co/leadbox-1551884709.js"></script>
					<img id="brokerage-logo" src="<?= get_stylesheet_directory_uri() . '/assets/images/sage-logo.png' ?>">
				</div>
			</div>
		</header>
		<?php endif ?>


		<div id="main">

			<?php if (!is_home() && !is_front_page()): ?>

				<?php if ( get_post_type() == 'project' && is_singular() ): ?>
					<div id='breadcrumbs'>
						<div class="container">
							<div class="simpleflex">
								<?php get_template_part( 'templates/project/components/tabs' ) ?>
								<div class="leadpages__button leadpages__button--top fade">
									<?php echo leadpages_form_button( get_field( 'leadpagesform' ) ) ?>
								</div>
							</div>
						</div>
					</div>
				<?php elseif ( !is_page() && !is_tax( 'city' ) && !is_tax( 'neighbourhood' ) ): ?>
					<div id='breadcrumbs'>
						<div class="container">
							<?php echo avia_breadcrumbs(['before' => '']) ?>
						</div>
					</div>
				<?php endif ?>

			<?php endif ?>

			<?php if ( is_front_page() ) get_template_part('templates/home/search') ?>
