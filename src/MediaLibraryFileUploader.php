<?php

class MediaLibraryFileUploader {
    public function __construct() {
        add_action('init', array($this, 'initialize'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_upload_file', array($this, 'handle_file_upload'));
    }

    public function initialize() {
        // Initialization code here
        add_action('admin_menu', array($this, 'add_admin_page'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('media-library-file-uploader-style', plugins_url('assets/css/style.css', __FILE__));
        wp_enqueue_script('media-library-file-uploader-script', plugins_url('assets/js/script.js', __FILE__), array('jquery'), null, true);
    }

    public function add_admin_page() {
        add_menu_page('Media Library File Uploader', 'File Uploader', 'manage_options', 'media-library-file-uploader', array($this, 'render_admin_page'));
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>Upload File to Media Library</h1>
            <form id="file-upload-form" method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('file_upload_nonce', 'security'); ?>
                <input type="file" id="file" name="file" /><br><br>
                <input type="text" id="title" name="title" placeholder="Title" /><br><br>
                <textarea id="description" name="description" placeholder="Description"></textarea><br><br>
                <button type="submit">Upload</button>
            </form>
            <div id="drop-area">
                <p>Drag and drop a file here</p>
            </div>
        </div>
        <?php
    }

    public function handle_file_upload() {
        // Check nonce for security
        check_ajax_referer('file_upload_nonce', 'security');

        // Check if a file is uploaded
        if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
            wp_send_json_error('No file uploaded or there was an upload error.');
        }

        // Sanitize and validate the title and description
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);

        // Handle the file upload
        $file = $_FILES['file'];
        $upload = wp_handle_upload($file, array('test_form' => false));

        if (isset($upload['error']) && $upload['error'] != 0) {
            wp_send_json_error($upload['error']);
        }

        // Prepare an array of post data for the attachment
        $attachment = array(
            'guid' => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title' => $title,
            'post_content' => $description,
            'post_status' => 'inherit'
        );

        // Insert the attachment into the media library
        $attachment_id = wp_insert_attachment($attachment, $upload['file']);

        if (is_wp_error($attachment_id)) {
            wp_send_json_error($attachment_id->get_error_message());
        }

        // Generate attachment metadata and update the database record
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Send success response
        wp_send_json_success('File uploaded successfully.');

        wp_die(); // This is required to terminate immediately and return a proper response
    }
}