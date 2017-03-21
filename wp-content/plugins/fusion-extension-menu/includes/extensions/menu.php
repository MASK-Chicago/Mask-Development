<?php
/**
 * @package Fusion_Extension_Menu
 */

/**
 * Menu Extension.
 *
 * Function for adding a Menu element to the Fusion Engine.
 *
 * @since 1.0.0
 */

class FusionMenu	{

	public function __construct() {
		
		//add menu shortcode
		add_shortcode('fsn_menu', array($this, 'menu_shortcode'));
		
		//load menu layout via AJAX
		add_action('wp_ajax_menu_load_layout', array($this, 'load_menu_layout'));
		
		//load saved menu layout fields
		add_filter('fsn_element_params', array($this, 'load_saved_menu_layout_fields'), 10, 3);
		
		//initialize menu
		add_action('init', array($this, 'init_menu'), 12);
				
		//add main menu layout
		add_filter('add_menu_layout', array($this, 'main_layout'));
		
		//add stacked menu layout
		add_filter('add_menu_layout', array($this, 'stacked_layout'));
		
		//add inline menu layout
		add_filter('add_menu_layout', array($this, 'inline_layout'));
		
	}
	
	/**
	 * Load Menu Layout
	 *
	 * @since 1.0.0
	 */
	 
	public function load_menu_layout() {
		//verify nonce
		check_ajax_referer( 'fsn-admin-edit-menu', 'security' );
		
		//verify capabilities
		if ( !current_user_can( 'edit_post', intval($_POST['post_id']) ) )
			die( '-1' );
			
		global $fsn_menu_layouts;
		$menu_layout = sanitize_text_field($_POST['menu_layout']);
		$response_array = array();
		
		if (!empty($fsn_menu_layouts) && !empty($menu_layout)) {
			$response_array = array();
			foreach($fsn_menu_layouts[$menu_layout]['params'] as $param) {						
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
				$param_output = '<div class="form-group menu-layout'. ( !empty($param['class']) ? ' '. esc_attr($param['class']) : '' ) .'"'. ( $dependency === true ? $dependency_string : '' ) .'>';
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
	 * Load Saved Menu Layout Fields
	 *
	 * @since 1.0.0
	 */
	 
	public function load_saved_menu_layout_fields($params, $shortcode, $saved_values) {
	
		global $fsn_menu_layouts;
		
		if ($shortcode == 'fsn_menu' && !empty($saved_values['menu-layout']) && array_key_exists($saved_values['menu-layout'], $fsn_menu_layouts)) {
			$saved_layout = $saved_values['menu-layout'];
			$params_to_add = !empty($fsn_menu_layouts[$saved_layout]['params']) ? $fsn_menu_layouts[$saved_layout]['params'] : '';
			if (!empty($params_to_add)) {
				for ($i=0; $i < count($params_to_add); $i++) {
					if (empty($params_to_add[$i]['class'])) {
						$params_to_add[$i]['class'] = 'menu-layout';
					} else {
						$params_to_add[$i]['class'] .= ' menu-layout';
					}
				}
				//add layout params to initial load
				array_splice($params, 1, 0, $params_to_add);
			}
		}
		
		return $params;
	}
	
	/**
	 * Initialize Menu
	 *
	 * @since 1.0.0
	 */
	 
	public function init_menu() {
	
		//MAP SHORTCODE
		if (function_exists('fsn_map')) {							
			
			//define menu layouts
			$menu_layouts = array();
			
			//get layouts
			$menu_layouts = apply_filters('add_menu_layout', $menu_layouts);
			
			//create menu layouts global
			global $fsn_menu_layouts;
			$fsn_menu_layouts = $menu_layouts;
			
			//pass layouts array to script
			wp_localize_script('fsn_menu', 'fsnMenu', $menu_layouts);
			
			//get menu layout options
			if (!empty($menu_layouts)) {
				$menu_layout_options = array();
				$smart_supported = array();
				$layout_specific_params = array();
				$menu_layout_options[''] = 'Choose menu layout.';
				foreach($menu_layouts as $key => $value) {
					//create array of layouts for select layout dropdown
					$menu_layout_options[$key] = $value['name'];
				}
				//add layout list items to global
				foreach($menu_layouts as $menu_layout) {
					if (!empty($menu_layout['params'])) {
						foreach($menu_layout['params'] as $menu_layout_param) {
							if ($menu_layout_param['type'] == 'custom_list') {
								global $fsn_custom_lists;	
								$fsn_custom_lists[$menu_layout_param['id']]['parent'] = 'fsn_menu';
								$fsn_custom_lists[$menu_layout_param['id']]['params'] = $menu_layout_param['item_params'];
							}
						}
					}
				}
			}
									
			$params_array = array(		
				array(
					'type' => 'select',
					'options' => $menu_layout_options,
					'param_name' => 'menu_layout',
					'label' => __('Type', 'fusion-extension-menu'),
				)			
			);
			
			fsn_map(array(
				'name' => __('Menu', 'fusion-extension-menu'),
				'shortcode_tag' => 'fsn_menu',
				'description' => __('Add Menu. Choose the menu type to see additional configuration options.', 'fusion-extension-menu'),
				'icon' => 'menu',
				'params' => $params_array
			));
		}
	}
	
	/**
	 * Menu shortcode
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts The shortcode attributes.
	 * @param string $content The shortcode content.
	 */
	
	public function menu_shortcode( $atts, $content ) {		
		extract( shortcode_atts( array(			
			'menu_layout' => false
		), $atts ) );
		
		/**
		 * Enqueue Scripts
		 */
		 
		wp_enqueue_script('bootstrap_hover_dropdown');
		//plugin
		wp_enqueue_script('fsn_menu');
		
		$output = '';
		
		if (!empty($menu_layout)) {
			$output .= '<div class="fsn-menu '. esc_attr($menu_layout) .' '. fsn_style_params_class($atts) .'">';
				$callback_function = 'fsn_get_'. sanitize_text_field($menu_layout) .'_menu';
				$output .= call_user_func($callback_function, $atts, $content);
			$output .= '</div>';
		}
		
		return $output;
	}
	
	/**
	 * Main menu layout
	 */
	 
	public function main_layout($menu_layouts) {
		
		//main menu layout
		$menus = wp_get_nav_menus();
		$menu_options = array();
		if (!empty($menus)) {
			$menu_options[''] = __('Choose menu.', 'fusion-extension-menu');
			foreach ($menus as $menu) {
				$menu_options[$menu->term_id] = $menu->name;
			}
		}
		
		$main_layout = array(
			'name' => __('Main', 'fusion-extension-menu'),
			'params' => array(
				array(
					'type' => 'select',
					'options' => $menu_options,
					'param_name' => 'menu_id',
					'label' => __('Menu', 'fusion-extension-menu'),
					'help' => __('Existing Menus can be customized and new Menus can be added under Appearance > Menus in the Dashboard.', 'fusion-extension-menu')
				),
				array(
					'type' => 'image',
					'param_name' => 'mobile_logo_id',
					'label' => __('Mobile Logo', 'fusion-extension-menu'),
					'section' => 'advanced'
				),
				array(
					'type' => 'checkbox',
					'param_name' => 'mobile_search',
					'label' => __('Mobile Search Form', 'fusion-extension-menu'),
					'help' => __('Check to add search form to mobile menu dropdown.', 'fusion-extension-menu'),
					'section' => 'advanced'
				)
			)
		);
		$menu_layouts['main'] = $main_layout;
		
		return $menu_layouts;
	}
	
	/**
	 * Stacked menu layout
	 */
	 
	public function stacked_layout($menu_layouts) {
				
		$stacked_layout = array(
			'name' => __('Stacked', 'fusion-extension-menu'),
			'params' => array(
				array(
					'type' => 'custom_list',
					'param_name' => 'list_items',
					'id' => 'menu_layout_stacked', //each custom list requires a unique ID
					'item_params' => array(
						array(
							'type' => 'button',
							'param_name' => 'button',
							'label' => __('Button', 'fusion-extension-menu'),
							'help' => __('Link to external or internal content.', 'fusion-extension-menu')
						),
						array(
							'type' => 'text',
							'param_name' => 'user_classes',
							'label' => __('CSS Classes', 'fusion-extension-menu'),
							'help' => __('Separate multiple classes with a space.', 'fusion-extension-menu')
						)
					),
					'label' => __('Menu Items', 'fusion-extension-menu'),
					'help' => __('Drag-and-drop blocks to re-order.', 'fusion-extension-menu'),
				)
			)
		);
		$menu_layouts['stacked'] = $stacked_layout;
		
		return $menu_layouts;
	}
	
	/**
	 * Inline menu layout
	 */
	 
	public function inline_layout($menu_layouts) {
		
		$button_style_options = array(
			'btn btn-default' => __('Default', 'fusion-extension-menu'),
			'btn btn-primary' => __('Primary', 'fusion-extension-menu'),
			'btn btn-success' => __('Success', 'fusion-extension-menu'),
			'btn btn-info' => __('Info', 'fusion-extension-menu'),
			'btn btn-warning' => __('Warning', 'fusion-extension-menu'),
			'btn btn-danger' => __('Danger', 'fusion-extension-menu'),
			'btn btn-link' => __('Link', 'fusion-extension-menu'),
			'none' => __('None', 'fusion-extension-menu')
		);
		$button_style_options = apply_filters('fsn_button_style_options', $button_style_options);
		
		$button_size_options = array(
			'default' => __('Default', 'fusion-extension-menu'),
			'btn-lg' => __('Large', 'fusion-extension-menu'),
			'btn-sm' => __('Small', 'fusion-extension-menu'),
			'btn-xs' => __('Extra Small', 'fusion-extension-menu')	
		);
		$button_size_options = apply_filters('fsn_button_size_options', $button_size_options);
				
		$inline_layout = array(
			'name' => __('Inline', 'fusion-extension-menu'),
			'params' => array(
				array(
					'type' => 'custom_list',
					'param_name' => 'list_items',
					'id' => 'menu_layout_inline', //each custom list requires a unique ID
					'item_params' => array(
						array(
							'type' => 'button',
							'param_name' => 'button',
							'label' => __('Button', 'fusion-extension-menu'),
							'help' => __('Link to external or internal content.', 'fusion-extension-menu')
						),
						array(
							'type' => 'select',
							'options' => $button_style_options,
							'param_name' => 'button_style',
							'label' => __('Style', 'fusion-extension-menu')
						),
						array(
							'type' => 'select',
							'options' => $button_size_options,
							'param_name' => 'button_size',
							'label' => __('Size', 'fusion-extension-menu'),
							'dependency' => array(
								'param_name' => 'button_style',
								'value' => array('btn btn-default', 'btn btn-primary', 'btn btn-success', 'btn btn-info', 'btn btn-warning', 'btn btn-danger', 'btn btn-link')
							)
						),
						array(
							'type' => 'text',
							'param_name' => 'user_classes',
							'label' => __('CSS Classes', 'fusion-extension-menu'),
							'help' => __('Separate multiple classes with a space.', 'fusion-extension-menu')
						)
					),
					'label' => __('Menu Items', 'fusion-extension-menu'),
					'help' => __('Drag-and-drop blocks to re-order.', 'fusion-extension-menu'),
				)
			)
		);
		$menu_layouts['inline'] = $inline_layout;
		
		return $menu_layouts;
	}
	
}
	
$fsn_menu = new FusionMenu();

//Main Menu

//render menu layout ** function name must follow fsn_get_[menu layout key]_menu
function fsn_get_main_menu($atts = false, $content = false) {
	extract( shortcode_atts( array(
		'menu_id' => '',
		'mobile_logo_id' => '',
		'mobile_search' => ''
	), $atts ) );
	
	$output = '';
	if (!empty($menu_id)) {
		$unique_id = uniqid();
		if (!empty($mobile_logo_id)) {
			$mobile_logo_attrs = wp_get_attachment_image_src($mobile_logo_id, 'full');
			$mobile_brand = '<img src="'. esc_url($mobile_logo_attrs[0]) .'" alt="'. esc_attr(get_bloginfo('name')) .'">';
		} else {
			$mobile_brand = get_bloginfo('name');
		}
		$mobile_brand = apply_filters('fsn_menu_main_mobile_brand', $mobile_brand);
		
		ob_start();
		?>
		<nav class="navbar navbar-default" role="navigation">
            <?php do_action('fsn_before_main_menu', $atts, $content); ?>
            <div class="navbar-header">
            	<a class="navbar-brand visible-xs<?php echo esc_attr(!empty($mobile_logo_id)) ? ' brand-image' : '' ?>" href="<?php echo esc_url(home_url()); ?>"><?php echo $mobile_brand; ?></a>
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-nav-collapse-<?php echo esc_attr($unique_id); ?>">
                    <span class="sr-only"><?php _e('Toggle navigation', 'fusion-extension-menu'); ?></span>
                    <span class="icon-collapsed"><i class="material-icons">&#xE5D2;</i></span>
                    <span class="icon-collapse"><i class="material-icons">&#xE5CD;</i></span>
                </button>                
            </div>
            <div id="main-nav-collapse-<?php echo esc_attr($unique_id); ?>" class="collapse navbar-collapse">
                <?php do_action('fsn_prepend_main_menu', $atts, $content); ?>
                <?php
                wp_nav_menu(array(
					'menu' => $menu_id,
					'container' => false,
					'items_wrap' => '<ul class="nav navbar-nav">%3$s</ul>',
					'fallback_cb' => false,
					'walker' => new Bootstrap_MultiCol_Dropdown_Walker_Nav_Menu
				));
				?>
				<?php if (!empty($mobile_search)) : ?>
					<form role="search" method="get" class="visible-xs mobile-searchform clearfix" action="<?php echo esc_url(home_url('/')); ?>">
						<input type="text" name="s" class="search-query form-control" placeholder="<?php _e('Search...', 'fusion-extension-menu'); ?>">
						<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
					</form>
				<?php endif; ?>
				<?php do_action('fsn_append_main_menu', $atts, $content); ?>
            </div>
            <?php do_action('fsn_after_main_menu', $atts, $content); ?>
	    </nav>
		<?php
		$output .= ob_get_clean();
	}
	
	return $output;	
}

/**
 * Multi-column Nav Menu Walker
 */
 
//custom build for submenus to support Bootstrap dropdowns
class Bootstrap_MultiCol_Dropdown_Walker_Nav_Menu extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$submenu = ($depth > 0) ? ' sub-menu' : '';
		$output	   .= "\n$indent<div class=\"dropdown-menu$submenu depth_$depth multi-col-nav-container\"><div class=\"container-fluid\"><div class=\"row\"><div class=\"multi-col-nav\"><ul class=\"dropdown-menu\">\n";
	}
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul></div></div></div></div>\n";
	}
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		$li_attributes = '';
		$class_names = $value = '';
 
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		// managing divider: add divider class to an element to get a divider before it.
		$divider_class_position = array_search('divider', $classes);
		if($divider_class_position !== false){
			$output .= "<li class=\"divider\"></li>\n";
			unset($classes[$divider_class_position]);
		}
		
		// managing column: add column class to an element to get a column before it.
		$column_class_position = array_search('column', $classes);
		if($column_class_position !== false){
			$output .= "</ul></div><div class=\"multi-col-nav\"><ul class=\"dropdown-menu\">\n";
			unset($classes[$column_class_position]);
		}
		
		// managing headings
		$heading_class_position = array_search('h5', $classes);
		if($heading_class_position !== false){
			$is_label = true;
		} else {
			$is_label = false;
		}
		
		$classes[] = ($args->walker->has_children) ? 'dropdown' : '';
		$classes[] = ($item->current || $item->current_item_ancestor) ? 'active' : '';
		$classes[] = 'menu-item-' . $item->ID;
		if($depth && $args->walker->has_children){
			$classes[] = 'dropdown-submenu';
		}
		
 
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
 
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';
 
		$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';
 
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= ($args->walker->has_children) 	    ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';
 
		$item_output = $args->before;
		$item_output .= $is_label === true && $item->url == '#' ? '' : '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= ($depth == 0 && $args->walker->has_children) ? '<b class="caret"></b>' : '';
		$item_output .= $is_label === true && $item->url == '#' ? '' : '</a>';
		$item_output .= $args->after;
 
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

//Stacked Menu

//render menu wrapper ** function name must follow fsn_get_[menu layout key]_menu
function fsn_get_stacked_menu($atts = false, $content = false) {
	
	$output = '<ul class="list-unstyled">'. do_shortcode($content) .'</ul>';
	return $output;
		
}

//render list item ** function name must follow fsn_get_[list_id]_list_item
function fsn_get_menu_layout_stacked_list_item($atts = false, $content = false) {
	extract( shortcode_atts( array(
		'button' => '',
		'user_classes' => ''
	), $atts ) );
	
	$output = '';
	
	if (!empty($button)) {
		$button_object = fsn_get_button_object($button);
		$output .= '<li'. (!empty($user_classes) ? ' class="'. esc_attr($user_classes) .'"' : '') .'><a'. fsn_get_button_anchor_attributes($button_object) .'>'. esc_html($button_object['button_label']) .'</a></li>';
			
	}
		
	return $output;
}

//Inline Menu

//render menu wrapper ** function name must follow fsn_get_[menu layout key]_menu
function fsn_get_inline_menu($atts = false, $content = false) {
	
	$output = '<ul class="list-inline">'. do_shortcode($content) .'</ul>';
	return $output;
		
}

//render list item ** function name must follow fsn_get_[list_id]_list_item
function fsn_get_menu_layout_inline_list_item($atts = false, $content = false) {
	extract( shortcode_atts( array(
		'button' => '',
		'button_style' => '',
		'button_size' => '',
		'user_classes' => ''
	), $atts ) );
	
	$output = '';
	
	if (!empty($button)) {
		$button_object = fsn_get_button_object($button);
		if (!empty($button_style) && $button_style != 'none') {
			$button_classes = $button_style;
			$button_classes .= !empty($button_size) && $button_size != 'default' ? ' '. $button_size : '';
		} else {
			$button_classes = '';
		}
		$output .= '<li'. (!empty($user_classes) ? ' class="'. esc_attr($user_classes) .'"' : '') .'><a'. fsn_get_button_anchor_attributes($button_object, $button_classes) .'>'. esc_html($button_object['button_label']) .'</a></li>';
			
	}
		
	return $output;
}

?>