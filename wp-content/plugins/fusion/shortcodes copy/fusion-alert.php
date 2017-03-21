<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Alert {

	/**
	 * The alert class.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $alert_class;

	/**
	 * The icon class.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $icon_class;

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

		add_filter( 'fusion_attr_alert-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_alert-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_alert-shortcode-button', array( $this, 'button_attr' ) );

		add_shortcode( 'fusion_alert', array( $this, 'render' ) );

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
				'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
				'class'               => '',
				'id'                  => '',
				'accent_color'        => '',
				'background_color'    => '',
				'border_size'         => '',
				'box_shadow'          => 'no',
				'icon'                => '',
				'type'                => 'general',
				'animation_type'      => '',
				'animation_direction' => 'left',
				'animation_speed'     => '',
				'animation_offset'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
			), $args
		);
		$defaults['border_size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_size'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		switch ( self::$args['type'] ) {

			case 'general':
				$this->alert_class = 'info';
				if ( ! $icon || 'none' !== $icon ) {
					self::$args['icon'] = $icon = 'fa-info-circle';
				}
				break;
			case 'error':
				$this->alert_class = 'danger';
				if ( ! $icon || 'none' !== $icon ) {
					self::$args['icon'] = $icon = 'fa-exclamation-triangle';
				}
				break;
			case 'success':
				$this->alert_class = 'success';
				if ( ! $icon || 'none' !== $icon ) {
					self::$args['icon'] = $icon = 'fa-check-circle';
				}
				break;
			case 'notice':
				$this->alert_class = 'warning';
				if ( ! $icon || 'none' !== $icon ) {
					self::$args['icon'] = $icon = 'fa-lg fa-cog';
				}
				break;
			case 'blank':
				$this->alert_class = 'blank';
				break;
			case 'custom':
				$this->alert_class = 'custom';
				break;
		}

		$html = '<div ' . FusionBuilder::attributes( 'alert-shortcode' ) . '>';
		$html .= '  <button ' . FusionBuilder::attributes( 'alert-shortcode-button' ) . '>&times;</button>';
		if ( $icon && 'none' !== $icon ) {
			$html .= '<span ' . FusionBuilder::attributes( 'alert-icon' ) . '>';
			$html .= '<i ' . FusionBuilder::attributes( 'alert-shortcode-icon' ) . '></i>';
			$html .= '</span>';
		}
		// Make sure the title text is not wrapped with an unattributed p tag.
		$content = preg_replace( '!^<p>(.*?)</p>$!i', '$1', trim( $content ) );

		$html .= do_shortcode( $content );
		$html .= '</div>';

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

		$attr = array();

		$attr['class'] = 'fusion-alert alert ' . self::$args['type'] . ' alert-dismissable alert-' . $this->alert_class;

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], $attr );

		if ( 'yes' === self::$args['box_shadow'] ) {
			$attr['class'] .= ' alert-shadow';
		}

		if ( 'custom' === $this->alert_class ) {
			$attr['style']  = 'background-color:' . self::$args['background_color'] . ';';
			$attr['style'] .= 'color:' . self::$args['accent_color'] . ';';
			$attr['style'] .= 'border-color:' . self::$args['accent_color'] . ';';
			$attr['style'] .= 'border-width:' . self::$args['border_size'] . ';';
		}

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

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}

	/**
	 * Builds theicon  attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {
		return array(
			'class' => 'fa fa-lg ' . FusionBuilder::font_awesome_name_handler( self::$args['icon'] ),
		);
	}


	/**
	 * Builds the button attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function button_attr() {

		$attr = array();

		if ( 'custom' === $this->alert_class ) {
			$attr['style'] = 'color:' . self::$args['accent_color'] . ';border-color:' . self::$args['accent_color'] . ';';
		}

		$attr['type']         = 'button';
		$attr['class']        = 'close toggle-alert';
		$attr['data-dismiss'] = 'alert';
		$attr['aria-hidden']  = 'true';

		return $attr;

	}
}

new FusionSC_Alert();


/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_alert() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Alert', 'fusion-builder' ),
		'shortcode'       => 'fusion_alert',
		'icon'            => 'fa fa-lg fa-exclamation-triangle',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-alert-preview.php',
		'preview_id'      => 'fusion-builder-block-module-alert-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Alert Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the type of alert message. Choose custom for advanced color options below.', 'fusion-builder' ),
				'param_name'  => 'type',
				'default'     => 'error',
				'value'       => array(
					esc_attr__( 'General', 'fusion-builder' ) => 'general',
					esc_attr__( 'Error', 'fusion-builder' )   => 'error',
					esc_attr__( 'Success', 'fusion-builder' ) => 'success',
					esc_attr__( 'Notice', 'fusion-builder' )  => 'notice',
					esc_attr__( 'Custom', 'fusion-builder' )  => 'custom',
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Accent Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom setting only. Set the border, text and icon color for custom alert boxes.', 'fusion-builder' ),
				'param_name'  => 'accent_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Custom setting only. Set the background color for custom alert boxes.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Width', 'fusion-builder' ),
				'param_name'  => 'border_size',
				'value'       => '1px',
				'description' => esc_attr__( 'Custom setting only. Set the border width for custom alert boxes. In pixels.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),

			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Custom Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'custom',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Box Shadow', 'fusion-builder' ),
				'description' => esc_attr__( 'Display a box shadow below the alert box.', 'fusion-builder' ),
				'param_name'  => 'box_shadow',
				'default'     => 'no',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Alert Content', 'fusion-builder' ),
				'description' => esc_attr__( "Insert the alert's content.", 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => esc_html__( 'Your Content Goes Here', 'fusion-builder' ),
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
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )    => 'down',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
					esc_attr__( 'Bottom', 'fusion-builder' ) => 'up',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Static', 'fusion-builder' ) => 'static',
				),
				'default'     => 'left',
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
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )                                => '',
					esc_attr__( 'Top of element hits bottom of viewport', 'fusion-builder' ) => 'top-into-view',
					esc_attr__( 'Top of element hits middle of viewport', 'fusion-builder' ) => 'top-mid-of-view',
					esc_attr__( 'Bottom of element enters viewport', 'fusion-builder' )      => 'bottom-in-view',
				),
				'default'     => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_alert' );
