<?php

namespace ASI_WPGraphQL\Inc\Helpers;

use WP_Error;

use const ASI_WPGraphQL\Constants\PLUGIN_HTML_TEMPLATES_DIR;
use const ASI_WPGraphQL\Constants\PLUGIN_PREFIX;

/**
 * Checks if the user ID exists
 */
function user_id_exists($user_id)
{
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id));
    return empty($count) || 1 > $count ? false : true;
}

/**
 * Renders the contents of the given template to a string and returns it.
 *
 * @param string $template_name The name of the template to render (without .php)
 * @param array  $attributes    The PHP variables for the template
 *
 * @return string               The contents of the template.
 */
function get_template_html($template_name, $attributes = null)
{
    if (!$attributes) {
        $attributes = array();
    }

    /**
     * Notes:
     * The output buffer collects everything that is printed between 
     * ob_start and ob_end_clean so that it can then be retrieved as a string using ob_get_contents.
     * 
     * Notes: the do actions are called by add action, gives chance to other devs to add further customizations
     */
    ob_start();

    do_action('' . PLUGIN_PREFIX . '_customize_html_template_before_' . $template_name);

    require(PLUGIN_HTML_TEMPLATES_DIR . $template_name . '.php');

    do_action('' . PLUGIN_PREFIX . '_customize_html_template_after_' . $template_name);

    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

/**
 * Inserts an image from a remote request, unattached to any post parent, in the upload directory. Does not create sub-sizes.
 * We want to handle two cases:
 * 1. Upload new image. $ID param must be null
 * 2. Replace existing image. $ID param must be set
 * 
 * @param file_id Reference to the file in the $_FILES object
 * @param ID An existing attachment_id to replace.
 * @return Int|WP_Error The attachment_id or a WP_Error
 */
function remote_insert_unattached_image($file_id, $ID = null)
{

    //Return if file is not an image
    if (0 !== strpos($_FILES[$file_id]['type'], 'image/')) {
        return new WP_Error(400, 'File type unauthorized');
    }

    //Ultimately, we want to call wp_insert_attachment() as it gives us the ability to update an existing attachment
    //However, the parameter it accepts is a path to an EXISTING FILE IN THE UPLOAD DIRECTORY. 

    //Therefore:
    //1. We first want to upload the pending replacement to the upload directory

    //Basically, we copy what media_handle_upload() does but tweak it a little bit to
    //handle images only and always set post parent to 0

    $time = current_time('mysql');
    $user_uploaded_file = $_FILES[$file_id];

    //Needed to bypass security checks when not handling a form since some of these tests rely on $_POST
    $upload_overrides = array(
        'test_form' => false
    );

    //https://codex.wordpress.org/Function_Reference/wp_handle_upload
    $file = wp_handle_upload($user_uploaded_file, $upload_overrides, $time);
    //Handle errors
    if (isset($file['error'])) {
        return new WP_Error('upload_error', $file['error']);
    }

    $name = $_FILES[$file_id]['name'];
    $ext  = pathinfo($name, PATHINFO_EXTENSION);
    $final_name = wp_basename($name, ".$ext");

    $url     = $file['url'];
    $type    = $file['type'];
    $file    = $file['file'];
    $title   = sanitize_text_field($final_name);
    $content = '';
    $excerpt = '';

    // Use image exif/iptc data for title and caption defaults if possible.
    $image_meta = wp_read_image_metadata($file);

    if ($image_meta) {
        if (trim($image_meta['title']) && !is_numeric(sanitize_title($image_meta['title']))) {
            $title = $image_meta['title'];
        }

        if (trim($image_meta['caption'])) {
            $excerpt = $image_meta['caption'];
        }
    }

    // Construct the attachment array.
    $attachment = [
        'post_mime_type' => $type,
        'guid'           => $url,
        'post_title'     => $title,
        'post_content'   => $content,
        'post_excerpt'   => $excerpt,
    ];

    if ($ID) {
        $attachment['ID'] = $ID;
    }

    //2. Now we can call wp_insert_attachment().
    //https://developer.wordpress.org/reference/functions/wp_insert_attachment/
    $attachment_id = wp_insert_attachment($attachment, $file, 0, true); //Unattached since 3rd param is 0

    //Either the id or a WP_Error
    return $attachment_id;
}
