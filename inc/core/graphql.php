<?php

namespace ASI_WPGraphQL\Inc\Core\GraphQl;

use ASI_WPGraphQL\Inc\Core\User;
use ASI_WPGraphQL\Inc\Helpers\Validation as ValidationHelper;

use const ASI_WPGraphQL\Constants\AIM_REGISTER_ASI_MEMBER;
use const ASI_WPGraphQL\Constants\ASI_ADMIN;
use const ASI_WPGraphQL\Constants\ASI_MEMBER;
use const ASI_WPGraphQL\Constants\ASI_ROLE;
use const ASI_WPGraphQL\Constants\ATTACHMENT_ID;
use const ASI_WPGraphQL\Constants\ATTACHMENT_ID_DEFAULT;
use const ASI_WPGraphQL\Constants\BUSINESS_CATEGORIES;
use const ASI_WPGraphQL\Constants\BUSINESS_DESCRIPTION;
use const ASI_WPGraphQL\Constants\BUSINESS_NAME;
use const ASI_WPGraphQL\Constants\CITY;
use const ASI_WPGraphQL\Constants\COUNTRY;
use const ASI_WPGraphQL\Constants\FULL_NAME;
use const ASI_WPGraphQL\Constants\PHONE_NUM;
use const ASI_WPGraphQL\Constants\PLUGIN_PREFIX;
use const ASI_WPGraphQL\Constants\PROFILE_PHOTO;
use const ASI_WPGraphQL\Constants\PROVINCE;
use const ASI_WPGraphQL\Constants\SOCIAL_FACEBOOK;
use const ASI_WPGraphQL\Constants\SOCIAL_WEBSITE;
use const ASI_WPGraphQL\Constants\STREET;
use const ASI_WPGraphQL\Constants\TEL_NUM;
use const ASI_WPGraphQL\Constants\TYPE_PROFILE_PHOTO;

if (!defined('ABSPATH')) exit; //Exit if accessed directly

//https://docs.wpgraphql.com/extending/types/#register_graphql_object_type
add_action('graphql_register_types', __NAMESPACE__  . '\register_profile_photo_type');
function register_profile_photo_type()
{
    \register_graphql_object_type(TYPE_PROFILE_PHOTO, [
        'description' => __("ASI member's profile photo url and attachment userId", PLUGIN_PREFIX),
        'fields' => [
            'attachment_id' => [
                'type' => 'Int',
                'description' => __('Attachment userId of the attachment post type', PLUGIN_PREFIX),
            ],
            'url' => [
                'type' => 'String',
                'description' => __('The photo src url', PLUGIN_PREFIX),
            ],
        ],
    ]);
}

//https://docs.wpgraphql.com/getting-started/custom-fields-and-meta/
add_action('graphql_register_types', __NAMESPACE__ . '\register_user_metas_in_wpgraphql');
function register_user_metas_in_wpgraphql()
{
    foreach (User::$metas as $key => $val) {
        if ($val['graphQlObjType'] && $val['graphQlType']) {
            \register_graphql_field($val['graphQlObjType'], $key, array(
                //The schema only has 'Int' type, everything else convert to uppercase
                'type' => $val['graphQlType'],
                'resolve' => function ($obj, $dunno, $app_context, $resolve_info) {
                    //Resolve by field name
                    switch ($resolve_info->fieldName) {
                        case ASI_ROLE:
                            $role = get_userdata($obj->userId)->roles[0];
                            if ($role === ASI_MEMBER || $role === ASI_ADMIN) {
                                return $role;
                            }
                            break;

                        case BUSINESS_CATEGORIES:
                            $return  = get_user_meta($obj->userId, $resolve_info->fieldName, true);
                            if (!$return) {
                                $return = [];
                            }
                            return $return;

                        case PROFILE_PHOTO:
                            $id = $obj->userId;
                            $return = get_user_meta($obj->userId, $resolve_info->fieldName, false);
                            if (!$return) {
                                $return = [
                                    ATTACHMENT_ID => ATTACHMENT_ID_DEFAULT,
                                    'url'           => ''
                                ];
                            }
                            return $return[0];


                        default:
                            $return = get_user_meta($obj->userId, $resolve_info->fieldName, true);
                            if (!$return) {
                                return null;
                            }
                            return $return;
                    }

                    //Resolve by Type
                    switch ($resolve_info->returnType->name) {
                        case 'Int':
                            //returns 0 instead of null when not found
                            $return = get_user_meta($obj->userId, $resolve_info->fieldName, true);
                            if (!$return) {
                                $return = 0;
                            }
                            return $return;
                        default:
                            return null;
                    }
                }
            ));
        }
    }
}

# This is the action that is executed as the GraphQL Schema is being built.
add_action('graphql_register_types', __NAMESPACE__ . '\register_custom_mutations');
function register_custom_mutations()
{

    # This function registers a mutation to the Schema.
    # The first argument, in this case `exampleMutation`, is the name of the mutation in the Schema
    # The second argument is an array to configure the mutation.
    # The config array accepts 3 key/value pairs for: inputFields, outputFields and mutateAndGetPayload.
    \register_graphql_mutation('updateAsiMemberAccount', [

        # inputFields expects an array of Fields to be used for inputtting values to the mutation
        'inputFields' => [
            'userId' => [
                'type' => 'Int',
                'description' => __('Database userId of the user we want to update ', PLUGIN_PREFIX),
            ],
            FULL_NAME           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s full name', PLUGIN_PREFIX)
            ],
            BUSINESS_NAME           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s business name', PLUGIN_PREFIX)
            ],
            BUSINESS_DESCRIPTION           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s business description', PLUGIN_PREFIX)
            ],
            BUSINESS_CATEGORIES           => [
                'type'          => ['list_of' => 'String'],
                'description'   => __('Update the user\'s business categories', PLUGIN_PREFIX)
            ],
            STREET           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: street', PLUGIN_PREFIX)
            ],
            CITY           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: city', PLUGIN_PREFIX)
            ],
            PROVINCE           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: province', PLUGIN_PREFIX)
            ],
            COUNTRY           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: country', PLUGIN_PREFIX)
            ],
            PHONE_NUM           => [
                'type'          => 'Int',
                'description'   => __('Update the user\'s phone number', PLUGIN_PREFIX)
            ],
            TEL_NUM           => [
                'type'          => 'Int',
                'description'   => __('Update the user\'s tel number', PLUGIN_PREFIX)
            ],
            SOCIAL_WEBSITE           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s social presence: website', PLUGIN_PREFIX)
            ],
            SOCIAL_FACEBOOK           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s social presence: facebook', PLUGIN_PREFIX)
            ],
            //https://github.com/wp-graphql/wp-graphql/issues/311
            // PROFILE_PHOTO           => [
            //     'type'          => 'String',
            //     'description'   => __('Update the user\'s profile photo', PLUGIN_PREFIX)
            // ]
        ],

        # outputFields expects an array of fields that can be asked for in response to the mutation
        # the resolve function is optional, but can be useful if the mutateAndPayload doesn't return an array
        # with the same key(s) as the outputFields
        'outputFields' => [
            'userId' => [
                'type' => 'Int',
                'description' => __('Database userId of the user we want to update ', PLUGIN_PREFIX),
            ],
            FULL_NAME           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s full name', PLUGIN_PREFIX)
            ],
            BUSINESS_NAME           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s business name', PLUGIN_PREFIX)
            ],
            BUSINESS_DESCRIPTION           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s business description', PLUGIN_PREFIX)
            ],
            BUSINESS_CATEGORIES           => [
                'type'          => ['list_of' => 'String'],
                'description'   => __('Update the user\'s business categories', PLUGIN_PREFIX)
            ],
            STREET           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: street', PLUGIN_PREFIX)
            ],
            CITY           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: city', PLUGIN_PREFIX)
            ],
            PROVINCE           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: province', PLUGIN_PREFIX)
            ],
            COUNTRY           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s location: country', PLUGIN_PREFIX)
            ],
            PHONE_NUM           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s phone number', PLUGIN_PREFIX)
            ],
            TEL_NUM           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s tel number', PLUGIN_PREFIX)
            ],
            SOCIAL_WEBSITE           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s social presence: website', PLUGIN_PREFIX)
            ],
            SOCIAL_FACEBOOK           => [
                'type'          => 'String',
                'description'   => __('Update the user\'s social presence: facebook', PLUGIN_PREFIX)
            ]
        ],

        # mutateAndGetPayload expects a function, and the function gets passed the $input, $context, and $info
        # the function should return enough info for the outputFields to resolve with
        'mutateAndGetPayload' => function ($input, $context, $info) {
            if (!is_user_logged_in()) {
                throw new \RuntimeException('Unauthenticated user', 403);
            }
            if (empty($input['userId'])) {
                throw new \RuntimeException('Required inputs are empty', 403);
            }

            if (!current_user_can('update_asi_member')) {
                throw new \RuntimeException('You are not allowed to update accounts', 403);
            }

            //for validation
            $data = [];
            $data['userId'] = $input['userId'];
            foreach ($input as $key => $val) {
                if (!empty($input[$key])) {
                    $data[$key] = $val;
                }
            }

            //We expect either the saved data or a WP_Eerror
            $result = ValidationHelper::instance()->validate_and_save_new_data_of_user($data, ASI_MEMBER);
            if (is_wp_error($result)) {
                foreach ($result->get_error_codes() as $error_code) {
                    throw new \RuntimeException($result->get_error_message($error_code));
                }
            }
            //if validation is successful, we'll reach below

            return $result;
        }
    ]);
}

/**
 * Run an action after the additional data has been updated. This is a great spot to hook into to
 * update additional data related to users, such as setting relationships, updating additional usermeta,
 * or sending emails to Kevin... whatever you need to do with the userObject.
 *
 * @param int $user_id The userId of the user being mutated
 * @param array $input The input for the mutation
 * @param string $mutation_name The name of the mutation (ex: create, update, delete)
 * @param AppContext $context The AppContext passed down the resolve tree
 * @param ResolveInfo $info The ResolveInfo passed down the Resolve Tree
 */

add_action('graphql_user_object_mutation_update_additional_data', __NAMESPACE__ . '\graphql_register_user_mutation', 10, 5);

function graphql_register_user_mutation($user_id, $input, $mutation_name, $context, $info)
{
    //TODO maybe check mutation name
    if (isset($input['aim']) && $input['aim'] === AIM_REGISTER_ASI_MEMBER) {
        // Consider other sanitization if necessary and validation such as which
        // user role/capability should be able to insert this value, etc.
        $user = get_userdata($user_id);
        $user->set_role(ASI_MEMBER);
    }
}
