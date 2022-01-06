<?php
/*
 * Copyright (C) 2022 Mishigami Lodge, Order of the Arrow, BSA
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

add_action( 'wp_ajax_mish_get_units_autocomplete', 'mish_get_units_autocomplete' );
add_action( 'wp_ajax_nopriv_mish_get_units_autocomplete', 'mish_get_units_autocomplete' ); // need this to serve non logged in users
function mish_get_units_autocomplete() {
    global $wpdb;
    $dbprefix = $wpdb->prefix . "mish_";
    $term = $_GET['term'];
    $term = intval($term);
    $oaonly = $_GET['oaonly'];
    $oaonly = intval($oaonly);
    $extrawhere = "";
    if ($oaonly) {
        $extrawhere = " AND unit_type IN('Troop', 'Ship', 'Crew')";
    }
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT unit_type, unit_num, unit_desig, chapter_name, oalm_chapter_name, district_name, unit_city, charter_org
        FROM ${dbprefix}units AS un
        LEFT JOIN ${dbprefix}chapters AS ch ON un.chapter_id = ch.id
        LEFT JOIN ${dbprefix}districts AS di ON un.district_id = di.id
        WHERE un.unit_num LIKE %s $extrawhere
        ORDER BY un.unit_num, un.unit_desig
    ", Array("%" . $term . "%")));
    wp_send_json($results);
}
