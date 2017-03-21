<?php
/**
 * @package Fusion_Extension_Button
 */

/**
 * Button Extension
 *
 * Class for adding a Button element to the Fusion engine
 *
 * @since 1.0.0
 */
 
class FusionButton	{

	public function __construct() {
		
		//add button shortcode
		add_shortcode('fsn_button', array($this, 'button_shortcode'));
		
		//load button layout via AJAX
		add_action('wp_ajax_button_load_layout', array($this, 'load_button_layout'));
		
		//load saved button layout fields
		add_filter('fsn_element_params', array($this, 'load_saved_button_layout_fields'), 10, 3);
		
		//initialize button
		add_action('init', array($this, 'init_button'), 12);
				
		//add standard button layout
		add_filter('add_button_layout', array($this, 'standard_layout'));
		
	}
	
	/**
	 * Load Button Layout
	 *
	 * @since 1.0.0
	 */
	 
	public function load_button_layout() {
		//verify nonce
		check_ajax_referer( 'fsn-admin-edit-button', 'security' );
		
		//verify capabilities
		if ( !current_user_can( 'edit_post', intval($_POST['post_id']) ) )
			die( '-1' );
			
		global $fsn_button_layouts;
		$button_layout = sanitize_text_field($_POST['button_layout']);
		$response_array = array();
		
		if (!empty($fsn_button_layouts) && !empty($button_layout)) {
			$response_array = array();
			foreach($fsn_button_layouts[$button_layout]['params'] as $param) {						
				$param_value = '';
				$param['section'] = !empty($param['section']) ? $param['section'] : 'general';
				//check for dependency
				$dependency = !empty($param['dependency']) ? true : false;
				if ($dependency === true) {
					$depends_on_field = $param['dependency']['param_name'];
					$depends_on_not_empty = !empty($param['dependency']['not_empty']) ? $param['dependency']['not_empty'] : false;
					if (!empty($param['dependency']['value']) && is_array($param['dependency']['value'])) {
						$depends_on_value = json_encode($param['dependency']['value']);
					} else if (!empty($param['dependency']['value'])) {
						$depends_on_value = $param['dependency']['value'];
					} else {
						$depends_on_value = '';
					}
					$dependency_callback = !empty($param['dependency']['callback']) ? $param['dependency']['callback'] : '';
					$dependency_string = ' data-dependency-param="'. esc_attr($depends_on_field) .'"'. ($depends_on_not_empty === true ? ' data-dependency-not-empty="true"' : '') . (!empty($depends_on_value) ? ' data-dependency-value="'. esc_attr($depends_on_value) .'"' : '') . (!empty($dependency_callback) ? ' data-dependency-callback="'. esc_attr($dependency_callback) .'"' : '');
				}
				$param_output = '<div class="form-group button-layout'. ( !empty($param['class']) ? ' '. esc_attr($param['class']) : '' ) .'"'. ( $dependency === true ? $dependency_string : '' ) .'>';
					$param_output .= FusionCore::get_input_field($param, $param_value);
				$param_output .= '</div>';
				$response_array[] = array(
					'section' => $param['section'],
					'output' => $param_output
				);
			}
		}
		
		header('Content-type: application/json');
		
		echo json_encode($response_array);
		
		exit;
	}
	
	/**
	 * Load Saved Button Layout Fields
	 *
	 * @since 1.0.0
	 */
	 
	public function load_saved_button_layout_fields($params, $shortcode, $saved_values) {
	
		global $fsn_button_layouts;
		
		//load standard layout by default
		$saved_values['button-layout'] = !empty($saved_values['button-layout']) ? $saved_values['button-layout'] : 'standard';
		
		if ($shortcode == 'fsn_button' && !empty($saved_values['button-layout']) && array_key_exists($saved_values['button-layout'], $fsn_button_layouts)) {
			$saved_layout = $saved_values['button-layout'];
			$params_to_add = !empty($fsn_button_layouts[$saved_layout]['params']) ? $fsn_button_layouts[$saved_layout]['params'] : '';
			if (!empty($params_to_add)) {
				for ($i=0; $i < count($params_to_add); $i++) {
					if (empty($params_to_add[$i]['class'])) {
						$params_to_add[$i]['class'] = 'button-layout';
					} else {
						$params_to_add[$i]['class'] .= ' button-layout';
					}
				}
				//add layout params to initial load
				array_splice($params, 1, 0, $params_to_add);
			}
		}
		
		return $params;
	}
	
	/**
	 * Initialize Button
	 *
	 * @since 1.0.0
	 */
	 
	public function init_button() {
	
		//MAP SHORTCODE
		if (function_exists('fsn_map')) {						
			
			//define button layouts
			$button_layouts = array();
			
			//get layouts
			$button_layouts = apply_filters('add_button_layout', $button_layouts);
			
			//create button layouts global
			global $fsn_button_layouts;
			$fsn_button_layouts = $button_layouts;
			
			//pass layouts array to script
			wp_localize_script('fsn_button_admin', 'fsnButton', $button_layouts);
			
			//get button layout options
			if (!empty($button_layouts)) {
				$button_layout_options = array();
				$smart_supported = array();
				$layout_specific_params = array();
				foreach($button_layouts as $key => $value) {
					//create array of layouts for select layout dropdown
					$button_layout_options[$key] = $value['name'];
				}
				//add layout list items to global
				foreach($button_layouts as $button_layout) {
					foreach($button_layout['params'] as $button_layout_param) {
						if ($button_layout_param['type'] == 'custom_list') {
							global $fsn_custom_lists;	
							$fsn_custom_lists[$button_layout_param['id']]['parent'] = 'fsn_button';
							$fsn_custom_lists[$button_layout_param['id']]['params'] = $button_layout_param['item_params'];
						}
					}
				}
			}
									
			$params_array = array(		
				array(
					'type' => 'select',
					'options' => $button_layout_options,
					'param_name' => 'button_layout',
					'label' => __('Type', 'fusion-extension-button')
				)		
			);
			
			fsn_map(array(
				'name' => __('Button', 'fusion-extension-button'),
				'shortcode_tag' => 'fsn_button',
				'description' => __('Add button. Buttons can link to external or internal content.', 'fusion-extension-button'),
				'icon' => 'add_box',
				'disable_style_params' => array('text_align','text_align_xs','font_size','color'),
				'params' => $params_array
			));
		}
	}
	
	/**
	 * Button shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts The shortcode attributes.
	 * @param string $content The shortcode content.
	 */
	
	public function button_shortcode( $atts, $content ) {		
		extract( shortcode_atts( array(			
			'button_layout' => 'standard'
		), $atts ) );
		
		$output = '';
		
		if (!empty($button_layout)) {
			$callback_function = 'fsn_get_'. sanitize_text_field($button_layout) .'_button';
			$output .= '<div class="fsn-button '. esc_attr($button_layout) .' '. fsn_style_params_class($atts) .'">';
				$output .= call_user_func($callback_function, $atts, $content);
			$output .= '</div>';
		}
		
		return $output;
	}
	
	/**
	 * Standard Button layout
	 */
	 
	public function standard_layout($button_layouts) {
		
		$button_style_options = array(
			'btn btn-default' => __('Default', 'fusion-extension-button'),
			'btn btn-primary' => __('Primary', 'fusion-extension-button'),
			'btn btn-success' => __('Success', 'fusion-extension-button'),
			'btn btn-info' => __('Info', 'fusion-extension-button'),
			'btn btn-warning' => __('Warning', 'fusion-extension-button'),
			'btn btn-danger' => __('Danger', 'fusion-extension-button'),
			'btn btn-link' => __('Link', 'fusion-extension-button'),
			'none' => __('None', 'fusion-extension-button')
		);
		$button_style_options = apply_filters('fsn_button_style_options', $button_style_options);
		
		$button_size_options = array(
			'default' => __('Default', 'fusion-extension-button'),
			'btn-lg' => __('Large', 'fusion-extension-button'),
			'btn-sm' => __('Small', 'fusion-extension-button'),
			'btn-xs' => __('Extra Small', 'fusion-extension-button')
		);
		$button_size_options = apply_filters('fsn_button_size_options', $button_size_options);
		
		//standard button layout
		$standard_layout = array(
			'name' => __('Standard', 'fusion-extension-button'),
			'params' => array(
				array(
					'type' => 'button',
					'param_name' => 'button',
					'label' => __('Button', 'fusion-extension-button'),
					'help' => __('Link to external or internal content.', 'fusion-extension-button')
				),
				array(
					'type' => 'select',
					'options' => $button_style_options,
					'param_name' => 'button_style',
					'label' => __('Style', 'fusion-extension-button')
				),
				array(
					'type' => 'select',
					'options' => $button_size_options,
					'param_name' => 'button_size',
					'label' => __('Size', 'fusion-extension-button'),
					'dependency' => array(
						'param_name' => 'button_style',
						'value' => array('btn btn-default', 'btn btn-primary', 'btn btn-success', 'btn btn-info', 'btn btn-warning', 'btn btn-danger', 'btn btn-link')
					)
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'button_btn_block',
					'label' => __('Full Width Button', 'fusion-extension-button'),
					'help' => __('Make button span the full width of the Column.' , 'fusion-extension-button'),
					'dependency' => array(
						'param_name' => 'button_style',
						'value' => array('btn btn-default', 'btn btn-primary', 'btn btn-success', 'btn btn-info', 'btn btn-warning', 'btn btn-danger', 'btn btn-link')
					)
				)
			)
		);
		$button_layouts['standard'] = $standard_layout;
		
		return $button_layouts;
	}
 
}
 
$fsn_button = new FusionButton();

//STANDARD Button

//render button layout ** function name must follow fsn_get_[button layout key]_button
function fsn_get_standard_button($atts = false, $content = false) {
	extract( shortcode_atts( array(
		'button' => '',
		'button_style' => '',
		'button_size' => '',
		'button_btn_block' => ''
	), $atts ) );
	
	$output = '';
	
	if (!empty($button)) {
		$button_object = fsn_get_button_object($button);
		
		if (!empty($button_style) && $button_style != 'none') {
			$button_classes = $button_style;
			$button_classes .= !empty($button_size) && $button_size != 'default' ? ' '. $button_size : '';
			$button_classes .= !empty($button_btn_block) ? ' btn-block' : '';
		} else {
			$button_classes = '';
		}
		
		$output .= apply_filters('fsn_standard_button_output', '<a'. fsn_get_button_anchor_attributes($button_object, $button_classes) .'>'. esc_html($button_object['button_label']) .'</a>', $atts);
	}
	
	return $output;
}

?>