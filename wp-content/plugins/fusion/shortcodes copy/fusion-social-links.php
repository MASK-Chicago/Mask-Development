<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_SocialLinks {

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

		add_filter( 'fusion_attr_social-links-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_social-links-shortcode-social-networks', array( $this, 'social_networks_attr' ) );
		add_filter( 'fusion_attr_social-links-shortcode-icon', array( $this, 'icon_attr' ) );

		add_shortcode( 'fusion_social_links', array( $this, 'render' ) );

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
				'hide_on_mobile'     => fusion_builder_default_visibility( 'string' ),
				'class'              => '',
				'id'                 => '',
				'icons_boxed'        => ( ! class_exists( 'Avada' ) || Avada()->settings->get( 'social_links_boxed' ) == 1 ) ? 'yes' : Avada()->settings->get( 'social_links_boxed' ),
				'icons_boxed_radius' => ( class_exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'social_links_boxed_radius' ) ) : '',
				'color_type'         => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'social_links_color_type' ) : '',
				'icon_colors'        => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'social_links_icon_color' ) : '',
				'box_colors'         => ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'social_links_box_color' ) : '',
				'icon_order'         => '',
				'show_custom'        => 'no',
				'alignment'          => '',
				'tooltip_placement'  => strtolower( FusionBuilder::get_theme_option( 'social_links_tooltip_placement' ) ),
				'facebook'           => '',
				'twitter'            => '',
				'instagram'          => '',
				'linkedin'           => '',
				'dribbble'           => '',
				'rss'                => '',
				'youtube'            => '',
				'pinterest'          => '',
				'flickr'             => '',
				'vimeo'              => '',
				'tumblr'             => '',
				'google'             => '',
				'googleplus'         => '',
				'digg'               => '',
				'blogger'            => '',
				'skype'              => '',
				'myspace'            => '',
				'deviantart'         => '',
				'yahoo'              => '',
				'reddit'             => '',
				'forrst'             => '',
				'paypal'             => '',
				'dropbox'            => '',
				'soundcloud'         => '',
				'vk'                 => '',
				'xing'               => '',
				'yelp'               => '',
				'spotify'            => '',
				'email'              => '',
			),
			$args
		);
		foreach ( $args as $key => $arg ) {
			if ( false !== strpos( $key, 'custom_' ) ) {
				$defaults[ $key ] = $arg;
			}
		}
		$defaults['icons_boxed_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['icons_boxed_radius'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		self::$args['linktarget'] = ( FusionBuilder::get_theme_option( 'social_icons_new' ) ) ? '_blank' : '_self';

		if ( '' == $defaults['color_type'] ) {
			$defaults['box_colors']  = ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'social_links_box_color' ) : '';
			$defaults['icon_colors'] = ( class_exists( 'Avada' ) ) ? Avada()->settings->get( 'social_links_icon_color' ) : '';
		}

		$social_networks = fusion_builder_get_social_networks( $defaults );

		$social_networks = fusion_builder_sort_social_networks( $social_networks );

		$icons = fusion_builder_build_social_links( $social_networks, 'social-links-shortcode-icon', $defaults );

		$html  = '<div ' . FusionBuilder::attributes( 'social-links-shortcode' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'social-links-shortcode-social-networks' ) . '>';
		$html .= '<div ' . FusionBuilder::attributes( 'fusion-social-networks-wrapper' ) . '>';
		$html .= $icons;
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		if ( $alignment ) {
			$html = '<div class="align' . $alignment . '">' . $html . '</div>';
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
			'class' => 'fusion-social-links',
		) );

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		return $attr;

	}

	/**
	 * Builds the social-networks attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	function social_networks_attr() {

		$attr = array(
			'class' => 'fusion-social-networks',
		);

		if ( 'yes' == self::$args['icons_boxed'] ) {
			$attr['class'] .= ' boxed-icons';
		}

		return $attr;

	}

	/**
	 * Builds the icon attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $args The arguments array.
	 * @return array
	 */
	function icon_attr( $args ) {

		$attr = array(
			'class' => '',
			'style' => '',
		);

		$tooltip = ucfirst( $args['social_network'] );
		if ( 'custom_' === substr( $args['social_network'], 0, 7 ) ) {
			$attr['class'] .= 'custom ';
			$tooltip = str_replace( 'custom_', '', $args['social_network'] );
			$args['social_network'] = strtolower( $tooltip );
		}

		$attr['class'] .= 'fusion-social-network-icon fusion-tooltip fusion-' . $args['social_network'] . ' fusion-icon-' . $args['social_network'];

		$link = $args['social_link'];

		$attr['target'] = self::$args['linktarget'];

		if ( '_blank' == $attr['target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		if ( 'mail' === $args['social_network'] ) {
			$link = ( 'http' === substr( $args['social_link'], 0, 4 ) ) ? $args['social_link'] : 'mailto:' . antispambot( str_replace( 'mailto:', '', $args['social_link'] ) );
			$attr['target'] = '_self';
		}

		$attr['href'] = $link;

		if ( FusionBuilder::get_theme_option( 'nofollow_social_links' ) ) {
			$attr['rel'] = 'nofollow';
		}

		if ( $args['icon_color'] ) {
			$attr['style'] = 'color:' . $args['icon_color'] . ';';
		}

		if ( 'yes' == self::$args['icons_boxed'] && $args['box_color'] ) {
			$attr['style'] .= 'background-color:' . $args['box_color'] . ';border-color:' . $args['box_color'] . ';';
		}

		if ( 'yes' == self::$args['icons_boxed'] && self::$args['icons_boxed_radius'] || '0' === self::$args['icons_boxed_radius'] ) {
			if ( 'round' == self::$args['icons_boxed_radius'] ) {
				self::$args['icons_boxed_radius'] = '50%';
			}
			$attr['style'] .= 'border-radius:' . self::$args['icons_boxed_radius'] . ';';
		}

		if ( 'none' != strtolower( self::$args['tooltip_placement'] ) ) {
			$attr['data-placement'] = strtolower( self::$args['tooltip_placement'] );
			if ( 'Googleplus' == $tooltip ) {
				$tooltip = 'Google+';
			}
			$attr['data-title']  = $tooltip;
			$attr['data-toggle'] = 'tooltip';
		}

		$attr['title'] = $tooltip;

		return $attr;

	}
}
new FusionSC_SocialLinks();


/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_social_links() {
	$social_options = array(
		'name'      => esc_attr__( 'Social Links', 'fusion-builder' ),
		'shortcode' => 'fusion_social_links',
		'icon'      => 'fusiona-link',
		'params'    => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Boxed Social Icons', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'icons_boxed',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Yes', 'fusion-builder' )     => 'yes',
					esc_attr__( 'No', 'fusion-builder' )      => 'no',
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Social Icon Box Radius', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the radius of the boxed icons. In pixels (px), ex: 1px, or "round". ', 'fusion-builder' ),
				'param_name'  => 'icons_boxed_radius',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icons_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icons Color Type', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to get a boxed icons. Choose default for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'color_type',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' )       => '',
					esc_attr__( 'Custom Colors', 'fusion-builder' ) => 'custom',
					esc_attr__( 'Brand Colors', 'fusion-builder' )  => 'brand',
				),
				'default'     => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the color of social icons. ', 'fusion-builder' ),
				'param_name'  => 'icon_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Social Icon Custom Box Colors', 'fusion-builder' ),
				'description' => esc_attr__( 'Specify the box color of social icons. ', 'fusion-builder' ),
				'param_name'  => 'box_colors',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'icons_boxed',
						'value'    => 'no',
						'operator' => '!=',
					),
					array(
						'element'  => 'color_type',
						'value'    => 'brand',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Social Icon Tooltip Position', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose the display position for tooltips. Choose default for theme option selection.' ),
				'param_name'  => 'tooltip_placement',
				'value'       => array(
					esc_attr__( 'Default', 'fusion-builder' ) => '',
					esc_attr__( 'Top', 'fusion-builder' )     => 'top',
					esc_attr__( 'Bottom', 'fusion-builder' )  => 'bottom',
					esc_attr__( 'Left', 'fusion-builder' )    => 'left',
					esc_attr__( 'Right', 'fusion-builder' )   => 'Right',
				),
				'default'     => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Blogger Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Blogger link.', 'fusion-builder' ),
				'param_name'  => 'blogger',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Deviantart Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Deviantart link.', 'fusion-builder' ),
				'param_name'  => 'deviantart',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Digg Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Digg link.', 'fusion-builder' ),
				'param_name'  => 'digg',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dribbble Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dribbble link.', 'fusion-builder' ),
				'param_name'  => 'dribbble',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Dropbox Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Dropbox link.', 'fusion-builder' ),
				'param_name'  => 'dropbox',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Facebook Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Facebook link.', 'fusion-builder' ),
				'param_name'  => 'facebook',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Flickr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Flickr link.', 'fusion-builder' ),
				'param_name'  => 'flickr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Forrst Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Forrst link.', 'fusion-builder' ),
				'param_name'  => 'forrst',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Google+ Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Google+ link.', 'fusion-builder' ),
				'param_name'  => 'googleplus',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Instagram Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Instagram link.', 'fusion-builder' ),
				'param_name'  => 'instagram',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'LinkedIn Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom LinkedIn link.', 'fusion-builder' ),
				'param_name'  => 'linkedin',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Myspace Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Myspace link.', 'fusion-builder' ),
				'param_name'  => 'myspace',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'PayPal Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom PayPal link.', 'fusion-builder' ),
				'param_name'  => 'paypal',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Pinterest Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Pinterest link.', 'fusion-builder' ),
				'param_name'  => 'pinterest',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Reddit Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Reddit link.', 'fusion-builder' ),
				'param_name'  => 'reddit',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'RSS Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom RSS link.', 'fusion-builder' ),
				'param_name'  => 'rss',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Skype Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Skype link.', 'fusion-builder' ),
				'param_name'  => 'skype',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'SoundCloud Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom SoundCloud link.', 'fusion-builder' ),
				'param_name'  => 'soundcloud',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Spotify Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Spotify link.', 'fusion-builder' ),
				'param_name'  => 'spotify',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Tumblr Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Tumblr link.', 'fusion-builder' ),
				'param_name'  => 'tumblr',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Twitter Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Twitter link.', 'fusion-builder' ),
				'param_name'  => 'twitter',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Vimeo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Vimeo link.', 'fusion-builder' ),
				'param_name'  => 'vimeo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'VK Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom VK link.', 'fusion-builder' ),
				'param_name'  => 'vk',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Xing Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Xing link.', 'fusion-builder' ),
				'param_name'  => 'xing',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yahoo Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yahoo link.', 'fusion-builder' ),
				'param_name'  => 'yahoo',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Yelp Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Yelp link.', 'fusion-builder' ),
				'param_name'  => 'yelp',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Youtube Link', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert your custom Youtube link.', 'fusion-builder' ),
				'param_name'  => 'youtube',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Email Address', 'fusion-builder' ),
				'description' => esc_attr__( 'Insert an email address to display the email icon.', 'fusion-builder' ),
				'param_name'  => 'email',
				'value'       => '',
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Show Custom Social Icon', 'fusion-builder' ),
				'description' => esc_attr__( 'Show the custom social icon specified in Theme Options.', 'fusion-builder' ),
				'param_name'  => 'show_custom',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
			),
		),
	);
	$custom_social_networks = fusion_builder_get_custom_social_networks();
	if ( is_array( $custom_social_networks ) ) {
		$custom_networks = array();
		foreach ( $custom_social_networks as $key => $custom_network ) {
			$social_options['params'][] = array(
				'type'        => 'textfield',
				'heading'     => sprintf( esc_attr__( 'Custom %s Link', 'fusion-builder' ), $key + 1 ),
				'description' => esc_attr__( 'Insert your custom social link.', 'fusion-builder' ),
				'param_name'  => 'custom_' . $key,
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'show_custom',
						'value'    => 'yes',
						'operator' => '==',
					),
				),
			);
		}
	}
	$social_options['params'][] = array(
		'type'        => 'radio_button_set',
		'heading'     => esc_attr__( 'Alignment', 'fusion-builder' ),
		'description' => esc_attr__( "Select the icon's alignment.", 'fusion-builder' ),
		'param_name'  => 'alignment',
		'value'       => array(
			esc_attr__( 'Text Flow', 'fusion-builder' ) => '',
			esc_attr__( 'Left', 'fusion-builder' )      => 'left',
			esc_attr__( 'Center', 'fusion-builder' )    => 'center',
			esc_attr__( 'Right', 'fusion-builder' )     => 'right',
		),
		'default'     => '',
	);
	$social_options['params'][] = array(
		'type'        => 'checkbox_button_set',
		'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
		'param_name'  => 'hide_on_mobile',
		'value'       => fusion_builder_visibility_options( 'full' ),
		'default'     => fusion_builder_default_visibility( 'array' ),
		'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
	);
	$social_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
		'param_name'  => 'class',
		'value'       => '',
		'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
	);
	$social_options['params'][] = array(
		'type'        => 'textfield',
		'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
		'param_name'  => 'id',
		'value'       => '',
		'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
	);
	fusion_builder_map( $social_options );
}
add_action( 'fusion_builder_before_init', 'fusion_element_social_links' );
