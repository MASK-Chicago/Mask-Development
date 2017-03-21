<?php
/**
 * @package Fusion_Extension_Divider
 */

/**
 * Divider Extension.
 *
 * Function for adding a Divider element to the Fusion Engine
 *
 * @since 1.0.0
 */

/**
 * Map Shortcode
 */

add_action('init', 'fsn_init_divider', 12);
function fsn_init_divider() {
 
	if (function_exists('fsn_map')) {
		
		fsn_map(array(
			'name' => __('Divider', 'fusion-extension-divider'),
			'shortcode_tag' => 'fsn_divider',
			'description' => __('Add divider. Displays a line with options for orientation, width, thickness, color, and opacity.', 'fusion-extension-divider'),
			'icon' => 'power_input',
			'disable_style_params' => array('text_align','text_align_xs','font_size','color'),
			'params' => array(
				array(
					'type' => 'select',
					'options' => array(
						'horizontal' => __('Horizontal', 'fusion-extension-divider'),
						'vertical' => __('Vertical', 'fusion-extension-divider'),
					),
					'param_name' => 'orientation',
					'label' => __('Orientation', 'fusion-extension-divider')
				),
				array(
					'type' => 'text',
					'param_name' => 'width',
					'label' => __('Width (1 - 12)', 'fusion-extension-divider'),
					'help' => __('Input number of columns divider will span. Max and default is 12.', 'fusion-extension-divider'),
					'dependency' => array(
						'param_name' => 'orientation',
						'value' => 'horizontal'
					)
				),
				array(
					'type' => 'text',
					'param_name' => 'offset',
					'label' => __('Offset (1 - 12)', 'fusion-extension-divider'),
					'help' => __('Input number of blank columns to the left of the divider.', 'fusion-extension-divider'),
					'dependency' => array(
						'param_name' => 'orientation',
						'value' => 'horizontal'
					)
				),
				array(
					'type' => 'text',
					'param_name' => 'height',
					'label' => __('Height', 'fusion-extension-divider'),
					'help' => __('e.g. 200px', 'fusion-extension-divider'),
					'dependency' => array(
						'param_name' => 'orientation',
						'value' => 'vertical'
					)
				),
				array(
					'type' => 'text',
					'param_name' => 'offset_top',
					'label' => __('Offset', 'fusion-extension-divider'),
					'help' => __('e.g. 100px', 'fusion-extension-divider'),
					'dependency' => array(
						'param_name' => 'orientation',
						'value' => 'vertical'
					)
				),
				array(
					'type' => 'text',
					'param_name' => 'thickness',
					'label' => __('Thickness', 'fusion-extension-divider'),
					'help' => __('e.g. 2px', 'fusion-extension-divider'),
					'section' => 'style'
				),
				array(
					'type' => 'colorpicker',
					'param_name' => 'border_color',
					'label' => __('Color', 'fusion-extension-divider'),
					'section' => 'style'
				),
				array(
					'type' => 'text',
					'param_name' => 'border_color_opacity',
					'label' => __('Color Opacity', 'fusion-extension-divider'),
					'help' => __('Value between 0 and 1.', 'fusion-extension-divider'),
					'section' => 'style'
				)
			)
		));
	}
}

/**
 * Output Shortcode
 */

function fsn_divider_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'orientation' => 'horizontal',
		'width' => '12',
		'offset' => false,
		'border_color' => '',
		'border_color_opacity' => '',
		'thickness' => '',
		'height' => '',
		'offset_top' => ''
	), $atts ) );
	
	$output = '';
	$style_rules = '';
	
	if ($orientation == 'horizontal') {
		//HORIZONTAL DIVIDER
		if (!empty($border_color)) {
			if (!empty($border_color_opacity)) { 
				$rgb = fsn_hex2rgb($border_color);	
				$style_rules .= 'border-color:'. $border_color .';border-color:rgba('. $rgb[0] .','. $rgb[1] .','. $rgb[2] .','. $border_color_opacity .');';
			} else {
				$style_rules .= 'border-color:'. $border_color .';';	
			}
		} else if (!empty($border_color_opacity)) {
			$style_rules .= 'opacity:'. $border_color_opacity .';';	
		}
		if (!empty($thickness)) {
			$style_rules .= 'border-width:'. $thickness .';';
		}
		$style = !empty($style_rules) ? ' style="'. esc_attr($style_rules) .'"' : '';
		$output .= '<div class="row">';
			$output .= '<div class="col-sm-'. esc_attr($width) . (!empty($offset) ? ' col-sm-offset-'. esc_attr($offset) : '') .'">';
				$output .= '<div class="fsn-divider '. fsn_style_params_class($atts) .'"><hr'. (!empty($style) ? $style : '') .'></div>';
			$output .= '</div>';
		$output .= '</div>';
	} else if ($orientation == 'vertical') {
		//VERTICAL DIVIDER
		if (!empty($border_color)) {
			if (!empty($border_color_opacity)) { 
				$rgb = fsn_hex2rgb($border_color);	
				$border_color = 'rgba('. $rgb[0] .','. $rgb[1] .','. $rgb[2] .','. $border_color_opacity .')';
				$style_rules .= 'background:'. $border_color .';background:rgba('. $rgb[0] .','. $rgb[1] .','. $rgb[2] .','. $border_color_opacity .');';	
			} else {
				$style_rules .= 'background:'. $border_color .';';	
			}
		} else if (!empty($border_color_opacity)) {
			$style_rules .= 'opacity:'. $border_color_opacity .';';	
		}
		if (!empty($thickness)) {
			$style_rules .= 'width:'. $thickness .';';
		}
		if (!empty($height) || !empty($offset_top)) {
			$style_rules .= (!empty($height) ? 'height:'. $height .';' : '') . (!empty($offset_top) ? 'top:'. $offset_top .';' : '');
		}
		$style = !empty($style_rules) ? ' style="'. esc_attr($style_rules) .'"' : '';
		$output .= '<div class="fsn-divider '. fsn_style_params_class($atts) .'"><span class="fsn-divider-vertical"'. (!empty($style) ? $style : '') .'></span></div>';
	}
	
	return $output;
}
add_shortcode('fsn_divider', 'fsn_divider_shortcode');
 
?>