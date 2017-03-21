<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Title {

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

		add_filter( 'fusion_attr_title-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_title-shortcode-heading', array( $this, 'heading_attr' ) );
		add_filter( 'fusion_attr_title-shortcode-sep', array( $this, 'sep_attr' ) );

		add_shortcode( 'fusion_title', array( $this, 'render' ) );

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
				'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
				'class'          => '',
				'id'             => '',
				'content_align'  => 'left',
				'margin_top'     => FusionBuilder::get_theme_option( 'title_margin', 'top' ),
				'margin_bottom'  => FusionBuilder::get_theme_option( 'title_margin', 'bottom' ),
				'sep_color'      => '',
				'size'           => 1,
				'style_tag'      => '',
				'style_type'     => FusionBuilder::get_theme_option( 'title_style_type' ),
			), $args
		);

		$defaults['margin_top']    = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_top'], 'px' );
		$defaults['margin_bottom'] = FusionBuilder::validate_shortcode_attr_value( $defaults['margin_bottom'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		if ( 1 === count( explode( ' ', self::$args['style_type'] ) ) ) {
			$style_type .= ' solid';
		}

		if ( ! self::$args['style_type'] || 'default' == self::$args['style_type'] ) {
			self::$args['style_type'] = $style_type = FusionBuilder::get_theme_option( 'title_style_type' );
		}

		// Make sure the title text is not wrapped with an unattributed p tag.
		$content = preg_replace( '!^<p>(.*?)</p>$!i', '$1', trim( $content ) );

		if ( false !== strpos( $style_type, 'underline' ) || false !== strpos( $style_type, 'none' ) ) {

			$html = sprintf( '<div %s><h%s %s>%s</h%s></div>', FusionBuilder::attributes( 'title-shortcode' ), $size,
			FusionBuilder::attributes( 'title-shortcode-heading' ), do_shortcode( $content ), $size );

		} else {

			if ( 'right' == self::$args['content_align'] ) {

				$html = sprintf(
					'<div %s><div %s><div %s></div></div><h%s %s>%s</h%s></div>',
					FusionBuilder::attributes( 'title-shortcode' ),
					FusionBuilder::attributes( 'title-sep-container' ),
					FusionBuilder::attributes( 'title-shortcode-sep' ),
					$size,
					FusionBuilder::attributes( 'title-shortcode-heading' ),
					do_shortcode( $content ),
					$size
				);
			} elseif ( 'center' == self::$args['content_align'] ) {

				$html = sprintf(
					'<div %s><div %s><div %s></div></div><h%s %s>%s</h%s><div %s><div %s></div></div></div>',
					FusionBuilder::attributes( 'title-shortcode' ),
					FusionBuilder::attributes( 'title-sep-container title-sep-container-left' ),
					FusionBuilder::attributes( 'title-shortcode-sep' ), $size,
					FusionBuilder::attributes( 'title-shortcode-heading' ),
					do_shortcode( $content ),
					$size,
					FusionBuilder::attributes( 'title-sep-container title-sep-container-right' ),
					FusionBuilder::attributes( 'title-shortcode-sep' )
				);

			} else {

				$html = sprintf(
					'<div %s><h%s %s>%s</h%s><div %s><div %s></div></div></div>',
					FusionBuilder::attributes( 'title-shortcode' ),
					$size,
					FusionBuilder::attributes( 'title-shortcode-heading' ),
					do_shortcode( $content ),
					$size,
					FusionBuilder::attributes( 'title-sep-container' ),
					FusionBuilder::attributes( 'title-shortcode-sep' )
				);
			}
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
			'class' => 'fusion-title title',
			'style' => '',
		) );

		if ( strpos( self::$args['style_type'], 'underline' ) !== false ) {
			$styles = explode( ' ', self::$args['style_type'] );

			foreach ( $styles as $style ) {
				$attr['class'] .= ' sep-' . $style;
			}

			if ( self::$args['sep_color'] ) {
				$attr['style'] = sprintf( 'border-bottom-color:%s;', self::$args['sep_color'] );
			}
		} elseif ( false !== strpos( self::$args['style_type'], 'none' ) ) {
			$attr['class'] .= ' fusion-sep-none';
		}

		if ( 'center' == self::$args['content_align'] ) {
			$attr['class'] .= ' fusion-title-center';
		}

		$title_size = 'two';
		if ( '1' == self::$args['size'] ) {
			$title_size = 'one';
		} else if ( '2' == self::$args['size'] ) {
			$title_size = 'two';
		} else if ( '3' == self::$args['size'] ) {
			$title_size = 'three';
		} else if ( '4' == self::$args['size'] ) {
			$title_size = 'four';
		} else if ( '5' == self::$args['size'] ) {
			$title_size = 'five';
		} else if ( '6' == self::$args['size'] ) {
			$title_size = 'six';
		}

		$attr['class'] .= ' fusion-title-size-' . $title_size;

		if ( self::$args['margin_top'] ) {
			$attr['style'] .= sprintf( 'margin-top:%s;', self::$args['margin_top'] );
		}

		if ( self::$args['margin_bottom'] ) {
			$attr['style'] .= sprintf( 'margin-bottom:%s;', self::$args['margin_bottom'] );
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
	 * Builds the heading attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function heading_attr() {

		$attr = array(
			'class' => 'title-heading-' . self::$args['content_align'],
		);

		if ( self::$args['style_tag'] ) {
			$attr['style'] = self::$args['style_tag'];
		}

		return $attr;

	}

	/**
	 * Builds the separator attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function sep_attr() {

		$attr = array(
			'class' => 'title-sep',
		);

		$styles = explode( ' ', self::$args['style_type'] );

		foreach ( $styles as $style ) {
			$attr['class'] .= ' sep-' . $style;
		}

		if ( self::$args['sep_color'] ) {
			$attr['style'] = sprintf( 'border-color:%s;', self::$args['sep_color'] );
		}

		return $attr;

	}
}
new FusionSC_Title();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_title() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Title', 'fusion-builder' ),
		'shortcode'       => 'fusion_title',
		'icon'            => 'fusiona-H',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-title-preview.php',
		'preview_id'      => 'fusion-builder-block-module-title-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the title text.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the title size, H1-H6.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => array(
					'H1' => '1',
					'H2' => '2',
					'H3' => '3',
					'H4' => '4',
					'H5' => '5',
					'H6' => '6',
				),
				'default' => '1',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Title Alignment', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to align the heading left or right.', 'fusion-builder' ),
				'param_name'  => 'content_align',
				'value'       => array(
					esc_attr__( 'Left', 'fusion-builder' )   => 'left',
					esc_attr__( 'Center', 'fusion-builder' ) => 'center',
					esc_attr__( 'Right', 'fusion-builder' )  => 'right',
				),
				'default' => 'left',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Separator', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the kind of the title separator you want to use.' ),
				'param_name'  => 'style_type',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )          => 'default',
					esc_attr__( 'Single Solid', 'fusion-builder' )     => 'single solid',
					esc_attr__( 'Single Dashed', 'fusion-builder' )    => 'single dashed',
					esc_attr__( 'Single Dotted', 'fusion-builder' )    => 'single dotted',
					esc_attr__( 'Double Solid', 'fusion-builder' )     => 'double solid',
					esc_attr__( 'Double Dashed', 'fusion-builder' )    => 'double dashed',
					esc_attr__( 'Double Dotted', 'fusion-builder' )    => 'double dotted',
					esc_attr__( 'Underline Solid', 'fusion-builder' )  => 'underline solid',
					esc_attr__( 'Underline Dashed', 'fusion-builder' ) => 'underline dashed',
					esc_attr__( 'Underline Dotted', 'fusion-builder' ) => 'underline dotted',
					esc_attr__( 'None', 'fusion-builder' )             => 'none',
				),
				'default' => 'default',
				'group'   => esc_attr__( 'Design Options', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Separator Color', 'fusion-builder' ),
				'param_name'  => 'sep_color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the separator color. ', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design Options', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'style_type',
						'value'    => 'none',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Margin', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'margin_top'    => '',
					'margin_bottom' => '',

				),
				'description'      => esc_attr__( 'Spacing above and below the title. In px, em or %, e.g. 10px.', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_title' );
