<?php
/*
 * Copyright (C) 2021 Mishigami Lodge, Order of the Arrow, BSA
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

global $mish_db_version;
$mish_db_version = 1;

function mish_create_table($ddl)
{
    global $wpdb;
    $table = "";
    if (preg_match("/create table\s+`?(\w+)`?\s/i", $ddl, $match)) {
        $table = $match[1];
    } else {
        return false;
    }
    foreach ($wpdb->get_col("SHOW TABLES", 0) as $tbl) {
        if ($tbl == $table) {
            return true;
        }
    }
    // if we get here it doesn't exist yet, so create it
    $wpdb->query($ddl);
    // check if it worked
    foreach ($wpdb->get_col("SHOW TABLES", 0) as $tbl) {
        if ($tbl == $table) {
            return true;
        }
    }
    return false;
}

register_activation_hook(__FILE__, 'mish_install');
function mish_install()
{
    /* Reference: http://codex.wordpress.org/Creating_Tables_with_Plugins */

    global $wpdb;
    global $mish_db_version;

    $dbprefix = $wpdb->prefix . "mish_";

    //
    // CREATE THE TABLES IF THEY DON'T EXIST
    //

    // This code checks if each table exists, and creates it if it doesn't.
    // No checks are made that the DDL for the table actually matches,
    // only if it doesn't exist yet. If the columns or indexes need to
    // change it'll need update code (see below).

    $sql = "CREATE TABLE `${dbprefix}chapters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oalm_chapter_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `chapter_name` varchar(120) CHARACTER SET utf8 DEFAULT NULL,
  `chief_email` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `adviser_email` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
    );";
    mish_create_table($sql);

    $sql = "CREATE TABLE `${dbprefix}districts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `district_name` varchar(120) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
    );";
    mish_create_table($sql);

    $sql = "CREATE TABLE `${dbprefix}units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL,
  `district_id` int(11) NOT NULL,
  `unit_type` varchar(10) NOT NULL,
  `unit_num` int(11) NOT NULL,
  `unit_desig` varchar(2) NOT NULL DEFAULT '',
  `unit_city` varchar(45) DEFAULT NULL,
  `unit_state` varchar(3) DEFAULT NULL,
  `unit_county` varchar(45) DEFAULT NULL,
  `charter_org` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `troop_UNIQUE` (`district_id`,`unit_type`,`unit_num`,`unit_desig`),
  KEY `chapter_id_fkey_idx` (`chapter_id`),
  KEY `district_id_fkey_idx` (`district_id`),
  CONSTRAINT `chapter_id_fkey` FOREIGN KEY (`chapter_id`) REFERENCES `${dbprefix}chapters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `district_id_fkey` FOREIGN KEY (`district_id`) REFERENCES `${dbprefix}districts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );";
    mish_create_table($sql);

    $sql = "CREATE TABLE `${dbprefix}induction_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Status` VARCHAR(20) NOT NULL,
  `District` VARCHAR(30) NOT NULL,
  `Chapter` VARCHAR(30) NOT NULL,
  `Unit_Location` VARCHAR(30),
  `Unit_Type` VARCHAR(30),
  `Unit_Number` VARCHAR(30),
  `Unit_Designation` VARCHAR(30),
  `Unit_City` VARCHAR(30),
  `Unit_State` VARCHAR(30),
  `Unit_County` VARCHAR(30),
  `Visit_Type` VARCHAR(30),
  `Visit_Date` VARCHAR(30),
  `Visit_Time` VARCHAR(30),
  `Virtual_Visit` VARCHAR(30),
  `Unit_Leader` VARCHAR(30),
  `Unit_Leader_Phone` VARCHAR(30),
  `Unit_Leader_Email` VARCHAR(30),
  `Requester_Name` VARCHAR(30),
  `Requester_Phone` VARCHAR(30),
  `Requester_Email` VARCHAR(30),
  `Requested_Dates` VARCHAR(30),
  `Elected_Count` VARCHAR(30),
  `Announcement_Status` VARCHAR(30),
  `Announcement_Date` VARCHAR(30),
  `Posted_Date` VARCHAR(30),
  `Approved_Date` VARCHAR(30),
  `Callout_Event` VARCHAR(30),
  `Welcome_Event` VARCHAR(30),
  `Decline_Reason` VARCHAR(30)
   );";
    mish_create_table($sql);

    //
    // DATABASE UPDATE CODE
    //

    // Check the stored database schema version and compare it to the version
    // required for this version of the plugin.  Run any SQL updates required
    // to bring the DB schema into compliance with the current version.
    // If new tables are created, you don't need to do anything about that
    // here, since the table code above takes care of that.  All that needs
    // to be done here is to make any required changes to existing tables.
    // Don't forget that any changes made here also need to be made to the DDL
    // for the tables above.

    $installed_version = get_option("mish_db_version");
    if (empty($installed_version)) {
        // if we get here, it's a new install, and the schema will be correct
        // from the initialization of the tables above, so make it the
        // current version so we don't run any update code.
        $installed_version = $mish_db_version;
        add_option("mish_db_version", $mish_db_version);
    }

    # if ($installed_version < 2) {
    #     # run code for updating from schema version 1 to version 2 here.
    # }

    # if ($installed_version < 3) {
    #     # run code for updating from schema version 2 to version 3 here.
    # }

    # insert next database revision update code immediately above this line.
    # don't forget to increment $mish_db_version at the top of the file.
    if ($installed_version < $mish_db_version) {
        // updates are done, update the schema version to say we did them
        update_option("mish_db_version", $mish_db_version);
    }
}

add_action('plugins_loaded', 'mish_update_db_check');
function mish_update_db_check()
{
    # first: if the database schema in the code doesn't match what's in the DB
    # go upgrade it
    global $mish_db_version;
    if (get_option("mish_db_version") != $mish_db_version) {
        mish_install();
    }
    # second: set up any defaults for the settings the plugin uses
    # add_option does nothing if the option already exists, sets default value
    # if it does not.
    #add_option('mish_some_option_name', 'the value');
}

