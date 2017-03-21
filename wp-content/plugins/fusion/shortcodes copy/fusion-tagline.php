<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Tagline {

	/**
	 * The tagline box counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $tagline_box_counter = 1;

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

		add_filter( 'fusion_attr_tagline-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_tagline-shortcode-reading-box', array( $this, 'reading_box_attr' ) );
		add_filter( 'fusion_attr_tagline-shortcode-button', array( $this, 'button_attr' ) );

		add_shortcode( 'fusion_tagline_box', array( $this, 'render' ) );

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
	function render( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile'      => fusion_builder_default_visibility( 'string' ),
				'class'               => '',
				'id'                  => '',
				'backgroundcolor'     => FusionBuilder::get_theme_option( 'tagline_bg' ),
				'border'              => '0px',
				'bordercolor'         => FusionBuilder::get_theme_option( 'tagline_border_color' ),
				'button'              => '',
				'buttoncolor'         => 'default',
				'button_shape'        => FusionBuilder::get_theme_option( 'button_shape' ),
				'button_size'         => FusionBuilder::get_theme_option( 'button_size' ),
				'button_type'         => FusionBuilder::get_theme_option( 'button_type' ),
				'content_alignment'   => 'left',
				'description'         => '',
				'highlightposition'   => 'left',
				'link'                => '',
				'linktarget'          => '_self',
				'margin_bottom'       => ( '' !== FusionBuilder::get_theme_option( 'tagline_margin', 'bottom' ) && class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'tagline_margin', 'bottom' ) ) : '0px',
				'margin_top'          => ( '' !== FusionBuilder::get_theme_option( 'tagline_margin', 'top' ) && class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'tagline_margin', 'top' ) ) : '0px',
				'modal'               => '',
				'shadow'              => 'no',
				'shadowopacity'       => '0.7',
				'title'               => '',
				'animation_type'      => '',
				'animation_direction' => 'left',
				'animation_speed'     => '',
				'animation_offset'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
			), $args
		);

		$defaults['border'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border'], 'px' );

		if ( $defaults['modal'] ) {
			$defaults['link'] = '#';
		}

		$defaults['button_type'] = strtolower( $defaults['button_type'] );

		extract( $defaults );

		self::$args = $defaults;
		$additional_content = '';

		$styles = "<style type='text/css'>.reading-box-container-{$this->tagline_box_counter} .element-bottomshadow:before,.reading-box-container-{$this->tagline_box_counter} .element-bottomshadow:after{opacity:{$shadowopacity};}</style>";

		if ( ( isset( $link ) && $link ) && ( isset( $button ) && $button ) && 'center' !== self::$args['content_alignment'] ) {
			self::$args['button_class'] = ' fusion-desktop-button continue';
			$additional_content = '<a ' . FusionBuilder::attributes( 'tagline-shortcode-button' ) . '><span>' . $button . '</span></a>';
		}

		if ( isset( $title ) && $title ) {
			$additional_content .= '<h2>' . $title . '</h2>';
		}

		if ( isset( $description ) && $description ) {
			$additional_content .= '<div class="reading-box-description">' . $description . '</div>';
		}

		if ( $content ) {
			$additional_content .= '<div class="reading-box-additional">' . do_shortcode( $content ) . '</div>';
		}

		if ( ( isset( $link ) && $link ) && ( isset( $button ) && $button ) ) {
			self::$args['button_class'] = ' fusion-mobile-button';
			$additional_content .= '<a ' . FusionBuilder::attributes( 'tagline-shortcode-button' ) . '><span>' . $button . '</span></a>';
		}
		// If its a custom color scheme selected, then created a style block.
		if ( false !== strpos( $defaults['buttoncolor'], 'scheme-' ) && class_exists( 'Avada' ) ) {
			$scheme_id    = str_replace( 'scheme-', '', $defaults['buttoncolor'] );
			$custom_color = ( class_exists( 'Avada' ) && method_exists( 'Avada_Settings', 'get_custom_color' ) ) ? Avada()->settings->get_custom_color( $scheme_id ) : '';
			// If the scheme exists and has options then create style block.
			$accent_color       = ( isset( $custom_color['button_accent_color'] ) ) ? strtolower( $custom_color['button_accent_color'] ) : '#ffffff';
			$accent_hover_color = ( isset( $custom_color['button_accent_hover_color'] ) ) ? strtolower( $custom_color['button_accent_hover_color'] ) : '#ffffff';
			$bevel_color        = ( isset( $custom_color['button_bevel_color'] ) ) ? strtolower( $custom_color['button_bevel_color'] ) : '#54770F';
			$gradient_colors    = strtolower( $custom_color['button_gradient_top_color'] ) . '|' . strtolower( $custom_color['button_gradient_bottom_color'] );
			$gradient_hover_colors = strtolower( $custom_color['button_gradient_top_color_hover'] ) . '|' . strtolower( $custom_color['button_gradient_bottom_color_hover'] );

			$button_3d_styles = '';
			if ( ( '3d' === $button_type ) && $bevel_color ) {
				if ( 'small' === $button_size ) {
					$button_3d_add = 0;
				} elseif ( 'medium' === $button_size ) {
					$button_3d_add = 1;
				} elseif ( 'large' === $button_size ) {
					$button_3d_add = 2;
				} elseif ( 'xlarge' === $button_size ) {
					$button_3d_add = 3;
				}

				$button_3d_shadow_part_1 = 'inset 0px 1px 0px #fff,';
				$button_3d_shadow_part_2 = '0px ' . ( 2 + $button_3d_add ) . 'px 0px ' . $bevel_color . ',';
				$button_3d_shadow_part_3 = '1px ' . ( 4 + $button_3d_add ) . 'px ' . ( 4 + $button_3d_add ) . 'px 3px rgba(0,0,0,0.3)';
				if ( 'small' === $button_size ) {
					$button_3d_shadow_part_3 = str_replace( '3px', '2px', $button_3d_shadow_part_3 );
				}
				$button_3d_shadow = $button_3d_shadow_part_1 . $button_3d_shadow_part_2 . $button_3d_shadow_part_3;
				$button_3d_styles = '-webkit-box-shadow:' . $button_3d_shadow . ';-moz-box-shadow:' . $button_3d_shadow . ';box-shadow:' . $button_3d_shadow . ';';
			}

			$text_color_styles       = 'color:' . $accent_color . ';';
			$text_color_hover_styles = 'color:' . $accent_hover_color . ';';
			$general_styles          = 'border-color:' . $accent_color . ';';
			$hover_styles            = 'border-color:' . $accent_hover_color . ';';

			if ( $gradient_colors ) {
				// Checking for deprecated separators.
				$grad_colors = explode( '|', $gradient_colors );
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
			}
			if ( $gradient_hover_colors ) {
				// Checking for deprecated separators.
				$grad_colors = explode( '|', $gradient_hover_colors );
				if ( 1 == count( $grad_colors ) || empty( $grad_colors[1] ) || $grad_colors[0] == $grad_colors[1] ) {
					$gradient_styles = "background: {$grad_colors[0]};";
				} else {
					$gradient_hover_styles =
					"background: {$grad_colors[0]};
					background-image: -webkit-gradient( linear, left bottom, left top, from( {$grad_colors[1]} ), to( {$grad_colors[0]} ) );
					background-image: -webkit-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image:   -moz-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image:     -o-linear-gradient( bottom, {$grad_colors[1]}, {$grad_colors[0]} );
					background-image: linear-gradient( to top, {$grad_colors[1]}, {$grad_colors[0]} );";
				}
			}

			$styles .= '<style type=\'text/css\'>.reading-box-container-' . $this->tagline_box_counter . ' .button{' . $button_3d_styles . $text_color_styles . $general_styles . $gradient_styles . '} .reading-box-container-' . $this->tagline_box_counter . ' .button:hover{' . $text_color_hover_styles . $hover_styles . $gradient_hover_styles . '}</style>';

		}

		$html = $styles . '<div ' . FusionBuilder::attributes( 'tagline-shortcode' ) . '><div ' . FusionBuilder::attributes( 'tagline-shortcode-reading-box' ) . '>' . $additional_content . '</div></div>';

		$this->tagline_box_counter++;

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
			'class' => 'fusion-reading-box-container reading-box-container-' . $this->tagline_box_counter,
		) );

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

		$attr['style'] = '';

		if ( self::$args['margin_top'] || '0' === self::$args['margin_top'] ) {
			if ( class_Exists( 'Avada_Sanitize' ) ) {
				$attr['style'] .= 'margin-top:' . Avada_Sanitize::get_value_with_unit( self::$args['margin_top'] ) . ';';
			} else {
				$attr['style'] .= 'margin-top:' . self::$args['margin_top'] . ';';
			}
		}

		if ( self::$args['margin_bottom'] || '0' === self::$args['margin_bottom'] ) {
			if ( class_Exists( 'Avada_Sanitize' ) ) {
				$attr['style'] .= 'margin-bottom:' . Avada_Sanitize::get_value_with_unit( self::$args['margin_bottom'] ) . ';';
			} else {
				$attr['style'] .= 'margin-bottom:' . self::$args['margin_bottom'] . ';';
			}
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
	 * Builds the reading-box attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function reading_box_attr() {

		$attr = array(
			'class' => 'reading-box',
		);

		if ( 'right' === self::$args['content_alignment'] ) {
			$attr['class'] .= ' reading-box-right';
		} elseif ( 'center' === self::$args['content_alignment'] ) {
			$attr['class'] .= ' reading-box-center';
		}

		if ( 'yes' === self::$args['shadow'] ) {
			$attr['class'] .= ' element-bottomshadow';
		}

		$attr['style']  = 'background-color:' . self::$args['backgroundcolor'] . ';';
		$attr['style'] .= 'border-width:' . self::$args['border'] . ';';
		$attr['style'] .= 'border-color:' . self::$args['bordercolor'] . ';';
		if ( 'none' !== self::$args['highlightposition'] ) {
			if ( str_replace( 'px', '', self::$args['border'] ) > 3  ) {
				$attr['style'] .= 'border-' . self::$args['highlightposition'] . '-width:' . self::$args['border'] . ';';
			} else {
				$attr['style'] .= 'border-' . self::$args['highlightposition'] . '-width:3px;';
			}
			$attr['style'] .= 'border-' . self::$args['highlightposition'] . '-color:' . FusionBuilder::get_theme_option( 'primary_color' ) . ';';
		}
		$attr['style'] .= 'border-style:solid;';

		return $attr;
	}

	/**
	 * Builds the button attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function button_attr() {

		$attr = array(
			'class' => 'button fusion-button button-' . self::$args['buttoncolor'] . ' button-' . self::$args['button_shape'] . ' fusion-button-' . self::$args['button_size'] . ' button-' . self::$args['button_size'] . ' button-' . self::$args['button_type'] . self::$args['button_class'],
		);
		$attr['class'] = strtolower( $attr['class'] );

		if ( 'right' === self::$args['content_alignment'] ) {
			$attr['class'] .= ' continue-left';
		} elseif ( 'center' === self::$args['content_alignment'] ) {
			$attr['class'] .= ' continue-center';
		} else {
			$attr['class'] .= ' continue-right';
		}

		if ( 'flat' === self::$args['button_type'] ) {
			$attr['style'] = '-webkit-box-shadow:none;-moz-box-shadow:none;box-shadow:none;';
		}

		$attr['href'] = self::$args['link'];
		$attr['target'] = self::$args['linktarget'];

		if ( '_blank' == $attr['target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		if ( self::$args['modal'] ) {
			$attr['data-toggle'] = 'modal';
			$attr['data-target'] = '.' . self::$args['modal'];
		}

		return $attr;

	}
}
new FusionSC_Tagline();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_tagline_box() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Tagline Box', 'fusion-builder' ),
		'shortcode'       => 'fusion_tagline_box',
		'icon'            => 'fusiona-list-alt',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-tagline-preview.php',
		'preview_id'      => 'fusion-builder-block-module-tagline-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color. ', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Shadow', 'fusion-builder' ),
				'description' => esc_attr__( 'Show the shadow below the box.', 'fusion-builder' ),
				'param_name'  => 'shadow',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Shadow Opacity', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the opacity of the shadow.', 'fusion-builder' ),
				'param_name'  => 'shadowopacity',
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
				'default'     => '0.7',
				'dependency'  => array(
					array(
						'element'  => 'shadow',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Border Size', 'fusion-builder' ),
				'param_name'  => 'border',
				'description' => esc_attr__( 'In pixels.', 'fusion-builder' ),
				'min'         => '0',
				'max'         => '20',
				'value'       => '1',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'border',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Highlight Border Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the highlight. This border highlight is from theme options primary color and does not take the color from border color above.', 'fusion-builder' ),
				'param_name'  => 'highlightposition',
				'value'       => array(
					esc_attr__( 'Top', 'fusion-builder' )    => 'top',
					esc_attr__( 'Bottom', 'fusion-builder' ) => 'bottom',
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
					esc_attr__( 'None', 'fusion-builder' )   => 'none',
				),
				'default'     => 'left',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Content Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how the content should be displayed.', 'fusion-builder' ),
				'param_name'  => 'content_alignment',
				'value'       => array(
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Center', 'fusion-builder' ) => 'center',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
				),
				'default'     => 'left',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Link', 'fusion-builder' ),
				'description' => esc_attr__( 'The url the button will link to.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Button Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the text that will display in the button.', 'fusion-builder' ),
				'param_name'  => 'button',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window <br />_blank = open in new window.', 'fusion-builder' ),
				'param_name'  => 'linktarget',
				'value'       => array(
					esc_attr__( '_self', 'fusion-builder' )  => '_self',
					esc_attr__( '_blank', 'fusion-builder' ) => '_blank',
				),
				'default'     => '_self',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Modal Window Anchor', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the class name of the modal window you want to open on button click.', 'fusion-builder' ),
				'param_name'  => 'modal',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Size', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's size. Choose default for theme option selection." ),
				'param_name'  => 'button_size',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Small', 'fusion-builder' )   => 'small',
					esc_attr__( 'Medium', 'fusion-builder' )  => 'medium',
					esc_attr__( 'Large', 'fusion-builder' )   => 'large',
					esc_attr__( 'XLarge', 'fusion-builder' )  => 'xlarge',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Type', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's type. Choose default for theme option selection." ),
				'param_name'  => 'button_type',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Flat', 'fusion-builder' )    => 'flat',
					esc_attr__( '3D', 'fusion-builder' )      => '3d',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Button Shape', 'fusion-builder' ),
				'description' => esc_attr__( "Select the button's shape. Choose default for theme option selection." ),
				'param_name'  => 'button_shape',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Square', 'fusion-builder' )  => 'square',
					esc_attr__( 'Pill', 'fusion-builder' )    => 'pill',
					esc_attr__( 'Round', 'fusion-builder' )   => 'round',
				),
				'default'     => '',
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Button Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the button color. Default uses theme option selection.', 'fusion-builder' ),
				'param_name'  => 'buttoncolor',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )    => 'default',
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
				'dependency'  => array(
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Tagline Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the title text.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Tagline Description', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the description text.', 'fusion-builder' ),
				'param_name'  => 'description',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Additional Content', 'fusion-builder' ),
				'description' => esc_attr__( 'This is additional content you can add to the tagline box. This will show below the title and description if one is used.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'description'      => esc_attr__( 'Spacing above and below the tagline. In px, em or %, e.g. 10px.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'margin_top'    => '',
					'margin_bottom' => '',
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
				'type'        => 'select',
				'heading'     => __( 'Speed of Animation', 'fusion-builder' ),
				'description' => __( 'Type in speed of animation in seconds (0.1 - 1).', 'fusion-builder' ),
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
				'default'     => '0.3',
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
add_action( 'fusion_builder_before_init', 'fusion_element_tagline_box' );
