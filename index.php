<?php
/**
 * Plugin Name:		   Bootstrap Flat File slider
 * Plugin URI:		   http://clariontechnologies.co.in
 * Description:		   Twitter Bootstrap based  professional WordPress  carousel slider plugin.
 * Version: 		   1.0.0
 * Author: 			   clarion 
 * Author URI: 		   http://clariontechnologies.co.in
 */

// Constant

define('PLUGIN_FOLDER_PATH',plugin_dir_path(__FILE__));

 // Add files for admin and frontend
 include(plugin_dir_path( __FILE__ ).'/libs/cpt.php' );
 include(plugin_dir_path( __FILE__ ).'/admin/slider-add-new.php' );
 include(plugin_dir_path( __FILE__ ).'/libs/slider-slider-view.php' );
 
 // Add scripts
 function slider_slider_enqueue_scripts() {
//Plugin Main CSS File.
 	 wp_enqueue_style('slider-bootstrap-css', plugins_url('assets/css/bootstrap.min.css', __FILE__ ) );
     wp_enqueue_style( 'slider-slider-main', plugins_url('assets/css/slider-slider-main.css', __FILE__ ) );
     wp_enqueue_script('slider-bootstrap-js', plugins_url('assets/js/bootstrap.min.js', __FILE__ ));
	 wp_enqueue_script('slider-validation-js', plugins_url('assets/js/validation.js', __FILE__ ));
  }

 //This hook ensures our scripts and styles are only loaded in the admin.
 add_action( 'wp_enqueue_scripts', 'slider_slider_enqueue_scripts' );

