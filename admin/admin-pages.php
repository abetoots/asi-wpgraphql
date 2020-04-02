<?php

use const ASI_WPGraphQL\Constants\PLUGIN_PREFIX;

settings_errors(); ?>
<form method="post" action="options.php">
    <?php settings_fields('react-wpgraphql-plugin-settings'); ?>
    <?php do_settings_sections('' . PLUGIN_PREFIX . '_do_settings_section'); ?>
    <?php submit_button(); ?>
</form>