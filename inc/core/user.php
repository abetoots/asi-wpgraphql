<?php

namespace ASI_WPGraphQL\Inc\Core;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use const ASI_WPGraphQL\Constants\ASI_ROLE;
use const ASI_WPGraphQL\Constants\BUSINESS_CATEGORIES;
use const ASI_WPGraphQL\Constants\BUSINESS_DESCRIPTION;
use const ASI_WPGraphQL\Constants\BUSINESS_NAME;
use const ASI_WPGraphQL\Constants\CITY;
use const ASI_WPGraphQL\Constants\COUNTRY;
use const ASI_WPGraphQL\Constants\FULL_NAME;
use const ASI_WPGraphQL\Constants\PHONE_NUM;
use const ASI_WPGraphQL\Constants\PROFILE_PHOTO;
use const ASI_WPGraphQL\Constants\PROVINCE;
use const ASI_WPGraphQL\Constants\SOCIAL_FACEBOOK;
use const ASI_WPGraphQL\Constants\SOCIAL_WEBSITE;
use const ASI_WPGraphQL\Constants\STREET;
use const ASI_WPGraphQL\Constants\TEL_NUM;
use const ASI_WPGraphQL\Constants\TYPE_PROFILE_PHOTO;

class user
{

    public static $metas = [
        FULL_NAME => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        BUSINESS_NAME  => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        BUSINESS_DESCRIPTION  => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        BUSINESS_CATEGORIES  => [
            'type'              => 'array',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => ['list_of' => 'String'],
            'graphQlObjType'    => 'User'
        ],
        STREET  => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'string',
            'graphQlObjType'    => 'User'
        ],
        CITY  => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        PROVINCE  => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        COUNTRY => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'string',
            'graphQlObjType'    => 'User'
        ],
        PHONE_NUM => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        TEL_NUM => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        SOCIAL_WEBSITE => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        SOCIAL_FACEBOOK => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'String',
            'graphQlObjType'    => 'User'
        ],
        PROFILE_PHOTO => [
            'type'              => 'object',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => TYPE_PROFILE_PHOTO,
            'graphQlObjType'    => 'User'
        ],
        ASI_ROLE => [
            'type'              => 'string',
            'rest'              => false,
            'obj_type'          => 'user',
            'graphQlType'       => 'string',
            'graphQlObjType'    => 'User'
        ]
    ];

    /**
     * Define user metas and show in REST API
     */

    private function register_user_metas()
    {
        foreach (self::$metas as $key => $val) {
            register_meta($val['obj_type'], $key, array(
                "type" => $val['type'],
                "show_in_rest" => $val['rest']
            ));
        }
    }

    /**
     * Instance
     *
     * @access private
     * @static
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @access public
     *
     * @return Plugin An instance of the class.
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     *  Plugin class constructor
     *
     * @access public
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     *  Init function that handles all hooks and filters
     *
     * @access public
     */
    public function init()
    {
        $this->register_user_metas();
    }
}
user::instance();
