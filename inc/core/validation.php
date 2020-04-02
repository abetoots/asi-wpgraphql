<?php

namespace ASI_WPGraphQL\Inc\Core\Validation;

use ASI_WPGraphQL\Inc\Helpers\Validation;

use const ASI_WPGraphQL\Constants\ASI_ADMIN;
use const ASI_WPGraphQL\Constants\ASI_MEMBER;
use const ASI_WPGraphQL\Constants\BUSINESS_CATEGORIES;
use const ASI_WPGraphQL\Constants\BUSINESS_DESCRIPTION;
use const ASI_WPGraphQL\Constants\BUSINESS_NAME;
use const ASI_WPGraphQL\Constants\CITY;
use const ASI_WPGraphQL\Constants\COUNTRY;
use const ASI_WPGraphQL\Constants\FULL_NAME;
use const ASI_WPGraphQL\Constants\PHONE_NUM;
use const ASI_WPGraphQL\Constants\PLUGIN_PREFIX;
use const ASI_WPGraphQL\Constants\PROVINCE;
use const ASI_WPGraphQL\Constants\SOCIAL_FACEBOOK;
use const ASI_WPGraphQL\Constants\SOCIAL_WEBSITE;
use const ASI_WPGraphQL\Constants\STREET;
use const ASI_WPGraphQL\Constants\TEL_NUM;

/**
 * Handle custom validation for our data
 *
 * @access public
 *  
 */
add_filter('' . PLUGIN_PREFIX . '_custom_validation_on_user_update', __NAMESPACE__ . '\validate_asi_user_data_shared', 10, 2);
add_filter('' . PLUGIN_PREFIX . '_custom_validation_on_user_registration', __NAMESPACE__ . '\validate_asi_user_data_shared', 10, 2);
function validate_asi_user_data_shared($data, $role)
{
    //If using WPGraphQl, WPGraphQl's type checking should be fine
    return;
}

add_filter('' . PLUGIN_PREFIX . '_custom_save_on_user_update', __NAMESPACE__ . '\sanitize_asi_user_data_before_saving', 10, 2);
function sanitize_asi_user_data_before_saving($data, $role)
{
    $errors = array();
    $newData = array();

    if ($role === ASI_MEMBER) {
        foreach ($data as $key => $val) {
            $safeData = sanitizeAsiUserData($key, $val, $data['userId']);
            //Only trigger update_user_meta on data we have sanitized
            if ($safeData) {
                $success = update_user_meta($data['userId'], $key, $safeData);
                if ($success) {
                    $newData[$key] = $safeData;
                } else {
                    $errors[] = 'update_failed';
                }
            } else { //data we don't sanitize is to be returned normally (e.g. clientMutationId , userId)
                $newData[$key] = $val;
            }
        }
    }

    if (!empty($errors)) {
        return Validation::instance()->build_errors($errors);
    }

    return $newData;
}

/**
 * We must handle updating our asi user after it is inserted
 *
 * @access public
 * 
 */
add_action('' . PLUGIN_PREFIX . 'after_success_validate_and_register_new_user', __NAMESPACE__ . '\update_asi_users', 10, 3);
function update_asi_users($user_id, $data, $role)
{
    //Check if asi users to avoid updating for other user roles
    if ($role === ASI_MEMBER || $role === ASI_ADMIN) {
        foreach ($data as $key => $val) {
            $safeData = sanitizeAsiUserData($key, $val, '');
            if ($safeData) {
                update_user_meta($user_id, $key, $safeData);
            }
        }
    }

    if ($role === ASI_MEMBER) {
        update_user_meta($user_id, 'show_admin_bar_front', 'false');
    }
}


//If a user ID is passed in, this function will expect
//to sanitize a value based on a previous value from the database
function sanitizeAsiUserData($key, $val, $userId = '')
{
    switch ($key) {
        case FULL_NAME:
            return sanitize_text_field($val);
        case BUSINESS_NAME:
            return sanitize_text_field($val);
        case BUSINESS_DESCRIPTION:
            return sanitize_text_field($val);
        case BUSINESS_CATEGORIES:
            $array = [];
            foreach ($val as $i) {
                $array[] = sanitize_text_field($i);
            }
            return $array; //validation handled by WPGraphQl itself
        case STREET:
            return sanitize_text_field($val);
        case CITY:
            return sanitize_text_field($val);
        case PROVINCE:
            return sanitize_text_field($val);
        case COUNTRY:
            return sanitize_text_field($val);
        case PHONE_NUM:
            return sanitize_text_field(strval($val));
        case TEL_NUM:
            return sanitize_text_field(strval($val));
        case SOCIAL_WEBSITE:
            return esc_url_raw($val);
        case SOCIAL_FACEBOOK:
            return esc_url_raw($val);
        default:
            return false;
    }
}
