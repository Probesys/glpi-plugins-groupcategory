<?php

// Version of the plugin
define('PLUGIN_GROUPCATEGORY_VERSION', "1.4.0RC3");
define('PLUGIN_GROUPCATEGORY_GLPI_MIN_VERSION', '9.4');
define('PLUGIN_GROUPCATEGORY_NAMESPACE', 'groupcategory');
// Maximum GLPI version, exclusive
define("PLUGIN_GROUPCATEGORY_GLPI_MAX_VERSION", "9.6");

if (!defined("PLUGIN_GROUPCATEGORY_DIR")) {
    define("PLUGIN_GROUPCATEGORY_DIR", Plugin::getPhpDir("groupcategory"));
}
if (!defined("PLUGIN_GROUPCATEGORY_WEB_DIR")) {
    define("PLUGIN_GROUPCATEGORY_WEB_DIR", Plugin::getWebDir("groupcategory"));
}


/**
 * Plugin description
 *
 * @return boolean
 */
function plugin_version_groupcategory()
{
    return [
      'name' => 'GroupCategory',
      'version' => PLUGIN_GROUPCATEGORY_VERSION,
      'author' => '<a href="https://www.probesys.com">PROBESYS</a>',
      'homepage' => 'https://github.com/Probesys/glpi-plugins-groupcategory',
      'license' => 'GPLv2+',
      'minGlpiVersion' => PLUGIN_GROUPCATEGORY_GLPI_MIN_VERSION,
    ];
}

/**
 * Initialize plugin
 *
 * @return boolean
 */
function plugin_init_groupcategory()
{
    if (Session::getLoginUserID()) {
        global $PLUGIN_HOOKS;

        $PLUGIN_HOOKS['csrf_compliant'][PLUGIN_GROUPCATEGORY_NAMESPACE] = true;
        //$PLUGIN_HOOKS['post_show_item'][PLUGIN_GROUPCATEGORY_NAMESPACE] = ['PluginGroupcategoryGroupcategory', 'post_show_item'];
        $PLUGIN_HOOKS['post_item_form'][PLUGIN_GROUPCATEGORY_NAMESPACE] = ['PluginGroupcategoryGroupcategory', 'post_item_form'];
        $PLUGIN_HOOKS['pre_item_update'][PLUGIN_GROUPCATEGORY_NAMESPACE] = [
          'Group' => 'plugin_groupcategory_group_update',
        ];
    }
}

/**
 * Check plugin's prerequisites before installation
 */
function plugin_groupcategory_check_prerequisites()
{
    if (version_compare(GLPI_VERSION, PLUGIN_GROUPCATEGORY_GLPI_MIN_VERSION, 'lt') || version_compare(GLPI_VERSION, PLUGIN_GROUPCATEGORY_GLPI_MAX_VERSION, 'ge')) {
        echo __('This plugin requires GLPI >= ' . PLUGIN_GROUPCATEGORY_GLPI_MIN_VERSION . ' and GLPI < ' . PLUGIN_GROUPCATEGORY_GLPI_MAX_VERSION . '<br>');
    } else {
        return true;
    }
    return false;
}

/**
 * Check if config is compatible with plugin
 *
 * @return boolean
 */
function plugin_groupcategory_check_config()
{
    // nothing to do
    return true;
}
