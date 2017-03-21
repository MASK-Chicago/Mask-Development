<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Slider {

	/**
	 * Sliders counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $slider_counter = 1;

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

		add_filter( 'fusion_attr_slider-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_slider-shortcode-slide-link', array( $this, 'slide_link_attr' ) );
		add_filter( 'fusion_attr_slider-shortcode-slide-li', array( $this, 'slide_li_attr' ) );
		add_filter( 'fusion_attr_slider-shortcode-slide-img', array( $this, 'slide_img_attr' ) );
		add_filter( 'fusion_attr_slider-shortcode-slide-img-wrapper', array( $this, 'slide_img_wrapper_attr' ) );

		add_shortcode( 'fusion_slider', array( $this, 'render_parent' ) );
		add_shortcode( 'fusion_slide', array( $this, 'render_child' ) );

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
	public function render_parent( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
				'class'          => '',
				'id'             => '',
				'height'         => '100%',
				'width'          => '100%',
				'hover_type'     => 'none',
			), $args
		);

		$defaults['width']  = FusionBuilder::validate_shortcode_attr_value( $defaults['width'], 'px' );
		$defaults['height'] = FusionBuilder::validate_shortcode_attr_value( $defaults['height'], 'px' );

		extract( $defaults );

		self::$parent_args = $defaults;

		$html = '<div ' . FusionBuilder::attributes( 'slider-shortcode' ) . '><ul ' . FusionBuilder::attributes( 'slides' ) . '>' . do_shortcode( $content ) . '</ul></div>';

		$this->slider_counter++;

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

		$attr = fusion_builder_visibility_atts( self::$parent_args['hide_on_mobile'], array(
			'class' => 'fusion-slider-sc flexslider', // FIXXXME had clearfix class; group mixin working?
		) );

		if ( self::$parent_args['hover_type'] ) {
			$attr['class'] .= ' flexslider-hover-type-' . self::$parent_args['hover_type'];
		}

		$attr['style'] = 'max-width:' . self::$parent_args['width'] . ';height:' . self::$parent_args['height'] . ';';

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
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'lightbox'   => 'no',
				'link'       => null,
				'linktarget' => '_self',
				'type'       => 'image',
			), $args
		);

		extract( $defaults );

		self::$child_args = $defaults;

		self::$child_args['alt']   = '';
		self::$child_args['title'] = '';
		self::$child_args['src']   = $src = str_replace( '&#215;', 'x', $content );

		if ( 'image' === $type ) {

			$image_id = FusionBuilder::get_attachment_id_from_url( $src );
			if ( ! empty( $link ) && $link ) {
				$image_id = FusionBuilder::get_attachment_id_from_url( $link );
			}

			if ( $image_id ) {
				self::$child_args['alt']   = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				self::$child_args['title'] = get_post_field( 'post_excerpt', $image_id );
			}
		}

		if ( $link && ! empty( $link ) && 'image' === $type ) {
			self::$child_args['link'] = $link;
		}

		$html = '<li ' . FusionBuilder::attributes( 'slider-shortcode-slide-li' ) . '>';

		if ( $link && ! empty( $link ) ) {
			$html .= '<a ' . FusionBuilder::attributes( 'slider-shortcode-slide-link' ) . '>';
		}

		if ( ! empty( $type ) && 'video' === $type ) {
			$html .= '<div ' . FusionBuilder::attributes( 'full-video' ) . '>' . do_shortcode( $content ) . '</div>';
		} else {
			$html .= '<span ' . FusionBuilder::attributes( 'slider-shortcode-slide-img-wrapper' ) . '><img ' . FusionBuilder::attributes( 'slider-shortcode-slide-img' ) . ' /></span>';
		}

		if ( $link && ! empty( $link ) ) {
			$html .= '</a>';
		}

		$html .= '</li>';

		return $html;

	}

	/**
	 * Builds the slider-link attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function slide_link_attr() {

		$attr = array();

		if ( 'yes' === self::$child_args['lightbox'] ) {
			$attr['class'] = 'lightbox-enabled';
			$attr['data-rel'] = 'prettyPhoto[gallery_slider_' . $this->slider_counter . ']';
		}
		$image_id = FusionBuilder::get_attachment_id_from_url( self::$child_args['link'] );
		if ( isset( $image_id ) && $image_id ) {
			$attr['data-caption'] = get_post_field( 'post_excerpt', $image_id );
			$attr['data-title'] = get_post_field( 'post_title', $image_id );
		}
		$attr['href'] = self::$child_args['link'];
		$attr['target'] = self::$child_args['linktarget'];

		if ( '_blank' == $attr['target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		$attr['title'] = self::$child_args['title'];

		return $attr;

	}

	/**
	 * Builds the slider-list-item attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function slide_li_attr() {
		return array(
			'class' => ( 'video' === self::$child_args['type'] ) ? 'video' : 'image',
		);
	}

	/**
	 * Builds the slider image attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function slide_img_attr() {
		return array(
			'src' => self::$child_args['src'],
			'alt' => self::$child_args['alt'],
		);
	}

	/**
	 * Builds the image-wrapper attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function slide_img_wrapper_attr() {
		if ( self::$parent_args['hover_type'] ) {
			return array(
				'class' => 'hover-type-' . self::$parent_args['hover_type'],
			);
		}
		return array();
	}
}
new FusionSC_Slider();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_slider() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Slider', 'fusion-builder' ),
		'shortcode'     => 'fusion_slider',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_slide',
		'icon'          => 'fusiona-uniF61C',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-slider-preview.php',
		'preview_id'    => 'fusion-builder-block-module-slider-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_slide type="image" link="" linktarget="_self" lightbox="no" /]',
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Hover Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the hover effect type.', 'fusion-builder' ),
				'param_name'  => 'hover_type',
				'value'       => array(
					esc_attr__( 'None', 'fusion-builder' )     => 'none',
					esc_attr__( 'Zoom In', 'fusion-builder' )  => 'zoomin',
					esc_attr__( 'Zoom Out', 'fusion-builder' ) => 'zoomout',
					esc_attr__( 'Lift Up', 'fusion-builder' )  => 'liftup',
				),
				'default'     => 'none',
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Image Size Dimensions', 'fusion-builder' ),
				'description'      => esc_attr__( 'Dimensions in percentage (%) or pixels (px).', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'       	   => array(
					'width'  => '100%',
					'height' => '100%',
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
add_action( 'fusion_builder_before_init', 'fusion_element_slider' );

/**
 * Map shortcode to Fusion Builder.
 */
function fusion_element_slide() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Slide', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this textblock.', 'fusion-builder' ),
		'shortcode'         => 'fusion_slide',
		'option_dependency' => 'type',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Content', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
				'hidden'      => true,
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Slide Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a video or image slide.', 'fusion-builder' ),
				'param_name'  => 'type',
				'value'       => array(
					esc_attr__( 'Image', 'fusion-builder' ) => 'image',
					esc_attr__( 'Video', 'fusion-builder' ) => 'video',
				),
				'default'     => 'image',
			),
			array(
				'type'             => 'upload',
				'heading'          => esc_attr__( 'Image', 'fusion-builder' ),
				'description'      => esc_attr__( 'Upload an image to display.', 'fusion-builder' ),
				'param_name'       => 'image',
				'remove_from_atts' => true,
				'value'            => '',
				'dependency'       => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'             => 'textarea',
				'heading'          => esc_attr__( 'Video Element or Video Embed Code', 'fusion-builder' ),
				'description'      => __( 'Click the Youtube or Vimeo Element button below then enter your unique video ID, or copy and paste your video embed code. <p><a href="#" class="insert-slider-video" data-type="fusion_youtube">Add YouTube Video</a></p><p><a href="#" class="insert-slider-video" data-type="fusion_vimeo">Add Vimeo Video</a></p>.', 'fusion-builder' ),
				'param_name'       => 'video',
				'remove_from_atts' => true,
				'value'            => '',
				'dependency'       => array(
					array(
						'element'  => 'type',
						'value'    => 'video',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Full Image Link or External Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url of where the image will link to. If lightbox option is enabled, you have to add the full image link to show it in the lightbox.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Lighbox', 'fusion-builder' ),
				'description' => esc_attr__( 'Show image in Lightbox.', 'fusion-builder' ),
				'param_name'  => 'lightbox',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
				'dependency'  => array(
					array(
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
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
						'element'  => 'type',
						'value'    => 'image',
						'operator' => '==',
					),
					array(
						'element'  => 'link',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'lightbox',
						'value'    => 'no',
						'operator' => '==',
					),
				),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_slide' );
