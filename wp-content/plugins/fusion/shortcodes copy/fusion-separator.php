<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Separator {

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

		add_filter( 'fusion_attr_separator-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_separator-shortcode-icon-wrapper', array( $this, 'icon_wrapper_attr' ) );
		add_filter( 'fusion_attr_separator-shortcode-icon', array( $this, 'icon_attr' ) );

		add_shortcode( 'fusion_separator', array( $this, 'render' ) );

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
				'hide_on_mobile'    => fusion_builder_default_visibility( 'string' ),
				'class'             => '',
				'id'                => '',
				'alignment'         => 'center',
				'bottom_margin'     => '',
				'border_size'       => FusionBuilder::get_theme_option( 'separator_border_size' ),
				'icon'              => '',
				'icon_circle'       => FusionBuilder::get_theme_option( 'separator_circle' ),
				'icon_circle_color' => '',
				'sep_color'         => FusionBuilder::get_theme_option( 'sep_color' ),
				'style_type'        => 'none',
				'top_margin'        => '',
				'width'             => '',
				'bottom'            => '', // Deprecated.
				'color'             => '', // Deprecated.
				'style'             => '', // Deprecated.
				'top'               => '', // Deprecated.
			), $args
		);

		$defaults['border_size']   = FusionBuilder::validate_shortcode_attr_value( $defaults['border_size'], 'px' );
		$defaults['width']         = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
		$defaults['top_margin']    = FusionBuilder::validate_shortcode_attr_value( $defaults['top_margin'], 'px' );
		$defaults['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $defaults['bottom_margin'], 'px' );

		if ( '0' === $defaults['icon_circle'] ) {
			$defaults['icon_circle'] = 'no';
		}

		if ( $defaults['style'] ) {
			$defaults['style_type'] = $defaults['style'];
		} elseif ( 'default' === $defaults['style_type'] ) {
			$defaults['style_type'] = Avada()->settings->get( 'separator_style_type' );
		}

		extract( $defaults );

		self::$args = $defaults;

		self::$args['style_type'] = str_replace( ' ', '|', $style_type );

		if ( $bottom ) {
			self::$args['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $bottom, 'px' );
		}

		if ( $color ) {
			self::$args['sep_color'] = $color;
		}

		if ( $top ) {
			self::$args['top_margin'] = FusionBuilder::validate_shortcode_attr_value( $top, 'px' );

			if ( ! $bottom && 'none' != $defaults['style'] ) {
				self::$args['bottom_margin'] = FusionBuilder::validate_shortcode_attr_value( $top, 'px' );
			}
		}

		$icon_insert = '';
		if ( $icon && 'none' !== $style_type ) {
			$icon_insert = '<span ' . FusionBuilder::attributes( 'separator-shortcode-icon-wrapper' ) . '><i ' . FusionBuilder::attributes( 'separator-shortcode-icon' ) . '></i></span>';
		}

		$html = '<div ' . FusionBuilder::attributes( 'fusion-sep-clear' ) . '></div><div ' . FusionBuilder::attributes( 'separator-shortcode' ) . '>' . $icon_insert . '</div>';

		if ( 'right' === self::$args['alignment'] ) {
			$html .= '<div ' . FusionBuilder::attributes( 'fusion-sep-clear' ) . '></div>';
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
			'class' => 'fusion-separator',
			'style' => '',
		) );

		if ( ! self::$args['width'] || '100%' == self::$args['width'] ) {
			$attr['class'] .= ' fusion-full-width-sep';
		}

		$styles = explode( '|', self::$args['style_type'] );

		if ( ! in_array( 'none', $styles ) && ! in_array( 'single', $styles ) && ! in_array( 'double', $styles ) && ! in_array( 'shadow', $styles ) ) {
			$styles[] .= 'single';
		}

		foreach ( $styles as $style ) {
			$attr['class'] .= ' sep-' . $style;
		}

		if ( self::$args['sep_color'] ) {
			if ( 'shadow' === self::$args['style_type'] ) {

				$shadow = 'background:radial-gradient(ellipse at 50% -50% , ' . self::$args['sep_color'] . ' 0px, rgba(255, 255, 255, 0) 80%) repeat scroll 0 0 rgba(0, 0, 0, 0);';

				$attr['style']  = $shadow;
				$attr['style'] .= str_replace( 'radial-gradient', '-webkit-radial-gradient', $shadow );
				$attr['style'] .= str_replace( 'radial-gradient', '-moz-radial-gradient', $shadow );
				$attr['style'] .= str_replace( 'radial-gradient', '-o-radial-gradient', $shadow );
			} elseif ( 'none' !== self::$args['style_type'] ) {

				$attr['style'] = 'border-color:' . self::$args['sep_color'] . ';';
			}
		}

		if ( in_array( 'single', $styles ) ) {
			$attr['style'] .= 'border-top-width:' . self::$args['border_size'] . ';';
		}

		if ( in_array( 'double', $styles ) ) {
			$attr['style'] .= 'border-top-width:' . self::$args['border_size'] . ';border-bottom-width:' . self::$args['border_size'] . ';';
		}

		if ( 'center' === self::$args['alignment'] ) {
			$attr['style'] .= 'margin-left: auto;margin-right: auto;';
		} elseif ( 'right' === self::$args['alignment'] ) {
			$attr['style'] .= 'float:right;';
			$attr['class'] .= ' fusion-clearfix';
		}

		$attr['style'] .= 'margin-top:' . self::$args['top_margin'] . ';';

		if ( self::$args['bottom_margin'] ) {
			$attr['style'] .= 'margin-bottom:' . self::$args['bottom_margin'] . ';';
		}

		if ( self::$args['width'] ) {
			$attr['style'] .= 'width:100%;max-width:' . self::$args['width'] . ';';
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
	 * Builds the icon-wrapper attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_wrapper_attr() {

		$attr = array(
			'class' => 'icon-wrapper',
		);

		$circle_color = self::$args['sep_color'];
		if ( 'no' === self::$args['icon_circle'] ) {
			$circle_color = 'transparent';
		}

		$attr['style'] = 'border-color:' . $circle_color . ';';

		if ( self::$args['icon_circle_color'] ) {
			$attr['style'] .= 'background-color:' . self::$args['icon_circle_color'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the icon attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {
		return array(
			'class' => 'fa ' . FusionBuilder::font_awesome_name_handler( self::$args['icon'] ),
			'style' => 'color:' . self::$args['sep_color'] . ';',
		);
	}
}
new FusionSC_Separator();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_separator() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Separator', 'fusion-builder' ),
		'shortcode'  => 'fusion_separator',
		'icon'       => 'fusiona-minus',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-separator-preview.php',
		'preview_id' => 'fusion-builder-block-module-separator-preview-template',
		'params'     => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Style', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the separator line style.' ),
				'param_name'  => 'style_type',
				'value'       => array(
					esc_attr__( 'No Style', 'fusion-builder' )             => 'none',
					esc_attr__( 'Single Border Solid', 'fusion-builder' )  => 'single solid',
					esc_attr__( 'Double Border Solid', 'fusion-builder' )  => 'double solid',
					esc_attr__( 'Single Border Dashed', 'fusion-builder' ) => 'single|dashed',
					esc_attr__( 'Double Border Dashed', 'fusion-builder' ) => 'double|dashed',
					esc_attr__( 'Single Border Dotted', 'fusion-builder' ) => 'single|dotted',
					esc_attr__( 'Double Border Dotted', 'fusion-builder' ) => 'double|dotted',
					esc_attr__( 'Shadow', 'fusion-builder' )               => 'shadow',
					esc_attr__( 'Default', 'fusion-builder' )              => 'default',
				),
				'default'     => 'none',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Separator Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the separator color. ', 'fusion-builder' ),
				'param_name'  => 'sep_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'top_margin'    => '',
					'bottom_margin' => '',

				),
				'description' => esc_attr__( 'Spacing above and below the separator. In px, em or %, e.g. 10px.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Border Size', 'fusion-builder' ),
				'param_name'  => 'border_size',
				'value'       => '',
				'min'         => '0',
				'max'         => '50',
				'step'        => '1',
				'default'     => '',
				'description' => esc_attr__( 'In pixels. ', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Circled Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have a circle in separator color around the icon.', 'fusion-builder' ),
				'param_name'  => 'icon_circle',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'default'     => '',
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
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Circle Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the circle around the icon.', 'fusion-builder' ),
				'param_name'  => 'icon_circle_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'icon',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'icon_circle',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Separator Width', 'fusion-builder' ),
				'param_name'       => 'dimensions_width',
				'value'            => array(
					'width' => '',
				),
				'description'      => esc_attr__( 'In pixels (px or %), ex: 1px, ex: 50%. Leave blank for full width.', 'fusion-builder' ),
				'group'            => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the separator alignment; only works when a width is specified.', 'fusion-builder' ),
				'param_name'  => 'alignment',
				'value'       => array(
					esc_attr__( 'Center', 'fusion-builder' ) => 'center',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
				),
				'default'     => 'center',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_separator' );
