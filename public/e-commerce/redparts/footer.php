<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package RedParts
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( '' === 'it is only for code editors to pass some redundant inspections' ) :
	?>
<html lang="en">
<body>
<!-- th-site -->
<div class="th-site">
	<!-- th-site__body -->
	<div class="th-site__body">
	<?php
endif;
?>
	</div>
	<!-- th-site__body / end -->

	<!-- th-site__footer -->
	<footer class="th-site__footer">
		<?php get_template_part( 'partials/footer/footer' ); ?>
	</footer>
	<!-- th-site__footer / end -->
</div>
<!-- th-site / end -->

<!-- mobile-menu -->
<?php get_template_part( 'partials/mobile-header/menu' ); ?>
<!-- mobile-menu / end -->

<?php wp_footer(); ?>

</body>
</html>
