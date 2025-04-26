<?php
/**
 * This file contains helpers to working with colors.
 *
 * @noinspection DuplicatedCode
 *
 * @package RedParts
 * @since 1.0.0
 */

namespace RedParts;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'RedParts\Color' ) ) {
	/**
	 * Class Color
	 *
	 * @property-read array $rgb Array of RGB components.
	 */
	class Color {
		/**
		 * Parse color.
		 *
		 * @param Color|string $color - Color.
		 *
		 * @return Color|false
		 */
		public static function parse( $color ) {
			if ( $color instanceof Color ) {
				return $color;
			}

			$color = trim( $color );

			if ( preg_match( '/^#(?P<hex>(?:[A-Fa-f0-9]{3}){1,2})$/', $color, $mr ) ) {
				$rgb = array_map(
					function ( $part ) {
						return hexdec( substr( $part . $part, 0, 2 ) );
					},
					str_split( $mr['hex'], strlen( $mr['hex'] ) === 3 ? 1 : 2 )
				);

				return new Color( ...$rgb );
			} elseif ( preg_match( '/^rgba\(\s*(?P<hex>#(?:[A-Fa-f0-9]{3}){1,2})\s*,\s*(?P<alpha>[01](?:\.\d{1,2})?|(?:\.\d{1,2}))\s*\)$/', $color, $mr ) ) {
				$color = self::parse( $mr['hex'] );

				$color->alpha = (float) $mr['alpha'];

				return $color;
			} elseif ( preg_match( '/^rgba\((?P<rgb>(?:\s*[0-9]+\s*,){3})\s*(?P<alpha>[01](?:\.\d{1,2})?|(?:\.\d{1,2}))\s*\)$/', $color, $mr ) ) {
				$channels   = explode( ',', trim( $mr['rgb'], ' ,' ) );
				$channels   = array_map( 'intval', array_map( 'trim', $channels ) );
				$channels[] = (float) $mr['alpha'];

				return new Color( ...$channels );
			} else {
				return false;
			}
		}

		/**
		 * A red channel of color.
		 *
		 * @var int
		 */
		public $red = 0;

		/**
		 * A green channel of color.
		 *
		 * @var int
		 */
		public $green = 0;

		/**
		 * A blu channel of color.
		 *
		 * @var int
		 */
		public $blue = 0;

		/**
		 * A alpha channel of color.
		 *
		 * @var int
		 */
		public $alpha = 0;

		/**
		 * Color constructor.
		 *
		 * @param int   $red   - A red channel of color. Integer from 0 to 255.
		 * @param int   $green - A green channel of color. Integer from 0 to 255.
		 * @param int   $blue  - A blue channel of color. Integer from 0 to 255.
		 * @param float $alpha - A alpha channel of color. Float from 0.0 to 1.0.
		 */
		public function __construct( int $red, int $green, int $blue, $alpha = 1.0 ) {
			$this->red   = $red;
			$this->green = $green;
			$this->blue  = $blue;
			$this->alpha = $alpha;
		}

		/**
		 * Returns true if color is opaque.
		 *
		 * @return bool
		 */
		public function is_opaque(): bool {
			return 100 === (int) round( $this->alpha * 100 );
		}

		/**
		 * Returns true if color fully transparent.
		 *
		 * @return bool
		 */
		public function is_transparent(): bool {
			return 0 === (int) round( $this->alpha );
		}

		/**
		 * Returns true if current color is white.
		 *
		 * @return bool
		 */
		public function is_white(): bool {
			foreach ( $this->rgb as $channel ) {
				if ( 255 !== $channel ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Returns true if color is dark.
		 *
		 * @return bool
		 */
		public function is_dark(): bool {
			return in_array( $this->type(), array( 'dark', 'black' ), true );
		}

		/**
		 * Compares colors and returns true if they are the same.
		 *
		 * @param Color|string $color - Color.
		 *
		 * @return bool
		 */
		public function compare( $color ): bool {
			if ( is_string( $color ) ) {
				$color = self::parse( $color );
			}

			return $this->to( '#rgba' ) === $color->to( '#rgba' );
		}

		/**
		 * Compares the luminance of colors and returns true if the current color is lighter than the specified color.
		 *
		 * @param Color|string $color - Color.
		 * @return bool
		 * @noinspection PhpUnused
		 */
		public function lighter_than( $color ): bool {
			if ( is_string( $color ) ) {
				$color = self::parse( $color );
			}

			return $this->contrast( '#fff' ) < $color->contrast( '#fff' );
		}

		/**
		 * Compares the luminance of colors and returns true if the current color is darker than the specified color.
		 *
		 * @noinspection PhpUnused
		 *
		 * @param Color|string $color - Color.
		 *
		 * @return bool
		 */
		public function darker_than( $color ): bool {
			if ( is_string( $color ) ) {
				$color = self::parse( $color );
			}

			return $this->contrast( '#fff' ) > $color->contrast( '#fff' );
		}

		/**
		 * Returns color in the specified format.
		 *
		 * @param string $format - Color format.
		 *
		 * @return string
		 */
		public function to( string $format ): string {
			switch ( strtolower( $format ) ) {
				case '#rgb':
					return '#' . implode(
						'',
						array_map(
							function ( $channel ) {
								return str_pad( dechex( $channel ), 2, '0', STR_PAD_LEFT );
							},
							$this->rgb
						)
					);
				case '#rgba':
					return $this->to( '#rgb' ) . str_pad( dechex( round( $this->alpha * 255 ) ), 2, '0', STR_PAD_LEFT );
				case 'rgb()':
					return "rgb($this->red, $this->green, $this->blue)";
				case 'rgba()':
					$alpha = number_format( $this->alpha, 2, '.', '' );
					$alpha = rtrim( trim( $alpha, '0' ), '.' );

					if ( '' === $alpha ) {
						$alpha = 0;
					}

					return "rgba($this->red, $this->green, $this->blue, $alpha)";
				default:
					return $this->to( 'rgba()' );
			}
		}

		/**
		 * Merges colors and returns new color.
		 * If contrast is specified, such a transparency value will be selected that the contrast of the final color relative
		 * to the current color matches the specified contrast.
		 *
		 * @param Color|string $color    - The color to be combined with the current color.
		 * @param float|null   $contrast - The contrast of result color relative to the current color.
		 *
		 * @return Color
		 */
		public function merge( $color, $contrast = null ): Color {
			if ( is_string( $color ) ) {
				$color = self::parse( $color );
			}

			if ( null !== $contrast ) {
				$color = clone $color;
				$alpha = .5;
				$step  = $alpha;

				for ( $i = 0; $i < 10; $i++ ) {
					$step = $step / 2;

					$color->alpha = $alpha;

					$current_contrast = $this->merge( $color )->contrast( $this );

					if ( $current_contrast > $contrast ) {
						$alpha -= $step;
					} else {
						$alpha += $step;
					}
				}

				return $this->merge( $color );
			}

			$red   = min( 255, (int) round( $this->red * ( 1 - $color->alpha ) + $color->red * $color->alpha ) );
			$green = min( 255, (int) round( $this->green * ( 1 - $color->alpha ) + $color->green * $color->alpha ) );
			$blue  = min( 255, (int) round( $this->blue * ( 1 - $color->alpha ) + $color->blue * $color->alpha ) );

			return new Color( $red, $green, $blue );
		}

		/**
		 * Returns color luminance.
		 *
		 * @return float
		 */
		public function luminance(): float {
			$rgb = array_map(
				function( $part ) {
					$part = $part / 255;

					if ( $part <= 0.03928 ) {
						return $part / 12.92;
					} else {
						return pow( ( $part + 0.055 ) / 1.055, 2.4 );
					}
				},
				$this->rgb
			);

			return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
		}


		/**
		 * Compares to colors and return contrast value.
		 *
		 * @param string $color - Color.
		 *
		 * @return float
		 */
		public function contrast( string $color ) {
			if ( is_string( $color ) ) {
				$color = self::parse( $color );
			}

			$l1 = $this->luminance();
			$l2 = $color->luminance();

			return ( max( $l1, $l2 ) + 0.05 ) / ( min( $l1, $l2 ) + 0.05 );
		}

		/**
		 * Returns type of color. One of [white, light, dark, black].
		 *
		 * @return string
		 */
		public function type(): string {
			static $cache = array();

			$hex = $this->to( '#rgb' );

			if ( ! isset( $cache[ $hex ] ) ) {
				$white_contrast = $this->contrast( '#fff' );
				$black_contrast = $this->contrast( '#000' );

				if ( 1.0 === $white_contrast && 21.0 === $black_contrast ) {
					$result = 'white';
				} elseif ( 21.0 === $white_contrast && 1.0 === $black_contrast ) {
					$result = 'black';
				} elseif ( $white_contrast >= 3.0 && $black_contrast < 10.0 ) {
					$result = 'dark';
				} else {
					$result = 'light';
				}

				$cache[ $hex ] = $result;
			}

			return $cache[ $hex ];
		}

		/**
		 * Returns value of magic properties.
		 *
		 * @param string $name Property name.
		 *
		 * @return array|null
		 * @noinspection PhpMissingReturnTypeInspection
		 */
		public function __get( string $name ) {
			if ( 'rgb' === $name ) {
				return array( $this->red, $this->green, $this->blue );
			}

			return null;
		}

		/**
		 * Returns color in the rgba format.
		 *
		 * @return string
		 */
		public function __toString(): string {
			if ( $this->is_opaque() ) {
				return $this->to( '#rgb' );
			}

			return $this->to( 'rgba()' );
		}
	}
}
