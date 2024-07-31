<?php
/*
Plugin Name: PDF Viewer Role
Description: Creates a new user role "PDF Viewer" with specific permissions and a custom admin page for uploading/viewing/removing a PDF document.
Version: 1.6
Author: Your Name
*/

// Add new role on plugin activation
function pdf_viewer_role_add() {
    add_role(
        'pdf_viewer',
        'PDF Viewer',
        array(
            'read' => true,
        )
    );
}
register_activation_hook(__FILE__, 'pdf_viewer_role_add');

// Remove role on plugin deactivation
function pdf_viewer_role_remove() {
    remove_role('pdf_viewer');
}
register_deactivation_hook(__FILE__, 'pdf_viewer_role_remove');

// Add a custom admin menu
function pdf_viewer_add_admin_menu() {
    add_menu_page(
        'PDF Viewer',
        'PDF Viewer',
        'upload_files', // Only users who can upload files can access this menu
        'pdf-viewer',
        'pdf_viewer_admin_page',
        'dashicons-media-document',
        6
    );
}
add_action('admin_menu', 'pdf_viewer_add_admin_menu');

// Display the admin page
function pdf_viewer_admin_page() {
    if (isset($_POST['upload_pdf']) && !empty($_FILES['pdf_file']['name'])) {
        pdf_viewer_handle_file_upload();
    }

    if (isset($_POST['remove_pdf'])) {
        pdf_viewer_handle_file_removal();
    }
    
    $pdf_url = get_option('pdf_viewer_pdf_url');
    ?>
    <div class="wrap">
        <h1>Upload PDF Document</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="pdf_file" accept="application/pdf" required>
            <?php submit_button('Upload PDF', 'primary', 'upload_pdf'); ?>
        </form>
        <?php if ($pdf_url): ?>
            <h2>Uploaded PDF</h2>
            <a href="<?php echo esc_url(add_query_arg('view_pdf', '1', home_url())); ?>" target="_blank">View PDF</a>
            <form method="post">
                <?php submit_button('Remove PDF', 'secondary', 'remove_pdf'); ?>
            </form>
        <?php endif; ?>
        <h2>Instructions</h2>
        <p>To display the login form for accessing the PDF, create a new page and add the following shortcode:</p>
        <code>[pdf_viewer_login_form]</code>
    </div>
    <?php
}

// Handle the file upload
function pdf_viewer_handle_file_upload() {
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    $uploadedfile = $_FILES['pdf_file'];
    $upload_overrides = array('test_form' => false);

    $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        update_option('pdf_viewer_pdf_url', $movefile['url']);
        echo '<div class="updated"><p>PDF uploaded successfully.</p></div>';
    } else {
        echo '<div class="error"><p>There was an error uploading the file: ' . $movefile['error'] . '</p></div>';
    }
}

// Handle the file removal
function pdf_viewer_handle_file_removal() {
    $pdf_url = get_option('pdf_viewer_pdf_url');
    if ($pdf_url) {
        $attachment_id = attachment_url_to_postid($pdf_url);
        if ($attachment_id) {
            wp_delete_attachment($attachment_id, true);
        }
        delete_option('pdf_viewer_pdf_url');
        echo '<div class="updated"><p>PDF removed successfully.</p></div>';
    } else {
        echo '<div class="error"><p>No PDF to remove.</p></div>';
    }
}

// Restrict access to the admin menu for PDF Viewer role
function pdf_viewer_restrict_admin_access() {
    if (current_user_can('pdf_viewer') && is_admin()) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('admin_init', 'pdf_viewer_restrict_admin_access');

// Hide admin bar for PDF Viewer role
function pdf_viewer_hide_admin_bar() {
    if (current_user_can('pdf_viewer')) {
        add_filter('show_admin_bar', '__return_false');
    }
}
add_action('after_setup_theme', 'pdf_viewer_hide_admin_bar');

// Serve the PDF if the user has the correct role
function pdf_viewer_serve_pdf() {
    if (isset($_GET['view_pdf'])) {
        if (is_user_logged_in() && current_user_can('pdf_viewer')) {
            $pdf_url = get_option('pdf_viewer_pdf_url');
            if ($pdf_url) {
                header('Content-Type: application/pdf');
                readfile($pdf_url);
                exit;
            }
        }
        wp_redirect(home_url());
        exit;
    }
}
add_action('template_redirect', 'pdf_viewer_serve_pdf');

// Hide direct access to the uploaded PDF
function pdf_viewer_protect_uploaded_pdf($data, $postarr) {
    if ($data['post_mime_type'] === 'application/pdf') {
        $data['post_status'] = 'private';
    }
    return $data;
}
add_filter('wp_insert_post_data', 'pdf_viewer_protect_uploaded_pdf', 10, 2);

// Add a custom toolbar for PDF Viewer role
function pdf_viewer_custom_toolbar() {
    if (is_user_logged_in() && current_user_can('pdf_viewer')) {
        $pdf_url = get_option('pdf_viewer_pdf_url');
        ?>
        <style>
            body {
                margin-top: 50px; /* Adjust the value as needed */
            }
            .pdf-viewer-toolbar {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                background: #23282d;
                color: #fff;
                padding: 10px;
                text-align: right;
                z-index: 9999;
            }
            .pdf-viewer-toolbar a.button {
                color: #fff;
                margin-left: 3px;
                text-decoration: none;
                background: #0073aa;
                padding: 5px 10px;
                border-radius: 3px;
            }
            .pdf-viewer-toolbar a.button:hover {
                background: #005177;
            }
        </style>
        <div class="pdf-viewer-toolbar">
            <?php if ($pdf_url): ?>
                <a class="button" href="<?php echo esc_url(add_query_arg('view_pdf', '1', home_url())); ?>" target="_blank">Download PDF</a>
            <?php endif; ?>
            <a class="button" href="<?php echo wp_logout_url(home_url()); ?>">Logout</a>
        </div>
        <?php
    }
}
add_action('wp_footer', 'pdf_viewer_custom_toolbar');

// Shortcode to display login form
function pdf_viewer_login_form_shortcode() {
    if (is_user_logged_in()) {
        return '';
    } else {
        ob_start();
        wp_login_form();
        return ob_get_clean();
    }
}
add_shortcode('pdf_viewer_login_form', 'pdf_viewer_login_form_shortcode');
