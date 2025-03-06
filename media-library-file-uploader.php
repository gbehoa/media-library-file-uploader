<?php
/**
 * Plugin Name: Media Library File Uploader
 * Description: A WordPress plugin that allows users to upload files directly to the media library.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include the main plugin class
require_once plugin_dir_path( __FILE__ ) . 'src/MediaLibraryFileUploader.php';

// Initialize the plugin
function run_media_library_file_uploader() {
    $plugin = new MediaLibraryFileUploader();
    $plugin->run();
}

add_action( 'plugins_loaded', 'run_media_library_file_uploader' );
?>