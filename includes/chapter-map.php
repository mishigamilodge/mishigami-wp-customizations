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
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
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

add_action( 'wp_ajax_mish_load_chapter_blurb', 'mish_load_chapter_blurb' );
add_action( 'wp_ajax_nopriv_mish_load_chapter_blurb', 'mish_load_chapter_blurb' ); // need this to serve non logged in users
function mish_load_chapter_blurb() {
    $chapter = $_GET['chapter'];
    $posts = get_posts(array('title' => $chapter, 'post_type' => 'mish_chapter'));
    if (count($posts) == 0) {
        wp_send_json(array('content' => 'No content found for this chapter.'));
        die();
    } else {
        $content = apply_filters( 'the_content', $posts[0]->post_content );
        wp_send_json(array('content' => $content));
        die();
    }
}

# set up custom post type for the email templates
function mish_chapter_post_type() {
    $labels = array(
        'name'                => 'Chapter Blurbs',
        'singular_name'       => 'Chapter Blurb',
        'menu_name'           => 'Chapter Blurbs',
        'all_items'           => 'All Blurbs',
        'view_item'           => 'View Blurb',
        'add_new_item'        => 'Add New Blurb',
        'add_new'             => 'Add New',
        'edit_item'           => 'Edit Blurb',
        'update_item'         => 'Update Blurb',
        'search_items'        => 'Search Blurbs',
        'not_found'           => 'Not Found',
        'not_found_in_trash'  => 'Not found in Trash',
    );
    $args = array(
        'label'               => 'mish_chapter',
        'description'         => 'Chapter blurbs for display on the chapter map page',
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'revisions', ),
        'hierarchical'        => false,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-location',
        'show_in_nav_menus'   => false,
        'show_in_admin_bar'   => false,
        'menu_position'       => 30,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
        'show_in_rest'        => true,
    );
    register_post_type( 'mish_chapter', $args );

}
add_action( 'init', 'mish_chapter_post_type', 0 );
add_action( 'admin_head', function() {
    $screen = get_current_screen();
    if ( 'mish_chapter' != $screen->post_type )
        return;
    $description = "
<p>Blurbs for each chapter, to be shown on the chapter map page.  The title
should match the name of the chapter, as returned by the label on the map.  You
can put anything here you would have in a post (including shortcodes) but keep
in mind that space is limited, so don't get too extravagant. Suggestions: brief
teaser about your chapter, social media links for your chapter, a link to your
chapter web page if you have one, and a calendar of upcoming events (use a
shortcode to pull that from your chapter's google calendar).</p>
";
    get_current_screen()->add_help_tab(
        array(
            'id'      => 'overview',
            'title'   => __( 'Overview' ),
            'content' => $description,
        )
    );
});
add_filter( 'enter_title_here', function( $title ) {
    $screen = get_current_screen();

    if  ( 'mish_chapter' == $screen->post_type ) {
        $title = 'Chapter name';
    }

    return $title;
} );
