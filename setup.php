<?php
/*
 * @version $Id: setup.php 196 2016-04-08 21:00:00Z jul-m $
 -------------------------------------------------------------------------
 Archires plugin for GLPI
 Copyright (C) 2003-2013 by the archires Development Team.

 https://forge.indepnet.net/projects/archires
 -------------------------------------------------------------------------
 * Updated by Julien MEUGNIER - https://github.com/jul-m/archires
 -------------------------------------------------------------------------

 LICENSE

 This file is part of archires.

 Archires is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Archires is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Archires. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

// Init the hooks of the plugins -Needed
function plugin_init_archires() {
   global $PLUGIN_HOOKS,$CFG_GLPI;

   $PLUGIN_HOOKS['csrf_compliant']['archires'] = true;

   Plugin::registerClass('PluginArchiresProfile', array('addtabon' => array('Profile')));

   $PLUGIN_HOOKS['change_profile']['archires'] = array('PluginArchiresProfile','changeProfile');
   $PLUGIN_HOOKS['pre_item_purge']['archires'] = array('Profile' => array('PluginArchiresProfile',
                                                       'purgeProfiles'));

   if (Session::getLoginUserID()) {
      if (plugin_archires_haveRight("archires","r")) {
          
         $PLUGIN_HOOKS['menu_toadd']['archires'] = array('plugins' => 'PluginArchiresArchires');
      }

      if (plugin_archires_haveRight("archires","w")) {

         $PLUGIN_HOOKS['use_massive_action']['archires'] = 1;
      }
      
      // Config page
      if (plugin_archires_haveRight("archires","w") || Session::haveRight("config", UPDATE)) {
          
         $PLUGIN_HOOKS['config_page']['archires'] = 'front/config.form.php';
      }
   }
}


// Get the name and the version of the plugin - Needed
function plugin_version_archires() {

   return array('name'           => _n('Network Architecture', 'Network Architectures', 2, 'archires'),
                'version'        => '2.1.1',
                'author'         => 'Xavier Caillaud, Remi Collet, Nelly Mahu-Lasson, Sebastien Prudhomme',
                'license'        => 'GPLv2+',
                'homepage'       => 'https://forge.indepnet.net/projects/archires',
                'minGlpiVersion' => '0.85');
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_archires_check_prerequisites() {

   if (version_compare(GLPI_VERSION, '0.85', 'lt') || version_compare(GLPI_VERSION, '0.91', 'ge')) {
      _e('This plugin requires GLPI >= 0.85 and <= 0.90', 'archires');
      return false;
   }
   return true;
}


// Uninstall process for plugin : need to return true if succeeded : may display messages or add to message after redirect
function plugin_archires_check_config() {
   return true;
}


function plugin_archires_haveRight($module,$right) {

   $matches=array(""  => array("","r","w",READ,CREATE), // ne doit pas arriver normalement
                  "r" => array("r","w",READ,CREATE),
                  "w" => array("w",CREATE),
                  "1" => array("1"),
                  "0" => array("0","1")); // ne doit pas arriver non plus

   if (isset($_SESSION["glpi_plugin_archires_profile"][$module])
       && in_array($_SESSION["glpi_plugin_archires_profile"][$module],$matches[$right])) {
      return true;
   }
   return false;
}
?>
