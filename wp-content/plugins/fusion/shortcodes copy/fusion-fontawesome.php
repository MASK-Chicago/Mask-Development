<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_FontAwesome {

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

		add_filter( 'fusion_attr_fontawesome-shortcode', array( $this, 'attr' ) );
		add_shortcode( 'fusion_fontawesome', array( $this, 'render' ) );

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
				'alignment'           => '',
				'circle'              => 'yes',
				'circlecolor'         => '',
				'circlebordercolor'   => '',
				'flip'                => '',
				'icon'                => '',
				'iconcolor'           => '',
				'rotate'              => '',
				'size'                => 'medium',
				'spin'                => 'no',
				'animation_type'      => '',
				'animation_direction' => 'down',
				'animation_speed'     => '0.1',
				'animation_offset'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
			), $args
		);

		extract( $defaults );

		// Dertmine line-height and margin from font size.
		$defaults['font_size']            = FusionBuilder::validate_shortcode_attr_value( self::convert_deprecated_sizes( $defaults['size'] ), '' );
		$defaults['circle_yes_font_size'] = $defaults['font_size'] * 0.88;
		$defaults['line_height']          = $defaults['font_size'] * 1.76;
		$defaults['icon_margin']          = $defaults['font_size'] * 0.5;
		$defaults['icon_margin_position'] = ( is_rtl() ) ? 'left' : 'right';

		self::$args = $defaults;

		$html = '<i ' . FusionBuilder::attributes( 'fontawesome-shortcode' ) . '>' . do_shortcode( $content ) . '</i>';

		if ( $alignment ) {
			$html = '<div class="align' . $alignment . '">' . $html . '</div>';
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
			'class' => 'fa fontawesome-icon ' . FusionBuilder::font_awesome_name_handler( self::$args['icon'] ) . ' circle-' . self::$args['circle'],
		) );

		$attr['style'] = '';

		if ( 'yes' == self::$args['circle'] ) {

			if ( self::$args['circlebordercolor'] ) {
				$attr['style'] .= 'border-color:' . self::$args['circlebordercolor'] . ';';
			}

			if ( self::$args['circlecolor'] ) {
				$attr['style'] .= 'background-color:' . self::$args['circlecolor'] . ';';
			}

			$attr['style'] .= 'font-size:' . self::$args['circle_yes_font_size'] . 'px;';

			$attr['style'] .= 'line-height:' . self::$args['line_height'] . 'px;height:' . self::$args['line_height'] . 'px;width:' . self::$args['line_height'] . 'px;';

		} else {
			$attr['style'] .= 'font-size:' . self::$args['font_size'] . 'px;';
		}

		if (  'center' == self::$args['alignment'] ) {
			$attr['style'] .= 'margin-left:0;margin-right:0;';
		} else {
			$attr['style'] .= 'margin-' . self::$args['icon_margin_position'] . ':' . self::$args['icon_margin'] . 'px;';
		}

		if ( self::$args['iconcolor'] ) {
			$attr['style'] .= 'color:' . self::$args['iconcolor'] . ';';
		}

		if ( self::$args['rotate'] ) {
			$attr['class'] .= ' fa-rotate-' . self::$args['rotate'];
		}

		if ( 'yes' == self::$args['spin'] ) {
			$attr['class'] .= ' fa-spin';
		}

		if ( self::$args['flip'] ) {
			$attr['class'] .= ' fa-flip-' . self::$args['flip'];
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
	 * Converts deprecated image sizes to their new names.
	 *
	 * @access public
	 * @since 1.0
	 * @param  string $size The name of the old image-size.
	 * @return string       The name of the new image-size.
	 */
	public function convert_deprecated_sizes( $size ) {
		switch ( $size ) {
			case 'small':
				$size = '10px';
				break;
			case 'medium':
				$size = '18px';
				break;
			case 'large':
				$size = '40px';
				break;
			default:
				break;
		}

		return $size;
	}
}
new FusionSC_FontAwesome();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_font_awesome() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Font Awesome Icon', 'fusion-builder' ),
		'shortcode'  => 'fusion_fontawesome',
		'icon'       => 'fusiona-flag',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-font-awesome-preview.php',
		'preview_id' => 'fusion-builder-block-module-font-awesome-preview-template',
		'params'     => array(
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Select Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Size of Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Set the size of the icon. In pixels (px), ex: 13px.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Flip Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to flip the icon.', 'fusion-builder' ),
				'param_name'  => 'flip',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' )       => '',
					esc_attr__( 'Horizontal', 'fusion-builder' ) => 'horizontal',
					esc_attr__( 'Vertical', 'fusion-builder' )   => 'vertical',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Rotate Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to rotate the icon.', 'fusion-builder' ),
				'param_name'  => 'rotate',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' ) => '',
					'90'  => '90',
					'180' => '180',
					'270' => '270',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Spinning Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to let the icon spin.', 'fusion-builder' ),
				'param_name'  => 'spin',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Icon in Circle', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display the icon in a circle.', 'fusion-builder' ),
				'param_name'  => 'circle',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the icon. ', 'fusion-builder' ),
				'param_name'  => 'iconcolor',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Circle Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the circle. ', 'fusion-builder' ),
				'param_name'  => 'circlecolor',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'circle',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Icon Circle Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the circle border. ', 'fusion-builder' ),
				'param_name'  => 'circlebordercolor',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'circle',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
				'description' => esc_attr__( "Select the icon's alignment.", 'fusion-builder' ),
				'param_name'  => 'alignment',
				'value'       => array(
					esc_attr__( 'Text Flow', 'fusion-builder' ) => '',
					esc_attr__( 'Center', 'fusion-builder' )    => 'center',
					esc_attr__( 'Left', 'fusion-builder' )      => 'left',
					esc_attr__( 'Right', 'fusion-builder' )     => 'right',
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
				'default'     => 'down',
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
				'heading'     => esc_attr__( 'Speed of Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
				'param_name'  => 'animation_speed',
				'value'       => array(
					'1'   => '1',
					'0.1' => '0.1',
					'0.2' => '0.2',
					'0.3' => '0.3',
					'0.4' => '0.4',
					'0.5' => '0.5',
					'0.6' => '0.6',
					'0.7' => '0.7',
					'0.8' => '0.8',
					'0.9' => '0.9',
				),
				'default'     => '0.1',
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
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_font_awesome' );
