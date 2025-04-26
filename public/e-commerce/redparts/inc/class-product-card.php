<?php
/**
 * This file contains code related to the product card only.
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

use RedParts\Sputnik\Featured_Attributes;
use RedParts\Sputnik\Compare;
use RedParts\Sputnik\Garage;
use RedParts\Sputnik\Wishlist;
use RedParts\Sputnik\Quickview;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Product_Card' ) ) {
	/**
	 * Class Product_Card
	 */
	class Product_Card extends Singleton {
		/**
		 * Initialization.
		 */
		public function init() {
			add_action( 'init', array( $this, 'deferred_init' ) );
		}

		/**
		 * Deferred initialization.
		 *
		 * @since 1.5.0
		 */
		public function deferred_init() {
			/**
			 * Hook: woocommerce_before_shop_loop_item.
			 */
			/**
			 * Hook: woocommerce_before_shop_loop_item_title.
			 *
			 * @hooked RedParts\Product_Card::info_open_tag - 100
			 */
			/**
			 * Hook: woocommerce_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_product_link_open  - 5
			 * @hooked woocommerce_template_loop_product_title      - 10
			 * @hooked woocommerce_template_loop_product_link_close - 15
			 */
			/**
			 * Hook: woocommerce_after_shop_loop_item_title.
			 *
			 * @hooked RedParts\Product_Card::info_close_tag - 100
			 */
			/**
			 * Hook: woocommerce_after_shop_loop_item.
			 *
			 * @hooked RedParts\Product_Card::footer         - 100
			 * @hooked RedParts\Product_Card::card_close_tag - 200
			 */

			// Remove redundant hooks.
			// Hook: woocommerce_before_shop_loop_item.
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open' );
			// Hook: woocommerce_before_shop_loop_item_title.
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail' );
			// Hook: woocommerce_shop_loop_item_title.
			// Hook: woocommerce_after_shop_loop_item_title.
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price' );
			// Hook: woocommerce_after_shop_loop_item.
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

			// Adding hooks.
			// Hook: woocommerce_before_shop_loop_item.
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'card_open_tag' ), 100 );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'actions_list' ), 200 );
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'image' ), 300 );
			// Hook: woocommerce_before_shop_loop_item_title.
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'info_open_tag' ), 100 );
			// Hook: woocommerce_shop_loop_item_title.
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 5 );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15 );
			// Hook: woocommerce_after_shop_loop_item_title.
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'info_close_tag' ), 100 );
			// Hook: woocommerce_after_shop_loop_item.
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'footer' ), 100 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'card_close_tag' ), 200 );
			// Hook: redparts_product_card_actions.
			add_action( 'redparts_product_card_actions', array( $this, 'quickview_button' ), 100 );
			if (
				class_exists( '\RedParts\Sputnik\Wishlist' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Wishlist::instance()->is_enabled()
			) {
				add_action( 'redparts_product_card_actions', array( Wishlist::instance(), 'the_button' ), 200 );
			}
			if (
				class_exists( '\RedParts\Sputnik\Compare' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Compare::instance()->is_enabled()
			) {
				add_action( 'redparts_product_card_actions', array( Compare::instance(), 'the_button' ), 300 );
			}
			// Hook: redparts_product_card_actions.
			add_action( 'redparts_product_card_image', 'woocommerce_template_loop_product_link_open', 100 );
			add_action( 'redparts_product_card_image', 'woocommerce_template_loop_product_thumbnail', 200 );
			add_action( 'redparts_product_card_image', 'woocommerce_template_loop_product_link_close', 300 );
			add_action( 'redparts_product_card_image', array( $this, 'compatibility_badge' ), 400 );
			// Hook: redparts_product_card_before_title.
			add_action( 'redparts_product_card_before_title', array( $this, 'meta' ), 100 );
			add_action( 'redparts_product_card_before_title', array( $this, 'name_open_tag' ), 200 );
			add_action( 'redparts_product_card_before_title', array( $this, 'badges' ), 300 );
			// Hook: redparts_product_card_badges.
			add_action( 'redparts_product_card_badges', 'woocommerce_show_product_loop_sale_flash', 100 );
			// Hook: redparts_product_card_after_title.
			add_action( 'redparts_product_card_after_title', array( $this, 'name_close_tag' ), 100 );
			add_action( 'redparts_product_card_after_title', array( $this, 'rating' ), 200 );
			add_action( 'redparts_product_card_after_title', array( $this, 'featured_attributes' ), 300 );
			// Hook: redparts_product_card_footer.
			add_action( 'redparts_product_card_footer', array( $this, 'prices' ), 100 );
			add_action( 'redparts_product_card_footer', array( $this, 'addtocart' ), 200 );
			if (
				class_exists( '\RedParts\Sputnik\Wishlist' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Wishlist::instance()->is_enabled()
			) {
				add_action( 'redparts_product_card_footer', array( Wishlist::instance(), 'the_button' ), 300 );
			}
			if (
				class_exists( '\RedParts\Sputnik\Compare' ) &&
				redparts_sputnik_version_is( '>=', '1.5.0' ) &&
				Compare::instance()->is_enabled()
			) {
				add_action( 'redparts_product_card_footer', array( Compare::instance(), 'the_button' ), 400 );
			}

			// This is necessary for the widgets to display the product name correctly.
			remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

			// Filters.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'addtocart_html' ), 10, 1 );
		}

		/**
		 * Outputs quickview button.
		 */
		public function quickview_button() {
			global $post;

			if ( ! post_password_required( $post ) ) {
				if ( class_exists( '\RedParts\Sputnik\Quickview' ) ) {
					Quickview::instance()->the_button( false, 'th-product-card__quickview' );
				}
			}
		}

		/**
		 * Outputs card open tag.
		 */
		public function card_open_tag() {
			global $product;
			global $woocommerce_loop;

			$has_sku = wc_product_sku_enabled() && $product->get_sku();

			$classes = array(
				'th-product-card',
				array( 'th-product-card--has-meta' => $has_sku ),
			);

			if ( ! empty( $woocommerce_loop['redparts_product_card_class'] ) ) {
				$classes[] = $woocommerce_loop['redparts_product_card_class'];
			}

			echo '<div class="' . esc_attr( redparts_get_classes( ...$classes ) ) . '">';
		}

		/**
		 * Outputs card close tag.
		 */
		public function card_close_tag() {
			echo '</div>';
		}

		/**
		 * Outputs info open tag.
		 */
		public function info_open_tag() {
			echo '<div class="th-product-card__info">';

			/**
			 * Hook: redparts_product_card_before_title.
			 *
			 * @hooked RedParts\Product_Card::meta          - 100
			 * @hooked RedParts\Product_Card::name_open_tag - 200
			 * @hooked RedParts\Product_Card::badges        - 300
			 */
			do_action( 'redparts_product_card_before_title' );
		}

		/**
		 * Outputs info close tag.
		 */
		public function info_close_tag() {
			/**
			 * Hook: redparts_product_card_after_title.
			 *
			 * @hooked RedParts\Product_Card::name_close_tag      - 100
			 * @hooked RedParts\Product_Card::rating              - 200
			 * @hooked RedParts\Product_Card::featured_attributes - 300
			 */
			do_action( 'redparts_product_card_after_title' );

			echo '</div>';
		}

		/**
		 * Outputs meta.
		 */
		public function meta() {
			global $product;

			if ( wc_product_sku_enabled() && $product->get_sku() ) :
				?>
				<div class="th-product-card__meta">
					<span class="th-product-card__meta-title">
						<?php esc_html_e( 'SKU: ', 'redparts' ); ?>
					</span>
					<span class="th-product-card__meta-value">
						<?php echo esc_html( $product->get_sku() ); ?>
					</span>
				</div>
				<?php
			else :
				echo '<div class="th-product-card__meta"></div>';
			endif;
		}

		/**
		 * Outputs name open tag.
		 */
		public function name_open_tag() {
			echo '<div class="th-product-card__name">';
		}

		/**
		 * Outputs name close tag.
		 */
		public function name_close_tag() {
			echo '</div>';
		}

		/**
		 * Outputs badges.
		 */
		public function badges() {
			echo '<div class="th-product-card__badges">';

			/**
			 * Hook: redparts_product_card_badges.
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 100
			 */
			do_action( 'redparts_product_card_badges' );

			echo '</div>';
		}

		/**
		 * Outputs actions list.
		 */
		public function actions_list() {
			echo '<div class="th-product-card__actions-list">';

			/**
			 * Hook: redparts_product_card_actions.
			 *
			 * @hooked RedParts\Product_Card::quickview_button - 100
			 * @hooked RedParts\Sputnik\Wishlist::the_button   - 200
			 * @hooked RedParts\Sputnik\Compare::the_button    - 300
			 */
			do_action( 'redparts_product_card_actions' );

			echo '</div>';
		}

		/**
		 * Outputs image.
		 */
		public function image() {
			/**
			 * Hook: redparts_product_card_before_image.
			 */
			do_action( 'redparts_product_card_before_image' );

			echo '<div class="th-product-card__image">';

			/**
			 * Hook: redparts_product_card_image.
			 *
			 * @hooked woocommerce_template_loop_product_link_open  - 100
			 * @hooked woocommerce_template_loop_product_thumbnail  - 200
			 * @hooked woocommerce_template_loop_product_link_close - 300
			 * @hooked RedParts\Product_Card::compatibility_badge   - 400
			 */
			do_action( 'redparts_product_card_image' );

			echo '</div>';

			/**
			 * Hook: redparts_product_card_after_image.
			 */
			do_action( 'redparts_product_card_after_image' );
		}

		/**
		 * Outputs vehicle compatibility badge.
		 */
		public function compatibility_badge() {
			if ( ! class_exists( '\RedParts\Sputnik\Garage' ) ) {
				return;
			}

			global $product;

			$args = array( 'class' => 'th-product-card__fit' );

			if ( in_the_loop() ) {
				$args['scope'] = wc_get_loop_prop( 'redparts_compatibility_badge_scope', 'global' );
			}

			Garage::instance()->the_compatibility_badge( $product->get_id(), $args );
		}

		/**
		 * Outputs product card footer content.
		 */
		public function footer() {
			?>
			<div class="th-product-card__footer">
				<?php
				/**
				 * Hook: redparts_product_card_footer.
				 *
				 * @hooked RedParts\Product_Card::prices         - 100
				 * @hooked RedParts\Product_Card::addtocart      - 200
				 * @hooked RedParts\Sputnik\Wishlist::the_button - 300
				 * @hooked RedParts\Sputnik\Compare::the_button  - 400
				 */
				do_action( 'redparts_product_card_footer' );
				?>
			</div>
			<?php
		}

		/**
		 * Outputs product card prices.
		 */
		public function prices() {
			echo '<div class="th-product-card__prices">';

			woocommerce_template_loop_price();

			echo '</div>';
		}

		/**
		 * Outputs addtocart button.
		 */
		public function addtocart() {
			echo '<div class="th-product-card__addtocart">';

			woocommerce_template_loop_add_to_cart();

			echo '</div>';
		}

		/**
		 * Returns HTML of addtocart button.
		 *
		 * @param string $link Button HTML.
		 *
		 * @return string
		 */
		public function addtocart_html( string $link ): string {
			$icons =
				redparts_get_icon( 'cart-20', 'th-icon-cart' ) .
				redparts_get_icon( 'search-20', 'th-icon-cart-added' );

			return preg_replace( '#^(.*>)(.+)</a>$#', '$1<span>$2</span>' . $icons . '</a>', $link );
		}

		/**
		 * Outputs product rating.
		 */
		public function rating() {
			global $product;

			if ( ! wc_review_ratings_enabled() ) {
				return;
			}

			$rating = $product->get_average_rating();
			$count  = $product->get_review_count();

			/* translators: %s: rating */
			$label = sprintf( esc_html__( 'Rated %s out of 5', 'redparts' ), $rating );

			$reviews_url = $product->get_permalink() . '#' . ( 0 < $count ? 'reviews' : 'review_form_wrapper' );

			?>
			<div class="th-product-card__rating">
				<div class="th-product-card__rating-stars star-rating" role="img" aria-label="<?php echo esc_attr( $label ); ?>">
					<?php echo wp_kses( wc_get_star_rating_html( $rating, $count ), 'redparts_star_rating' ); ?>
				</div>
				<div class="th-product-card__rating-legend">
					<a href="<?php echo esc_attr( $reviews_url ); ?>">
						<?php
						if ( 0 < $count ) {
							/* translators: %s: rating */
							echo esc_html( sprintf( _n( '%s Review', '%s Reviews', $count, 'redparts' ), $count ) ); // SKIP-ESC.
						} else {
							echo esc_html__( 'No Reviews', 'redparts' );
						}
						?>
					</a>
				</div>
			</div>
			<?php
		}

		/**
		 * Outputs featured attributes.
		 */
		public function featured_attributes() {
			if ( class_exists( '\RedParts\Sputnik\Featured_Attributes' ) ) {
				Featured_Attributes::instance()->the_featured_attributes( 'th-product-card__features' );
			}
		}
	}
}
