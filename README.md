# PDF Viewer Role Plugin

## Description

The PDF Viewer Role Plugin creates a new user role called "PDF Viewer" with specific permissions. This plugin includes a custom admin page for uploading, viewing, and removing a PDF document. It also provides a shortcode to display a login form and ensures that only users with the "PDF Viewer" role can access the PDF document.

## Installation

1. Download the plugin files and upload them to your WordPress site's `wp-content/plugins` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Assign the "PDF Viewer" role to the users who should have access to the PDF document.

## Usage

### Uploading the PDF Document

1. Go to the WordPress admin dashboard.
2. Navigate to the `PDF Viewer` menu.
3. Upload the PDF document using the provided form.

### Removing the PDF Document

1. Go to the WordPress admin dashboard.
2. Navigate to the `PDF Viewer` menu.
3. Click the "Remove PDF" button to delete the uploaded PDF document.

### Displaying the Login Form

1. Create a new page in WordPress.
2. Add the following shortcode to the page content: [pdf_viewer_login_form]
3. Publish the page.

### Toolbar for PDF Viewer Role

When users with the "PDF Viewer" role are logged in, a custom toolbar will appear at the top of the website, including:
- A button to download the PDF document.
- A logout button.

## Shortcode

### `[pdf_viewer_login_form]`

This shortcode displays the default WordPress login form. If the user is logged in, the form is hidden.

## Additional Information

- The admin page includes instructions on how to use the shortcode.
- Users with the "PDF Viewer" role are restricted from accessing the WordPress admin dashboard.
- The admin bar is hidden for users with the "PDF Viewer" role.

## Customization

Feel free to customize the plugin code to suit your needs. If you encounter any issues or have suggestions for improvements, please let us know.

## License

This plugin is licensed under the GPLv2 (or later) license.
