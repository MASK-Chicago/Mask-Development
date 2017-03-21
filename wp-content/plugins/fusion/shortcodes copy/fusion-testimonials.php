<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Testimonials {

	/**
	 * The testimonials counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $testimonials_counter = 1;

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

		add_filter( 'fusion_attr_testimonials-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-testimonials', array( $this, 'testimonials_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-quote', array( $this, 'quote_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-blockquote', array( $this, 'blockquote_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-review', array( $this, 'review_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-thumbnail', array( $this, 'thumbnail_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-image', array( $this, 'image_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-author', array( $this, 'author_attr' ) );
		add_filter( 'fusion_attr_testimonials-shortcode-pagination', array( $this, 'pagination_attr' ) );

		add_shortcode( 'fusion_testimonials', array( $this, 'render_parent' ) );
		add_shortcode( 'fusion_testimonial', array( $this, 'render_child' ) );

	}

	/**
	 * Render the parent shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args     Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_parent( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile'  => fusion_builder_default_visibility( 'string' ),
				'class'           => '',
				'id'              => '',
				'backgroundcolor' => strtolower( FusionBuilder::get_theme_option( 'testimonial_bg_color' ) ),
				'design'          => 'classic',
				'random'          => FusionBuilder::get_theme_option( 'testimonials_random' ),
				'textcolor'       => strtolower( FusionBuilder::get_theme_option( 'testimonial_text_color' ) ),
			), $args
		);

		if ( 'yes' === $defaults['random'] || '1' === $defaults['random'] ) {
			$defaults['random'] = 1;
		} else {
			$defaults['random'] = 0;
		}

		extract( $defaults );

		self::$parent_args = $defaults;

		$styles  = '<style type="text/css" scoped="scoped">';
		$styles .= '#fusion-testimonials-' . $this->testimonials_counter . ' a{border-color:' . $textcolor . ';}';
		$styles .= '#fusion-testimonials-' . $this->testimonials_counter . ' a:hover, #fusion-testimonials-' . $this->testimonials_counter . ' .activeSlide{background-color: ' . $textcolor . ';}';
		$styles .= '.fusion-testimonials.' . $design . '.fusion-testimonials-' . $this->testimonials_counter . ' .author:after{border-top-color:' . $backgroundcolor . ' !important;}';
		$styles .= '</style>';

		$pagination = '';
		if ( 'clean' === self::$parent_args['design'] ) {
			$pagination  = sprintf( '<div %s></div>', FusionBuilder::attributes( 'testimonials-shortcode-pagination' ) );
		}

		$html = sprintf( '<div %s>%s<div %s>%s</div>%s</div>', FusionBuilder::attributes( 'testimonials-shortcode' ), $styles,
		FusionBuilder::attributes( 'testimonials-shortcode-testimonials' ), do_shortcode( $content ), $pagination );

		$this->testimonials_counter++;

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
			'class' => 'fusion-testimonials ' . self::$parent_args['design'] . ' fusion-testimonials-' . $this->testimonials_counter,
		) );

		$attr['data-random'] = self::$parent_args['random'];

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the testimonials attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function testimonials_attr() {
		return array(
			'class' => 'reviews',
		);
	}

	/**
	 * Render the child shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args   Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string         HTML output.
	 */
	public function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'avatar'              => 'male',
				'company'             => '',
				'image'               => '',
				'image_border_radius' => '',
				'link'                => '',
				'name'                => '',
				'target'              => '_self',
				'gender'              => '',  // Deprecated.
			), $args
		);

		$defaults['image_border_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['image_border_radius'], 'px' );

		if ( 'round' === $defaults['image_border_radius'] ) {
			$defaults['image_border_radius'] = '50%';
		}

		extract( $defaults );

		self::$child_args = $defaults;

		// Check for deprecated.
		if ( $gender ) {
			self::$child_args['avatar'] = $gender;
		}

		if ( 'clean' === self::$parent_args['design'] ) {
			return $this->render_child_clean( $content );
		}
		return $this->render_child_classic( $content );

	}

	/**
	 * Render classic design.
	 *
	 * @access private
	 * @since 1.0
	 * @param string $content The content.
	 * @return string
	 */
	private function render_child_classic( $content ) {
		$inner_content = $thumbnail = $pic = '';

		if ( self::$child_args['name'] ) {

			if ( 'image' === self::$child_args['avatar'] && self::$child_args['image'] ) {

				$image_id = FusionBuilder::get_attachment_id_from_url( self::$child_args['image'] );
				self::$child_args['alt'] = '';
				if ( $image_id ) {
					self::$child_args['alt'] = get_post_field( '_wp_attachment_image_alt', $image_id );
				}

				$pic = sprintf( '<img %s />', FusionBuilder::attributes( 'testimonials-shortcode-image' ) );
			}

			if ( 'image' === self::$child_args['avatar'] && ! self::$child_args['image'] ) {
				self::$child_args['avatar'] = 'none';
			}

			if ( 'none' !== self::$child_args['avatar'] ) {
				$thumbnail = sprintf( '<span %s>%s</span>', FusionBuilder::attributes( 'testimonials-shortcode-thumbnail' ), $pic );
			}

			$inner_content .= sprintf( '<div %s>%s<span %s>', FusionBuilder::attributes( 'testimonials-shortcode-author' ), $thumbnail, FusionBuilder::attributes( 'company-name' ) );

			if ( self::$child_args['name'] ) {
				$inner_content .= sprintf( '<strong>%s</strong>', self::$child_args['name'] );
			}

			if ( self::$child_args['name'] && self::$child_args['company'] ) {
				$inner_content .= ', ';
			}

			if ( self::$child_args['company'] ) {

				if ( ! empty( self::$child_args['link'] ) && self::$child_args['link'] ) {

					$combined_attribs = 'target="' . self::$child_args['target'] . '"';
					if ( '_blank' == self::$child_args['target'] ) {
						$combined_attribs = 'target="' . self::$child_args['target'] . '" rel="noopener noreferrer"';
					}
					$inner_content .= sprintf( '<a href="%s" %s>%s</a>', self::$child_args['link'], $combined_attribs, sprintf( '<span>%s</span>', self::$child_args['company'] ) );

				} else {

					$inner_content .= sprintf( '<span>%s</span>', self::$child_args['company'] );

				}
			}

			$inner_content .= '</span></div>';
		}

		$html = sprintf(
			'<div %s><blockquote><q %s>%s</q></blockquote>%s</div>',
			FusionBuilder::attributes( 'testimonials-shortcode-review' ),
			FusionBuilder::attributes( 'testimonials-shortcode-quote' ),
			do_shortcode( $content ),
			$inner_content
		);

		return $html;

	}

	/**
	 * Render clean design.
	 *
	 * @access private
	 * @since 1.0
	 * @param string $content The content.
	 * @return string
	 */
	private function render_child_clean( $content ) {
		$thumbnail = $pic = $author = '';

		if ( 'image' === self::$child_args['avatar'] && self::$child_args['image'] ) {

			$image_id = FusionBuilder::get_attachment_id_from_url( self::$child_args['image'] );
			self::$child_args['alt'] = '';
			if ( $image_id ) {
				self::$child_args['alt'] = get_post_field( '_wp_attachment_image_alt', $image_id );
			}

			$pic = sprintf( '<img %s />', FusionBuilder::attributes( 'testimonials-shortcode-image' ) );
		}

		if ( 'image' === self::$child_args['avatar'] && ! self::$child_args['image'] ) {
			self::$child_args['avatar'] = 'none';
		}

		if ( 'none' !== self::$child_args['avatar'] ) {
			$thumbnail = sprintf( '<div %s>%s</div>', FusionBuilder::attributes( 'testimonials-shortcode-thumbnail' ), $pic );
		}

		$author .= sprintf( '<div %s><span %s>', FusionBuilder::attributes( 'testimonials-shortcode-author' ), FusionBuilder::attributes( 'company-name' ) );

		if ( self::$child_args['name'] ) {
			$author .= sprintf( '<strong>%s</strong>', self::$child_args['name'] );
		}

		if ( self::$child_args['name'] && self::$child_args['company'] ) {
			$author .= ', ';
		}

		if ( self::$child_args['company'] ) {

			if ( ! empty( self::$child_args['link'] ) && self::$child_args['link'] ) {
				$author .= sprintf( '<a href="%s" target="%s">%s</a>', self::$child_args['link'], self::$child_args['target'], sprintf( '<span>%s</span>', self::$child_args['company'] ) );
			} else {
				$author .= sprintf( '<span>%s</span>', self::$child_args['company'] );
			}
		}

		$author .= '</span></div>';

		$html = sprintf(
			'<div %s>%s<blockquote %s><q %s>%s</q></blockquote>%s</div>',
			FusionBuilder::attributes( 'testimonials-shortcode-review' ),
			$thumbnail,
			FusionBuilder::attributes( 'testimonials-shortcode-blockquote' ),
			FusionBuilder::attributes( 'testimonials-shortcode-quote' ),
			do_shortcode( $content ),
			$author
		);

		return $html;
	}

	/**
	 * Builds the blockquote attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function blockquote_attr() {

		$attr = array(
			'style' => '',
		);
		$bgcolor = ( class_exists( 'Avada_color' ) ) ? Avada_color::new_color( self::$parent_args['backgroundcolor'] ) : '';
		if ( 'clean' === self::$parent_args['design'] && ( 'transparent' == self::$parent_args['backgroundcolor'] || '0' == $bgcolor->alpha ) ) {
			$attr['style'] .= 'margin: -25px;';
		}

		$attr['style'] .= 'background-color:' . self::$parent_args['backgroundcolor'] . ';';

		return $attr;

	}

	/**
	 * Builds the quotes attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function quote_attr() {
		return array(
			'style' => 'background-color:' . self::$parent_args['backgroundcolor'] . ';color:' . self::$parent_args['textcolor'] . ';',
		);
	}

	/**
	 * Builds the review attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function review_attr() {

		$attr = array(
			'class' => 'review ',
		);

		if ( 'none' === self::$child_args['avatar'] ) {
			$attr['class'] .= 'no-avatar';
		} else if ( 'image' === self::$child_args['avatar'] ) {
			$attr['class'] .= 'avatar-image';
		} else {
			$attr['class'] .= self::$child_args['avatar'];
		}

		return $attr;

	}

	/**
	 * Builds the thumbnail attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function thumbnail_attr() {

		$attr = array(
			'class' => 'testimonial-thumbnail',
		);

		if ( 'image' !== self::$child_args['avatar'] ) {
			$attr['class'] .= ' doe';
			$attr['style'] = sprintf( 'color:%s;', self::$parent_args['textcolor'] );
		}

		return $attr;

	}

	/**
	 * Builds the image attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function image_attr() {

		$attr = array(
			'class' => 'testimonial-image',
			'src'   => self::$child_args['image'],
			'alt'   => self::$child_args['alt'],
		);

		if ( 'image' === self::$child_args['avatar'] ) {
			$attr['style'] = sprintf( '-webkit-border-radius: %s;-moz-border-radius: %s;border-radius: %s;',
			self::$child_args['image_border_radius'], self::$child_args['image_border_radius'],  self::$child_args['image_border_radius'] );
		}

		return $attr;

	}

	/**
	 * Builds the author attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function author_attr() {
		return array(
			'class' => 'author',
			'style' => 'color:' . self::$parent_args['textcolor'] . ';',
		);
	}

	/**
	 * Builds the pagination attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function pagination_attr() {
		return array(
			'class' => 'testimonial-pagination',
			'id'    => 'fusion-testimonials-' . $this->testimonials_counter,
		);
	}
}
new FusionSC_Testimonials();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_testimonials() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Testimonials', 'fusion-builder' ),
		'shortcode'     => 'fusion_testimonials',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_testimonial',
		'icon'          => 'fusiona-bubbles',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-testimonials-preview.php',
		'preview_id'    => 'fusion-builder-block-module-testimonials-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_testimonial name="Your Content Goes Here" avatar="male" image="" image_border_radius="" company="Your Content Goes Here" link="" target="_self"]Your Content Goes Here[/fusion_testimonial]',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Design', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a design for the shortcode.' ),
				'param_name'  => 'design',
				'value'       => array(
					esc_attr__( 'Classic', 'fusion-builder' ) => 'classic',
					esc_attr__( 'Clean', 'fusion-builder' )   => 'clean',
				),
				'default'     => 'classic',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color. ', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the text color. ', 'fusion-builder' ),
				'param_name'  => 'textcolor',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Random Order', 'fusion-builder' ),
				'description' => esc_attr__( 'Turn on to display testimonials in a random order.' ),
				'param_name'  => 'random',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
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
add_action( 'fusion_builder_before_init', 'fusion_element_testimonials' );


/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_testimonial() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Testimonial', 'fusion-builder' ),
		'shortcode'         => 'fusion_testimonial',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the name of the person.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Avatar', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose which kind of Avatar to be displayed.', 'fusion-builder' ),
				'param_name'  => 'avatar',
				'value'       => array(
					esc_attr__( 'Male', 'fusion-builder' )   => 'male',
					esc_attr__( 'Female', 'fusion-builder' ) => 'female',
					esc_attr__( 'Image', 'fusion-builder' )  => 'image',
					esc_attr__( 'None', 'fusion-builder' )   => 'none',
				),
				'default'     => 'male',
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Custom Avatar', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload a custom avatar image.', 'fusion-builder' ),
				'param_name'  => 'image',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'avatar',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the radius of the testimonial image. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'image_border_radius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'avatar',
						'value'    => 'image',
						'operator' => '==',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Company', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the name of the company.', 'fusion-builder' ),
				'param_name'  => 'company',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the url the company name will link to.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => __( '_self = open in same window <br />_blank = open in new window.', 'fusion-builder' ),
				'param_name'  => 'target',
				'value'       => array(
					'_self'   => '_self',
					'_blank'  => '_blank',
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
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Testimonial Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Add the testimonial content.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_testimonial' );
