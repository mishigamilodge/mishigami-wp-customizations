<?php
/*
 * Copyright (C) 2018 David D. Miller
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

add_shortcode( 'mish_chapter_map', 'mish_chapter_map' );
function mish_chapter_map() {
    global $mish_openlayer;
    wp_enqueue_style( 'mish-map', plugins_url('css/chapter-map.css', dirname(__FILE__)));
    wp_enqueue_style( 'mish-openlayer-css', plugins_url($mish_openlayer . '/ol.css', dirname(__FILE__)));
    wp_enqueue_script( 'openlayer', plugins_url($mish_openlayer . '/ol.js', dirname(__FILE__)), false, false, true );
    wp_enqueue_script( 'mish-map-layer-counties', plugins_url('map-resources/layers/MILPCounties_1.js', dirname(__FILE__)), array( 'openlayer' ), false, true );
    wp_enqueue_script( 'mish-map-layer-counties-style', plugins_url('map-resources/styles/MILPCounties_1_style.js', dirname(__FILE__)), array( 'openlayer', 'mish-map-layer-counties' ), false, true );
    wp_enqueue_script( 'mish-map-layer-chapters', plugins_url('map-resources/layers/MishigamiChapters_3.js', dirname(__FILE__)), array( 'openlayer' ), false, true );
    wp_enqueue_script( 'mish-map-layer-chapters-style', plugins_url('map-resources/styles/MishigamiChapters_3_style.js', dirname(__FILE__)), array( 'openlayer', 'mish-map-layer-chapters', 'mish-map-layer-counties' ), false, true );
    wp_enqueue_script( 'mish-map-js', plugins_url('js/chapter-map.js', dirname(__FILE__)), array( 'openlayer', 'jquery' ), false, true );
    wp_localize_script( 'mish-map-js', 'mish_map', array(
        'layersdir' => plugins_url('map-resources/layers/', dirname(__FILE__)),
    ) );

    ob_start();
    ?>
    <div id="mish_map"></div>
    <div id="mish_map_info">Click on a chapter on the map to see its info here.</div>
    <div class="clear"></div>
    <div id="mish_map_layers">
<table><thead><tr><td>Select Layers</td></tr></thead>
<tbody><tr><td>
<form>
      <input type="checkbox" checked="checked" name="baselayer" id="baselayer"><label for="baselayer"> OpenStreetMap</label><br>
      <input type="checkbox" checked="checked" name="countylayer" id="countylayer"><label for="countylayer"> Michigan Counties (Lower Peninsula)</label><br>
      <input type="checkbox" checked="checked" name="chapterlayer" id="chapterlayer"><label for="chapterlayer"> Michigami Chapters</label><br>
</form></td></tr></tbody></table>
    </div>
    <?php
    return ob_get_clean();
}
