<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_CountersCircle {

	/**
	 * Parent SC arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	public static $parent_args;

	/**
	 * Child SC arguments.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	public static $child_args;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_counters-circle-shortcode', array( $this, 'parent_attr' ) );
		add_shortcode( 'fusion_counters_circle', array( $this, 'render_parent' ) );

		add_filter( 'fusion_attr_counter-circle-shortcode', array( $this, 'child_attr' ) );
		add_filter( 'fusion_attr_counter-circle-wrapper-shortcode', array( $this, 'child_wrapper_attr' ) );
		add_shortcode( 'fusion_counter_circle', array( $this, 'render_child' ) );

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
	function render_parent( $args, $content = '' ) {

		$defaults = shortcode_atts(
			array(
				'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
				'class'            => '',
				'id'               => '',
				'animation_offset' => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
			), $args
		);

		extract( $defaults );

		self::$parent_args = $defaults;

		return '<div ' . FusionBuilder::attributes( 'counters-circle-shortcode' ) . '>' . do_shortcode( $content ) . '</div>';

	}

	/**
	 * Builds the parent attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function parent_attr() {

		$attr = fusion_builder_visibility_atts(
			self::$parent_args['hide_on_mobile'],
			array(
				'class' => 'fusion-counters-circle counters-circle',
			)
		);

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		return $attr;

	}

	/**
	 * Render the child shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'         => '',
				'id'            => '',
				'countdown'     => 'no',
				'filledcolor'   => strtolower( FusionBuilder::get_theme_option( 'counter_filled_color' ) ),
				'unfilledcolor' => strtolower( FusionBuilder::get_theme_option( 'counter_unfilled_color' ) ),
				'scales'        => 'no',
				'size'          => '220',
				'speed'         => '1500',
				'value'         => '1',
			), $args
		);

		$defaults['size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['size'], '' );

		extract( $defaults );

		self::$child_args = $defaults;

		self::$child_args['scales'] = false;
		if ( 'yes' == $scales ) {
			self::$child_args['scales'] = true;
		}

		self::$child_args['countdown'] = false;
		if ( 'yes' == $countdown ) {
			self::$child_args['countdown'] = true;
		}

		$output = '<div ' . FusionBuilder::attributes( 'counter-circle-shortcode' ) . '>' . do_shortcode( $content ) . '</div>';

		return '<div ' . FusionBuilder::attributes( 'counter-circle-wrapper-shortcode' ) . '>' . $output . '</div>';

	}

	/**
	 * Builds the child attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function child_attr() {

		$attr = array(
			'class' => 'fusion-counter-circle counter-circle counter-circle-content',
		);

		if ( self::$child_args['class'] ) {
			$attr['class'] .= ' ' . self::$child_args['class'];
		}

		if ( self::$child_args['id'] ) {
			$attr['id'] = self::$child_args['id'];
		}

		$multiplicator = self::$child_args['size'] / 220;
		$stroke_size   = 11 * $multiplicator;
		$font_size     = 50 * $multiplicator;

		$attr['data-percent']       = self::$child_args['value'];
		$attr['data-countdown']     = self::$child_args['countdown'];
		$attr['data-filledcolor']   = self::$child_args['filledcolor'];
		$attr['data-unfilledcolor'] = self::$child_args['unfilledcolor'];
		$attr['data-scale']         = self::$child_args['scales'];
		$attr['data-size']          = self::$child_args['size'];
		$attr['data-speed']         = self::$child_args['speed'];
		$attr['data-strokesize']    = $stroke_size;

		$attr['style'] = 'font-size:' . $font_size . 'px;height:' . self::$child_args['size'] . 'px;width:' . self::$child_args['size'] . 'px;line-height:' . self::$child_args['size'] . 'px;';

		return $attr;

	}

	/**
	 * Builds the child-wrapper attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function child_wrapper_attr() {

		$attr = array(
			'class' => 'counter-circle-wrapper',
			'style' => 'height:' . self::$child_args['size'] . 'px;width:' . self::$child_args['size'] . 'px;line-height:' . self::$child_args['size'] . 'px;',
			'data-originalsize' => self::$child_args['size'],
		);

		if ( self::$parent_args['animation_offset'] ) {
			$animations = FusionBuilder::animations( array( 'offset' => self::$parent_args['animation_offset'] ) );
			$attr       = array_merge( $attr, $animations );
		}
		return $attr;
	}
}
new FusionSC_CountersCircle();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_counters_circle() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Counter Circles', 'fusion-builder' ),
		'shortcode'     => 'fusion_counters_circle',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_counter_circle',
		'icon'          => 'fusiona-clock',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_counter_circle value="50" filledcolor="" unfilledcolor="" size="220" scales="no" countdown="no" speed="1500"]Your Content Goes Here[/fusion_counter_circle]',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Offset of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls when the animation should start.', 'fusion-builder' ),
				'param_name'  => 'animation_offset',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )                                => '',
					esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ) => 'top-into-view',
					esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ) => 'top-mid-of-view',
					esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' )      => 'bottom-in-view',
				),
				'default'     => '',
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
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
				'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
				'param_name'  => 'id',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_counters_circle' );


/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_counter_circle() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Counter Circle', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this block.', 'fusion-builder' ),
		'shortcode'         => 'fusion_counter_circle',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Area Percentage', 'fusion-builder' ),
				'description' => esc_attr__( 'From 1% to 100%.', 'fusion-builder' ),
				'param_name'  => 'value',
				'value'       => '50',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Color', 'fusion-builder' ),
				'param_name'  => 'filledcolor',
				'value'       => '',
				'description' => esc_attr__( 'Controls the color of the filled in area. ', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Unfilled Color', 'fusion-builder' ),
				'param_name'  => 'unfilledcolor',
				'value'       => '',
				'description' => esc_attr__( 'Controls the color of the unfilled in area. ', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Size of the Counter', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert size of the counter in px. ex: 220.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => '220',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Scales', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show a scale around circles.', 'fusion-builder' ),
				'param_name'  => 'scales',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Countdown', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to let the circle filling move counter clockwise.', 'fusion-builder' ),
				'param_name'  => 'countdown',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Animation Speed', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert animation speed in milliseconds.', 'fusion-builder' ),
				'param_name'  => 'speed',
				'value'       => '1500',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Circle Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert text for counter circle box, keep it short.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_counter_circle' );
