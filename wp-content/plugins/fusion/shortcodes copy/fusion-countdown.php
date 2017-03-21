<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_Countdown {

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
	 * The countdown counter.
	 *
	 * @access private
	 * @since 1.0
	 * @var int
	 */
	private $countdown_counter = 1;

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		add_filter( 'fusion_attr_countdown-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_countdown-shortcode-counter-wrapper', array( $this, 'counter_wrapper_attr' ) );
		add_filter( 'fusion_attr_countdown-shortcode-link', array( $this, 'link_attr' ) );

		add_shortcode( 'fusion_countdown', array( $this, 'render' ) );

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
				'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
				'class'                 => '',
				'id'                    => '',
				'background_color'      => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_background_color' ) : '',
				'background_image'      => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_background_image', 'url' ) : '',
				'background_position'   => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_background_position' ) : '',
				'background_repeat'     => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_background_repeat' ) : '',
				'border_radius'         => '',
				'counter_box_color'     => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_counter_box_color' ) : '',
				'counter_text_color'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_counter_text_color' ) : '',
				'countdown_end'         => '2000-01-01 00:00:00',
				'dash_titles'           => 'short',
				'heading_text'          => '',
				'heading_text_color'    => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_heading_text_color' ) : '',
				'link_text'             => '',
				'link_text_color'       => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_link_text_color' ) : '',
				'link_target'           => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_link_target' ) : '',
				'link_url'              => '',
				'show_weeks'            => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_show_weeks' ) : '',
				'subheading_text'       => '',
				'subheading_text_color' => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_subheading_text_color' ) : '',
				'timezone'              => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_timezone' ) : '',
			), $args
		);

		$defaults['border_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['border_radius'], 'px' );

		if ( 'default' === $defaults['link_target'] ) {
			$defaults['link_target'] = ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'countdown_link_target' ) : '';
		}
		extract( $defaults );

		self::$args = $defaults;

		$html = '<div ' . FusionBuilder::attributes( 'countdown-shortcode' ) . '>';
		$html .= self::get_styles();
		$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-heading-wrapper' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-subheading' ) . '>' . $subheading_text . '</div>';
		$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-heading' ) . '>' . $heading_text . '</div>';
		$html .= '</div>';

		$html .= '<div ' . FusionBuilder::attributes( 'countdown-shortcode-counter-wrapper' ) . '>';

		$dashes = array(
			array(
				'show'      => $show_weeks,
				'class'     => 'weeks',
				'shortname' => esc_attr__( 'Weeks', 'fusion-builder' ),
				'longname'  => esc_attr__( 'Weeks', 'fusion-builder' ),
			),
			array(
				'show'      => 'yes',
				'class'     => 'days',
				'shortname' => esc_attr__( 'Days', 'fusion-builder' ),
				'longname'  => esc_attr__( 'Days', 'fusion-builder' ),
			),
			array(
				'show'      => 'yes',
				'class'     => 'hours',
				'shortname' => esc_attr__( 'Hrs', 'fusion-builder' ),
				'longname'  => esc_attr__( 'Hours', 'fusion-builder' ),
			),
			array(
				'show'      => 'yes',
				'class'     => 'minutes',
				'shortname' => esc_attr__( 'Min', 'fusion-builder' ),
				'longname'  => esc_attr__( 'Minutes', 'fusion-builder' ),
			),
			array(
				'show'      => 'yes',
				'class'     => 'seconds',
				'shortname' => esc_attr__( 'Sec', 'fusion-builder' ),
				'longname'  => esc_attr__( 'Seconds', 'fusion-builder' ),
			),
		);

		$dash_class = '';
		if ( ! self::$args['counter_box_color'] || 'transparent' == self::$args['counter_box_color'] ) {
			$dash_class = ' fusion-no-bg';
		}

		$dashes_count = count( $dashes );

		for ( $i = 0; $i < $dashes_count; $i++ ) {
			if ( 'yes' == $dashes[ $i ]['show'] ) {
				$html .= '<div class="fusion-dash-wrapper ' . $dash_class . '">';
				$html .= '<div class="fusion-dash fusion-dash-' . $dashes[ $i ]['class'] . '">';
				$html .= ( 'days' == $dashes[ $i ]['class'] ) ? '<div class="fusion-first-digit fusion-digit">0</div>' : '';
				$html .= '<div class="fusion-digit">0</div><div class="fusion-digit">0</div>';
				$html .= '<div class="fusion-dash-title">' . $dashes[ $i ][ $dash_titles . 'name' ] . '</div>';
				$html .= '</div></div>';
			}
		}

		$html .= '</div>';

		$html .= '<div ' . FusionBuilder::attributes( 'fusion-countdown-link-wrapper' ) . '>';
		$html .= '<a ' . FusionBuilder::attributes( 'countdown-shortcode-link' ) . '>' . $link_text . '</a>';
		$html .= '</div>';

		$html .= do_shortcode( $content );
		$html .= '</div>';

		$this->countdown_counter++;

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
			'class' => 'fusion-countdown fusion-countdown-' . $this->countdown_counter,
		);

		$attr = fusion_builder_visibility_atts( self::$args['hide_on_mobile'], $attr );

		if ( ! self::$args['background_image'] && ( ! self::$args['background_color'] || 'transparent' == self::$args['background_color'] ) ) {
			$attr['class'] .= ' fusion-no-bg';
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
	 * Builds the counter-wrapper attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function counter_wrapper_attr() {

		$attr = array(
			'class' => 'fusion-countdown-counter-wrapper',
			'id'    => 'fusion-countdown-' . $this->countdown_counter,
		);

		if ( 'site_time' == self::$args['timezone'] ) {
			$attr['data-gmt-offset'] = get_option( 'gmt_offset' );
		}

		if ( self::$args['countdown_end'] ) {
			$attr['data-timer'] = date( 'Y-m-d-H-i-s', strtotime( self::$args['countdown_end'] ) );
		}

		$attr['data-omit-weeks'] = ( 'yes' == self::$args['show_weeks'] ) ? '0' : '1';

		return $attr;
	}

	/**
	 * Builds the link attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function link_attr() {

		$attr = array(
			'class'  => 'fusion-countdown-link',
			'target' => self::$args['link_target'],
			'href'   => self::$args['link_url'],
		);

		if ( '_blank' == self::$args['link_target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		return $attr;
	}

	/**
	 * Gets the CSS styles.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_styles() {
		$styles = '';

		// Set custom background styles.
		if ( self::$args['background_image'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' {';
			$styles .= 'background:url(' . self::$args['background_image'] . ') ' . self::$args['background_position'] . ' ' . self::$args['background_repeat'] . ' ' . self::$args['background_color'] . ';';

			if ( 'no-repeat' == self::$args['background_repeat'] ) {
				$styles .= '-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;';
			}
			$styles .= '}';

		} elseif ( self::$args['background_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' {background-color:' . self::$args['background_color'] . ';}';
		}

		if ( self::$args['border_radius'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ', .fusion-countdown-' . $this->countdown_counter . ' .fusion-dash {border-radius:' . self::$args['border_radius'] . ';}';
		}

		if ( self::$args['heading_text_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-heading {color:' . self::$args['heading_text_color'] . ';}';
		}

		if ( self::$args['subheading_text_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-subheading {color:' . self::$args['subheading_text_color'] . ';}';
		}

		if ( self::$args['counter_text_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-counter-wrapper {color:' . self::$args['counter_text_color'] . ';}';
		}

		if ( self::$args['counter_box_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-dash {background-color:' . self::$args['counter_box_color'] . ';}';
		}

		if ( self::$args['link_text_color'] ) {
			$styles .= '.fusion-countdown-' . $this->countdown_counter . ' .fusion-countdown-link {color:' . self::$args['link_text_color'] . ';}';
		}

		if ( $styles ) {
			$styles = '<style type="text/css" scoped="scoped">' . $styles . '</style>';
		}

		return $styles;
	}
}
new FusionSC_Countdown();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_countdown() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Countdown', 'fusion-builder' ),
		'shortcode'  => 'fusion_countdown',
		'icon'       => 'fusiona-calendar-check-o',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-countdown-preview.php',
		'preview_id' => 'fusion-builder-block-module-countdown-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Countdown Timer End', 'fusion-builder' ),
				'description' => __( 'Set the end date and time for the countdown time.
					Use SQL time format: YYYY-MM-DD HH:MM:SS.
					E.g: 2016-05-10 12:30:00.', 'fusion-builder' ),
				'param_name'  => 'countdown_end',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Timezone', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose which timezone should be used for the countdown calculation.', 'fusion-builder' ),
				'param_name'  => 'timezone',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )          => '',
					esc_attr__( 'Timezone of Site', 'fusion-builder' ) => 'site_time',
					esc_attr__( 'Timezone of User', 'fusion-builder' ) => 'user_time',
				),
				'default'     => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Weeks', 'fusion-builder' ),
				'description' => esc_attr__( 'Select "yes" to show weeks for longer countdowns.', 'fusion-builder' ),
				'param_name'  => 'show_weeks',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'default'     => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the countdown wrapping box.', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Background Image', 'fusion-builder' ),
				'description' => esc_attr__( 'Upload an image to display in the background.', 'fusion-builder' ),
				'param_name'  => 'background_image',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Background Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the position of the background image.', 'fusion-builder' ),
				'param_name'  => 'background_position',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )       => '',
					esc_attr__( 'Left Top', 'fusion-builder' )      => 'left top',
					esc_attr__( 'Left Center', 'fusion-builder' )   => 'left center',
					esc_attr__( 'Left Bottom', 'fusion-builder' )   => 'left bottom',
					esc_attr__( 'Right Top', 'fusion-builder' )     => 'right top',
					esc_attr__( 'Right Center', 'fusion-builder' )  => 'right center',
					esc_attr__( 'Right Bottom', 'fusion-builder' )  => 'right bottom',
					esc_attr__( 'Center Top', 'fusion-builder' )    => 'center top',
					esc_attr__( 'Center Center', 'fusion-builder' ) => 'center center',
					esc_attr__( 'Center Bottom', 'fusion-builder' ) => 'center bottom',
				),
				'default'     => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'background_image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'select',
				'heading'     => esc_attr__( 'Background Repeat', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how the background image repeats.' ),
				'param_name'  => 'background_repeat',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )                            => '',
					esc_attr__( 'No Repeat', 'fusion-builder' )                          => 'no-repeat',
					esc_attr__( 'Repeat Vertically and Horizontally', 'fusion-builder' ) => 'repeat',
					esc_attr__( 'Repeat Horizontally', 'fusion-builder' )                => 'repeat-x',
					esc_attr__( 'Repeat Vertically', 'fusion-builder' )                  => 'repeat-y',
				),
				'default'     => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'background_image',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Border Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the radius of outer box and also the countdown. In pixels (px), ex: 1px.', 'fusion-builder' ),
				'param_name'  => 'border_radius',
				'value'       => '',
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Countdown Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a background color for the countdown.', 'fusion-builder' ),
				'param_name'  => 'counter_box_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Countdown Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown timer.', 'fusion-builder' ),
				'param_name'  => 'counter_text_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Heading Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a heading text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'heading_text',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Countdown Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown timer.', 'fusion-builder' ),
				'param_name'  => 'counter_text_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Heading Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown heading.', 'fusion-builder' ),
				'param_name'  => 'heading_text_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Subheading Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a subheading text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'subheading_text',
				'value'       => 'Your Content Goes Here',
				'placeholder' => true,
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Subheading Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown subheading.', 'fusion-builder' ),
				'param_name'  => 'subheading_text_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link URL', 'fusion-builder' ),
				'description' => esc_attr__( 'Add a url for the link. E.g: http://example.com.', 'fusion-builder' ),
				'param_name'  => 'link_url',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link Text', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a link text for the countdown.', 'fusion-builder' ),
				'param_name'  => 'link_text',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'link_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Link Text Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose a text color for the countdown link.', 'fusion-builder' ),
				'param_name'  => 'link_text_color',
				'value'       => '',
				'group'       => __( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'link_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Link Target', 'fusion-builder' ),
				'description' => esc_attr__( '_self = open in same window
                                      _blank = open in new window', 'fusion-builder' ),
				'param_name'  => 'link_target',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => 'default',
					esc_attr__( '_self', 'fusion-builder' )   => '_self',
					esc_attr__( '_blank', 'fusion-builder' )  => '_blank',
				),
				'default'     => 'default',
				'dependency'  => array(
					array(
						'element'  => 'link_url',
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
add_action( 'fusion_builder_before_init', 'fusion_element_countdown' );
