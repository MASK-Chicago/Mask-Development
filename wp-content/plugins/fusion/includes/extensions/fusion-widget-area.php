<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_WidgetArea {

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
	 * Counter for widgets.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $widget_counter = 1;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_widget-shortcode', array( $this, 'attr' ) );
		add_shortcode( 'fusion_widget_area', array( $this, 'render' ) );

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
				'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
				'class'            => '',
				'id'               => '',
				'background_color' => '',
				'name'             => '',
				'padding'          => '',
			), $args
		);

		$defaults['padding'] = FusionBuilder::validate_shortcode_attr_value( $defaults['padding'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		$html = '<div ' . FusionBuilder::attributes( 'widget-shortcode' ) . '>';
		$html .= self::get_styles();

		ob_start();
		// @codingStandardsIgnoreStart
		if ( function_exists( 'dynamic_sidebar' ) && dynamic_sidebar( $name ) ) {
			// All is good, dynamic_sidebar() already called the rendering.
		}
		// @codingStandardsIgnoreEnd
		$html .= ob_get_clean();

		$html .= '<div ' . FusionBuilder::attributes( 'fusion-additional-widget-content' ) . '>';
		$html .= do_shortcode( $content );
		$html .= '</div>';
		$html .= '</div>';

		$this->widget_counter++;

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
			'class' => 'fusion-widget-area fusion-widget-area-' . $this->widget_counter . ' fusion-content-widget-area',
		) );

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;
	}

	/**
	 * Gets the CSS styles.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	function get_styles() {
		$styles = '';

		if ( self::$args['background_color'] ) {
			$styles .= '.fusion-widget-area-' . $this->widget_counter . ' {background-color:' . self::$args['background_color'] . ';}';
		}

		if ( self::$args['padding'] ) {
			if ( strpos( self::$args['padding'], '%' ) === false && strpos( self::$args['padding'], 'px' ) === false ) {
				self::$args['padding'] = self::$args['padding'] . 'px';
			}

			$_padding = ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::get_value_with_unit( self::$args['padding'] ) : self::$args['padding'];
			$styles .= '.fusion-widget-area-' . $this->widget_counter . ' {padding:' . $_padding . ';}';
		}

		if ( $styles ) {
			$styles = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
		}

		return $styles;
	}
}
new FusionSC_WidgetArea();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_widget_area() {
	fusion_builder_map( array(
		'name'      => esc_attr__( 'Widget Area', 'fusion-builder' ),
		'shortcode' => 'fusion_widget_area',
		'icon'      => 'fusiona-dashboard',
		'params'    => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Widget Area Name', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the name of the widget area to display.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => FusionBuilder::fusion_get_sidebars(),
				'default'     => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Backgound Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the widget area.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
			),
			array(
				'type'        => 'dimension',
				'heading'     => esc_attr__( 'Padding', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-builder' ),
				'param_name'  => 'padding',
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
add_action( 'fusion_builder_before_init', 'fusion_element_widget_area' );
