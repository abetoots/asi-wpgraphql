<?php

use const ASI_WPGraphQL\Constants\ASI_ADMIN;
use const ASI_WPGraphQL\Constants\ASI_MEMBER;

/**
 * Class Plugin
 *
 * Main Plugin class
 */
class Plugin
{

    /**
     * Include Class Files
     *
     * @access private
     */
    private function include_class_files()
    {
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'admin/settings.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/constants.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/requests-handler.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/user.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/graphql.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/validation.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/helpers/validation.php');
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/helpers/utilities.php');
    }

    /**
     *Register initial role definitions
     *  
     * @access private
     */
    public function define_initial_roles()
    {
        //Avoid nasty bugs
        if (get_role(ASI_MEMBER)) {
            remove_role(ASI_MEMBER);
        }
        add_role(ASI_MEMBER, 'ASI Member', array(
            'read'      => true
        ));

        if (get_role(ASI_ADMIN)) {
            remove_role(ASI_ADMIN);
        }
        add_role(ASI_ADMIN, 'ASI Admin', array(
            'read'      => true
        ));
    }

    /**
     * Add same custom capabilities to the defined roles
     * 
     * @access private
     * 
     * @uses get_role();
     * @uses add_cap();
     */
    public function add_custom_caps()
    {

        $asiMember = get_role(ASI_MEMBER);
        $asi_member_caps = [
            'upload_files'
        ];
        foreach ($asi_member_caps as $c) {
            $asiMember->add_cap($c);
        }

        $asiAdmin = get_role(ASI_ADMIN);
        $asi_admin_caps = [
            'list_users',
            'list_asi_members',
            'delete_asi_member',
            'create_asi_member',
            'update_asi_member',
            'upload_files'
        ];
        foreach ($asi_admin_caps as $c) {
            $asiAdmin->add_cap($c);
        };

        $admin = get_role('administrator');
        $admin_caps = [
            'list_asi_members',
            'delete_asi_member',
            'create_asi_member',
            'update_asi_member',
            'create_asi_user',
            'update_asi_user',
            'delete_asi_user'
        ];
        foreach ($admin_caps as $c) {
            $admin->add_cap($c);
        };
    }

    /**
     * Include libraries
     *
     * @access private
     */
    private function include_libraries()
    {
        if (!class_exists('ACF') || !function_exists('get_field')) {
            include_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'libraries/advanced-custom-fields-pro/acf.php');
        }
    }

    /**
     * Scripts only meant to run on development
     * Dev scripts are git ignored
     */
    public static function enable_dev_scripts()
    {
        require_once(ASI_WPGRAPHQL_PLUGIN_DIR . 'inc/core/dev-scripts.php');
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
        $this->include_class_files();
        $this->include_libraries();
        //Add custom roles
        add_action("init", array($this, 'define_initial_roles'));
        // Add role capabilities, priority must be after the initial role definition.
        add_action('init', array($this, 'add_custom_caps'), 11);
    }
}
// Instantiate Plugin Class
Plugin::instance();
if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
    //Dev only
    Plugin::enable_dev_scripts();
}
