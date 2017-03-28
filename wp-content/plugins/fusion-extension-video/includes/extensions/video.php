<?php
/**
 * @package Fusion_Extension_Video
 */

/**
 * Video Fusion Extension.
 *
 * Function for adding a Video element to the Fusion Engine
 *
 * @since 1.0.0
 */

/**
 * Map Shortcode
 */

add_action('init', 'fsn_init_video', 12);
function fsn_init_video() {
	
	if (function_exists('fsn_map')) {
				
		$video_src_options = array(
			'' => __('Choose video source.'),
			'youtube' => __('YouTube', 'fusion-extension-video'),
			'vimeo' => __('Vimeo', 'fusion-extension-video'),
			'self_hosted' => __('Self-Hosted', 'fusion-extension-video')
		);
		$video_src_options = apply_filters('fsn_video_src_options', $video_src_options);
				
		$video_params = array(
			array(
				'type' => 'select',
				'options' => $video_src_options,
				'param_name' => 'video_src',
				'label' => __('Video Source', 'fusion-extension-video')
			),
			array(
				'type' => 'text',
				'param_name' => 'youtube_url',
				'label' => __('YouTube Video Link', 'fusion-extension-video'),
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'youtube'
				)
			),
			array(
				'type' => 'text',
				'param_name' => 'vimeo_url',
				'label' => __('Vimeo Video Link', 'fusion-extension-video'),
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'vimeo'
				)
			),
			array(
				'type' => 'video',
				'param_name' => 'mp4_id',
				'label' => __('MP4 Video', 'fusion-extension-video'),
				'help' => __('MP4 format only.', 'fusion-extension-video'),
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'image',
				'param_name' => 'poster',
				'label' => __('Cover Image', 'fusion-extension-video'),
				'help' => __('Cover image should be same size as the video.', 'fusion-extension-video'),
				'section' => 'advanced',
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'controls',
				'label' => __('Controls', 'fusion-extension-video'),
				'section' => 'advanced',
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'autoplay',
				'label' => __('Auto-play', 'fusion-extension-video'),
				'section' => 'advanced',
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'loop',
				'label' => __('Loop', 'fusion-extension-video'),
				'section' => 'advanced',
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'checkbox',
				'param_name' => 'mute',
				'label' => __('Mute', 'fusion-extension-video'),
				'section' => 'advanced',
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			),
			array(
				'type' => 'button',
				'param_name' => 'video_button',
				'label' => __('Button', 'fusion-extension-video'),
				'help' => __('Link to external or internal content.', 'fusion-extension-video'),
				'dependency' => array(
					'param_name' => 'video_src',
					'value' => 'self_hosted'
				)
			)			
		);
		
		//filter video params
		$video_params = apply_filters('fsn_video_params', $video_params);
		
		fsn_map(array(
			'name' => __('Video', 'fusion-extension-video'),
			'shortcode_tag' => 'fsn_video',	
			'description' => __('Add video. Youtube, Vimeo, and Self-hosted videos are supported.', 'fusion-extension-video'),
			'icon' => 'play_circle_filled',
			'disable_style_params' => array('text_align','text_align_xs','font_size','color'),
			'params' => $video_params
		));
	}
}

/**
 * Output Shortcode
 */

function fsn_video_shortcode( $atts, $content ) {		
	extract( shortcode_atts( array(							
		'video_src' => '',
		'mp4_id' => '',
		'poster' => '',
		'controls' => '',
		'autoplay' => '',
		'loop' => '',
		'mute' => '',
		'video_button' => '',
		'youtube_url' => '',
		'vimeo_url' => ''
	), $atts ) );
	
	//plugin
	wp_enqueue_script('fsn_video');
	
	$output = '';
	
	$output .= '<div class="fsn-video '. esc_attr($video_src) .' '. fsn_style_params_class($atts) .'">';
		//action executed before the video output
		ob_start();
		do_action('fsn_before_video', $atts);
		$output .= ob_get_clean();
		
		switch($video_src) {
			case 'self_hosted':
				//videoJS
			 	wp_enqueue_script('video_js');
				
				// Add VideoJS Flash Fallback to footer
				add_action('wp_footer', 'fsn_video_js_flash_fallback', 99);
				
				$video_id = uniqid();
				
				if (!empty($mp4_id)) {
					$mp4_src = wp_get_attachment_url($mp4_id);
					//$video_metadata = wp_get_attachment_metadata($mp4_id);
					//$width = $video_metadata['width'];
					//$height = $video_metadata['height'];
				}
				if (!empty($poster)) {
					$poster_attrs = wp_get_attachment_image_src($poster, 'full');
				}
				
				$detect = new Mobile_Detect();
				if ($detect->isMobile()) {
					$controls = true;
					$use_native_controls = true;
				} else {
					$use_native_controls = false;
				}
				if (!empty($video_button)) {
					//get button
					$button_object = fsn_get_button_object($video_button);
					$output .= '<a'.fsn_get_button_anchor_attributes($button_object, 'video-button') .'>';
				}
				$output .= '<div class="embed-container">';
					$output .= '<video id="video_'. esc_attr($video_id) .'" class="video-js vjs-default-skin" preload="auto" width="auto" height="auto"'. (!empty($poster) ? ' poster="'. esc_attr($poster_attrs[0]) .'"' : '') . (!empty($controls) ? ' controls' : '') . (!empty($autoplay) ? ' autoplay' : '') . (!empty($loop) ? ' loop' : '') . (!empty($mute) ? ' muted' : '') . (empty($use_native_controls) ? ' data-setup="{}"' : '') .'>';
						$output .= '<source src="'. esc_url($mp4_src) .'" type="video/mp4" />';
					$output .= '</video>';
					$output .= !empty($video_button) && empty($autoplay) && empty($controls) ? '<span class="video-play-button"></span>' : '';
				$output .= '</div>';
				$output .= '<div class="video-fallback">';
					$output .= !empty($poster) ? '<img class="wp-post-image" src="'. esc_url($poster_attrs[0]) .'" alt="" width="'. esc_attr($poster_attrs[1]) .'" height="'. esc_attr($poster_attrs[2]) .'">': '';
					$output .= !empty($video_button) ? '<span class="video-play-button"></span>' : '';
				$output .= '</div>';
				if (!empty($video_button)) {
					$output .= '</a>';
				}
				break;
			case 'youtube':
				if (!empty($youtube_url) && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $youtube_url, $match)) {
			    	$id = $match[1];
					$output .= '<div class="embed-container">';
						$output .= '<iframe src="//www.youtube.com/embed/'. esc_attr($id) .'?enablejsapi=1&wmode=transparent" frameborder="0" allowfullscreen></iframe>';
					$output .= '</div>';
				}
				break;
			case 'vimeo':
				if (!empty($vimeo_url) && preg_match("/(?:https?:\/\/)?(?:www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/", $vimeo_url, $match)) {
			    	$id = $match[3];
					$output .= '<div class="embed-container">';
						$output .= '<iframe src="//player.vimeo.com/video/'. esc_attr($id) .'?color=ffffff&title=0&byline=0&portrait=0&api=1" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
					$output .= '</div>';
				}
				break;
		}
		//action executed after the video output
		ob_start();
		do_action('fsn_after_video', $atts);
		$output .= ob_get_clean();
		
	$output .= '</div>';
	
	return $output;
}
add_shortcode('fsn_video', 'fsn_video_shortcode');

?>