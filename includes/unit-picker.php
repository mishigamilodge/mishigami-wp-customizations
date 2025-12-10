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

add_action( 'wp_enqueue_scripts', 'mish_unit_autocomplete_loader' );
function mish_unit_autocomplete_loader() {
    $pageuri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    if (in_array($pageuri, [
            '/chapters/',
            '/transfer/'
        ])) {
        $wp_scripts = wp_scripts();
        wp_enqueue_script( 'jquery-ui-autocomplete');
        wp_enqueue_style('jquery-ui-css',
                    'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css',
                    false,
                    $wp_scripts->registered['jquery-ui-core']->ver,
                    false);
        wp_enqueue_style( 'mish-unit-picker-css', plugins_url('css/unit-picker.css', dirname(__FILE__)), array(), filemtime(dirname(__FILE__) . '/../css/unit-picker.css'));
        wp_enqueue_script( 'mish-unit-picker', plugins_url('js/unit-picker.js', dirname(__FILE__)), array( 'jquery-ui-autocomplete' ), filemtime(dirname(__FILE__) . '/../js/unit-picker.js'), true );
        wp_localize_script( 'mish-unit-picker', 'mish', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mish_get_units_autocomplete_nonce' ),
        ) );
    }
}

add_action( 'wp_ajax_mish_get_units_autocomplete', 'mish_get_units_autocomplete' );
add_action( 'wp_ajax_nopriv_mish_get_units_autocomplete', 'mish_get_units_autocomplete' ); // need this to serve non logged in users
function mish_get_units_autocomplete() {
    check_ajax_referer( 'mish_get_units_autocomplete_nonce', 'nonce' );
    global $wpdb;
    $dbprefix = $wpdb->prefix . 'mish_';
    
    // Sanitize the search term
    $term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
    // Limit term length to prevent abuse
    if (strlen($term) > 100) {
        $term = substr($term, 0, 100);
    }
    // Escape for LIKE clause
    $search_term = $wpdb->esc_like($term);
    
    $oaonly = isset( $_GET['oaonly'] ) ? intval( $_GET['oaonly'] ) : 0;
    $districts = isset( $_GET['districts'] ) ? intval( $_GET['districts'] ) : 0;
    
    // Build the LIKE pattern for searching
    $search_pattern = '%' . $search_term . '%';
    
    if ($districts) {
        // Query with districts filter
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT unit_type, unit_num, unit_desig, chapter_name, oalm_chapter_name, district_name, unit_city, charter_org
            FROM `{$dbprefix}units` AS un
            LEFT JOIN `{$dbprefix}chapters` AS ch ON un.chapter_id = ch.id
            LEFT JOIN `{$dbprefix}districts` AS di ON un.district_id = di.id
            WHERE (CONCAT(un.unit_type, ' ', un.unit_num, ' ', un.unit_desig) LIKE %s" .
            ($oaonly ? " AND un.unit_type IN('Troop', 'Ship', 'Crew')" : "") .
            " OR (un.unit_type IN('District','Council') AND di.district_name LIKE %s))
            ORDER BY un.unit_num, un.unit_desig
        ", array($search_pattern, $search_pattern)));
    } else {
        // Query without districts filter
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT unit_type, unit_num, unit_desig, chapter_name, oalm_chapter_name, district_name, unit_city, charter_org
            FROM `{$dbprefix}units` AS un
            LEFT JOIN `{$dbprefix}chapters` AS ch ON un.chapter_id = ch.id
            LEFT JOIN `{$dbprefix}districts` AS di ON un.district_id = di.id
            WHERE (CONCAT(un.unit_type, ' ', un.unit_num, ' ', un.unit_desig) LIKE %s" .
            ($oaonly ? " AND un.unit_type IN('Troop', 'Ship', 'Crew')" : "") .
            ")
            ORDER BY un.unit_num, un.unit_desig
        ", array($search_pattern)));
    }
    
    wp_send_json($results);
}
