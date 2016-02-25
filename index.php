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

define('FFSB_SLIDER_FOLDER_PATH',plugin_dir_path(__FILE__));

 // Add files for admin and frontend
 include(plugin_dir_path( __FILE__ ).'/libs/cpt.php' );
 include(plugin_dir_path( __FILE__ ).'/admin/slider-add-new.php' );
 include(plugin_dir_path( __FILE__ ).'/libs/slider-slider-view.php' );
 
 // Add scripts
 function ffsb_slider_enqueue_scripts() {
//Plugin Main CSS File.
 	 wp_enqueue_style('ffsb-slider-bootstrap-css', plugins_url('assets/css/bootstrap.min.css', __FILE__ ) );
     wp_enqueue_style( 'ffsb-slider-slider-main', plugins_url('assets/css/slider-slider-main.css', __FILE__ ) );
     wp_enqueue_script('ffsb-slider-bootstrap-js', plugins_url('assets/js/bootstrap.min.js', __FILE__ ));
	 wp_enqueue_script('ffsb-slider-validation-js', plugins_url('assets/js/validation.js', __FILE__ ));
  }

 //This hook ensures our scripts and styles are only loaded in the admin.
 add_action( 'wp_enqueue_scripts', 'ffsb_slider_enqueue_scripts' );
 
 function ffsb_slider_activate() {
 
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/ffsb-slider';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }
}

register_activation_hook( __FILE__, 'ffsb_slider_activate' );

function ffsb_slider_deactivate() {
 
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir']."/ffsb-slider";
    if (! is_dir($upload_dir)) {
        throw new InvalidArgumentException("$upload_dir must be a directory");
    }
    if (substr($upload_dir, strlen($upload_dir) - 1, 1) != '/') {
        $upload_dir .= '/';
    }
    $files = glob($upload_dir . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($upload_dir);
}
 
register_deactivation_hook( __FILE__, 'ffsb_slider_deactivate' );

