<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Popover {

	/**
	 * The popover counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $popover_counter = 1;

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

		add_filter( 'fusion_attr_popover-shortcode', array( $this, 'attr' ) );
		add_shortcode( 'fusion_popover', array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args       Shortcode parameters.
	 * @param  string $sc_content Content between shortcode.
	 * @return string             HTML output.
	 */
	public function render( $args, $sc_content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'            => '',
				'id'               => '',
				'animation'        => true,
				'content'          => '',
				'content_bg_color' => FusionBuilder::get_theme_option( 'popover_content_bg_color' ),
				'delay'            => '50',
				'placement'        => strtolower( FusionBuilder::get_theme_option( 'popover_placement' ) ),
				'title'            => '',
				'title_bg_color'   => FusionBuilder::get_theme_option( 'popover_heading_bg_color' ),
				'bordercolor'      => FusionBuilder::get_theme_option( 'popover_border_color' ),
				'textcolor'        => FusionBuilder::get_theme_option( 'popover_text_color' ),
				'trigger'          => 'click',
			), $args
		);

		if ( 'default' === $defaults['placement'] ) {
			$defaults['placement'] = strtolower( FusionBuilder::get_theme_option( 'popover_placement' ) );
		}

		extract( $defaults );

		self::$args = $defaults;

		$arrow_color = $content_bg_color;
		if ( 'bottom' === $placement ) {
			$arrow_color = $title_bg_color;
		}

		$styles  = '<style type="text/css">';
		$styles .= '.popover-' . $this->popover_counter . '.' . $placement . ' .arrow{border-' . $placement . '-color:' . $bordercolor . ';}';
		$styles .= '.popover-' . $this->popover_counter . '{border-color:' . $bordercolor . ';}';
		$styles .= '.popover-' . $this->popover_counter . ' .popover-title{background-color:' . $title_bg_color . ';color:' . $textcolor . ';border-color:' . $bordercolor . ';}';
		$styles .= '.popover-' . $this->popover_counter . ' .popover-content{background-color:' . $content_bg_color . ';color:' . $textcolor . ';}';
		$styles .= '.popover-' . $this->popover_counter . '.' . $placement . ' .arrow:after{border-' . $placement . '-color:' . $arrow_color . ';}';
		$styles .= '</style>';

		$html = '<span ' . FusionBuilder::attributes( 'popover-shortcode' ) . '>' . $styles . do_shortcode( $sc_content ) . '</span>';

		$this->popover_counter++;

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

		$attr = array(
			'class' => 'fusion-popover popover-' . $this->popover_counter,
		);

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		$attr['data-animation'] = self::$args['animation'];
		$attr['data-class']     = 'popover-' . $this->popover_counter;
		$attr['data-container'] = 'popover-' . $this->popover_counter;
		$attr['data-content']   = self::$args['content'];
		$attr['data-delay']     = self::$args['delay'];
		$attr['data-placement'] = strtolower( self::$args['placement'] );
		$attr['data-title']     = self::$args['title'];
		$attr['data-toggle']    = 'popover';
		$attr['data-trigger']   = self::$args['trigger'];

		return $attr;

	}
}
new FusionSC_Popover();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_popover() {
	fusion_builder_map( array(
		'name'           => esc_attr__( 'Popover', 'fusion-builder' ),
		'shortcode'      => 'fusion_popover',
		'generator_only' => true,
		'icon'           => 'fusiona-uniF61C',
		'params'         => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Triggering Content', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
				'description' => esc_attr__( 'Content that will trigger the popover.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Popover Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Heading text of the popover.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Heading Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the popover heading. ', 'fusion-builder' ),
				'param_name'  => 'title_bg_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Contents Inside Popover', 'fusion-builder' ),
				'description' => esc_attr__( 'Text to be displayed inside the popover.', 'fusion-builder' ),
				'param_name'  => 'content',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Content Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color of the popover content area. ', 'fusion-builder' ),
				'param_name'  => 'content_bg_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Popover Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color of the of the popover box. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Popover Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls all the text color inside the popover box. Leave blank for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'textcolor',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Popover Trigger Method', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose mouse action to trigger popover.' ),
				'param_name'  => 'trigger',
				'value'       => array(
					esc_attr__( 'Hover', 'fusion-builder' ) => 'hover',
					esc_attr__( 'Click', 'fusion-builder' ) => 'click',
				),
				'default'     => 'click',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Popover Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position of the popover. Choose default for theme option selection.' ),
				'param_name'  => 'placement',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => 'default',
					esc_attr__( 'Top', 'fusion-builder' )     => 'top',
					esc_attr__( 'Bottom', 'fusion-builder' )  => 'bottom',
					esc_attr__( 'Left', 'fusion-builder' )    => 'left',
					esc_attr__( 'Right', 'fusion-builder' )   => 'right',
				),
				'default'     => 'default',
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
add_action( 'fusion_builder_before_init', 'fusion_element_popover' );
