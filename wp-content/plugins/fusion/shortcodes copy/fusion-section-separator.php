<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_SectionSeparator {

	/**
	 * An array of the shortcode arguments.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $args;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_section-separator-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_section-separator-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_section-separator-shortcode-divider-candy', array( $this, 'divider_candy_attr' ) );
		add_filter( 'fusion_attr_section-separator-shortcode-divider-candy-arrow', array( $this, 'divider_candy_arrow_attr' ) );
		add_filter( 'fusion_attr_section-separator-shortcode-divider-rounded-split', array( $this, 'divider_rounded_split_attr' ) );
		add_filter( 'fusion_attr_section-separator-shortcode-divider-svg', array( $this, 'divider_svg_attr' ) );

		add_shortcode( 'fusion_section_separator', array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render( $args, $content = '' ) {
		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'divider_type'     => 'triangle',
				'divider_position' => 'center',
				'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
				'class'            => '',
				'id'               => '',
				'backgroundcolor'  => FusionBuilder::get_theme_option( 'section_sep_bg' ),
				'bordersize'       => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'section_sep_border_size' ) : '',
				'bordercolor'      => FusionBuilder::get_theme_option( 'section_sep_border_color' ),
				'divider_candy'    => 'top',
				'icon'             => '',
				'icon_color'       => FusionBuilder::get_theme_option( 'icon_color' ),
			), $args
		);

		$defaults['bordersize'] = FusionBuilder::validate_shortcode_attr_value( $defaults['bordersize'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		if ( $icon ) {
			if ( ! $icon_color ) {
				self::$args['icon_color'] = $bordercolor;
			}

			$icon = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-icon' ) . '></div>';
		}

		$candy = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy-arrow' ) . '></div><div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy' ) . '></div>';

		if ( false !== strpos( self::$args['divider_candy'], 'top' ) && false !== strpos( self::$args['divider_candy'], 'bottom' ) ) {
			$candy = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-candy' ) . '></div>';
		}

		if ( 'triangle' === $divider_type ) {
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $icon . $candy . '</div>';
		} elseif ( 'bigtriangle' === $divider_type ) {
			$candy = '<svg id="bigTriangleCandy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 102" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

			if ( 'top' === $divider_candy ) {
				if ( 'right' === $divider_position ) {
					$candy .= '<path d="M0 104 L75 2 L100 104 Z"></path>';
				} elseif ( 'left' === $divider_position ) {
					$candy .= '<path d="M0 104 L25 2 L100 104 Z"></path>';
				} else {
					$candy .= '<path d="M0 104 L50 2 L100 104 Z"></path>';
				}
			} else {
				if ( 'right' === $divider_position ) {
					$candy .= '<path d="M0 0 L75 100 L100 0 Z"></path>';
				} elseif ( 'left' === $divider_position ) {
					$candy .= '<path d="M0 0 L25 100 L100 0 Z"></path>';
				} else {
					$candy .= '<path d="M0 0 L50 100 L100 0 Z"></path>';
				}
			}

			$candy .= '</svg>';
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		} elseif ( 'slant' === $divider_type ) {
			$candy = '<svg class="slantCandy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 102" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

			if ( 'left' === $divider_position && 'top' === $divider_candy ) {
				$candy .= '<path d="M100 0 L100 100 L0 0 Z"></path>';
			} elseif ( 'right' === $divider_position && 'top' === $divider_candy ) {
				$candy .= '<path d="M0 100 L0 0 L100 0 Z"></path>';
			} elseif ( 'right' === $divider_position && 'bottom' === $divider_candy ) {
				$candy .= '<path d="M100 0 L0 100 L100 100 Z"></path>';
			} else {
				$candy .= '<path d="M0 0 L0 100 L100 100 Z"></path>';
			}
			$candy .= '</svg>';
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		} elseif ( 'rounded-split' === $divider_type ) {
			$candy = sprintf( '<div %s></div>', FusionBuilder::attributes( 'section-separator-shortcode-divider-rounded-split' ) );
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		} elseif ( 'big-half-circle' === $divider_type ) {
			$candy = '<svg id="bigHalfCircleCandy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

			if ( 'top' === $divider_candy ) {
				$candy .= '<path d="M0 100 C40 0 60 0 100 100 Z"></path>';
			} else {
				$candy .= '<path d="M0 0 C55 180 100 0 100 0 Z"></path>';
			}

			$candy .= '</svg>';
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		} elseif ( 'curved' === $divider_type ) {
			$candy = '<svg id="curvedCandy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';

			if ( 'left' === $divider_position ) {
				if ( 'top' === $divider_candy ) {
					$candy .= '<path d="M0 100 C 20 0 50 0 100 100 Z"></path>';
				} else {
					$candy .= '<path d="M0 0 C 20 100 50 100 100 0 Z"></path>';
				}
			} else {
				if ( 'top' === $divider_candy ) {
					$candy .= '<path d="M0 100 C 60 0 75 0 100 100 Z"></path>';
				} else {
					$candy .= '<path d="M0 0 C 50 100 80 100 100 0 Z"></path>';
				}
			}

			$candy .= '</svg>';
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		} elseif ( 'clouds' === $divider_type ) {
			$candy = '<svg id="cloudsCandy" xmlns="http://www.w3.org/2000/svg" version="1.1" width="100%" height="100" viewBox="0 0 100 100" preserveAspectRatio="none" ' . FusionBuilder::attributes( 'section-separator-shortcode-divider-svg' ) . '>';
			$candy .= '<path d="M-5 100 Q 0 20 5 100 Z"></path>
						<path d="M0 100 Q 5 0 10 100"></path>
						<path d="M5 100 Q 10 30 15 100"></path>
						<path d="M10 100 Q 15 10 20 100"></path>
						<path d="M15 100 Q 20 30 25 100"></path>
						<path d="M20 100 Q 25 -10 30 100"></path>
						<path d="M25 100 Q 30 10 35 100"></path>
						<path d="M30 100 Q 35 30 40 100"></path>
						<path d="M35 100 Q 40 10 45 100"></path>
						<path d="M40 100 Q 45 50 50 100"></path>
						<path d="M45 100 Q 50 20 55 100"></path>
						<path d="M50 100 Q 55 40 60 100"></path>
						<path d="M55 100 Q 60 60 65 100"></path>
						<path d="M60 100 Q 65 50 70 100"></path>
						<path d="M65 100 Q 70 20 75 100"></path>
						<path d="M70 100 Q 75 45 80 100"></path>
						<path d="M75 100 Q 80 30 85 100"></path>
						<path d="M80 100 Q 85 20 90 100"></path>
						<path d="M85 100 Q 90 50 95 100"></path>
						<path d="M90 100 Q 95 25 100 100"></path>
						<path d="M95 100 Q 100 15 105 100 Z"></path>';
			$candy .= '</svg>';
			$html = '<div ' . FusionBuilder::attributes( 'section-separator-shortcode' ) . '>' . $candy . '</div>';
		}

		return $html;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function attr() {

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], array(
			'class' => 'fusion-section-separator section-separator',
		) );

		if ( 'triangle' === self::$args['divider_type'] ) {
			if ( self::$args['bordercolor'] ) {
				if ( 'bottom' === self::$args['divider_candy'] ) {
					$attr['style'] = 'border-bottom:' . self::$args['bordersize'] . ' solid ' . self::$args['bordercolor'] . ';';

				} elseif ( 'top' === self::$args['divider_candy'] ) {
					$attr['style'] = 'border-top:' . self::$args['bordersize'] . ' solid ' . self::$args['bordercolor'] . ';';

				} elseif ( false !== strpos( self::$args['divider_candy'], 'top' ) && false !== strpos( self::$args['divider_candy'], 'bottom' ) ) {
					$attr['style'] = 'border:' . self::$args['bordersize'] . ' solid ' . self::$args['bordercolor'] . ';';
				}
			}
		} elseif ( 'bigtriangle' === self::$args['divider_type'] || 'slant' === self::$args['divider_type'] || 'big-half-circle' === self::$args['divider_type'] || 'clouds' === self::$args['divider_type']  || 'curved' === self::$args['divider_type'] ) {
			$attr['style'] = 'padding:0;';
		}

		if ( 'rounded-split' === self::$args['divider_type'] ) {
			$attr['class'] .= ' rounded-split-separator';
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		global $fusion_fwc_type, $fusion_col_type;

		if ( ! empty( $fusion_fwc_type ) ) {
			$margin_left  = $fusion_fwc_type['padding']['left'];
			$margin_right = $fusion_fwc_type['padding']['right'];
			if ( isset( $fusion_col_type['type'] ) && '1_1' !== $fusion_col_type['type'] ) {
				$margin_left  = fusion_builder_single_dimension( $fusion_col_type['padding'], 'left' );
				$margin_right = fusion_builder_single_dimension( $fusion_col_type['padding'], 'right' );
			}
			$margin_left_unitless  = filter_var( $margin_left, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$margin_right_unitless = filter_var( $margin_right, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

			$container_percentage         = 100 - $margin_left_unitless - $margin_right_unitless;
			$margin_left_unitless_scaled  = $margin_left_unitless / $container_percentage * 100;
			$margin_right_unitless_scaled = $margin_right_unitless / $container_percentage * 100;

			$viewport_width = '100vw';

			if ( 'Top' !== FusionBuilder::get_theme_option( 'header_position' ) ) {
				$viewport_width = $viewport_width . ' - ' . intval( FusionBuilder::get_theme_option( 'side_header_width' ) ) . 'px';
			}

			if ( 'Boxed' === FusionBuilder::get_theme_option( 'layout' ) ) {
				$viewport_width = FusionBuilder::get_theme_option( 'site_width' ) . ' +  60px';
			}

			// 100% width template && non 100% interior width container.
			if ( $fusion_fwc_type['width_100_percent'] && 'contained' === $fusion_fwc_type['content'] && ( isset( $fusion_col_type['type'] ) && '1_1' === $fusion_col_type['type'] ) ) {

				// Both container paddings use px.
				if ( false !== strpos( $margin_left, 'px' ) && false !== strpos( $margin_right, 'px' ) ) {
					$margin_unit = 'px';

					$margin_difference_half = abs( $margin_left_unitless - $margin_right_unitless ) / 2 . $margin_unit;

					if ( $margin_left_unitless > $margin_right_unitless ) {
						$margin_left  = '- ' . $margin_difference_half;
						$margin_right = '+ ' . $margin_difference_half;
					} elseif ( $margin_left_unitless < $margin_right_unitless ) {
						$margin_left  = '+ ' . $margin_difference_half;
						$margin_right = '- ' . $margin_difference_half;
					} elseif ( $margin_left_unitless === $margin_right_unitless ) {
						$margin_left  = '';
						$margin_right = '';
					}

					$margin_left_negative = 'calc( (' . $viewport_width . ' - 100% ) / -2 ' . $margin_left . ' )';
					$margin_right_negative = 'calc( (' . $viewport_width . ' - 100% ) / -2  ' . $margin_right . ' )';
					$attr['class'] .= ' fusion-section-separator-with-offset';

					// Both container paddings use %.
				} elseif ( false !== strpos( $margin_left, '%' ) && false !== strpos( $margin_right, '%' ) ) {

					if ( 'Boxed' === FusionBuilder::get_theme_option( 'layout' ) ) {
						$margin_unit = '%';
						$margin_left_unitless = $margin_left_unitless_scaled;
						$margin_right_unitless = $margin_right_unitless_scaled;

						$margin_difference_half = abs( $margin_left_unitless - $margin_right_unitless ) / 2 . $margin_unit;

						if ( $margin_left_unitless > $margin_right_unitless ) {
							$margin_left  = '- ' . $margin_difference_half;
							$margin_right = '+ ' . $margin_difference_half;
						} elseif ( $margin_left_unitless < $margin_right_unitless ) {
							$margin_left  = '+ ' . $margin_difference_half;
							$margin_right = '- ' . $margin_difference_half;
						} elseif ( $margin_left_unitless === $margin_right_unitless ) {
							$margin_left  = '';
							$margin_right = '';
						}

						$margin_left_negative  = 'calc( (' . $viewport_width . ' - 100% ) / -2 ' . $margin_left . ' )';
						$margin_right_negative = 'calc( (' . $viewport_width . ' - 100% ) / -2  ' . $margin_right . ' )';
					} else {
						$margin_unit = 'vw';
						$margin_sum  = ' - ' . ( $margin_left_unitless + $margin_right_unitless ) . $margin_unit;

						$margin_left_negative  = 'calc( (' . $viewport_width . ' - 100% ' . $margin_sum . ') / -2 - ' . $margin_left_unitless . $margin_unit . ' )';
						$margin_right_negative = 'calc( (' . $viewport_width . ' - 100% ' . $margin_sum . ') / -2  - ' . $margin_right_unitless . $margin_unit . ' )';
					}

					$attr['class'] .= ' fusion-section-separator-with-offset';

					// Mixed container padding units.
				} else {
					$margin_left_final = $margin_left;
					if ( false !== strpos( $margin_left, '%' ) && 'Boxed' !== FusionBuilder::get_theme_option( 'layout' ) ) {
						$margin_left_final = $margin_left_unitless . 'vw';
					}

					$margin_right_final = $margin_right;
					if ( false !== strpos( $margin_right, '%' ) && 'Boxed' !== FusionBuilder::get_theme_option( 'layout' ) ) {
						$margin_right_final = $margin_right_unitless . 'vw';
					}

					$margin_left_negative  = 'calc( (' . $viewport_width . ' - 100% - ' . $margin_left . ' - ' . $margin_right . ') / -2 - ' . $margin_left_final . ' )';
					$margin_right_negative = 'calc( (' . $viewport_width . ' - 100% - ' . $margin_left . ' - ' . $margin_right . ') / -2 - ' . $margin_right_final . ' )';
				}

				// Non 100% width template.
			} else {
				if ( false !== strpos( $margin_left, '%' ) ) {
					$margin_left = $margin_left_unitless_scaled . '%';
					if ( false !== strpos( $margin_right, '%' ) ) {
						$margin_right = $margin_right_unitless_scaled . '%';
					}

					$margin_left_negative = 'calc( (100% + ' . $margin_left . ' + ' . $margin_right . ') * ' . $margin_left_unitless . ' / -100 )';
				} else {
					$margin_left_negative = '-' . $margin_left;
				}

				if ( false !== strpos( $margin_right, '%' ) ) {
					$margin_right = $margin_right_unitless_scaled . '%';
					if ( false !== strpos( $margin_left, '%' ) ) {
						$margin_left = $margin_left_unitless_scaled . '%';
					}

					$margin_right_negative = 'calc( (100% + ' . $margin_left . ' + ' . $margin_right . ') * ' . $margin_right_unitless . ' / -100 )';
				} else {
					$margin_right_negative = '-' . $margin_right;
				}
			}

			$attr['style'] .= 'margin-left:' . $margin_left_negative . ';';
			$attr['style'] .= 'margin-right:' . $margin_right_negative . ';';
		}

		return $attr;

	}

	/**
	 * Builds the rounded split attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function divider_svg_attr() {
		$attr = array();

		if ( 'bigtriangle' === self::$args['divider_type'] || 'slant' === self::$args['divider_type'] || 'big-half-circle' === self::$args['divider_type'] || 'clouds' === self::$args['divider_type'] || 'curved' === self::$args['divider_type'] ) {
			$attr['style'] = sprintf( 'fill:%s;stroke:%s;stroke-width:2;padding:0;', self::$args['backgroundcolor'], self::$args['backgroundcolor'] );
		}
		if ( 'slant' === self::$args['divider_type'] && 'bottom' === self::$args['divider_candy'] ) {
			$attr['style'] = sprintf( 'fill:%s;stroke:%s;stroke-width:0;padding:0;margin-bottom:-3px;display:block', self::$args['backgroundcolor'], self::$args['backgroundcolor'] );
		}

		return $attr;
	}

	/**
	 * Builds the rounded split attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function divider_rounded_split_attr() {
		return array(
			'class' => 'rounded-split ' . self::$args['divider_candy'],
			'style' => 'background-color:' . self::$args['backgroundcolor'] . ';',
		);
	}

	/**
	 * Builds the icon attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {

		$attr = array(
			'class' => 'section-separator-icon icon fa ' . FusionBuilder::font_awesome_name_handler( self::$args['icon'] ),
			'style' => 'color:' . self::$args['icon_color'] . ';',
		);

		if ( FusionBuilder::strip_unit( self::$args['bordersize'] ) > 1 ) {
			$divider_candy = self::$args['divider_candy'];
			if (  'bottom' === $divider_candy ) {
				$attr['style'] .= 'bottom:-' . ( FusionBuilder::strip_unit( self::$args['bordersize'] ) + 10 ) . 'px;top:auto;';
			} elseif ( 'top' === $divider_candy ) {
				$attr['style'] .= 'top:-' . ( FusionBuilder::strip_unit( self::$args['bordersize'] ) + 10 ) . 'px;';
			}
		}
		return $attr;

	}

	/**
	 * Builds the divider attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	public function divider_candy_attr( $args ) {

		$attr = array(
			'class' => 'divider-candy',
		);

		$divider_candy = ( $args ) ? $args['divider_candy'] : self::$args['divider_candy'];

		if ( 'bottom' === $divider_candy ) {
			$attr['class'] .= ' bottom';
			$attr['style'] = 'bottom:-' . ( FusionBuilder::strip_unit( self::$args['bordersize'] ) + 20 ) . 'px;border-bottom:1px solid ' . self::$args['bordercolor'] . ';border-left:1px solid ' . self::$args['bordercolor'] . ';';
		} elseif ( 'top' === $divider_candy ) {
			$attr['class'] .= ' top';
			$attr['style'] = 'top:-' . ( FusionBuilder::strip_unit( self::$args['bordersize'] ) + 20 ) . 'px;border-bottom:1px solid ' . self::$args['bordercolor'] . ';border-left:1px solid ' . self::$args['bordercolor'] . ';';
			// Modern setup, that won't work in IE8.
		} elseif ( false !== strpos( self::$args['divider_candy'], 'top' ) && false !== strpos( self::$args['divider_candy'], 'bottom' ) ) {
			$attr['class'] .= ' both';
			$attr['style'] = 'background-color:' . self::$args['backgroundcolor'] . ';border:1px solid ' . self::$args['bordercolor'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the divider-arrow attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	public function divider_candy_arrow_attr( $args ) {

		$attr = array(
			'class' => 'divider-candy-arrow',
		);

		$divider_candy = ( $args ) ? $args['divider_candy'] : self::$args['divider_candy'];

		// For borders of size 1, we need to hide the border line on the arrow, thus we set it to 0.
		$arrow_position = FusionBuilder::strip_unit( self::$args['bordersize'] );
		if ( '1' == $arrow_position ) {
			$arrow_position = 0;
		}

		if ( 'bottom' === $divider_candy ) {
			$attr['class'] .= ' bottom';
			$attr['style'] = 'top:' . $arrow_position . 'px;border-top-color: ' . self::$args['backgroundcolor'] . ';';
		} elseif ( 'top' === $divider_candy ) {
			$attr['class'] .= ' top';
			$attr['style'] = 'bottom:' . $arrow_position . 'px;border-bottom-color: ' . self::$args['backgroundcolor'] . ';';
		}

		return $attr;

	}
}
new FusionSC_SectionSeparator();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_section_separator() {
	fusion_builder_map( array(
		'name'      => esc_attr__( 'Section Separator', 'fusion-builder' ),
		'shortcode' => 'fusion_section_separator',
		'icon'      => 'fusiona-ellipsis',
		'params'    => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Section Separator Style', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of the section separator', 'fusion-builder' ),
				'param_name'  => 'divider_type',
				'value'       => array(
					esc_attr__( 'Triangle', 'fusion-builder' )        => 'triangle',
					esc_attr__( 'Slant', 'fusion-builder' )           => 'slant',
					esc_attr__( 'Big Triangle', 'fusion-builder' )    => 'bigtriangle',
					esc_attr__( 'Rounded Split', 'fusion-builder' )   => 'rounded-split',
					esc_attr__( 'Curved', 'fusion-builder' )          => 'curved',
					esc_attr__( 'Big Half Circle', 'fusion-builder' ) => 'big-half-circle',
					esc_attr__( 'Clouds', 'fusion-builder' )          => 'clouds',
				),
				'default' => 'triangle',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Horizontal Position of the Section Separator', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the horizontal position of the section separator.', 'fusion-builder' ),
				'param_name'  => 'divider_position',
				'value'       => array(
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Center', 'fusion-builder' ) => 'center',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
				),
				'default'     => 'center',
				'dependency'  => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'triangle',
						'operator' => '!=',
					),
					array(
						'element'  => 'divider_type',
						'value'    => 'rounded-split',
						'operator' => '!=',
					),
					array(
						'element'  => 'divider_type',
						'value'    => 'big-half-circle',
						'operator' => '!=',
					),
					array(
						'element'  => 'divider_type',
						'value'    => 'clouds',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Vertical Position of the Section Separator', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the vertical position of the section separator.', 'fusion-builder' ),
				'param_name'  => 'divider_candy',
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )            => 'top',
					esc_attr__( 'Bottom', 'fusion-builder' )         => 'bottom',
					esc_attr__( 'Top and Bottom', 'fusion-builder' ) => 'bottom,top',
				),
				'default'      => 'top',
				'dependency'   => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'clouds',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'triangle',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
				'description' => '',
				'param_name'  => 'icon_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'triangle',
						'operator' => '==',
					),
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => __( 'Border', 'fusion-builder' ),
				'heading'     => esc_attr__( 'Border', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'param_name'  => 'bordersize',
				'value'       => '1',
				'min'         => '0',
				'max'         => '50',
				'step'        => '1',
				'default'     => '1',
				'dependency'  => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'triangle',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => __( 'Border Color', 'fusion-builder' ),
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'divider_type',
						'value'    => 'triangle',
						'operator' => '==',
					),
					array(
						'element'  => 'bordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color of the Section Separator', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the section separator style. Leave empty for default value of #f6f6f6.', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'class',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_section_separator' );
