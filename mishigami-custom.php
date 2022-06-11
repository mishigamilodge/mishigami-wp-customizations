<?php
/*
 * Plugin Name: Mishigami Lodge Custom Stuff
 * Plugin URI: https://github.com/mishigamilodge/mishigami-wp-customizations
 * Description: Wordpress plugin to house custom stuff for this website
 * Version: 1.0
 * Author: Mishigami Lodge
 * Author URI: https://mishigami.org/
 * Author Email: codemonkeys@mishigami.org
 * */

/*
 * Copyright (C) 2020 Mishigami Lodge, Order of the Arrow, BSA
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

// We embed the openlayer library, but I want to keep track of what
// version we're embedding, so set the name of the directory it's in
// here and reference the variable from anywhere that needs it.
global $mish_openlayers;
$mish_openlayers = 'openlayers-v6.4.3-dist';

// All of the meat is in the includes directory, to keep it organized.
// Just pull it all in from here.
require_once("includes/initial-setup.php");
require_once("includes/admin-settings.php");
require_once("includes/chapter-map.php");
require_once("includes/unit-picker.php");
require_once("includes/induction-stats.php");
