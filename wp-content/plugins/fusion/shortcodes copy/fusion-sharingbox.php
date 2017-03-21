<?php

/**
 * Shortcode class.
 *
 * @package fusion-builder
 * @since 1.0
 */
class FusionSC_SharingBox {

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

		add_filter( 'fusion_attr_sharingbox-shortcode', array( $this, 'attr' ) );
		add_filter( 'fusion_attr_sharingbox-shortcode-tagline', array( $this, 'tagline_attr' ) );
		add_filter( 'fusion_attr_sharingbox-shortcode-social-networks', array( $this, 'social_networks_attr' ) );
		add_filter( 'fusion_attr_sharingbox-shortcode-icon', array( $this, 'icon_attr' ) );

		add_shortcode( 'fusion_sharing', array( $this, 'render' ) );

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
				'backgroundcolor'    => strtolower( FusionBuilder::get_theme_option( 'social_bg_color' ) ),
				'description'        => '',
				'color_type'         => '',
				'icon_colors'        => '',
				'box_colors'         => '',
				'icons_boxed'        => ( ! class_exists( 'Avada' ) || Avada()->settings->get( 'sharing_social_links_boxed' ) == 1 ) ? 'yes' : Avada()->settings->get( 'sharing_social_links_boxed' ),
				'icons_boxed_radius' => ( class_Exists( 'Avada_Sanitize' ) ) ? Avada_Sanitize::size( FusionBuilder::get_theme_option( 'sharing_social_links_boxed_radius' ) ) : '0px',
				'link'               => '',
				'pinterest_image'    => '',
				'social_networks'    => $this->get_theme_options_settings(),
				'tagline'            => '',
				'tagline_color'      => strtolower( FusionBuilder::get_theme_option( 'sharing_box_tagline_text_color' ) ),
				'title'              => '',
				'tooltip_placement'  => strtolower( FusionBuilder::get_theme_option( 'sharing_social_links_tooltip_placement' ) ),
			), $args
		);

		$defaults['icons_boxed_radius'] = FusionBuilder::validate_shortcode_attr_value( $defaults['icons_boxed_radius'], 'px' );

		extract( $defaults );

		self::$args = $defaults;

		$use_brand_colors = false;
		if ( 'brand' == $color_type || ( '' == $color_type && class_exists( 'Avada' ) && 'brand' === Avada()->settings->get( 'sharing_social_links_color_type' ) ) ) {
			$use_brand_colors = true;
			// Get a list of all the available social networks.
			$social_icon_boxed_colors = Avada_Data::fusion_social_icons( false, true );
			$social_icon_boxed_colors['googleplus'] = array(
				'label' => 'Google+',
				'color' => '#dc4e41',
			);
			$social_icon_boxed_colors['mail'] = array(
				'label' => esc_attr__( 'Email Address', 'fusion-builder' ),
				'color' => '#000000',
			);

		} elseif ( '' == $color_type && class_exists( 'Avada' ) && 'custom' === Avada()->settings->get( 'social_links_color_type' ) ) {
			// Custom social icon colors from theme options.
			$icon_colors = explode( '|', strtolower( FusionBuilder::get_theme_option( 'sharing_social_links_icon_color' ) ) );
			$box_colors  = explode( '|', strtolower( FusionBuilder::get_theme_option( 'sharing_social_links_box_color' ) ) );
		} else {
			$icon_colors = explode( '|', $icon_colors );
			$box_colors  = explode( '|', $box_colors );
		}

		$num_of_icon_colors = count( $icon_colors );
		$num_of_box_colors  = count( $box_colors );
		$social_networks    = explode( '|', $social_networks );

		$icons = '';

		$social_networks_count = count( $social_networks );
		for ( $i = 0; $i < $social_networks_count; $i++ ) {
			if ( 1 == $num_of_icon_colors ) {
				if ( ! is_array( $icon_colors ) ) {
					$icon_colors = array( $icon_colors );
				}
				$icon_colors[ $i ] = $icon_colors[0];
			}

			if ( 1 == $num_of_box_colors ) {
				if ( ! is_array( $box_colors ) ) {
					$box_colors = array( $box_colors );
				}
				$box_colors[ $i ] = $box_colors[0];
			}

			$network = $social_networks[ $i ];

			if ( true == $use_brand_colors ) {
				$icon_options = array(
					'social_network' => $network,
					'icon_color'     => ( 'yes' == $icons_boxed ) ? '#ffffff' : $social_icon_boxed_colors[ $network ]['color'],
					'box_color'      => ( 'yes' == $icons_boxed ) ? $social_icon_boxed_colors[ $network ]['color'] : '',
				);

			} else {
				$icon_options = array(
					'social_network' => $network,
					'icon_color'     => $i < count( $icon_colors ) ? $icon_colors[ $i ] : '',
					'box_color'      => $i < count( $box_colors ) ? $box_colors[ $i ] : '',
				);
			}

			$icons .= '<a ' . FusionBuilder::attributes( 'sharingbox-shortcode-icon', $icon_options ) . '></a>';
		}

		$html = '<div ' . FusionBuilder::attributes( 'sharingbox-shortcode' ) . '>';
		$html .= '<h4 ' . FusionBuilder::attributes( 'sharingbox-shortcode-tagline' ) . '>' . $tagline . '</h4>';
		$html .= '<div ' . FusionBuilder::attributes( 'sharingbox-shortcode-social-networks' ) . '>';
		$html .= $icons;
		$html .= '</div>';
		$html .= '</div>';

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
			'class' => 'share-box fusion-sharing-box',
		) );

		if ( 'yes' == self::$args['icons_boxed'] ) {
			$attr['class'] .= ' boxed-icons';
		}

		if ( self::$args['backgroundcolor'] ) {
			$attr['style'] = 'background-color:' . self::$args['backgroundcolor'] . ';';

			if ( 'transparent' == self::$args['backgroundcolor'] || 0 == Avada_Color::new_color( self::$args['backgroundcolor'] )->alpha ) {
				$attr['style'] .= 'padding:0;';
			}
		}

		if ( self::$args['class'] ) {
			$attr['class'] .= ' ' . self::$args['class'];
		}

		if ( self::$args['id'] ) {
			$attr['id'] = self::$args['id'];
		}

		$attr['data-title']       = self::$args['title'];
		$attr['data-description'] = self::$args['description'];
		$attr['data-link']        = self::$args['link'];
		$attr['data-image']       = self::$args['pinterest_image'];

		return $attr;

	}

	/**
	 * Builds the attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function tagline_attr() {

		$attr = array(
			'class' => 'tagline',
		);

		if ( self::$args['tagline_color'] ) {
			$attr['style'] = 'color:' . self::$args['tagline_color'] . ';';
		}

		return $attr;

	}

	/**
	 * Builds the social networks attributes array.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function social_networks_attr() {

		$attr = array(
			'class' => 'fusion-social-networks',
		);

		if ( 'yes' == self::$args['icons_boxed'] ) {
			$attr['class'] .= ' boxed-icons';
		}

		if ( ! self::$args['tagline'] ) {
			$attr['style'] = 'text-align: inherit;';
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
	public function icon_attr( $args ) {

		$description = self::$args['description'];
		$link        = self::$args['link'];
		$title       = self::$args['title'];
		$image       = rawurlencode( self::$args['pinterest_image'] );

		$attr = array(
			'class' => 'fusion-social-network-icon fusion-tooltip fusion-' . $args['social_network'] . ' fusion-icon-' . $args['social_network'],
		);

		$social_link = '';
		switch ( $args['social_network'] ) {
			case 'facebook':
				$social_link = 'https://m.facebook.com/sharer.php?u=' . $link;
				if ( ! wp_is_mobile() ) {
					$social_link = 'http://www.facebook.com/sharer.php?m2w&s=100&p&#91;url&#93;=' . $link . '&p&#91;images&#93;&#91;0&#93;=' . wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ) . '&p&#91;title&#93;=' . rawurlencode( $title );
				}
				break;

			case 'twitter':
				$social_link = 'https://twitter.com/share?text=' . rawurlencode( html_entity_decode( $title, ENT_COMPAT, 'UTF-8' ) ) . '&url=' . rawurlencode( $link );
				break;
			case 'linkedin':
				$social_link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( $link ) . '&amp;title=' . rawurlencode( $title ) . '&amp;summary=' . rawurlencode( $description );
				break;
			case 'reddit':
				$social_link = 'http://reddit.com/submit?url=' . $link . '&amp;title=' . $title;
				break;
			case 'tumblr':
				$social_link = 'http://www.tumblr.com/share/link?url=' . rawurlencode( $link ) . '&amp;name=' . rawurlencode( $title ) . '&amp;description=' . rawurlencode( $description );
				break;
			case 'googleplus':
				$social_link     = 'https://plus.google.com/share?url=' . $link;
				$attr['onclick'] = 'javascript:window.open(this.href,\'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\');return false;';
				break;
			case 'pinterest':
				$social_link = 'http://pinterest.com/pin/create/button/?url=' . rawurlencode( $link ) . '&amp;description=' . rawurlencode( $description ) . '&amp;media=' . $image;
				break;
			case 'vk':
				$social_link = 'http://vkontakte.ru/share.php?url=' . rawurlencode( $link ) . '&amp;title=' . rawurlencode( $title ) . '&amp;description=' . rawurlencode( $description );
				break;
			case 'mail':
				$social_link = 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $link );
				break;
		}

		$attr['href']   = $social_link;
		$attr['target'] = ( FusionBuilder::get_theme_option( 'social_icons_new' ) && 'mail' != $args['social_network'] ) ? '_blank' : '_self';

		if ( '_blank' == $attr['target'] ) {
			$attr['rel'] = 'noopener noreferrer';
		}

		if ( FusionBuilder::get_theme_option( 'nofollow_social_links' ) ) {
			$attr['rel'] = 'nofollow';
		}

		$attr['style'] = ( $args['icon_color'] ) ? 'color:' . $args['icon_color'] . ';' : '';

		if ( isset( self::$args['icons_boxed'] ) && 'yes' == self::$args['icons_boxed'] && $args['box_color'] ) {
			$attr['style'] .= 'background-color:' . $args['box_color'] . ';border-color:' . $args['box_color'] . ';';
		}

		if ( 'yes' == self::$args['icons_boxed'] && self::$args['icons_boxed_radius'] || '0' === self::$args['icons_boxed_radius'] ) {
			if ( 'round' == self::$args['icons_boxed_radius'] ) {
				self::$args['icons_boxed_radius'] = '50%';
			}
			$attr['style'] .= 'border-radius:' . self::$args['icons_boxed_radius'] . ';';
		}

		$attr['data-placement'] = self::$args['tooltip_placement'];
		$tooltip = $args['social_network'];
		if ( 'googleplus' == $tooltip ) {
			$tooltip = 'Google+';
		}
		$attr['data-title'] = ucfirst( $tooltip );
		$attr['title']      = ucfirst( $tooltip );

		if ( 'none' != self::$args['tooltip_placement'] ) {
			$attr['data-toggle'] = 'tooltip';
		}

		return $attr;

	}

	/**
	 * Gets the options from the theme.
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	public function get_theme_options_settings() {

		$social_media = array();

		if ( FusionBuilder::get_theme_option( 'sharing_facebook' ) ) {
			$social_media[] = array(
				'network' => 'facebook',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_twitter' ) ) {
			$social_media[] = array(
				'network' => 'twitter',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_linkedin' ) ) {
			$social_media[] = array(
				'network' => 'linkedin',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_reddit' ) ) {
			$social_media[] = array(
				'network' => 'reddit',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_tumblr' ) ) {
			$social_media[] = array(
				'network' => 'tumblr',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_google' ) ) {
			$social_media[] = array(
				'network' => 'googleplus',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_pinterest' ) ) {
			$social_media[] = array(
				'network' => 'pinterest',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_vk' ) ) {
			$social_media[] = array(
				'network' => 'vk',
			);
		}

		if ( FusionBuilder::get_theme_option( 'sharing_email' ) ) {
			$social_media[] = array(
				'network' => 'mail',
			);
		}

		$networks = array();

		foreach ( $social_media as $network ) {
			$networks[] = $network['network'];
		}
		return implode( '|', $networks );

	}
}
new FusionSC_SharingBox();

/**
 * Map shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_element_sharing_box() {
	fusion_builder_map( array(
		'name'       => esc_attr__( 'Sharing Box', 'fusion-builder' ),
		'shortcode'  => 'fusion_sharing',
		'icon'       => 'fusiona-share2',
		'preview'    => FUSION_BUILDER_PLUGIN_DIR . 'js/previews/fusion-sharingbox-preview.php',
		'preview_id' => 'fusion-builder-block-module-sharingbox-preview-template',
		'params'     => array(
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Tagline', 'fusion-builder' ),
				'description' => esc_attr__( 'The title tagline that will display.', 'fusion-builder' ),
				'param_name'  => 'tagline',
				'value'       => 'Share This Story, Choose Your Platform!',
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Tagline Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the text color. Leave blank for theme option selection.', 'fusion-builder' ),
				'param_name'  => 'tagline_color',
				'value'       => '',
				'dependency'  => array(
					array(
						'element'  => 'tagline',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Background Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the background color. ', 'fusion-builder' ),
				'param_name'  => 'backgroundcolor',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Title', 'fusion-builder' ),
				'description' => esc_attr__( 'The post title that will be shared.', 'fusion-builder' ),
				'param_name'  => 'title',
				'value'       => '',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Link to Share', 'fusion-builder' ),
				'description' => esc_attr__( 'The link that will be shared.', 'fusion-builder' ),
				'param_name'  => 'link',
				'value'       => '',
			),
			array(
				'type'        => 'textarea',
				'heading'     => esc_attr__( 'Description', 'fusion-builder' ),
				'description' => esc_attr__( 'The description that will be shared.', 'fusion-builder' ),
				'param_name'  => 'description',
				'value'       => '',
			),
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
				'description' => esc_attr__( 'Choose the display position for tooltips. Choose default for theme option selection.', 'fusion-builder' ),
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
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Choose Image to Share on Pinterest.', 'fusion-builder' ),
				'param_name'  => 'pinterest_image',
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
add_action( 'fusion_builder_before_init', 'fusion_element_sharing_box' );
