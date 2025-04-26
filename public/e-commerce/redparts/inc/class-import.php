<?php
/**
 * This file contains code related to the import of theme data.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use Redux;
use WP_Filesystem_Base;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Import' ) ) {
	/**
	 * Class Import
	 */
	class Import extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_filter( 'pt-ocdi/import_files', array( $this, 'ocdi_files' ) );
			add_action( 'pt-ocdi/after_import', array( $this, 'ocdi_after_import' ) );
		}

		/**
		 * Returns import files for One Click Demo Import plugin.
		 *
		 * @noinspection PhpUnused
		 */
		public function ocdi_files() {
			return array(
				array(
					'import_file_name'             => 'RedParts Demo',
					'local_import_file'            => trailingslashit( get_template_directory() ) . 'data/content.xml',
					'local_import_widget_file'     => trailingslashit( get_template_directory() ) . 'data/widgets.json',
					'local_import_customizer_file' => trailingslashit( get_template_directory() ) . 'data/customizer.dat',
					'local_import_redux'           => array(
						array(
							'file_path'   => trailingslashit( get_template_directory() ) . 'data/redux-theme.json',
							'option_name' => 'redparts_settings',
						),
						array(
							'file_path'   => trailingslashit( get_template_directory() ) . 'data/redux-plugin.json',
							'option_name' => 'redparts_sputnik_settings',
						),
					),
					'import_preview_image_url'     => get_template_directory_uri() . '/screenshot.png',
					'preview_url'                  => 'https://redparts.woocommerce.themeforest.scompiler.ru/',
				),
			);
		}

		/**
		 * Assign menus to their locations.
		 *
		 * @noinspection PhpUnused
		 */
		public function ocdi_after_import() {
			if ( ! current_user_can( 'import' ) ) {
				return;
			}

			// Assign front page.
			$front_page = get_page_by_title( 'Home One' );

			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front_page->ID );

			// Change permalink structure.
			update_option( 'permalink_structure', '/%category%/%postname%/' );

			$permalinks                 = (array) get_option( 'woocommerce_permalinks', array() );
			$permalinks['product_base'] = wc_sanitize_permalink( '/shop/%product_cat%/' );

			update_option( 'woocommerce_permalinks', $permalinks );

			// Assign menus to there locations.
			$topbar_start_menu = get_term_by( 'name', 'Topbar Start', 'nav_menu' );
			$main_menu         = get_term_by( 'name', 'Main', 'nav_menu' );
			$departments_menu  = get_term_by( 'name', 'Departments', 'nav_menu' );

			set_theme_mod(
				'nav_menu_locations',
				array(
					'redparts-topbar-start' => $topbar_start_menu->term_id,
					'redparts-main'         => $main_menu->term_id,
					'redparts-departments'  => $departments_menu->term_id,
					'redparts-mobile-menu'  => $main_menu->term_id,
				)
			);

			// Assign megamenus.
			foreach ( array( $main_menu, $departments_menu ) as $menu ) {
				$items = wp_get_nav_menu_items( $menu );

				if ( empty( $items ) ) {
					continue;
				}

				foreach ( $items as $item ) {
					$megamenu_menu_id = get_post_meta( $item->ID, '_redparts_megamenu_menu_id', true );

					if ( empty( $megamenu_menu_id ) ) {
						continue;
					}

					$megamenu = get_term_by( 'name', "[$menu->name] $item->title", 'nav_menu' );

					if ( $megamenu ) {
						update_post_meta( $item->ID, '_redparts_megamenu_menu_id', $megamenu->term_id );
					}
				}
			}

			// Assign menus to plugin settings.
			$social_links_menu  = get_term_by( 'name', 'Social Links', 'nav_menu' );
			$share_buttons_menu = get_term_by( 'name', 'Share Buttons', 'nav_menu' );

			Redux::set_option( 'redparts_sputnik_settings', 'social_links_menu', $social_links_menu->term_id );
			Redux::set_option( 'redparts_sputnik_settings', 'share_buttons_menu', $share_buttons_menu->term_id );

			// Assign menus to widgets.
			$widget_footer_links = get_option( 'widget_redparts_sputnik_footer_links' );

			if ( is_array( $widget_footer_links ) ) {
				foreach ( $widget_footer_links as $widget_id => $widget_settings ) {
					if ( empty( $widget_settings['columns'] ) ) {
						continue;
					}

					foreach ( $widget_settings['columns'] as $column_index => $column ) {
						if ( empty( $column['title'] ) ) {
							continue;
						}

						$menu_name = 'Footer [' . $column['title'] . ']';
						$menu      = get_term_by( 'name', $menu_name, 'nav_menu' );

						if ( $menu ) {
							$widget_footer_links[ $widget_id ]['columns'][ $column_index ]['menu'] = (string) $menu->term_id;
						}
					}
				}

				update_option( 'widget_redparts_sputnik_footer_links', $widget_footer_links );
			}

			// Updating the URL of custom links if the site is in a subdirectory.
			$parts = wp_parse_url( get_site_url() );

			if ( ! empty( $parts['path'] ) && ! empty( rtrim( $parts['path'], '\\' ) ) ) {
				$base_path = rtrim( $parts['path'], '\\' );

				$items = wp_get_nav_menu_items( $main_menu );

				foreach ( $items as $item ) {
					if ( 'custom' !== $item->type || 1 !== preg_match( '#^/([^/]|$)#', $item->url ) ) {
						continue;
					}

					$new_url = $base_path . $item->url;

					if ( substr( $item->url, 0, strlen( $base_path ) ) === $base_path ) {
						continue;
					}

					update_post_meta( $item->ID, '_menu_item_url', esc_url_raw( $new_url ) );
				}
			}

			// Updating WooCommerce attributes.
			$file_path = get_template_directory() . '/data/woocommerce-attributes.json';

			/** Filesystem object. @var WP_Filesystem_Base $wp_filesystem */
			global $wp_filesystem;

			if ( file_exists( $file_path ) && WP_Filesystem() && $wp_filesystem ) {
				$content = $wp_filesystem->get_contents( $file_path );

				if ( false !== $content ) {
					$attrs_data = json_decode( $content, true );
					$attrs      = wc_get_attribute_taxonomies();

					foreach ( $attrs as $attr ) {
						if ( empty( $attrs_data[ $attr->attribute_name ] ) ) {
							continue;
						}

						$attr_data = $attrs_data[ $attr->attribute_name ];

						$args = array(
							'name'         => $attr_data['attribute_label'],
							'slug'         => $attr_data['attribute_name'],
							'type'         => $attr_data['attribute_type'],
							'order_by'     => $attr_data['attribute_orderby'],
							'has_archives' => $attr_data['attribute_public'],
						);

						wc_update_attribute( $attr->attribute_id, $args );
					}
				}
			}
		}
	}
}
