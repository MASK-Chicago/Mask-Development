<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Dropcap {

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

		add_filter( 'fusion_attr_dropcap-shortcode', array( $this, 'attr' ) );
		add_shortcode( 'fusion_dropcap', array( $this, 'render' ) );

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
				'class'        => '',
				'id'           => '',
				'boxed'        => '',
				'boxed_radius' => '',
				'color'        => strtolower( FusionBuilder::get_theme_option( 'dropcap_color' ) ),
			), $args
		);

		extract( $defaults );

		self::$args = $defaults;

		return '<span ' . FusionBuilder::attributes( 'dropcap-shortcode' ) . '>' . do_shortcode( $content ) . '</span>';

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function attr() {

		$attr = array(
			'class' => 'fusion-dropcap dropcap',
			'style' => '',
		);

		if ( 'yes' == self::$args['boxed'] ) {
			$attr['class'] .= ' dropcap-boxed';

			if ( self::$args['boxed_radius'] || '0' === self::$args['boxed_radius'] ) {
				self::$args['boxed_radius'] = ( 'round' == self::$args['boxed_radius'] ) ? '50%' : self::$args['boxed_radius'];
				$attr['style'] = 'border-radius:' . self::$args['boxed_radius'] . ';';
			}

			$attr['style'] .= 'background-color:' . self::$args['color'] . ';';
		} else {
			$attr['style'] .= 'color:' . self::$args['color'] . ';';
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}
}
new FusionSC_Dropcap();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_dropcap() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Dropcap', 'fusion-builder' ),
		'shortcode'         => 'fusion_dropcap',
		'generator_only'    => true,
		'icon'              => 'fusiona-font',
		'params'            => array(
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Dropcap Letter', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the letter to be used as dropcap.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'A',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the dropcap letter. Leave blank for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Boxed Dropcap', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed dropcap.' ),
				'param_name'  => 'boxed',
				'value'       => array(
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Box Radius', 'fusion-builder' ),
				'param_name'  => 'boxed_radius',
				'value'       => '',
				'description' => esc_attr__( 'Choose the radius of the boxed dropcap. In pixels (px), ex: 1px, or "round".', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'boxed',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
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
add_action( 'fusion_builder_before_init', 'fusion_element_dropcap' );
