<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Button {

	/**
	 * The button counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $button_counter = 1;

	/**
	 * An array of the shortcode arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
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

		add_filter( 'fusion_attr_button-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_button-shortcode-icon-divder', array( $this, 'icon_divider_attr' ) );
		add_filter( 'fusion_attr_button-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_button-shortcode-button-text', array( $this, 'button_text_attr' ) );

		add_shortcode( 'fusion_button', array( $this, 'render' ) );

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
				'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
				'class'                 => '',
				'id'                    => '',
				'accent_color'          => ( '' !== FusionBuilder::get_theme_option( 'button_accent_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_accent_color' ) ) : '#ffffff',
				'accent_hover_color'    => ( '' !== FusionBuilder::get_theme_option( 'button_accent_hover_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_accent_hover_color' ) ) : '#ffffff',
				'bevel_color'           => ( '' !== FusionBuilder::get_theme_option( 'button_bevel_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_bevel_color' ) ) : '#54770F',
				'border_width'          => intval( FusionBuilder::get_theme_option( 'button_border_width' ) ) . 'px',
				'color'                 => 'default',
				'gradient_colors'       => '',
				'icon'                  => '',
				'icon_divider'          => 'no',
				'icon_position'         => 'left',
				'link'                  => '',
				'modal'                 => '',
				'shape'                 => ( '' !== FusionBuilder::get_theme_option( 'button_shape' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_shape' ) ) : 'round',
				'size'                  => ( '' !== FusionBuilder::get_theme_option( 'button_size' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_size' ) ) : 'large',
				'stretch'               => ( '' !== FusionBuilder::get_theme_option( 'button_span' ) ) ? FusionBuilder::get_theme_option( 'button_span' ) : 'no',
				'target'                => '_self',
				'title'                 => '',
				'type'                  => ( '' !== FusionBuilder::get_theme_option( 'button_type' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_type' ) ) : 'flat',
				'alignment'             => '',
				'animation_type'        => '',
				'animation_direction'   => 'down',
				'animation_speed'       => '',
				'animation_offset'      => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',

				// Combined in accent_color.
				'border_color'          => '',
				'icon_color'            => '',
				'text_color'            => '',

				// Combined in accent_hover_color.
				'border_hover_color'    => '',
				'icon_hover_color'      => '',
				'text_hover_color'      => '',

				// Combined with gradient_colors.
				'gradient_hover_colors' => '',

				'button_gradient_top_color'          => ( '' !== FusionBuilder::get_theme_option( 'button_gradient_top_color' ) ) ? FusionBuilder::get_theme_option( 'button_gradient_top_color' ) : '#a0ce4e',
				'button_gradient_bottom_color'       => ( '' !== FusionBuilder::get_theme_option( 'button_gradient_bottom_color' ) ) ? FusionBuilder::get_theme_option( 'button_gradient_bottom_color' ) : '#a0ce4e',
				'button_gradient_top_color_hover'    => ( '' !== FusionBuilder::get_theme_option( 'button_gradient_top_color_hover' ) ) ? FusionBuilder::get_theme_option( 'button_gradient_top_color_hover' ) : '#96c346',
				'button_gradient_bottom_color_hover' => ( '' !== FusionBuilder::get_theme_option( 'button_gradient_bottom_color_hover' ) ) ? FusionBuilder::get_theme_option( 'button_gradient_bottom_color_hover' ) : '#96c346',

			), $args
		);

		// BC support for old 'gradient_colors' format.
		$button_gradient_top_color    = $defaults['button_gradient_top_color'];
		$button_gradient_bottom_color = $defaults['button_gradient_bottom_color'];

		$button_gradient_top_color_hover    = $defaults['button_gradient_top_color_hover'];
		$button_gradient_bottom_color_hover = $defaults['button_gradient_bottom_color_hover'];

		if ( empty( $defaults['gradient_colors'] ) ) {
			$defaults['gradient_colors'] = strtolower( $defaults['button_gradient_top_color'] ) . '|' . strtolower( $defaults['button_gradient_bottom_color'] );
		}

		if ( empty( $defaults['gradient_hover_colors'] ) ) {
			$defaults['gradient_hover_colors'] = strtolower( $defaults['button_gradient_top_color_hover'] ) . '|' . strtolower( $defaults['button_gradient_bottom_color_hover'] );
		}

		$defaults['border_width'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_width'], 'px' );

		if ( 'default' === $defaults['color'] ) {
			$defaults['accent_color']          = ( '' !== FusionBuilder::get_theme_option( 'button_accent_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_accent_color' ) ) : '#ffffff';
			$defaults['accent_hover_color']    = ( '' !== FusionBuilder::get_theme_option( 'button_accent_hover_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_accent_hover_color' ) ) : '#ffffff';
			$defaults['bevel_color']           = ( '' !== FusionBuilder::get_theme_option( 'button_bevel_color' ) ) ? strtolower( FusionBuilder::get_theme_option( 'button_bevel_color' ) ) : '#54770F';
			$defaults['gradient_colors']       = strtolower( $button_gradient_top_color ) . '|' . strtolower( $button_gradient_bottom_color );
			$defaults['gradient_hover_colors'] = strtolower( $button_gradient_top_color_hover ) . '|' . strtolower( $button_gradient_bottom_color_hover );
		}
		// If its a custom color scheme selected, then set options based on that.
		if ( false !== strpos( $defaults['color'], 'scheme-' ) && class_exists( 'Avada' ) ) {
			$scheme_id = str_replace( 'scheme-', '', $defaults['color'] );
			$custom_color = ( class_exists( 'Avada' ) && method_exists( 'Avada_Settings', 'get_custom_color' ) ) ? Avada()->settings->get_custom_color( $scheme_id ) : '';
			// If the scheme exists and has options, use them.  Otherwise set the color scheme to default as fallback.
			if ( ! empty( $custom_color ) ) {
				$defaults['accent_color']          = ( isset( $custom_color['button_accent_color'] ) ) ? strtolower( $custom_color['button_accent_color'] ) : '#ffffff';
				$defaults['accent_hover_color']    = ( isset( $custom_color['button_accent_hover_color'] ) ) ? strtolower( $custom_color['button_accent_hover_color'] ) : '#ffffff';
				$defaults['bevel_color']           = ( isset( $custom_color['button_bevel_color'] ) ) ? strtolower( $custom_color['button_bevel_color'] ) : '#54770F';
				$defaults['gradient_colors']       = strtolower( $custom_color['button_gradient_top_color'] ) . '|' . strtolower( $custom_color['button_gradient_bottom_color'] );
				$defaults['gradient_hover_colors'] = strtolower( $custom_color['button_gradient_top_color_hover'] ) . '|' . strtolower( $custom_color['button_gradient_bottom_color_hover'] );
			} else {
				$defaults['color'] = 'default';
			}
		}
		// Combined variable settings.
		$old_border_color = $defaults['border_color'];
		$old_text_color = $defaults['text_color'];

		$defaults['border_color'] = $defaults['icon_color'] = $defaults['text_color'] = $defaults['accent_color'];
		$defaults['border_hover_color'] = $defaults['icon_hover_color'] = $defaults['text_hover_color'] = $defaults['accent_hover_color'];

		/*
		TODO:
		$defaults['gradient_hover_colors'] = $defaults['gradient_hover_colors']; // See below for array reverting.
		*/
		if ( $old_border_color ) {
			$defaults['border_color'] = $old_border_color;
		}

		if ( $old_text_color ) {
			$defaults['text_color'] = $old_border_color;
		}

		if ( $defaults['modal'] ) {
			$defaults['link'] = '#';
		}

		$defaults['type'] = strtolower( $defaults['type'] );

		extract( $defaults );

		self::$args = $defaults;

		$style_tag = $styles = '';
		// If its custom, default or a custom color scheme.
		if ( ( 'custom' === $color || 'default' === $color || false !== strpos( $color, 'scheme-' ) ) && ( $bevel_color || $accent_color || $accent_hover_color || $border_width || $gradient_colors ) ) {

			$general_styles = $text_color_styles = $button_3d_styles = $hover_styles = $text_color_hover_styles = $gradient_styles = $gradient_hover_styles = '';

			if ( ( '3d' === $type ) && $bevel_color ) {
				if ( 'small' === $size ) {
					$button_3d_add = 0;
				} elseif ( 'medium' === $size ) {
					$button_3d_add = 1;
				} elseif ( 'large' === $size ) {
					$button_3d_add = 2;
				} elseif ( 'xlarge' === $size ) {
					$button_3d_add = 3;
				}

				$button_3d_shadow_part_1 = 'inset 0px 1px 0px #fff,';

				$button_3d_shadow_part_2 = '0px ' . ( 2 + $button_3d_add ) . 'px 0px ' . $bevel_color . ',';

				$button_3d_shadow_part_3 = '1px ' . ( 4 + $button_3d_add ) . 'px ' . ( 4 + $button_3d_add ) . 'px 3px rgba(0,0,0,0.3)';
				if ( 'small' === $size ) {
					$button_3d_shadow_part_3 = str_replace( '3px', '2px', $button_3d_shadow_part_3 );
				}
				$button_3d_shadow = $button_3d_shadow_part_1 . $button_3d_shadow_part_2 . $button_3d_shadow_part_3;

				$button_3d_styles = '-webkit-box-shadow: ' . $button_3d_shadow . ';-moz-box-shadow: ' . $button_3d_shadow . ';box-shadow: ' . $button_3d_shadow . ';';
			}

			if ( $old_text_color ) {
				$text_color_styles .= 'color:' . $old_text_color . ';';
			} elseif ( $accent_color ) {
				$text_color_styles .= 'color:' . $accent_color . ';';
			}

			if ( $border_width ) {
				$general_styles .= 'border-width:' . $border_width . ';';
				$hover_styles .= 'border-width:' . $border_width . ';';
			}

			if ( $old_border_color ) {
				$general_styles .= 'border-color:' . $old_border_color . ';';
			} elseif ( $accent_color ) {
				$general_styles .= 'border-color:' . $border_color . ';';
			}

			if ( $old_text_color ) {
				$text_color_hover_styles .= 'color:' . $old_text_color . ';';
			} elseif ( $accent_hover_color ) {
				$text_color_hover_styles .= 'color:' . $accent_hover_color . ';';
			} elseif ( $accent_color ) {
				$text_color_hover_styles .= 'color:' . $accent_color . ';';
			}

			if ( $old_border_color ) {
				$hover_styles .= 'border-color:' . $old_border_color . ';';
			} elseif ( $accent_hover_color ) {
				$hover_styles .= 'border-color:' . $accent_hover_color . ';';
			} elseif ( $accent_color ) {
				$hover_styles .= 'border-color:' . $accent_color . ';';
			}

			if ( $text_color_styles ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ' .fusion-button-text, .fusion-button.button-' . $this->button_counter . ' i {' . $text_color_styles . '}';
			}

			if ( $general_styles ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ' {' . $general_styles . '}';
			}

			if ( $accent_color ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ' .fusion-button-icon-divider{border-color:' . $accent_color . ';}';
			}

			if ( $button_3d_styles ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . '.button-3d{' . $button_3d_styles . '}.button-' . $this->button_counter . '.button-3d:active{' . $button_3d_styles . '}';
			}

			if ( $text_color_hover_styles ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ':hover .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':hover i,.fusion-button.button-' . $this->button_counter . ':focus .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':focus i,.fusion-button.button-' . $this->button_counter . ':active .fusion-button-text, .fusion-button.button-' . $this->button_counter . ':active{' . $text_color_hover_styles . '}';
			}

			if ( $hover_styles ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ':hover, .fusion-button.button-' . $this->button_counter . ':focus, .fusion-button.button-' . $this->button_counter . ':active{' . $hover_styles . '}';
			}

			if ( $accent_hover_color ) {
				$styles .= '.fusion-button.button-' . $this->button_counter . ':hover .fusion-button-icon-divider, .fusion-button.button-' . $this->button_counter . ':hover .fusion-button-icon-divider, .fusion-button.button-' . $this->button_counter . ':active .fusion-button-icon-divider{border-color:' . $accent_hover_color . ';}';
			}

			if ( $gradient_colors && 'default' !== $color ) {
				// Checking for deprecated separators.
				if ( strpos( $gradient_colors, ';' ) ) {
					$grad_colors = explode( ';', $gradient_colors );
				} else {
					$grad_colors = explode( '|', $gradient_colors );
				}

				if ( 1 == count( $grad_colors ) || empty( $grad_colors[1] ) || $grad_colors[0] == $grad_colors[1] ) {
					$gradient_styles = "background: {$grad_colors[0]};";
				} else {
					$gradient_styles =
					"background: {$grad_colors[0]};
					background-image: -webkit-gradient( linear, left bottom, left top, from( {$grad_colors[1]} ), to( {$grad_colors[0]} ) );
					background-image: -webkit-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image:   -moz-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image:     -o-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image: linear-gradient( to top, {$grad_colors[1]}, {$grad_colors[0]} );";
				}

				$styles .= '.fusion-button.button-' . $this->button_counter . '{' . $gradient_styles . '}';
			}

			if ( $gradient_hover_colors && 'default' !== $color ) {

				// Checking for deprecated separators.
				if ( strpos( $gradient_hover_colors, ';' ) ) {
					$grad_hover_colors = explode( ';', $gradient_hover_colors );
				} else {
					$grad_hover_colors = explode( '|', $gradient_hover_colors );
				}

				// $grad_hover_colors = array_reverse( $grad_hover_colors ); // For combination of gradient_hover_colors and gradient_hover_colors.
				if ( 1 == count( $grad_hover_colors ) || '' == $grad_hover_colors[1] || $grad_hover_colors[0] == $grad_hover_colors[1] ) {
					$gradient_hover_styles = "background: {$grad_hover_colors[0]};";
				} else {
					$gradient_hover_styles .=
					"background: {$grad_hover_colors[0]};
					background-image: -webkit-gradient( linear, left bottom, left top, from( {$grad_hover_colors[1]} ), to( {$grad_hover_colors[0]} ) );
					background-image: -webkit-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
					background-image:   -moz-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
					background-image:     -o-linear-gradient( bottom, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );
					background-image: linear-gradient( to top, {$grad_hover_colors[1]}, {$grad_hover_colors[0]} );";
				}

				$styles .= '.fusion-button.button-' . $this->button_counter . ':hover,.button-' . $this->button_counter . ':focus,.fusion-button.button-' . $this->button_counter . ':active{' . $gradient_hover_styles . '}';
			}
		}

		if ( 'default' === self::$args['stretch'] ) {
			self::$args['stretch'] = FusionBuilder::get_theme_option( 'button_span' );
		}

		if ( 'yes' === self::$args['stretch'] ) {
			$styles .= '.fusion-button.button-' . $this->button_counter . '{width:100%;}';
		} elseif ( 'no' === self::$args['stretch'] ) {
			$styles .= '.fusion-button.button-' . $this->button_counter . '{width:auto;}';
		}

		if ( $styles ) {
			$style_tag = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
		}

		$icon_html = '';
		if ( $icon ) {
			$icon_html = '<i ' . FusionBuilder::attributes( 'button-shortcode-icon' ) . '></i>';

			if ( 'yes' === $icon_divider ) {
				$icon_html = '<span ' . FusionBuilder::attributes( 'button-shortcode-icon-divder' ) . '>' . $icon_html . '</span>';
			}
		}

		$button_text = '<span ' . FusionBuilder::attributes( 'button-shortcode-button-text' ) . '>' . do_shortcode( $content ) . '</span>';

		$inner_content = ( 'left' === $icon_position ) ? $icon_html . $button_text : $button_text . $icon_html;

		$html = $style_tag . '<a ' . FusionBuilder::attributes( 'button-shortcode' ) . '>' . $inner_content . '</a>';

		// Add wrapper to the button for alignment and scoped styling.
		if ( $alignment && 'no' === self::$args['stretch'] ) {
			$alignment = ' fusion-align' . $alignment;
		}

		$html = '<div class="fusion-button-wrapper' . $alignment . '">' . $html . '</div>';

		$this->button_counter++;

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

		$attr['class'] = 'fusion-button button-' . self::$args['type'] . ' button-' . self::$args['shape'] . ' button-' . self::$args['size'] . ' button-' . self::$args['color'] . ' button-' . $this->button_counter;

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], $attr );

		if ( self::$args['animation_type'] ) {
			$animations = FusionBuilder::animations( array(
				'type'      => self::$args['animation_type'],
				'direction' => self::$args['animation_direction'],
				'speed'     => self::$args['animation_speed'],
				'offset'    => self::$args['animation_offset'],
			) );

			$attr = array_merge( $attr, $animations );

			$attr['class'] .= ' ' . $attr['animation_class'];
			unset( $attr['animation_class'] );
		}

		$attr['target'] = self::$args['target'];
		if ( '_blank' === self::$args['target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		$attr['title'] = self::$args['title'];
		$attr['href']  = self::$args['link'];

		if ( self::$args['modal'] ) {
			$attr['data-toggle'] = 'modal';
			$attr['data-target'] = '.fusion-modal.' . self::$args['modal'];
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_divider_attr() {

		$attr = array();

		$attr['class'] = 'fusion-button-icon-divider button-icon-divider-' . self::$args['icon_position'];

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {

		$attr = array();

		$attr['class'] = 'fa ' . FusionBuilder::font_awesome_name_handler( self::$args['icon'] );

		if ( 'yes' !== self::$args['icon_divider'] ) {
			$attr['class'] .= ' button-icon-' . self::$args['icon_position'];
		}

		if ( self::$args['icon_color'] !== self::$args['accent_color'] ) {
			$attr['style'] = 'color:' . self::$args['icon_color'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function button_text_attr() {

		$attr = array(
			'class' => 'fusion-button-text',
		);

		if ( self::$args['icon'] && 'yes' === self::$args['icon_divider'] ) {
			$attr['class'] = 'fusion-button-text fusion-button-text-' . self::$args['icon_position'];
		}

		return $attr;

	}
}

new FusionSC_Button();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_button() {
	$standard_schemes = array(
		esc_attr__( 'Default', 'fusion-builder' )    => 'default',
		esc_attr__( 'Custom', 'fusion-builder' )     => 'custom',
		esc_attr__( 'Green', 'fusion-builder' )      => 'green',
		esc_attr__( 'Dark Green', 'fusion-builder' ) => 'darkgreen',
		esc_attr__( 'Orange', 'fusion-builder' )     => 'orange',
		esc_attr__( 'Blue', 'fusion-builder' )       => 'blue',
		esc_attr__( 'Red', 'fusion-builder' )        => 'red',
		esc_attr__( 'Pink', 'fusion-builder' )       => 'pink',
		esc_attr__( 'Dark Gray', 'fusion-builder' )  => 'darkgray',
		esc_attr__( 'Light Gray', 'fusion-builder' ) => 'lightgray',
	);
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Button', 'fusion-builder' ),
		'shortcode'  => 'fusion_button',
		'icon'       => 'fusiona-check-empty',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-button-preview.php',
		'preview_id' => 'fusion-builder-block-module-button-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button URL', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
				'description' => esc_attr__( "Add the button's url ex: http://example.com.", 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Text', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Button Text',
				'description' => esc_attr__( 'Add the text that will display on button.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Title Attribute', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
				'description' => esc_attr__( 'Set a title attribute for the button link.', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Target', 'fusion-builder' ),
				'description' => esc_attr__( '_self = open in same window, _blank = open in new window.', 'fusion-builder' ),
				'param_name'  => 'target',
				'default'     => '_self',
				'value'       => array(
					esc_attr__( '_self', 'fusion-builder' )  => '_self',
					esc_attr__( '_blank', 'fusion-builder' ) => '_blank',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's alignment." ),
				'param_name'  => 'alignment',
				'default'     => '',
				'value'       => array(
					esc_attr__( 'Text Flow', 'fusion-builder' ) => '',
					esc_attr__( 'Left', 'fusion-builder' )      => 'left',
					esc_attr__( 'Center', 'fusion-builder' )    => 'center',
					esc_attr__( 'Right', 'fusion-builder' )     => 'right',
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Modal Window Anchor', 'fusion-builder' ),
				'param_name'  => 'modal',
				'value'       => '',
				'description' => esc_attr__( 'Add the class name of the modal window you want to open on button click.', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Button Style', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's color. Select default or color name for theme options, or select custom to use advanced color options below.", 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )    => 'default',
					esc_attr__( 'Custom', 'fusion-builder' )     => 'custom',
					esc_attr__( 'Green', 'fusion-builder' )      => 'green',
					esc_attr__( 'Dark Green', 'fusion-builder' ) => 'darkgreen',
					esc_attr__( 'Orange', 'fusion-builder' )     => 'orange',
					esc_attr__( 'Blue', 'fusion-builder' )       => 'blue',
					esc_attr__( 'Red', 'fusion-builder' )        => 'red',
					esc_attr__( 'Pink', 'fusion-builder' )       => 'pink',
					esc_attr__( 'Dark Gray', 'fusion-builder' )  => 'darkgray',
					esc_attr__( 'Light Gray', 'fusion-builder' ) => 'lightgray',
				),
				'default'     => 'default',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Top Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the top color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_top_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#a0ce4e',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Bottom Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bottom color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_bottom_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#a0ce4e',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Top Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the top hover color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_top_color_hover',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#96c346',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Gradient Bottom Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bottom hover color of the button background.', 'fusion-builder' ),
				'param_name'  => 'button_gradient_bottom_color_hover',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#96c346',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Accent Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the button border, divider, text and icon.', 'fusion-builder' ),
				'param_name'  => 'accent_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#ffffff',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Accent Hover Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the hover color of the button border, divider, text and icon.', 'fusion-builder' ),
				'param_name'  => 'accent_hover_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#ffffff',
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button type.', 'fusion-builder' ),
				'param_name'  => 'type',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Flat', 'fusion-builder' )    => 'flat',
					esc_attr__( '3D', 'fusion-builder' )      => '3d',
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Button Bevel Color For 3D Mode', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the bevel color of the button when using 3D button type.', 'fusion-builder' ),
				'param_name'  => 'bevel_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#54770F',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'flat',
						'operator' => '!=',
					),
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Button Border Width', 'fusion-builder' ),
				'param_name'  => 'border_width',
				'description' => esc_attr__( 'Controls the border width. In pixels.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'color',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'value'       => '',
				'default'     => '',

			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button size.', 'fusion-builder' ),
				'param_name'  => 'size',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Small', 'fusion-builder' )   => 'small',
					esc_attr__( 'Medium', 'fusion-builder' )  => 'medium',
					esc_attr__( 'Large', 'fusion-builder' )   => 'large',
					esc_attr__( 'X-Large', 'fusion-builder' ) => 'xlarge',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Span', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls if the button spans the full width of its container.', 'fusion-builder' ),
				'param_name'  => 'stretch',
				'default'     => 'default',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => 'default',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Shape', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the button shape.', 'fusion-builder' ),
				'param_name'  => 'shape',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Square', 'fusion-builder' )  => 'square',
					esc_attr__( 'Pill', 'fusion-builder' )    => 'pill',
					esc_attr__( 'Round', 'fusion-builder' )   => 'round',
				),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the icon on the button.', 'fusion-builder' ),
				'param_name'  => 'icon_position',
				'value'       => array(
					esc_attr__( 'Left', 'fusion-builder' )  => 'left',
					esc_attr__( 'Right', 'fusion-builder' ) => 'right',
				),
				'default'     => 'left',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon Divider', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display a divider between icon and text.', 'fusion-builder' ),
				'param_name'  => 'icon_divider',
				'default'     => 'no',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Animation Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of animation to use on the element.', 'fusion-builder' ),
				'param_name'  => 'animation_type',
				'value'       => fusion_builder_available_animations(),
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Direction of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the incoming direction for the animation.', 'fusion-builder' ),
				'param_name'  => 'animation_direction',
				'default'     => 'left',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )    => 'down',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
					esc_attr__( 'Bottom', 'fusion-builder' ) => 'up',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Static', 'fusion-builder' ) => 'static',
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'value'       => '0.3',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'default'     => '',
				'group'       => esc_attr__( 'Animation', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'animation_type',
						'value'    => '',
						'operator' => '!=',
					),
				),
				'value'        => array(
					esc_attr__( 'Default', 'fusion-builder' )                                => '',
					esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ) => 'top-into-view',
					esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ) => 'top-mid-of-view',
					esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' )      => 'bottom-in-view',
				),
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
				'param_name'  => 'class',
				'value'       => '',
				'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_button' );
