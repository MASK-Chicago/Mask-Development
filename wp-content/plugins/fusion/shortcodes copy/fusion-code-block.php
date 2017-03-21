<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_FusionCode {

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
		add_shortcode( 'fusion_code', array( $this, 'render' ) );
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
		if ( base64_encode( base64_decode( $content ) ) === $content ) {
			$content = base64_decode( $content );
		}
		return do_shortcode( html_entity_decode( $content, ENT_QUOTES ) );
	}
}

new FusionSC_FusionCode();


/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_code_block() {
	fusion_builder_map( array(
		'name'        => esc_attr__( 'Code Block', 'fusion-builder' ),
		'shortcode'   => 'fusion_code',
		'icon'        => 'fusiona-code',
		'escape_html' => true,
		'params'      => array(
			array(
				'type'        => 'code',
				'heading'     => esc_attr__( 'Code', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this codeblock.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '',
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_code_block' );
