<?php
/**
 * The header for our theme
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( '' === 'it is only for code editors to pass some redundant inspections' ) :
	?>
	<!--suppress HtmlRequiredLangAttribute, HtmlRequiredTitleElement -->
	<?php
endif;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php

/**
 * Fire the wp_body_open action.
 *
 * @link https://make.wordpress.org/core/2019/04/24/miscellaneous-developer-updates-in-5-2/
 */
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}

?>

<!-- th-site -->
<div class="th-site">
	<!-- th-site__mobile-header -->
	<header class="th-site__mobile-header">
		<?php get_template_part( 'partials/mobile-header/header' ); ?>
	</header>
	<!-- th-site__mobile-header / end -->

	<!-- th-site__header -->
	<header class="th-site__header">
		<?php get_template_part( 'partials/desktop-header/index' ); ?>
	</header>
	<!-- th-site__header / end -->

	<!-- th-site__body -->
	<div class="th-site__body">
