<?php
/**
 * @package Fusion_Extension_Divider
 */
/**
 * Plugin Name: Fusion : Extension - Divider
 * Plugin URI: http://www.agencydominion.com/fusion/
 * Description: Divider Extension Package for Fusion.
 * Version: 1.1.3
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
 * License: GPL2
 */
 
/**
 * FusionExtensionDivider class.
 *
 * Class for initializing an instance of the Fusion Divider Extension.
 *
 * @since 1.0.0
 */


class FusionExtensionDivider	{ 
	public function __construct() {
						
		// Initialize the language files
		load_plugin_textdomain( 'fusion-extension-divider', false, plugin_dir_url( __FILE__ ) . 'languages' );
		
		// Enqueue front end scripts and styles
		add_action('wp_enqueue_scripts', array($this, 'front_enqueue_scripts_styles'));	
		
	}
	
	/**
	 * Enqueue JavaScript and CSS on Front End pages.
	 *
	 * @since 1.0.0
	 *
	 */
	 
	public function front_enqueue_scripts_styles() {
		wp_enqueue_style( 'fsn_divider', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-divider.css', false, '1.0.0' );
	}
}

$fsn_extension_divider = new FusionExtensionDivider();

//EXTENSIONS

//divider
require_once('includes/extensions/divider.php');

?>