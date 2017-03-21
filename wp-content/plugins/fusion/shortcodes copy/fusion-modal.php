<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Modal {

	/**
	 * The modals counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $modal_counter = 1;

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

		add_filter( 'fusion_attr_modal-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_modal-shortcode-dialog', array( $this, 'dialog_attr' ) );
		add_filter( 'fusion_attr_modal-shortcode-content', array( $this, 'content_attr' ) );
		add_filter( 'fusion_attr_modal-shortcode-heading', array( $this, 'heading_attr' ) );
		add_filter( 'fusion_attr_modal-shortcode-button', array( $this, 'button_attr' ) );
		add_filter( 'fusion_attr_modal-shortcode-button-footer', array( $this, 'button_footer_attr' ) );

		add_shortcode( 'fusion_modal', array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode paramters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'        => '',
				'id'           => '',
				'background'   => FusionBuilder::get_theme_option( 'modal_bg_color' ),
				'border_color' => FusionBuilder::get_theme_option( 'modal_border_color' ),
				'name'         => '',
				'size'         => 'small',
				'title'        => '',
				'show_footer'  => 'yes',
			), $args
		);

		extract( $defaults );

		self::$args = $defaults;

		$style = '';
		if ( $border_color ) {
			$style = '<style type="text/css">.modal-' . $this->modal_counter . ' .modal-header, .modal-' . $this->modal_counter . ' .modal-footer{border-color:' . $border_color . ';}</style>';
		}

		$html  = '<div ' . FusionBuilder::attributes( 'modal-shortcode' ) . '>';
		$html .= $style;
		$html .= '<div ' . FusionBuilder::attributes( 'modal-shortcode-dialog' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'modal-shortcode-content' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'modal-header' ) . '>';
		$html .= '<button ' . FusionBuilder::attributes( 'modal-shortcode-button' ) . '>&times;</button>';
		$html .= '<h3 ' . FusionBuilder::attributes( 'modal-shortcode-heading' ) . '>' . $title . '</h3>';
		$html .= '</div>';
		$html .= '<div ' . FusionBuilder::attributes( 'modal-body' ) . '>' . do_shortcode( $content ) . '</div>';

		if ( 'yes' == $show_footer ) {
			$html .= '<div ' . FusionBuilder::attributes( 'modal-footer' ) . '>';
			$html .= '<a ' . FusionBuilder::attributes( 'modal-shortcode-button-footer' ) . '>' . esc_attr__( 'Close', 'fusion-builder' ) . '</a>';
			$html .= '</div>';
		}

		$html .= '</div></div></div>';

		$this->modal_counter++;

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
			'class'           => 'fusion-modal modal fade modal-' . $this->modal_counter,
			'tabindex'        => '-1',
			'role'            => 'dialog',
			'aria-labelledby' => 'modal-heading-' . $this->modal_counter,
			'aria-hidden'     => 'true',
		);

		if ( self::$args['name'] ) {
			$attr['class'] .= ' ' . self::$args['name'];
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
	 * Builds the dialog attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function dialog_attr() {

		$attr = array(
			'class' => 'modal-dialog',
		);

		$attr['class'] .= ( 'small' == self::$args['size'] ) ? ' modal-sm' : ' modal-lg';

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
			'class' => 'modal-content fusion-modal-content',
		);

		if ( self::$args['background'] ) {
			$attr['style'] = 'background-color:' . self::$args['background'];
		}

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
		return array(
			'class'        => 'close',
			'type'         => 'button',
			'data-dismiss' => 'modal',
			'aria-hidden'  => 'true',
		);
	}

	/**
	 * Builds the heading attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function heading_attr() {
		return array(
			'class'        => 'modal-title',
			'id'           => 'modal-heading-' . $this->modal_counter,
			'data-dismiss' => 'modal',
			'aria-hidden'  => 'true',
		);
	}

	/**
	 * Builds the button attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function button_footer_attr() {
		return array(
			'class'        => 'fusion-button button-default button-medium button default medium',
			'data-dismiss' => 'modal',
		);
	}
}

class FusionSC_ModalTextLink {

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
	 * Initiate the shortcode
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_modal-text-link-shortcode', array( $this, 'attr' ) );
		add_shortcode( 'fusion_modal_text_link', array( $this, 'render' ) );

	}

	/**
	 * Render the shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode paramters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class' => '',
				'id'    => '',
				'name'  => '',
			), $args
		);

		extract( $defaults );

		self::$args = $defaults;

		$html = '<a ' . FusionBuilder::attributes( 'modal-text-link-shortcode' ) . '>' . do_shortcode( $content ) . '</a>';

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
			'class' => 'fusion-modal-text-link',
		);

		if ( self::$args['name'] ) {
			$attr['data-toggle'] = 'modal';
			$attr['data-target'] = '.fusion-modal.' . self::$args['name'];
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		$attr['href'] = '#';

		return $attr;

	}
}

new FusionSC_Modal();
new FusionSC_ModalTextLink();


/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_modal() {
	fusion_builder_map( array(
		'name'            => esc_attr__( 'Modal', 'fusion-builder' ),
		'shortcode'       => 'fusion_modal',
		'icon'            => 'fusiona-external-link',
		'preview'         => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-modal-preview.php',
		'preview_id'      => 'fusion-builder-block-module-modal-preview-template',
		'allow_generator' => true,
		'params'          => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name Of Modal', 'fusion-builder' ),
				'description' => esc_attr__( 'Needs to be a unique identifier (lowercase), used for button or modal_text_link element to open the modal. ex: mymodal.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Modal Heading', 'fusion-builder' ),
				'description' => esc_attr__( 'Heading text for the modal.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Size Of Modal', 'fusion-builder' ),
				'description' => esc_attr__( 'Select the modal window size.', 'fusion-builder' ),
				'param_name'  => 'size',
				'value'       => array(
					esc_attr__( 'Small', 'fusion-builder' ) => 'small',
					esc_attr__( 'Large', 'fusion-builder' ) => 'large',
				),
				'default'     => 'small',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the modal background color. ', 'fusion-builder' ),
				'param_name'  => 'background',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the modal border color. ', 'fusion-builder' ),
				'param_name'  => 'border_color',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Footer', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to show the modal footer with close button.', 'fusion-builder' ),
				'param_name'  => 'show_footer',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Contents of Modal', 'fusion-builder' ),
				'description' => esc_attr__( 'Add your content to be displayed in modal.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
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
add_action( 'fusion_builder_before_init', 'fusion_element_modal' );


/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_modal_link() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Modal Text Link', 'fusion-builder' ),
		'shortcode'         => 'fusion_modal_text_link',
		'generator_only'    => true,
		'icon'              => 'fusiona-external-link',
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name Of Modal', 'fusion-builder' ),
				'description' => esc_attr__( 'Unique identifier of the modal to open on click.', 'fusion-builder' ),
				'param_name'  => 'name',
				'value'       => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Text or HTML code', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert text or HTML code here (e.g: HTML for image). This content will be used to trigger the modal popup.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_modal_link' );
