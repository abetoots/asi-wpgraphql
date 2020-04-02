<?php

namespace ASI_WPGraphQL;

/**
 * Plugin Name: ASI Plugin
 * Plugin URI:  https://github.com/abetoots/asi-wpgraphql
 * Description: Plugin necessary for ASI's headless backend.
 * Version:     0.1.0
 * Author:      Abe Suni M. Caymo
 * Author URI:  https://abecaymo.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: asi-wpgraphql
 * Domain Path: /languages
 */
if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('ASI_WPGRAPHQL_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASI_WPGRAPHQL_PLUGIN_URL', plugin_dir_url(__FILE__));

final class ASI_WPGraphQL
{

    /**
     * Plugin Version
     *
     * @var string The plugin version.
     */
    const VERSION = '0.1.0';

    /**
     * Minimum PHP Version
     *
     * @var string Minimum PHP version required to run the plugin.
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Minimum ACF Version
     *
     * @var string Minimum ACF version required to run the plugin.
     */
    const MINIMUM_ACF_VERSION = '5.2.8';

    /**
     * Minimum WPGraphQl Version
     *
     * @var string Minimum ACF version required to run the plugin.
     */
    const WORKING_WPGRAPHQL_VERSION = '0.7.0';

    /**
     * Minimum WPGraphiQl Version
     *
     * @var string Minimum ACF version required to run the plugin.
     */
    const WORKING_WPGRAPHI_QL_VERSION = '1.0.1';

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // Load translation
        add_action('init', array($this, 'i18n'));

        // Init Plugin
        add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Load Textdomain
     *
     * Load plugin localization files.
     * Fired by `init` action hook.
     *
     * @access public
     */
    public function i18n()
    {
        load_plugin_textdomain('react-wpgraphql-plugin');
    }

    /**
     * Initialize the plugin
     *
     * Validates that Elementor is already loaded.
     * Checks for basic plugin requirements, if one check fails, don't continue, otherwise
     * if all checks have passed, include the plugin class.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @access public
     */
    public function init()
    {
        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }

        //Check if WPGraphQl is installed and activated
        if (!in_array('wp-graphql-' . self::WORKING_WPGRAPHQL_VERSION . '/wp-graphql.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action('admin_notices', array($this, 'admin_notice_required_wp_graphql'));
            return;
        }

        //Check if WPGrapihQl is installed and activated
        if (!in_array('wp-graphiql-' . self::WORKING_WPGRAPHI_QL_VERSION . '/wp-graphiql.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action('admin_notices', array($this, 'admin_notice_required_wp_graphi_ql'));
            return;
        }

        //Check if WPGrapihQl is installed and activated
        if (!in_array('wp-graphql-jwt-authentication/wp-graphql-jwt-authentication.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action('admin_notices', array($this, 'admin_notice_required_wp_graphql_jwt_authentication'));
            return;
        }

        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once('plugin.php');
    }

    /**
     * Admin notice - PHP Version
     *
     * Warning when the site doesn't have a minimum required PHP version.
     *
     * @access public
     */
    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s.', 'react-wpgraphql-plugin'),
            '<strong>' . esc_html__('React WPGraphql', 'react-wpgraphql-plugin') . '</strong>',
            '<strong>' . esc_html__('PHP', 'react-wpgraphql-plugin') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice - WPGraphQL
     *
     * Warning when the site doesn't have WPGraphQL installed and activated
     *
     * @access public
     */
    public function admin_notice_required_wp_graphql()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: WPGraphQL PHP 3: WPGraphQL url */
            esc_html__('"%1$s" requires "%2$s" version %3$s. "%4$s"', 'react-wpgraphql-plugin'),
            '<strong>' . esc_html__('React WPGraphql', 'react-wpgraphql-plugin') . '</strong>',
            '<strong>' . esc_html__('WPGraphQL', 'react-wpgraphql-plugin') . '</strong>',
            self::WORKING_WPGRAPHQL_VERSION,
            '<a href="https://github.com/wp-graphql/wp-graphql/releases">Go to plugin</a>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice - WPGrapihQL
     *
     * Warning when the site doesn't have WPGraphiQL installed and activated
     *
     * @access public
     */
    public function admin_notice_required_wp_graphi_ql()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s. "%4$s"', 'react-wpgraphql-plugin'),
            '<strong>' . esc_html__('React WPGraphql', 'react-wpgraphql-plugin') . '</strong>',
            '<strong>' . esc_html__('   WPGraphiQL', 'react-wpgraphql-plugin') . '</strong>',
            self::WORKING_WPGRAPHI_QL_VERSION,
            '<a href="https://github.com/wp-graphql/wp-graphiql">Go to plugin</a>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Admin notice - WPGraphQL JWT Authentication
     *
     * Warning when the site doesn't have WPGraphQL JWT Authentication installed and activated
     *
     * @access public
     */
    public function admin_notice_required_wp_graphql_jwt_authentication()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated. "%3$s"', 'react-wpgraphql-plugin'),
            '<strong>' . esc_html__('React WPGraphql', 'react-wpgraphql-plugin') . '</strong>',
            '<strong>' . esc_html__('   WPGraphiQL', 'react-wpgraphql-plugin') . '</strong>',
            '<a href="https://github.com/wp-graphql/wp-graphql-jwt-authentication">Go to plugin</a>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}
new ASI_WPGraphQL();
