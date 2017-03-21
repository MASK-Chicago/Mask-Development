<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_CountersBox {

	/**
	 * Parent SC arguments.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $parent_args;

	/**
	 * Child SC arguments.
	 *
	 * @static
	 * @access public
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

		add_filter( 'fusion_attr_counters-box-shortcode', array( $this, 'parent_attr' ) );
		add_filter( 'fusion_attr_counter-box-container', array( $this, 'container_attr' ) );
		add_shortcode( 'fusion_counters_box', array( $this, 'render_parent' ) );

		add_filter( 'fusion_attr_counter-box-shortcode', array( $this, 'child_attr' ) );
		add_filter( 'fusion_attr_counter-box-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_counter-box-shortcode-unit', array( $this, 'unit_attr' ) );
		add_filter( 'fusion_attr_counter-box-shortcode-counter', array( $this, 'counter_attr' ) );
		add_filter( 'fusion_attr_counter-box-shortcode-counter-container', array( $this, 'counter_container_attr' ) );
		add_filter( 'fusion_attr_counter-box-shortcode-content', array( $this, 'content_attr' ) );
		add_shortcode( 'fusion_counter_box', array( $this, 'render_child' ) );

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
				'hide_on_mobile'   => fusion_builder_default_visibility( 'string' ),
				'class'            => '',
				'id'               => '',
				'animation_offset' => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'animation_offset' ) : '',
				'body_color'       => strtolower( FusionBuilder::get_theme_option( 'counter_box_body_color' ) ),
				'body_size'        => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'counter_box_body_size' ) ) : '',
				'border_color'     => strtolower( FusionBuilder::get_theme_option( 'counter_box_border_color' ) ),
				'color'            => strtolower( FusionBuilder::get_theme_option( 'counter_box_color' ) ),
				'columns'          => '',
				'icon_size'        => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'counter_box_icon_size' ) ) : '',
				'icon_top'         => strtolower( FusionBuilder::get_theme_option( 'counter_box_icon_top' ) ),
				'title_size'       => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'counter_box_title_size' ) ) : '',
			), $args
		);

		$defaults['title_size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['title_size'], '' );
		$defaults['icon_size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['icon_size'], '' );
		$defaults['body_size'] = FusionBuilder::validate_shortcode_attr_value( $defaults['body_size'], '' );

		extract( $defaults );

		self::$parent_args = $defaults;

		self::$parent_args['columns'] = min( 6, self::$parent_args['columns'] );

		$this->set_num_of_columns( $content );

		return '<div ' . FusionBuilder::attributes( 'counters-box-shortcode' ) . '>' . do_shortcode( $content ) . '</div><div class="clearfix"></div>';

	}

	/**
	 * Builds the parent attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function parent_attr() {

		$attr = fusion_builder_visibility_atts(
			self::$parent_args['hide_on_mobile'],
			array(
				'class' => 'fusion-counters-box counters-box row fusion-clearfix fusion-columns-' . self::$parent_args['columns'],
			)
		);

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the container attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function container_attr() {
		return array(
			'class' => 'counter-box-container',
			'style' => 'border: 1px solid ' . self::$parent_args['border_color'] . ';',
		);
	}

	/**
	 * Render the child shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args     Shortcode parameters.
	 * @param  string $content  Content between shortcode.
	 * @return string           HTML output.
	 */
	function render_child( $args, $content = '' ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'     => '',
				'id'        => '',
				'delimiter' => '',
				'direction' => 'up',
				'icon'      => '',
				'unit'      => '',
				'unit_pos'  => 'suffix',
				'value'     => '20',
			), $args
		);

		extract( $defaults );

		self::$child_args = $defaults;

		self::$child_args['value'] = intval( $value );

		$unit_output = '';
		if ( $unit ) {
			$unit_output = '<span ' . FusionBuilder::attributes( 'counter-box-shortcode-unit' ) . '>' . $unit . '</span>';
		}

		$init_value = self::$child_args['value'];
		if ( 'up' == $direction ) {
			$init_value = 0;
		}

		$counter = '<span ' . FusionBuilder::attributes( 'counter-box-shortcode-counter' ) . '>' . $init_value . '</span>';

		$icon_output = '';
		if ( $icon ) {
			$icon_output = '<i ' . FusionBuilder::attributes( 'counter-box-shortcode-icon' ) . '></i>';
		}

		if ( 'prefix' == $unit_pos ) {
			$counter = $icon_output . $unit_output . $counter;
		} else {
			$counter = $icon_output . $counter . $unit_output;
		}

		$counter_wrapper = '<div ' . FusionBuilder::attributes( 'counter-box-shortcode-counter-container' ) . '>' . $counter . '</div>';
		$content_output  = '<div ' . FusionBuilder::attributes( 'counter-box-shortcode-content' ) . '>' . do_shortcode( $content ) . '</div>';

		return '<div ' . FusionBuilder::attributes( 'counter-box-shortcode' ) . '><div ' . FusionBuilder::attributes( 'counter-box-container' ) . '>' . $counter_wrapper . $content_output . '</div></div>';

	}

	/**
	 * Builds the child attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function child_attr() {

		$attr    = array();

		$columns = 1;
		if ( self::$parent_args['columns'] && ! empty( self::$parent_args['columns'] ) ) {
			$columns = 12 / self::$parent_args['columns'];
		}

		$attr['class'] = 'fusion-counter-box fusion-column col-counter-box counter-box-wrapper col-lg-' . $columns . ' col-md-' . $columns . ' col-sm-' . $columns;

		if ( '5' == self::$parent_args['columns'] ) {
			$attr['class'] = 'fusion-counter-box fusion-column col-counter-box counter-box-wrapper col-lg-2 col-md-2 col-sm-2';
		}

		if ( self::$child_args['class'] ) {
			$attr['class'] .= ' ' . self::$child_args['class'];
		}

		if ( self::$child_args['id'] ) {
			$attr['id'] = self::$child_args['id'];
		}

		if ( self::$parent_args['animation_offset'] ) {
			$animations = FusionBuilder::animations( array( 'offset' => self::$parent_args['animation_offset'] ) );

			$attr = array_merge( $attr, $animations );
		}

		return $attr;

	}

	/**
	 * Builds the icon attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function icon_attr() {

		$attr = array(
			'class' => 'counter-box-icon fa fontawesome-icon ' . FusionBuilder::font_awesome_name_handler( self::$child_args['icon'] ),
			'style' => 'font-size:' . self::$parent_args['icon_size'] . 'px;',
		);

		if ( 'yes' == self::$parent_args['icon_top'] ) {
			$attr['style'] .= 'display:block;';
		}

		return $attr;

	}

	/**
	 * Builds the unit attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function unit_attr() {
		return array(
			'class' => 'unit',
		);
	}

	/**
	 * Builds the counter attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function counter_attr() {
		return array(
			'class'          => 'display-counter',
			'data-value'     => self::$child_args['value'],
			'data-delimiter' => self::$child_args['delimiter'],
			'data-direction' => self::$child_args['direction'],
		);
	}

	/**
	 * Builds the container attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function counter_container_attr() {
		return array(
			'class' => 'content-box-percentage content-box-counter',
			'style' => 'color:' . self::$parent_args['color'] . ';font-size:' . self::$parent_args['title_size'] . 'px;line-height:normal;',
		);
	}

	/**
	 * Builds the content attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function content_attr() {
		return array(
			'class' => 'counter-box-content',
			'style' => 'color:' . self::$parent_args['body_color'] . ';font-size:' . self::$parent_args['body_size'] . 'px;',
		);
	}

	/**
	 * Calculate the number of columns automatically
	 *
	 * @access public
	 * @since 1.0
	 * @param  string $content Content to be parsed.
	 */
	public function set_num_of_columns( $content ) {
		if ( ! self::$parent_args['columns'] ) {
			preg_match_all( '/(\[fusion_counter_box (.*?)\](.*?)\[\/fusion_counter_box\])/s', $content, $matches );
			if ( is_array( $matches ) && ! empty( $matches ) ) {
				self::$parent_args['columns'] = min( 6, count( $matches[0] ) );
			} else {
				self::$parent_args['columns'] = 1;
			}
		} elseif ( self::$parent_args['columns'] > 6 ) {
			self::$parent_args['columns'] = 6;
		}
	}
}
new FusionSC_CountersBox();

/**
 * Map shortcode to Fusion Builder
 *
 * @since 1.0
 */
function fusion_element_counters_box() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Counter Boxes', 'fusion-builder' ),
		'shortcode'     => 'fusion_counters_box',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_counter_box',
		'icon'          => 'fusiona-browser',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-counter-box-preview.php',
		'preview_id'    => 'fusion-builder-block-module-counter-box-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_counter_box value="20" delimiter="" unit="" unit_pos="suffix" icon="" direction="up"]Your Content Goes Here[/fusion_counter_box]',
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Number of Columns', 'fusion-builder' ),
				'description' => esc_attr__( 'Set the number of columns per row.', 'fusion-builder' ),
				'param_name'  => 'columns',
				'value'       => '4',
				'min'         => '1',
				'max'         => '6',
				'step'        => '1',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Counter Box Title Font Color', 'fusion-builder' ),
				'param_name'  => 'color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the color of the counter "value" and icon. Leave blank for theme option styling.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Box Title Font Size', 'fusion-builder' ),
				'description' => esc_attr__( "Controls the size of the counter 'value' and icon. Enter the font size without 'px' ex: 50. Leave blank for theme option styling.", 'fusion-builder' ),
				'param_name'  => 'title_size',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Box Icon Size', 'fusion-builder' ),
				'description' => esc_attr__( "Controls the size of the icon. Enter the font size without 'px'. Default is 50. Leave blank for theme option styling.", 'fusion-builder' ),
				'param_name'  => 'icon_size',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Counter Box Icon Top', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the position of the icon. Select Default for theme option styling.', 'fusion-builder' ),
				'param_name'  => 'icon_top',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Counter Box Body Font Color', 'fusion-builder' ),
				'param_name'  => 'body_color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the color of the counter body text. Leave blank for theme option styling.', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Box Body Font Size', 'fusion-builder' ),
				'description' => esc_attr__( "Controls the size of the counter body text. Enter the font size without 'px' ex: 13. Leave blank for theme option styling.", 'fusion-builder' ),
				'param_name'  => 'body_size',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Counter Box Border Color', 'fusion-builder' ),
				'param_name'  => 'border_color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the color of the border.', 'fusion-builder' ),
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
add_action( 'fusion_builder_before_init', 'fusion_element_counters_box' );


/**
 * Map shortcode to Fusion Builder
 */
function fusion_element_counter_box() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Counter Box', 'fusion-builder' ),
		'description'       => esc_attr__( 'Enter some content for this block.', 'fusion-builder' ),
		'shortcode'         => 'fusion_counter_box',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Value', 'fusion-builder' ),
				'description' => esc_attr__( 'The number to which the counter will animate.', 'fusion-builder' ),
				'param_name'  => 'value',
				'value'       => '20',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Delimiter Digit', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert a delimiter digit for better readability. ex: ,', 'fusion-builder' ),
				'param_name'  => 'delimiter',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Box Unit', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert a unit for the counter. ex %', 'fusion-builder' ),
				'param_name'  => 'unit',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Unit Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the positioning of the unit.', 'fusion-builder' ),
				'param_name'  => 'unit_pos',
				'value'       => array(
					esc_attr__( 'After Counter', 'fusion-builder' )  => 'suffix',
					esc_attr__( 'Before Counter', 'fusion-builder' ) => 'prefix',
				),
				'default'     => 'suffix',
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Counter Direction', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to count up or down.', 'fusion-builder' ),
				'param_name'  => 'direction',
				'value'       => array(
					esc_attr__( 'Count Up', 'fusion-builder' )   => 'up',
					esc_attr__( 'Count Down', 'fusion-builder' ) => 'down',
				),
				'default'     => 'up',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Counter Box Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert text for counter box.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_counter_box' );
