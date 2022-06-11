<?php
/*
 * Copyright (C) 2022 David D. Miller
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

function mish_Chart_enqueue($hook) {
    wp_enqueue_script( 'Chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.min.js', __FILE__ );
}
add_shortcode( 'mish_induction_stats', 'mish_induction_stats' );
function mish_induction_stats() {
    add_action( 'wp_enqueue_scripts', 'mish_Chart_enqueue' );
    global $wpdb;
    $dbprefix = $wpdb->prefix . "mish_";
    wp_enqueue_style( 'mish-map', plugins_url('css/chapter-map.css', dirname(__FILE__)));
    wp_enqueue_script( 'mish-map-js', plugins_url('js/chapter-map.js?v=3', dirname(__FILE__)), array( 'openlayer', 'jquery' ), false, true );
    wp_localize_script( 'mish-map-js', 'mish_map', array(
        'layersdir' => plugins_url('map-resources/layers/', dirname(__FILE__)),
        'imagedir' => plugins_url('img/', dirname(__FILE__)),
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ) );

    $chapters = [];
    ob_start();
    $results = $wpdb->get_results("SELECT DISTINCT `Chapter` FROM `${dbprefix}induction_data`", OBJECT_K);
    $totalunits = $wpdb->get_var("SELECT COUNT(*) FROM `${dbprefix}induction_data`");
    foreach ($results AS $obj) {
       $obj->notsched = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Not Scheduled' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->declined = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Declined' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->requested = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Requested' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->sched = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Scheduled' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->posted = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Posted' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->approved = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data`  WHERE `Status` = 'Approved' AND `Chapter` = %s", array($obj->Chapter)));
       $obj->total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `${dbprefix}induction_data` WHERE `Chapter` = %s", array($obj->Chapter)));
       $chapters[$obj->Chapter] = $obj;
    }

?>
<canvas id="mishElectionChart" width="200" height="100"></canvas>
<div style="display:none;">
<!-- this hidden div is to ensure we have something on the page with these
     classes so we can pull the colors out of them to use in the charts, and
     then the colors can be changed in the CSS and they're consistent
     everywhere we use them -->
<div class="elec_notscheduled"></div>
<div class="elec_declined"></div>
<div class="elec_requested"></div>
<div class="elec_scheduled"></div>
<div class="elec_pastdue"></div>
<div class="elec_posted"></div>
<div class="elec_approved"></div>
</div>
<script type="text/javascript">
var $j = jQuery.noConflict();
function fixalpha(color, newalpha) {
    var pat = /^rgba?\((\d+),\s*(\d+),\s*(\d+)/;
    var m = pat.exec(color);
    return "rgba(" + m[1] + ", " + m[2] + ", " + m[3] + ", " + newalpha + ")";
}
<?php
    $chapterlist = sort(array_keys($chapters));
    $labellist = $chapterlist;
    $labellist[] = 'Entire Lodge';
?>
var ue_chartconfig = {
    type: 'horizontalBar',
    data: {
        labels: <?php echo wp_json_encode($labellist); ?>,
        datasets: [
        {
            label: 'Approved',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->approved / $obj->total) * 100);
                $count++;
                $total = $total + $obj->approved;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_approved").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_approved").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: 'Posted',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->posted / $obj->total) * 100);
                $count++;
                $total = $total + $obj->posted;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_posted").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_posted").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: 'Scheduled',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->sched / $obj->total) * 100);
                $count++;
                $total = $total + $obj->sched;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_sched").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_sched").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: 'Requested',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->requested / $obj->total) * 100);
                $count++;
                $total = $total + $obj->requested;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_requested").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_requested").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: 'Declined',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->declined / $obj->total) * 100);
                $count++;
                $total = $total + $obj->declined;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_declined").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_declined").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: 'Not Scheduled',
            data: [<?php
            $count = 0;
            $total = 0;
            foreach ($chapterlist AS $chapter) {
                $obj = $chapters->$chapter;
                if ($count > 0) { echo ","; };
                echo htmlspecialchars(($obj->notsched / $obj->total) * 100);
                $count++;
                $total = $total + $obj->notsched;
            }
            echo "," . htmlspecialchars(($total / $totalunits) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_notsched").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_notsched").css("background-color"), 1),
            borderWidth: 1
        }
        ]
    },
    options: {
        tooltips: {
            callbacks: {
                label: function(tooltipitem, data) {
                    return data.datasets[tooltipitem.datasetIndex].label + ": " + (Math.round(tooltipitem.xLabel * 10) / 10) + "%";
                }
            }
        },
        legend: {
            labels: {
                boxWidth: 20
            }
        },
        scales: {
            xAxes: [{
                stacked: true,
                ticks: {
                    beginAtZero:true,
                    suggestedMax:100
                }
            }],
            yAxes: [{
                stacked: true
            }]
        }
    }
};

var ue_chart = new Chart($j("#mishElectionChart"), ue_chartconfig);
</script>
    <?php
    return ob_get_clean();
}
