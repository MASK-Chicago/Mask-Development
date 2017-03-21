<?php
/**
 * @package Fusion_Extension_Button
 */
 
/**
 * Plugin Name: Fusion : Extension - Button
 * Plugin URI: http://www.agencydominion.com/fusion/
 * Description: Button Extension Package for Fusion.
 * Version: 1.1.5
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
 * License: GPL2
 */
 
/**
 * FusionExtensionButton class.
 *
 * Class for initializing an instance of the Fusion Button Extension.
 *
 * @since 1.0.0
 */

class FusionExtensionButton	{ 
	public function __construct() {
						
		// Initialize the language files
		load_plugin_textdomain( 'fusion-extension-button', false, plugin_dir_url( __FILE__ ) . 'languages' );
		
		// Enqueue admin scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));
		
	}
	
	/**
	 * Enqueue JavaScript and CSS on Admin pages.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	 
	public function admin_enqueue_scripts_styles($hook_suffix) {
		global $post;
		
		$options = get_option('fsn_options');
		$fsn_post_types = !empty($options['fsn_post_types']) ? $options['fsn_post_types'] : '';
		
		// Editor scripts and styles
		if ( ($hook_suffix == 'post.php' || $hook_suffix == 'post-new.php') && (!empty($fsn_post_types) && is_array($fsn_post_types) && in_array($post->post_type, $fsn_post_types)) ) {
			wp_enqueue_script( 'fsn_button_admin', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-button-admin.js', array('jquery'), '1.0.0', true );
			wp_localize_script( 'fsn_button_admin', 'fsnExtButtonJS', array(
					'fsnEditButtonNonce' => wp_create_nonce('fsn-admin-edit-button')
				)
			);
			//add translation strings to script
			$translation_array = array(
				'error' => __('Oops, something went wrong. Please reload the page and try again.','fusion-extension-button'),
				'layout_change' => __('Changing the Button Layout will erase the current Button. Continue?','fusion-extension-button')
			);
			wp_localize_script('fsn_button_admin', 'fsnExtButtonL10n', $translation_array);
		}
	}

}

$fsn_extension_button = new FusionExtensionButton();

//EXTENSIONS

//Button
require_once('includes/extensions/button.php');

?>