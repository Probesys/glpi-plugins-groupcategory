<?php
/**
 * ---------------------------------------------------------------------
 *  groupcategory is a plugin to customizes the list of accessible
 *  ticket categories for ticket requesters.
 *  ---------------------------------------------------------------------
 *  LICENSE
 *
 *  This file is part of groupcategory.
 *
 *  groupcategory is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  groupcategory is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Formcreator. If not, see <http://www.gnu.org/licenses/>.
 *  ---------------------------------------------------------------------
 *  @copyright Copyright © 2022-2023 probeSys'
 *  @license   http://www.gnu.org/licenses/agpl.txt AGPLv3+
 *  @link      https://github.com/Probesys/glpi-plugins-groupcategory
 *  @link      https://plugins.glpi-project.org/#/plugin/groupcategory
 *  ---------------------------------------------------------------------
 */

// Version of the plugin

define('PLUGIN_GROUPCATEGORY_VERSION', '1.5.2');
define('PLUGIN_GROUPCATEGORY_GLPI_MIN_VERSION', '9.4');
define('PLUGIN_GROUPCATEGORY_NAMESPACE', 'groupcategory');
// Maximum GLPI version, exclusive
define("PLUGIN_GROUPCATEGORY_GLPI_MAX_VERSION", "11.0");

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
    global $PLUGIN_HOOKS;
    $PLUGIN_HOOKS['csrf_compliant'][PLUGIN_GROUPCATEGORY_NAMESPACE] = true;
    //$PLUGIN_HOOKS['post_show_item'][PLUGIN_GROUPCATEGORY_NAMESPACE] = ['PluginGroupcategoryGroupcategory', 'post_show_item'];
    $PLUGIN_HOOKS['post_item_form'][PLUGIN_GROUPCATEGORY_NAMESPACE] = ['PluginGroupcategoryGroupcategory', 'post_item_form'];
    $PLUGIN_HOOKS['pre_item_update'][PLUGIN_GROUPCATEGORY_NAMESPACE] = [
      'Group' => 'plugin_groupcategory_group_update',
    ];
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
