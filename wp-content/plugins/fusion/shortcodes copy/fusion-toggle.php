<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Toggle {

	/**
	 * Counter for accordians.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $accordian_counter = 1;

	/**
	 * Counter for collapsed items.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $collapse_counter = 1;

	/**
	 * The ID of the collapsed item.
	 *
	 * @access private
	 * @since 1.0
	 * @var string
	 */
	private $collapse_id;

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

		add_filter( 'fusion_attr_toggle-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_toggle-shortcode-panelgroup', array( $this, 'panelgroup_attr' ) );
		add_filter( 'fusion_attr_toggle-shortcode-panel', array( $this, 'panel_attr' ) );
		add_filter( 'fusion_attr_toggle-shortcode-fa-icon', array( $this, 'fa_icon_attr' ) );
		add_filter( 'fusion_attr_toggle-shortcode-data-toggle', array( $this, 'data_toggle_attr' ) );
		add_filter( 'fusion_attr_toggle-shortcode-collapse', array( $this, 'collapse_attr' ) );

		add_shortcode( 'fusion_accordion', array( $this, 'render_parent' ) );
		add_shortcode( 'fusion_toggle', array( $this, 'render_child' ) );

	}

	/**
	 * Render the parent shortcode
	 *
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	function render_parent( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile' => fusion_builder_default_visibility( 'string' ),
				'divider_line'   => FusionBuilder::get_theme_option( 'accordion_divider_line' ),
				'class'          => '',
				'id'             => '',
			), $args
		);

		extract( $defaults );

		self::$parent_args = $defaults;

		$html = sprintf(
			'<div %s><div %s>%s</div></div>',
			FusionBuilder::attributes( 'toggle-shortcode' ),
			FusionBuilder::attributes( 'toggle-shortcode-panelgroup' ),
			do_shortcode( $content )
		);

		$this->accordian_counter++;

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
			'class' => 'accordian fusion-accordian',
		) );

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the panel-group attributes.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function panelgroup_attr() {
		return array(
			'class' => 'panel-group',
			'id'    => 'accordion-' . get_the_ID() . '-' . $this->accordian_counter,
		);
	}

	/**
	 * Render the child shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'open'  => 'no',
				'title' => '',
			), $args
		);

		extract( $defaults );

		self::$child_args = $defaults;
		self::$child_args['toggle_class'] = '';

		if ( 'yes' == $open ) {
			self::$child_args['toggle_class'] = 'in';
		}

		$this->collapse_id = substr( md5( sprintf( 'collapse-%s-%s-%s', get_the_ID(), $this->accordian_counter, $this->collapse_counter ) ), 15 );

		$html = sprintf(
			'<div %s><div %s><h4 %s><a %s><div %s><i %s></i></div><div %s>%s</div></a></h4></div><div %s><div %s>%s</div></div></div>',
			FusionBuilder::attributes( 'toggle-shortcode-panel' ),
			FusionBuilder::attributes( 'panel-heading' ),
			FusionBuilder::attributes( 'panel-title toggle' ),
			FusionBuilder::attributes( 'toggle-shortcode-data-toggle' ),
			FusionBuilder::attributes( 'fusion-toggle-icon-wrapper' ),
			FusionBuilder::attributes( 'toggle-shortcode-fa-icon' ),
			FusionBuilder::attributes( 'fusion-toggle-heading' ),
			$title,
			FusionBuilder::attributes( 'toggle-shortcode-collapse' ),
			FusionBuilder::attributes( 'panel-body toggle-content' ),
			do_shortcode( $content )
		);

		$this->collapse_counter++;

		return $html;

	}

	/**
	 * Builds the panel attributes.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function panel_attr() {

		$attr = array(
			'class' => 'fusion-panel panel-default',
		);

		if ( '0' == self::$parent_args['divider_line'] || 'no' == self::$parent_args['divider_line'] ) {
			$attr['class'] .= ' fusion-toggle-no-divider';
		}

		return $attr;

	}

	/**
	 * Builds the font-awesome icon attributes.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function fa_icon_attr() {
		return array(
			'class' => 'fa-fusion-box',
		);
	}

	/**
	 * Builds the data-toggle attributes.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function data_toggle_attr() {

		$attr = array();

		if ( 'yes' == self::$child_args['open'] ) {
			$attr['class'] = 'active';
		}

		$attr['data-toggle'] = 'collapse';
		$attr['data-parent'] = sprintf( '#accordion-%s-%s', get_the_ID(), $this->accordian_counter );
		$attr['data-target'] = '#' . $this->collapse_id;
		$attr['href']        = '#' . $this->collapse_id;

		return $attr;

	}

	/**
	 * Builds the collapse attributes.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function collapse_attr() {
		return array(
			'id'    => $this->collapse_id,
			'class' => 'panel-collapse collapse ' . self::$child_args['toggle_class'],
		);
	}
}
new FusionSC_Toggle();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_accordion() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Toggles', 'fusion-builder' ),
		'shortcode'     => 'fusion_accordion',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_toggle',
		'icon'          => 'fusiona-expand-alt',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-toggles-preview.php',
		'preview_id'    => 'fusion-builder-block-module-toggles-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_toggle title="Your Content Goes Here" open="no" ]Your Content Goes Here[/fusion_toggle]',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Divider Line', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to display a divider line between each item.', 'fusion-builder' ),
				'param_name'  => 'divider_line',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'default' => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_accordion' );


/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_toggle() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Toggle', 'fusion-builder' ),
		'shortcode'         => 'fusion_toggle',
		'hide_from_builder' => true,
		'allow_generator'   => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the toggle title.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Open by Default', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have the toggle open when page loads.', 'fusion-builder' ),
				'param_name'  => 'open',
				'value'       => array(
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
				),
				'default'     => 'no',
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Toggle Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert the toggle content.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_toggle' );
