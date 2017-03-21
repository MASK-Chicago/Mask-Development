<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Progressbar {

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

		add_filter( 'fusion_attr_progressbar-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_progressbar-shortcode-bar', array( $this, 'bar_attr' ) );
		add_filter( 'fusion_attr_progressbar-shortcode-content', array( $this, 'content_attr' ) );
		add_filter( 'fusion_attr_progressbar-shortcode-span', array( $this, 'span_attr' ) );

		add_shortcode( 'fusion_progress', array( $this, 'render' ) );

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
				'animated_stripes'  => 'no',
				'filledcolor'       => '',
				'height'            => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'progressbar_height' ) : '',
				'percentage'        => '70',
				'show_percentage'   => 'yes',
				'striped'           => 'no',
				'textcolor'         => '',
				'text_position'     => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'progressbar_text_position' ) : '',
				'unfilledcolor'     => '',
				'unit'              => '',
				'filledbordercolor' => FusionBuilder::get_theme_option( 'progressbar_filled_border_color' ),
				'filledbordersize'  => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'progressbar_filled_border_size' ) : '',
			), $args
		);

		$defaults['filledbordersize'] = FusionBuilder::validate_shortcode_attr_value( $defaults['filledbordersize'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		if ( ! $filledcolor ) {
			self::$args['filledcolor'] = FusionBuilder::get_theme_option( 'progressbar_filled_color' );
		}

		if ( ! $textcolor ) {
			self::$args['textcolor'] = FusionBuilder::get_theme_option( 'progressbar_text_color' );
		}

		if ( ! $unfilledcolor ) {
			self::$args['unfilledcolor'] = FusionBuilder::get_theme_option( 'progressbar_unfilled_color' );
		}

		$text = '<span ' . FusionBuilder::attributes( 'fusion-progressbar-text' ) . '>' . $content . '</span>';

		$value = '';
		if ( 'yes' == $show_percentage ) {
			$value = '<span ' . FusionBuilder::attributes( 'fusion-progressbar-value' ) . '>' . $percentage . $unit . '</span>';
		}

		$text_wrapper = '<span ' . FusionBuilder::attributes( 'progressbar-shortcode-span' ) . '>' . $text . ' ' . $value . '</span>';

		$bar = '<div ' . FusionBuilder::attributes( 'progressbar-shortcode-bar' ) . '><div ' . FusionBuilder::attributes( 'progressbar-shortcode-content' ) . '></div></div>';

		if ( 'above_bar' == $text_position ) {
			return '<div ' . FusionBuilder::attributes( 'progressbar-shortcode' ) . '>' . $text_wrapper . ' ' . $bar . '</div>';
		}

		return '<div ' . FusionBuilder::attributes( 'progressbar-shortcode' ) . '>' . $bar . ' ' . $text_wrapper . '</div>';

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
			'class' => 'fusion-progressbar',
		);

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], $attr );

		if ( 'above_bar' == self::$args['text_position'] ) {
			$attr['class'] .= ' fusion-progressbar-text-above-bar';
		} elseif ( 'below_bar' == self::$args['text_position'] ) {
			$attr['class'] .= ' fusion-progressbar-text-below-bar';
		} else {
			$attr['class'] .= ' fusion-progressbar-text-on-bar';
		}

		return $attr;

	}

	/**
	 * Builds the bar attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function bar_attr() {

		$attr = array(
			'style' => 'background-color:' . self::$args['unfilledcolor'] . ';',
			'class' => 'fusion-progressbar-bar progress-bar',
		);

		if ( self::$args['height'] ) {
			$attr['style'] .= 'height:' . self::$args['height'] . ';';
		}

		if ( 'yes' == self::$args['striped'] ) {
			$attr['class'] .= ' progress-striped';
		}

		if ( 'yes' == self::$args['animated_stripes'] ) {
			$attr['class'] .= ' active';
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
	 * Builds the content attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function content_attr() {

		$attr = array(
			'class' => 'progress progress-bar-content',
			'style' => 'width:0%;background-color:' . self::$args['filledcolor'] . ';',
		);

		if ( self::$args['filledbordersize'] && self::$args['filledbordercolor'] ) {
			$attr['style'] .= 'border: ' . self::$args['filledbordersize'] . ' solid ' . self::$args['filledbordercolor'] . ';';
		}

		$attr['role'] = 'progressbar';
		$attr['aria-valuemin'] = '0';
		$attr['aria-valuemax'] = '100';

		$attr['aria-valuenow'] = self::$args['percentage'];

		return $attr;

	}

	/**
	 * Builds the span attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function span_attr() {
		return array(
			'class' => 'progress-title',
			'style' => 'color:' . self::$args['textcolor'] . ';',
		);
	}
}
new FusionSC_Progressbar();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_progress() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Progress Bar', 'fusion-builder' ),
		'shortcode'  => 'fusion_progress',
		'icon'       => 'fusiona-tasks',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-progress-preview.php',
		'preview_id' => 'fusion-builder-block-module-progress-preview-template',
		'params'     => array(
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Progress Bar Height', 'fusion-builder' ),
				'description'      => esc_attr__( 'Insert a height for the progress bar. Enter value including any valid CSS unit, ex: 10px. ', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'height' => '',
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Text Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the position of the progress bar text. Choose "Default" for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'text_position',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )   => '',
					esc_attr__( 'On Bar', 'fusion-builder' )    => 'on_bar',
					esc_attr__( 'Above Bar', 'fusion-builder' ) => 'above_bar',
					esc_attr__( 'Below Bar', 'fusion-builder' ) => 'below_bar',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Display Percentage Value', 'fusion-builder' ),
				'description' => esc_attr__( 'Select if you want the filled area percentage value to be shown.', 'fusion-builder' ),
				'param_name'  => 'show_percentage',
				'value'       => array(
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Progress Bar Unit', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert a unit for the progress bar. ex %.', 'fusion-builder' ),
				'param_name'  => 'unit',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'show_percentage',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Area Percentage', 'fusion-builder' ),
				'description' => esc_attr__( 'From 1% to 100%.', 'fusion-builder' ),
				'param_name'  => 'percentage',
				'value'       => '70',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the filled in area. ', 'fusion-builder' ),
				'param_name'  => 'filledcolor',
				'value'       => '',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Filled Border Size', 'fusion-builder' ),
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'param_name'  => 'filledbordersize',
				'value'       => '',
				'min'         => '0',
				'max'         => '20',
				'step'        => '1',
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Filled Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color of the filled in area. ', 'fusion-builder' ),
				'param_name'  => 'filledbordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'filledbordersize',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Unfilled Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the unfilled in area. ', 'fusion-builder' ),
				'param_name'  => 'unfilledcolor',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Striped Filling', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get the filled area striped.', 'fusion-builder' ),
				'param_name'  => 'striped',
				'value'       => array(
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Animated Stripes', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get the the stripes animated.', 'fusion-builder' ),
				'param_name'  => 'animated_stripes',
				'value'       => array(
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'striped',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Progess Bar Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Text will show up on progess bar.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the text color. ', 'fusion-builder' ),
				'param_name'  => 'textcolor',
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
add_action( 'fusion_builder_before_init', 'fusion_element_progress' );
