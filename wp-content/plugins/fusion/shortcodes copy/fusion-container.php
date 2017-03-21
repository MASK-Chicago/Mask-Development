<?php

/**
 * Container shortcode.
 *
 * @since 1.0
 * @param array  $atts    The attributes array.
 * @param string $content The content.
 * @return string
 */
function fusion_builder_container( $atts, $content = '' ) {
	$atts = fusion_section_deprecated_args( $atts );
	extract( shortcode_atts( array(
			'hide_on_mobile'        => fusion_builder_default_visibility( 'string' ),
			'id'                    => '',
			'class'                 => '',

			// Background.
			'background_color'      => '',
			'background_image'      => '',
			'background_position'   => 'center center',
			'background_repeat'     => 'no-repeat',
			'background_parallax'   => 'none',
			'parallax_speed'        => '0.3',
			'opacity'               => '100',
			'break_parents'         => '0',
			'fade'                  => 'no',

			// Style.
			'hundred_percent'       => 'no',
			'padding_bottom'        => '',
			'padding_left'          => '',
			'padding_right'         => '',
			'padding_top'           => '',
			'border_color'          => '',
			'border_size'           => '',
			'border_style'          => 'solid',
			'equal_height_columns'  => 'no',
			'data_bg_height'        => '',
			'data_bg_width'         => '',
			'enable_mobile'         => 'no',
			'menu_anchor'           => '',
			'margin_top'            => '',
			'margin_bottom'         => '',

			// Video Background.
			'video_mp4'             => '',
			'video_webm'            => '',
			'video_ogv'             => '',
			'video_loop'            => 'yes',
			'video_mute'            => 'yes',
			'video_preview_image'   => '',
			'overlay_color'         => '',
			'overlay_opacity'       => '0.5',
			'video_url'             => '',
			'video_loop_refinement' => '',
			'video_aspect_ratio'    => '16:9',

		), $atts
	) );

	global $parallax_id;
	global $fusion_fwc_type;
	$fusion_fwc_type = array();

	$style = '';
	$classes = 'fusion-fullwidth fullwidth-box';
	$outer_html = '';

	// Video background.
	$video_bg = false;
	$video_src = '';

	// TODO: refactor this whole section.
	$c_page_id = FusionBuilder::get_current_page_id();

	$width_100 = false;
	$page_template = '';

	// Placeholder background color.
	if ( false !== strpos( $background_image, 'https://placehold.it/' ) ) {
		$dimensions = str_replace( 'x', '', str_replace( 'https://placehold.it/', '', $background_image ) );
		if ( is_numeric( $dimensions ) ) {
			$background_image = $background_image . '/333333/ffffff/';
		}
	}
	if ( function_exists( 'is_woocommerce' ) ) {
		if ( is_woocommerce() ) {
			$custom_fields = get_post_custom_values( '_wp_page_template', $c_page_id );
			$page_template = ( is_array( $custom_fields ) && ! empty( $custom_fields ) ) ? $custom_fields[0] : '';
		}
	}

	$border_color     = ( empty( $border_color ) && class_exists( 'Avada' ) ) ? Avada()->settings->get( 'full_width_border_color' ) : $border_color;
	$background_color = ( empty( $background_color ) && class_exists( 'Avada' ) ) ? Avada()->settings->get( 'full_width_bg_color' ) : $background_color;
	$border_size      = ( empty( $border_size ) && '0' != $border_size && class_exists( 'Avada' ) ) ? Avada()->settings->get( 'border_size' ) : $border_size;

	if ( ! empty( $video_mp4 ) ) {
		$video_src .= '<source src="' . $video_mp4 . '" type="video/mp4">';
		$video_bg = true;
	}

	if ( ! empty( $video_webm ) ) {
		$video_src .= '<source src="' . $video_webm . '" type="video/webm">';
		$video_bg = true;
	}

	if ( ! empty( $video_ogv ) ) {
		$video_src .= '<source src="' . $video_ogv . '" type="video/ogg">';
		$video_bg = true;
	}

	if ( ! empty( $video_url ) ) {
		$video_bg = true;
	}

	if ( true == $video_bg ) {

		$classes .= ' video-background';

		if ( ! empty( $video_url ) ) {
			$video_url = fusion_builder_get_video_provider( $video_url );

			if ( 'youtube' == $video_url['type'] ) {
				$outer_html .= "<div style='visibility: hidden' id='video-" . $parallax_id ++ . "' data-youtube-video-id='" . $video_url['id'] . "' data-mute='" . $video_mute . "' data-loop='" . ( 'yes' == $video_loop ? 1 : 0 ) . "' data-loop-adjustment='" . $video_loop_refinement . "' data-video-aspect-ratio='" . $video_aspect_ratio . "' data-overlay-opacity='" . $overlay_opacity . "'><div id='video-" . $parallax_id ++ . "-inner'></div></div>";
			} elseif ( 'vimeo' == $video_url['type'] ) {
				$outer_html .= '<div id="video-' . $parallax_id . '" data-vimeo-video-id="' . $video_url['id'] . '" data-mute="' . $video_mute . '" data-video-aspect-ratio="' . $video_aspect_ratio . '" style="visibility:hidden;"><iframe id="video-iframe-' . $parallax_id . '" src="//player.vimeo.com/video/' . $video_url['id'] . '?api=1&player_id=video-iframe-' . $parallax_id ++ . '&html5=1&autopause=0&autoplay=1&badge=0&byline=0&loop=' . ( 'yes' == $video_loop ? '1' : '0' ) . '&title=0" frameborder="0"></iframe></div>';
			}
		} else {
			$video_attributes = 'preload="auto" autoplay';

			if ( 'yes' == $video_loop ) {
				$video_attributes .= ' loop';
			}

			if ( 'yes' == $video_mute ) {
				$video_attributes .= ' muted';
			}

			// Video Preview Image.
			if ( ! empty( $video_preview_image ) ) {
				$video_preview_image_style = 'background-image:url(' . $video_preview_image . ');';
				$outer_html .= '<div class="fullwidth-video-image" style="' . $video_preview_image_style . '"></div>';
			}

			$outer_html .= '<div class="fullwidth-video"><video ' . $video_attributes . '>' . $video_src . '</video></div>';
		}

		// Video Overlay.
		if ( ! empty( $overlay_color ) ) {
			$overlay_style = 'background-color:' . $overlay_color . ';';

			if ( '' != $overlay_opacity ) {
				$overlay_style .= 'opacity:' . $overlay_opacity . ';';
			}

			$outer_html .= '<div class="fullwidth-overlay" style="' . $overlay_style . '"></div>';
		}
	}

	// Background.
	if ( ! empty( $background_color ) && ! ( 'yes' === $fade && ! empty( $background_image ) && false === $video_bg ) ) {
		$style .= 'background-color: ' . esc_attr( $background_color ) . ';';
	}

	if ( ! empty( $background_image ) && 'yes' != $fade ) {
		$style .= 'background-image: url("' . esc_url_raw( $background_image ) . '");';
	}

	if ( ! empty( $background_position ) ) {
		$style .= 'background-position: ' . esc_attr( $background_position ) . ';';
	}

	if ( ! empty( $background_repeat ) ) {
		$style .= 'background-repeat: ' . esc_attr( $background_repeat ) . ';';
	}

	// Get correct container padding.
	$paddings = array( 'top', 'right', 'bottom', 'left' );

	foreach ( $paddings as $padding ) {
		$padding_name = 'padding_' . $padding;

		if ( '' === ${$padding_name} ) {
			$is_hundred_percent_template = FusionBuilder::is_hundred_percent_template();

			// TO padding.
			if ( $is_hundred_percent_template ) {
				${$padding_name} = FusionBuilder::get_theme_option( 'container_padding_100', $padding );
			} else {
				${$padding_name} = FusionBuilder::get_theme_option( 'container_padding_default', $padding );
			}
		}

		// Fall back to px if no unit is set.
		if ( ${$padding_name} && false === strpos( ${$padding_name}, '%' ) && false === strpos( ${$padding_name}, 'px' ) ) {
			${$padding_name} .= 'px';
		}

		// Add padding to style.
		if ( ! empty( ${$padding_name} ) ) {
			$style .= 'padding-' . $padding . ':' . esc_attr( fusion_builder_check_value( ${$padding_name} ) ) . ';';
		}
	}

	// Margin; for separator conversion only.
	if ( ! empty( $margin_bottom ) ) {
		$style .= 'margin-bottom: ' . esc_attr( fusion_builder_check_value( $margin_bottom ) ) . ';';
	}

	if ( ! empty( $margin_top ) ) {
		$style .= 'margin-top: ' . esc_attr( fusion_builder_check_value( $margin_top ) ) . ';';
	}

	// Border.
	if ( ! empty( $border_size ) ) {
		$style .= 'border-top-width:' . esc_attr( FusionBuilder::validate_shortcode_attr_value( $border_size, 'px' ) ) . ';';
		$style .= 'border-bottom-width:' . esc_attr( FusionBuilder::validate_shortcode_attr_value( $border_size, 'px' ) ) . ';';
		$style .= 'border-color:' . esc_attr( $border_color ) . ';';
		$style .= 'border-top-style:' . esc_attr( $border_style ) . ';';
		$style .= 'border-bottom-style:' . esc_attr( $border_style ) . ';';
	}

	// Fading Background.
	if ( 'yes' === $fade && ! empty( $background_image ) && false === $video_bg ) {
		$bg_type    = 'faded';
		$fade_style = '';
		$classes .= ' faded-background';

		if ( $background_parallax ) {
			$fade_style .= 'background-attachment:' . $background_parallax . ';';
		}

		if ( $background_color ) {
			$fade_style .= 'background-color:' . $background_color . ';';
		}

		if ( $background_image ) {
			$fade_style .= 'background-image: url(' . $background_image . ');';
		}

		if ( $background_position ) {
			$fade_style .= 'background-position:' . $background_position . ';';
		}

		if ( $background_repeat ) {
			$fade_style .= 'background-repeat:' . $background_repeat . ';';
		}

		if ( 'no-repeat' === $background_repeat ) {
			$fade_style .= '-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;';
		}

		$outer_html .= '<div class="fullwidth-faded" style="' . $fade_style . '"></div>';
	}

	if ( ! empty( $background_image ) && false == $video_bg ) {
		if ( 'no-repeat' == $background_repeat ) {
			$style .= '-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;';
		}
	}

	// Parallax.
	$parallax_helper = '';
	if ( false === $video_bg && ! empty( $background_image ) ) {
		$parallax_data  = '';
		$parallax_data .= ' data-bg-align="' . esc_attr( $background_position ) . '"';
		$parallax_data .= ' data-direction="' . $background_parallax . '"';
		$parallax_data .= ' data-mute="' . ( 'mute' == $video_mute ? 'true' : 'false' ) . '"';
		$parallax_data .= ' data-opacity="' . esc_attr( $opacity ) . '"';
		$parallax_data .= ' data-velocity="' . esc_attr( (float) $parallax_speed * -1 ) . '"';
		$parallax_data .= ' data-mobile-enabled="' . ( ( 'yes' === $enable_mobile ) ? 'true' : 'false' ) . '"';
		$parallax_data .= ' data-break_parents="' . esc_attr( $break_parents ) . '"';
		$parallax_data .= ' data-bg-image="' . esc_attr( $background_image ) . '"';
		$parallax_data .= ' data-bg-repeat="' . esc_attr( isset( $background_repeat ) && 'no-repeat' != $background_repeat ? 'true' : 'false' ) . '"';

		$parallax_data .= ' data-bg-height="' . esc_attr( $data_bg_height ) . '"';
		$parallax_data .= ' data-bg-width="' . esc_attr( $data_bg_width ) . '"';

		if ( 'none' != $background_parallax && 'fixed' != $background_parallax ) {
			$parallax_helper = '<div class="fusion-bg-parallax" ' . $parallax_data . '></div>';
		}

		// Parallax css class.
		if ( ! empty( $background_parallax ) ) {
			$classes .= " fusion-parallax-{$background_parallax}";
		}

		if ( $background_parallax ) {
			$style .= 'background-attachment:' . $background_parallax . ';';
		}
	}

	// Custom CSS class.
	if ( ! empty( $class ) ) {
		$classes .= " {$class}";
	}

	if ( ( class_exists( 'Avada' ) && '100%' == Avada()->settings->get( 'site_width' ) ) ||
		is_page_template( '100-width.php' ) ||
		is_page_template( 'blank.php' ) ||
		( '1' == FusionBuilder::get_page_option( 'portfolio_width_100', 'portfolio_width_100', $c_page_id ) || 'yes' == FusionBuilder::get_page_option( 'portfolio_width_100', 'portfolio_width_100', $c_page_id ) && 'avada_portfolio' == get_post_type( $c_page_id ) ) ||
		'100-width.php' == $page_template ) {
		$width_100 = true;
	}

	// Hundred percent.
	$classes .= ( 'yes' == $hundred_percent ) ? ' hundred-percent-fullwidth' : ' nonhundred-percent-fullwidth';
	$fusion_fwc_type['content'] = ( 'yes' == $hundred_percent ) ? 'fullwidth' : 'contained';
	$fusion_fwc_type['width_100_percent'] = $width_100;
	$fusion_fwc_type['padding'] = array( 'left' => $padding_left, 'right' => $padding_right );

	// Equal column height.
	if ( 'yes' == $equal_height_columns ) {
		$classes .= ' fusion-equal-height-columns';
	}

	// Visibility classes.
	$classes = fusion_builder_visibility_atts( $hide_on_mobile, $classes );

	// CSS inline style.
	$style = ! empty( $style ) ? " style='{$style}'" : '';

	// Custom CSS ID.
	$id = ( '' !== $id ) ? 'id="' . esc_attr( $id ) . '"' : '';

	$output = $parallax_helper . '<div ' . $id . ' class="' . $classes . '" ' . $style . '>' . $outer_html . do_shortcode( fusion_builder_fix_shortcodes( $content ) ) . '</div>';

	// Menu anchor.
	if ( ! empty( $menu_anchor ) ) {
		$output = '<div id="' . $menu_anchor . '">' . $output . '</div>';
	}

	$fusion_fwc_type = array();

	return $output;
}
add_shortcode( 'fusion_builder_container', 'fusion_builder_container' );

/**
 * Map Column shortcode to Fusion Builder.
 *
 * @since 1.0
 */
function fusion_builder_add_section() {
	fusion_builder_map( array(
		'name'              => esc_attr__( 'Container', 'fusion-builder' ),
		'shortcode'         => 'fusion_builder_container',
		'hide_from_builder' => true,
		'params'            => array(
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( '100% Interior Content Width', 'fusion-builder' ),
				'description' => esc_attr__( 'Select if the interior content is contained to site width or 100% width.', 'fusion-builder' ),
				'param_name'  => 'hundred_percent',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Set Columns to Equal Height', 'fusion-builder' ),
				'description' => esc_attr__( 'Select to set all columns that are used inside the container to have equal height.', 'fusion-builder' ),
				'param_name'  => 'equal_height_columns',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Name Of Menu Anchor', 'fusion-builder' ),
				'description' => esc_attr__( 'This name will be the id you will have to use in your one page menu.', 'fusion-builder' ),
				'param_name'  => 'menu_anchor',
				'value'       => '',
				'group'       => esc_attr__( 'General', 'fusion-builder' ),
			),
			array(
				'type'        => 'checkbox_button_set',
				'heading'     => esc_attr__( 'Container Visibility', 'fusion-builder' ),
				'param_name'  => 'hide_on_mobile',
				'value'       => fusion_builder_visibility_options( 'full' ),
				'default'     => fusion_builder_default_visibility( 'array' ),
				'description' => esc_attr__( 'Choose to show or hide the section on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
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
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Container Background Color', 'fusion-builder' ),
				'param_name'  => 'background_color',
				'value'       => '',
				'description' => esc_attr__( 'Controls the background color of the container element.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'default'     => '',
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
				'description' => esc_attr__( 'Choose the postion of the background image.', 'fusion-builder' ),
				'param_name'  => 'background_position',
				'value'       => array(
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
				'default'     => 'center center',
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
				'description' => esc_attr__( 'Choose how the background image repeats.', 'fusion-builder' ),
				'param_name'  => 'background_repeat',
				'value'       => array(
					esc_attr__( 'No Repeat', 'fusion-builder' )                          => 'no-repeat',
					esc_attr__( 'Repeat Vertically and Horizontally', 'fusion-builder' ) => 'repeat',
					esc_attr__( 'Repeat Horizontally', 'fusion-builder' )                => 'repeat-x',
					esc_attr__( 'Repeat Vertically', 'fusion-builder' )                  => 'repeat-y',
				),
				'default'     => 'no-repeat',
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
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Fading Animation', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose to have the background image fade and blur on scroll. WARNING: Only works for images.', 'fusion-builder' ),
				'param_name'  => 'fade',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
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
				'heading'     => esc_attr__( 'Background Parallax', 'fusion-builder' ),
				'description' => esc_attr__( 'Choose how the background image scrolls and responds. This does not work for videos and must be set to "No Parallax" for the video to show.', 'fusion-builder' ),
				'param_name'  => 'background_parallax',
				'value'       => array(
					esc_attr__( 'No Parallax (no effects)', 'fusion-builder' )                      => 'none',
					esc_attr__( 'Fixed (fixed on desktop, non-fixed on mobile)', 'fusion-builder' ) => 'fixed',
					esc_attr__( 'Up (moves up on desktop and mobile)', 'fusion-builder' )           => 'up',
					esc_attr__( 'Down (moves down on desktop and mobile)', 'fusion-builder' )       => 'down',
					esc_attr__( 'Left (moves left on desktop and mobile)', 'fusion-builder' )       => 'left',
					esc_attr__( 'Right (moves right on desktop and mobile)', 'fusion-builder' )     => 'right',
				),
				'default'     => 'none',
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
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Enable Parallax on Mobile', 'fusion-builder' ),
				'description' => esc_attr__( 'Works for up/down/left/right only. Parallax effects would most probably cause slowdowns when your site is viewed in mobile devices. If the device width is less than 980 pixels, then it is assumed that the site is being viewed in a mobile device.', 'fusion-builder' ),
				'param_name'  => 'enable_mobile',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'no',
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
				'type'        => 'range',
				'heading'     => esc_attr__( 'Parallax Speed', 'fusion-builder' ),
				'description' => esc_attr__( 'The movement speed, value should be between 0.1 and 1.0. A lower number means slower scrolling speed. Higher scrolling speeds will enlarge the image more.', 'fusion-builder' ),
				'param_name'  => 'parallax_speed',
				'value'       => '0.3',
				'min'         => '0',
				'max'         => '1',
				'step'        => '0.1',
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
				'type'        => 'uploadfile',
				'heading'     => esc_attr__( 'Video MP4 Upload', 'fusion-builder' ),
				'description' => esc_attr__( 'Video must be in a 16:9 aspect ratio. Add your WebM video file. WebM and MP4 format must be included to render your video with cross browser compatibility. OGV is optional.', 'fusion-builder' ),
				'param_name'  => 'video_mp4',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'uploadfile',
				'heading'     => esc_attr__( 'Video WebM Upload', 'fusion-builder' ),
				'description' => esc_attr__( 'Video must be in a 16:9 aspect ratio. Add your WebM video file. WebM and MP4 format must be included to render your video with cross browser compatibility. OGV is optional.', 'fusion-builder' ),
				'param_name'  => 'video_webm',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'uploadfile',
				'heading'     => esc_attr__( 'Video OGV Upload', 'fusion-builder' ),
				'description' => esc_attr__( 'Add your OGV video file. This is optional.', 'fusion-builder' ),
				'param_name'  => 'video_ogv',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'YouTube/Vimeo Video URL or ID', 'fusion-builder' ),
				'description' => esc_attr__( "Enter the URL to the video or the video ID of your YouTube or Vimeo video you want to use as your background. If your URL isn't showing a video, try inputting the video ID instead. Ads will show up in the video if it has them.", 'fusion-builder' ),
				'param_name'  => 'video_url',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_attr__( 'Video Aspect Ratio', 'fusion-builder' ),
				'description' => esc_attr__( 'The video will be resized to maintain this aspect ratio, this is to prevent the video from showing any black bars. Enter an aspect ratio here such as: "16:9", "4:3" or "16:10". The default is "16:9".', 'fusion-builder' ),
				'param_name'  => 'video_aspect_ratio',
				'value'       => '16:9',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'or'          => true,
				'dependency'  => array(
					array(
						'element'  => 'video_mp4',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_ogv',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_webm',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Loop Video', 'fusion-builder' ),
				'param_name'  => 'video_loop',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'or'          => true,
				'dependency'  => array(
					array(
						'element'  => 'video_mp4',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_ogv',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_webm',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Mute Video', 'fusion-builder' ),
				'param_name'  => 'video_mute',
				'value'       => array(
					esc_attr__( 'Yes', 'fusion-builder' ) => 'yes',
					esc_attr__( 'No', 'fusion-builder' )  => 'no',
				),
				'default'     => 'yes',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'or'          => true,
				'dependency'  => array(
					array(
						'element'  => 'video_mp4',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_ogv',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_webm',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'colorpicker',
				'heading'     => esc_attr__( 'Video Overlay Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Select a color to show over the video as an overlay. Hex color code, ex: #fff.', 'fusion-builder' ),
				'param_name'  => 'overlay_color',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'or'          => true,
				'dependency'  => array(
					array(
						'element'  => 'video_mp4',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_ogv',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_webm',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Video Overlay Opacity', 'fusion-builder' ),
				'description' => esc_attr__( 'Opacity ranges between 0 (transparent) and 1 (opaque). ex: .4 .', 'fusion-builder' ),
				'param_name'  => 'overlay_opacity',
				'value'       => '0.5',
				'min'         => '0.1',
				'max'         => '1',
				'step'        => '0.1',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'overlay_color',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'upload',
				'heading'     => esc_attr__( 'Video Preview Image', 'fusion-builder' ),
				'description' => esc_attr__( 'IMPORTANT: This field must be used for self hosted videos. Self hosted videos do not work correctly on mobile devices. The preview image will be seen in place of your video on older browsers or mobile devices.', 'fusion-builder' ),
				'param_name'  => 'video_preview_image',
				'value'       => '',
				'group'       => esc_attr__( 'Background', 'fusion-builder' ),
				'or'          => true,
				'dependency'  => array(
					array(
						'element'  => 'video_mp4',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_ogv',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_webm',
						'value'    => '',
						'operator' => '!=',
					),
					array(
						'element'  => 'video_url',
						'value'    => '',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'range',
				'heading'     => esc_attr__( 'Container Border Size', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border size of the container element. In pixels.', 'fusion-builder' ),
				'param_name'  => 'border_size',
				'value'       => '',
				'min'         => '0',
				'max'         => '50',
				'default'     => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'        => 'colorpickeralpha',
				'heading'     => esc_attr__( 'Container Border Color', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border color of the container element.', 'fusion-builder' ),
				'param_name'  => 'border_color',
				'value'       => '',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'default'     => '#eae9e9',
				'dependency'  => array(
					array(
						'element'  => 'border_size',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'radio_button_set',
				'heading'     => esc_attr__( 'Border Style', 'fusion-builder' ),
				'description' => esc_attr__( 'Controls the border style.', 'fusion-builder' ),
				'param_name'  => 'border_style',
				'value'       => array(
					esc_attr__( 'Solid', 'fusion-builder' )  => 'solid',
					esc_attr__( 'Dashed', 'fusion-builder' ) => 'dashed',
					esc_attr__( 'Dotted', 'fusion-builder' ) => 'dotted',
				),
				'default'     => 'solid',
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
				'dependency'  => array(
					array(
						'element'  => 'border_size',
						'value'    => '0',
						'operator' => '!=',
					),
				),
			),
			array(
				'type'        => 'dimension',
				'remove_from_atts' => true,
				'heading'     => esc_attr__( 'Margin', 'fusion-builder' ),
				'param_name'  => 'spacing',
				'value'       => array(
					'margin_top'    => '',
					'margin_bottom' => '',

				),
				'description' => esc_attr__( 'Spacing above and below the section. In pixels. Use a number without px.', 'fusion-builder' ),
				'group'       => esc_attr__( 'Design', 'fusion-builder' ),
			),
			array(
				'type'             => 'dimension',
				'remove_from_atts' => true,
				'heading'          => esc_attr__( 'Padding', 'fusion-builder' ),
				'description'      => esc_attr__( 'In pixels or percentage, ex: 10px or 10%.', 'fusion-builder' ),
				'param_name'       => 'dimensions',
				'value'            => array(
					'padding_top'    => '20px',
					'padding_right'  => '',
					'padding_bottom' => '20px',
					'padding_left'   => '',
				),
				'group'            => esc_attr__( 'Design', 'fusion-builder' ),
			),
		),
	) );
}
add_action( 'fusion_builder_before_init', 'fusion_builder_add_section' );
