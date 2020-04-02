<?php

namespace ASI_WPGraphQL\Inc\Core\RequestsHandler;

use WP_Ajax_Response;
use WP_Error;

use function ASI_WPGraphQL\Inc\Helpers\remote_insert_unattached_image;

use const ASI_WPGraphQL\Constants\ACTION_AJAX_UPLOAD_FILE;
use const ASI_WPGraphQL\Constants\ATTACHMENT_ID;
use const ASI_WPGraphQL\Constants\ATTACHMENT_ID_DEFAULT;
use const ASI_WPGraphQL\Constants\PROFILE_PHOTO;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Handle uploading of files for logged in users
 * 
 */
add_action('wp_ajax_' . ACTION_AJAX_UPLOAD_FILE . '', __NAMESPACE__ . '\handle_profile_photo_upload');
function handle_profile_photo_upload()
{

    //Start defensive checks
    if (!is_user_logged_in()) {
        wp_send_json(new WP_Error('user_unauthorized', 'You are not authenticated'), 403);
    }

    if (!current_user_can('upload_files')) {
        wp_send_json(new WP_Error('user_forbidden', 'You are not allowed to upload files'), 403);
    }

    if (empty($_REQUEST[ATTACHMENT_ID])) {
        wp_send_json(new WP_Error('bad_request', 'Missing parameters'), 400);
    }

    //End defensive checks

    //The default attachment_id for new users is -1. The intent is to upload a new profile photo
    //! careful as $_REQUEST queries are strings
    if ($_REQUEST[ATTACHMENT_ID] == ATTACHMENT_ID_DEFAULT) {
        $result = remote_insert_unattached_image(PROFILE_PHOTO);
    } else {
        //User has an existing attachment id. The intent is to replace the existing profile photo
        $result = remote_insert_unattached_image(PROFILE_PHOTO, $_REQUEST[ATTACHMENT_ID]);
    }

    if (is_wp_error($result)) {
        wp_send_json($result, 500);
    }

    //Success, return the uploaded files' url
    $userId = get_current_user_id();
    $url = wp_get_attachment_url($result);
    $profilePhoto = [
        ATTACHMENT_ID   => $result,
        'url'           => $url
    ];

    $success = update_user_meta($userId, PROFILE_PHOTO, $profilePhoto);

    if ($success) {
        //Success response
        wp_send_json($profilePhoto, 200);
    } else {
        wp_send_json(new WP_Error('update_failed', 'Could not update profile photo'), 500);
    }
}
