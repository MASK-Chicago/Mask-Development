<?php
/**
 * @package Fusion_Extension_Blog
 */
/**
 * Plugin Name: Fusion : Extension - Blog
 * Plugin URI: http://www.agencydominion.com/fusion/
 * Description: Blog Extension Package for Fusion.
 * Version: 1.1.2
 * Author: Agency Dominion
 * Author URI: http://agencydominion.com
 * License: GPL2
 */
 
/**
 * FusionExtensionBlog class.
 *
 * Class for initializing an instance of the Fusion Blog Extension.
 *
 * @since 1.0.0
 */


class FusionExtensionBlog	{ 
	public function __construct() {
						
		// Initialize the language files
		load_plugin_textdomain( 'fusion-extension-blog', false, plugin_dir_url( __FILE__ ) . 'languages' );
		
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
		wp_enqueue_style( 'fsn_blog', plugin_dir_url( __FILE__ ) . 'includes/css/fusion-extension-blog.css', false, '1.0.0' );
	}
	
}

$fsn_extension_blog = new FusionExtensionBlog();

//EXTENSIONS

//blog
require_once('includes/extensions/blog.php');

?>