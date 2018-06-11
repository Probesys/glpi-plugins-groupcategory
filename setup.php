<?php

define('PLUGIN_GROUPCATEGORY_MIN_GLPI_VERSION', '9.2.1');
define('PLUGIN_GROUPCATEGORY_NAMESPACE', 'groupcategory');

/**
 * Plugin description
 *
 * @return boolean
 */
function plugin_version_groupcategory()
{
    return [
        'name' => 'GroupCategory',
        'version' => '1.1',
        'author' => 'Pierre de Vésian - <a href="http://www.probesys.com">Probesys</a>',
        'homepage' => 'https://probesys.com/',
        'license' => 'GPLv2+',
        'minGlpiVersion' => PLUGIN_GROUPCATEGORY_MIN_GLPI_VERSION,
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
        $PLUGIN_HOOKS['post_show_item'][PLUGIN_GROUPCATEGORY_NAMESPACE] = 'plugin_groupcategory_post_show_item';
        $PLUGIN_HOOKS['pre_item_update'][PLUGIN_GROUPCATEGORY_NAMESPACE] = [
            'Group' => 'plugin_groupcategory_group_update',
        ];
    }
}

/**
 * Check if plugin prerequisites are met
 *
 * @return boolean
 */
function plugin_groupcategory_check_prerequisites()
{
    $prerequisites_check_ok = false;

    try {
        if (version_compare(GLPI_VERSION, PLUGIN_GROUPCATEGORY_MIN_GLPI_VERSION, '<')) {
            throw new Exception('This plugin requires GLPI >= ' . PLUGIN_GROUPCATEGORY_MIN_GLPI_VERSION);
        }

        $prerequisites_check_ok = true;
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    return $prerequisites_check_ok;
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
