<?php
/**
 * RedParts customizer.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use WP_Customize_Manager;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Customizer' ) ) {
	/**
	 * Class Customizer.
	 */
	class Customizer extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'customize_register', array( $this, 'register' ) );
			add_action( 'customize_preview_init', array( $this, 'enqueue_scripts' ) );
			add_filter( 'customize_dynamic_setting_args', array( $this, 'dynamic_setting_args' ), 10, 2 );
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			wp_enqueue_script(
				'redparts-customizer',
				get_template_directory_uri() . '/assets/js/customizer.js',
				array( 'customize-preview' ),
				RED_PARTS_VERSION,
				true
			);
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function register( WP_Customize_Manager $wp_customize ) {
			$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
			$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

			if ( isset( $wp_customize->selective_refresh ) ) {
				$wp_customize->selective_refresh->add_partial(
					'blogname',
					array(
						'selector'        => '.site-title a',
						'render_callback' => array( $this, 'partial_blogname' ),
					)
				);
				$wp_customize->selective_refresh->add_partial(
					'blogdescription',
					array(
						'selector'        => '.site-description',
						'render_callback' => array( $this, 'partial_blogdescription' ),
					)
				);
				$wp_customize->selective_refresh->add_partial(
					'redparts_settings[mobile_header_logo]',
					array(
						'selector'        => '.th-logo--mobile .th-logo__image--mobile',
						'render_callback' => array( $this, 'mobile_header_logo' ),
					)
				);
			}
		}

		/**
		 * Modifies the customizer settings arguments.
		 *
		 * @param false|array $args The arguments to the WP_Customize_Setting constructor.
		 * @param string      $id   ID for dynamic setting, usually coming from `$_POST['customized']`.
		 *
		 * @return array
		 */
		public function dynamic_setting_args( $args, string $id ) {
			if ( 'redparts_settings[mobile_header_logo]' === $id ) {
				$args['transport'] = 'postMessage';
			}

			return $args;
		}

		/**
		 * Render the site title for the selective refresh partial.
		 *
		 * @noinspection PhpUnused
		 */
		public function partial_blogname() {
			bloginfo( 'name' );
		}

		/**
		 * Render the site tagline for the selective refresh partial.
		 *
		 * @noinspection PhpUnused
		 */
		public function partial_blogdescription() {
			bloginfo( 'description' );
		}

		/**
		 * Render the mobile logo for the selective refresh partial.
		 *
		 * @noinspection PhpUnused
		 */
		public function mobile_header_logo() {
			$header = Header::instance();

			ob_start();

			$header->the_mobile_logo();

			return ob_get_clean();
		}
	}
}
