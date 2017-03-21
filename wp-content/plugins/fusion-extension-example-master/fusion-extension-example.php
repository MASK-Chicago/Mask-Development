<?php
/**
 * @package Fusion_Extension_Example
 */
 
/**
 * Plugin Name: Fusion : Extension - Example
 * Plugin URI: http://www.agencydominion.com/fusion/
 * Description: Example Extension Package for Fusion.
 * Version: 1.1.1
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
 * License: GPL2
 */
 
/**
 * FusionExtensionExample class.
 *
 * Class for initializing an instance of the Fusion Example Extension.
 *
 * @since 1.0.0
 */

class FusionExtensionExample	{ 
	public function __construct() {
						
		// Initialize the language files
		load_plugin_textdomain( 'fusion-extension-example', false, plugin_dir_url( __FILE__ ) . 'languages' );
		
		// Enqueue admin scripts and styles
		add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts_styles'));
		
		// Enqueue front end scripts and styles
		add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts_styles'));
		
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
			wp_enqueue_script( 'fsn_example_admin', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-example-admin.js', array('jquery'), '1.0.0', true );
			wp_enqueue_style( 'fsn_example_admin', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-example-admin.css', false, '1.0.0' );
		}
	}
	
	/**
	 * Enqueue JavaScript and CSS on Front End pages.
	 *
	 * @since 1.0.0
	 *
	 */
	 
	 public function front_enqueue_scripts_styles() {
		//plugin
		wp_register_script( 'fsn_example', plugin_dir_url( __FILE__ ) . 'includes/js/fusion-extension-example.js', array('jquery','fsn_core'), '1.0.0', true );
		wp_enqueue_style( 'fsn_example', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-example.css', false, '1.0.0' );
	}
	
}

$fsn_extension_example = new FusionExtensionExample();

//EXTENSIONS

//example
require_once('includes/extensions/example.php');

?>