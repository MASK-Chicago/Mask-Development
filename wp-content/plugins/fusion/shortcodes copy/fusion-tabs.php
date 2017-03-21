<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Tabs {

	/**
	 * Tabs counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $tabs_counter = 1;

	/**
	 * Tab counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $tab_counter = 1;

	/**
	 * Array of our tabs.
	 *
	 * @access private
	 * @since 1.0
	 * @var array
	 */
	private $tabs = array();

	/**
	 * Whether the tab is active or not.
	 *
	 * @access private
	 * @since 1.0
	 * @var bool
	 */
	private $active = false;

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

		add_filter( 'fusion_attr_tabs-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_tabs-shortcode-link', array( $this, 'link_attr' ) );
		add_filter( 'fusion_attr_tabs-shortcode-icon', array( $this, 'icon_attr' ) );
		add_filter( 'fusion_attr_tabs-shortcode-tab', array( $this, 'tab_attr' ) );

		add_shortcode( 'fusion_old_tabs', array( $this, 'render_parent' ) );
		add_shortcode( 'fusion_old_tab', array( $this, 'render_child' ) );

		add_shortcode( 'fusion_tabs', array( $this, 'fusion_tabs' ) );
		add_shortcode( 'fusion_tab', array( $this, 'fusion_tab' ) );

	}

	/**
	 * Render the parent shortcode.
	 *
	 * @access public
	 * @since 1.0
	 * @param  array  $args    Shortcode parameters.
	 * @param  string $content Content between shortcode.
	 * @return string          HTML output.
	 */
	public function render_parent( $args, $content = '' ) {

		$html = '';
		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'hide_on_mobile'  => fusion_builder_default_visibility( 'string' ),
				'class'           => '',
				'id'              => '',
				'backgroundcolor' => FusionBuilder::get_theme_option( 'tabs_bg_color' ),
				'bordercolor'     => FusionBuilder::get_theme_option( 'tabs_border_color' ),
				'design'          => 'classic',
				'inactivecolor'   => FusionBuilder::get_theme_option( 'tabs_inactive_color' ),
				'justified'       => 'yes',
				'layout'          => 'horizontal',
			), $args
		);

		extract( $defaults );

		self::$parent_args = $defaults;

		$justified_class = '';
		if ( 'yes' === $justified && 'vertical' !== $layout ) {
			$justified_class = ' nav-justified';
		}

		$styles = '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li a{border-top-color:' . self::$parent_args['inactivecolor'] . ';background-color:' . self::$parent_args['inactivecolor'] . ';}';
		if ( 'clean' !== $design ) {
			$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs{background-color:' . self::$parent_args['backgroundcolor'] . ';}';
			$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a:hover,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a:focus{border-right-color:' . self::$parent_args['backgroundcolor'] . ';}';
		} else {
			$styles = '#wrapper .fusion-tabs.fusion-tabs-' . $this->tabs_counter . '.clean .nav-tabs li a{border-color:' . self::$parent_args['bordercolor'] . ';}.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li a{background-color:' . self::$parent_args['inactivecolor'] . ';}';
		}
		$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a:hover,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li.active a:focus{background-color:' . self::$parent_args['backgroundcolor'] . ';}';
		$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs li a:hover{background-color:' . self::$parent_args['backgroundcolor'] . ';border-top-color:' . self::$parent_args['backgroundcolor'] . ';}';
		$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .tab-pane{background-color:' . self::$parent_args['backgroundcolor'] . ';}';
		$styles .= '.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .nav-tabs,.fusion-tabs.fusion-tabs-' . $this->tabs_counter . ' .tab-content .tab-pane{border-color:' . self::$parent_args['bordercolor'] . ';}';
		$styles = '<style type="text/css">' . $styles . '</style>';

		$html = '<div ' . FusionBuilder::attributes( 'tabs-shortcode' ) . '>' . $styles . '<div ' . FusionBuilder::attributes( 'nav' ) . '><ul ' . FusionBuilder::attributes( 'nav-tabs' . $justified_class ) . '>';

		$is_first_tab = true;

		if ( empty( $this->tabs ) ) {
			$this->parse_tab_parameter( $content, 'fusion_old_tab', $args );
		}

		if ( strpos( $content, 'fusion_tab' ) ) {
			preg_match_all( '/(\[fusion_tab (.*?)\](.*?)\[\/fusion_tab\])/s', $content, $matches );
		} else {
			preg_match_all( '/(\[fusion_old_tab (.*?)\](.*?)\[\/fusion_old_tab\])/s', $content, $matches );
		}

		$tab_content  = '';

		$tabs_count = count( $this->tabs );
		for ( $i = 0; $i < $tabs_count; $i++ ) {
			$icon = '';
			if ( 'none' !== $this->tabs[ $i ]['icon'] ) {
				$icon = '<i ' . FusionBuilder::attributes( 'tabs-shortcode-icon', array( 'index' => $i ) ) . '></i>';
			}

			if ( $is_first_tab ) {
				$tab_nav = '<li ' . FusionBuilder::attributes( 'active' ) . '><a ' . FusionBuilder::attributes( 'tabs-shortcode-link', array( 'index' => $i ) ) . '><h4 ' . FusionBuilder::attributes( 'fusion-tab-heading' ) . '>' . $icon . $this->tabs[ $i ]['title'] . '</h4></a></li>';
				$is_first_tab = false;
			} else {
				$tab_nav = '<li><a ' . FusionBuilder::attributes( 'tabs-shortcode-link', array( 'index' => $i ) ) . '><h4 ' . FusionBuilder::attributes( 'fusion-tab-heading' ) . '>' . $icon . $this->tabs[ $i ]['title'] . '</h4></a></li>';
			}

			$html .= $tab_nav;

			$tab_content .= '<div ' . FusionBuilder::attributes( 'nav fusion-mobile-tab-nav' ) . '><ul ' . FusionBuilder::attributes( 'nav-tabs' . $justified_class ) . '>' . $tab_nav . '</ul></div>' . do_shortcode( $matches[1][ $i ] );
		}

		$html .= '</ul></div><div ' . FusionBuilder::attributes( 'tab-content' ) . '>' . $tab_content . '</div></div>';

		$this->tabs_counter++;
		$this->tab_counter = 1;
		$this->active = false;
		unset( $this->tabs );

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
			'class' => 'fusion-tabs fusion-tabs-' . $this->tabs_counter . ' ' . self::$parent_args['design'],
		) );

		if ( 'yes' !== self::$parent_args['justified'] && 'vertical' !== self::$parent_args['layout'] ) {
			$attr['class'] .= ' nav-not-justified';
		}

		if ( self::$parent_args['class'] ) {
			$attr['class'] .= ' ' . self::$parent_args['class'];
		}

		$attr['class'] .= ( 'vertical' === self::$parent_args['layout'] ) ? ' vertical-tabs' : ' horizontal-tabs';

		if ( self::$parent_args['id'] ) {
			$attr['id'] = self::$parent_args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the link attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $atts Default attributes.
	 * @return array
	 */
	public function link_attr( $atts ) {
		$attr = array(
			'class'       => 'tab-link',
			'data-toggle' => 'tab',
		);
		$index        = $atts['index'];
		$attr['id']   = 'fusion-tab-' . strtolower( preg_replace( '/\s+/', '', $this->tabs[ $index ]['title'] ) );
		$attr['href'] = '#' . $this->tabs[ $index ]['unique_id'];

		return $attr;
	}

	/**
	 * Builds the icon attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $atts Default attributes.
	 * @return array
	 */
	public function icon_attr( $atts ) {
		$index = $atts['index'];
		return array(
			'class' => 'fa fontawesome-icon ' . FusionBuilder::font_awesome_name_handler( $this->tabs[ $index ]['icon'] ),
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
				'icon'       => 'none',
				'id'         => '',
				'fusion_tab' => 'no',
			), $args
		);

		extract( $defaults );

		self::$child_args = $defaults;

		return '<div ' . FusionBuilder::attributes( 'tabs-shortcode-tab' ) . '>' . do_shortcode( $content ) . '</div>';

	}

	/**
	 * Builds the tab attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function tab_attr() {

		$attr = array(
			'class' => 'tab-pane fade',
		);

		if ( ! isset( $this->active ) ) {
			$this->active = false;
		}

		if ( ! $this->active ) {
			$attr['class'] = 'tab-pane fade in active';
			$this->active = true;
		}

		if ( 'yes' === self::$child_args['fusion_tab'] ) {
			$attr['id'] = self::$child_args['id'];
		} else {
			$index = self::$child_args['id'] - 1;
			$attr['id'] = $this->tabs[ $index ]['unique_id'];
		}

		return $attr;

	}

	/**
	 * Returns the fusion-tabs.
	 *
	 * @access public
	 * @since 1.0
	 * @param array       $atts    The attributes.
	 * @param null|string $content The content.
	 * @return string
	 */
	public function fusion_tabs( $atts, $content = null ) {

		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'class'           => '',
				'id'              => '',
				'backgroundcolor' => FusionBuilder::get_theme_option( 'tabs_bg_color' ),
				'bordercolor'     => FusionBuilder::get_theme_option( 'tabs_border_color' ),
				'design'          => 'classic',
				'inactivecolor'   => FusionBuilder::get_theme_option( 'tabs_inactive_color' ),
				'justified'       => 'yes',
				'layout'          => 'horizontal',
			), $atts
		);

		extract( $defaults );

		$atts = $defaults;

		$content = preg_replace( '/tab\][^\[]*/', 'tab]', $content );
		$content = preg_replace( '/^[^\[]*\[/', '[', $content );

		$this->parse_tab_parameter( $content, 'fusion_tab' );

		$shortcode_wrapper  = '[fusion_old_tabs design="' . $atts['design'] . '" layout="' . $atts['layout'] . '" justified="' . $atts['justified'] . '" backgroundcolor="' . $atts['backgroundcolor'] . '" inactivecolor="' . $atts['inactivecolor'] . '" bordercolor="' . $atts['bordercolor'] . '" class="' . $atts['class'] . '" id="' . $atts['id'] . '"]';
		$shortcode_wrapper .= $content;
		$shortcode_wrapper .= '[/fusion_old_tabs]';

		return do_shortcode( $shortcode_wrapper );
	}

	/**
	 * Returns the fusion-tab.
	 *
	 * @access public
	 * @since 1.0
	 * @param array       $atts    The attributes.
	 * @param null|string $content The content.
	 * @return string
	 */
	public function fusion_tab( $atts, $content = null ) {
		$defaults = FusionBuilder::set_shortcode_defaults(
			array(
				'id'    => '',
				'icon'  => '',
				'title' => '',
			), $atts
		);

		extract( $defaults );

		$atts = $defaults;

		// Create unique tab id for linking.
		$sanitized_title = hash( 'md5', $title, false );
		$sanitized_title = 'tab' . str_replace( '-', '_', $sanitized_title );
		$unique_id = 'tab-' . substr( md5( get_the_ID() . '-' . $this->tabs_counter . '-' . $this->tab_counter . '-' . $sanitized_title ), 13 );

		$shortcode_wrapper = '[fusion_old_tab id="' . $unique_id . '" icon="' . $icon . '" fusion_tab="yes"]' . do_shortcode( $content ) . '[/fusion_old_tab]';

		$this->tab_counter++;

		return do_shortcode( $shortcode_wrapper );
	}

	/**
	 * Parses the tab parameters.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $content The content.
	 * @param string $shortcode The shortcode.
	 * @param array  $args      The arguments.
	 */
	public function parse_tab_parameter( $content, $shortcode, $args = null ) {
		$preg_match_tabs_single = preg_match_all( FusionBuilder::get_shortcode_regex( $shortcode ), $content, $tabs_single );

		if ( is_array( $tabs_single[0] ) ) {
			foreach ( $tabs_single[0] as $key => $tab ) {

				if ( is_array( $args ) ) {
					$preg_match_titles = preg_match_all( '/' . $shortcode . ' id=([0-9]+)/i', $tab, $ids );

					if ( array_key_exists( '0', $ids[1] ) ) {
						$id = $ids[1][0];
					} else {
						$title = 'default';
					}

					foreach ( $args as $key => $value ) {
						if ( 'tab' . $id == $key ) {
							$title = $value;
						}
					}
				} else {
					$preg_match_titles = preg_match_all( '/' . $shortcode . ' title="([^\"]+)"/i', $tab, $titles );
					$title = ( array_key_exists( '0', $titles[1] ) ) ? $titles[1][0] : 'default';
				}
				$preg_match_icons = preg_match_all( '/' . $shortcode . '( id=[0-9]+| title="[^\"]+")? icon="([^\"]+)"/i', $tab, $icons );
				$icon = ( array_key_exists( '0', $icons[2] ) ) ? $icons[2][0] : 'none';

				// Create unique tab id for linking.
				$sanitized_title = hash( 'md5', $title, false );
				$sanitized_title = 'tab' . str_replace( '-', '_', $sanitized_title );
				$unique_id = 'tab-' . substr( md5( get_the_ID() . '-' . $this->tabs_counter . '-' . $this->tab_counter . '-' . $sanitized_title ), 13 );

				// Create array for every single tab shortcode.
				$this->tabs[] = array( 'title' => $title, 'icon' => $icon, 'unique_id' => $unique_id );

				$this->tab_counter++;
			}

			$this->tab_counter = 1;
		}
	}
}
new FusionSC_Tabs();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_tabs() {
	fusion_builder_map( array(
		'name'          => esc_attr__( 'Tabs', 'fusion-builder' ),
		'shortcode'     => 'fusion_tabs',
		'multi'         => 'multi_element_parent',
		'element_child' => 'fusion_tab',
		'icon'          => 'fusiona-folder',
		'preview'       => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-tabs-preview.php',
		'preview_id'    => 'fusion-builder-block-module-tabs-preview-template',
		'params'        => array(
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Enter some content for this contentbox.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => '[fusion_tab title="Your Content Goes Here" icon=""]Your Content Goes Here[/fusion_tab]',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Design', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a design for the element.' ),
				'param_name'  => 'design',
				'value'       => array(
					esc_attr__( 'Classic', 'fusion-builder' ) => 'classic',
					esc_attr__( 'Clean', 'fusion-builder' )   => 'clean',
				),
				'default'     => 'classic',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Layout', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the layout of the element.' ),
				'param_name'  => 'layout',
				'value'       => array(
					esc_attr__( 'Horizontal', 'fusion-builder' ) => 'horizontal',
					esc_attr__( 'Vertical', 'fusion-builder' )   => 'vertical',
				),
				'default'     => 'horizontal',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Justify Tabs', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get tabs stretched over full element width.', 'fusion-builder' ),
				'param_name'  => 'justified',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background tab color. ', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Inactive Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the inactive tab color. ', 'fusion-builder' ),
				'param_name'  => 'inactivecolor',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the color of the outer tab border. ', 'fusion-builder' ),
				'param_name'  => 'bordercolor',
				'value'       => '',
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
add_action( 'fusion_builder_before_init', 'fusion_element_tabs' );


/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_tab() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Tab', 'fusion-builder' ),
		'shortcode'         => 'fusion_tab',
		'hide_from_builder' => true,
		'allow_generator'   => true,
		'params'            => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Tab Title', 'fusion-builder' ),
				'description' => esc_attr__( 'Title of the tab.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'iconpicker',
				'heading'     => esc_attr__( 'Icon', 'fusion-builder' ),
				'param_name'  => 'icon',
				'value'       => '',
				'description' => esc_attr__( 'Click an icon to select, click again to deselect.', 'fusion-builder' ),
			),
			array(
				'type'        => 'tinymce',
				'heading'     => esc_attr__( 'Tab Content', 'fusion-builder' ),
				'description' => esc_attr__( 'Add content for the tab.', 'fusion-builder' ),
				'param_name'  => 'element_content',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_element_tab' );
