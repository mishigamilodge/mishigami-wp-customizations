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

## BEGIN OA TOOLS MENU CODE

# This code is designed to be used in any OA-related plugin. It conditionally
# Adds an "OA Tools" top-level menu in the WP Admin if it doesn't already
# exist. Any OA-related plugins can then add submenus to it.
# NOTE: if you copy this to another plugin, you also need to copy the
# referenced SVG file.

if (!function_exists('oa_tools_add_menu')) {
    add_action( 'admin_menu', 'oa_tools_add_menu', 9 );
    function oa_tools_add_menu() {
        $oa_tools_icon = file_get_contents("img/oa_trademark.svg", true);
        global $menu;
        $menu_exists = false;
        foreach($menu as $k => $item) {
            if ($item[2] == 'oa_tools') {
                $menu_exists = true;
            }
        }
        if (!$menu_exists) {
            add_menu_page( "OA Tools", "OA Tools", 'none', 'oa_tools', 'oa_tools_menu', 'data:image/svg+xml;base64,' . base64_encode($oa_tools_icon), 3 );
        }
    }
    function oa_tools_menu() {
        # this is a no-op, the page can be blank. It's going to go to the first
        # submenu anyway when it's picked.
    }
}

## END OA TOOLS MENU CODE

add_action('admin_menu', 'mish_config_menu', 9);
function mish_config_menu() {
    add_submenu_page( "oa_tools", "Units", "Units", 'manage_options', 'mish_config_units', 'mish_config_units');
    add_submenu_page( "oa_tools", "Chapters", "Chapters", 'manage_options', 'mish_config_chapters', 'mish_config_chapters');
}
function mish_tools_page() {
    # this is a no-op, the page can be blank. It's going to go to the first
    # submenu anyway when it's picked.
}
function mish_config_units() {
    global $wpdb;
    $dbprefix = $wpdb->prefix . "mish_";

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    // =========================
    // form processing code here
    // =========================

    if (isset($_FILES['oa_unit_file'])) {
        if (preg_match('/\.xlsx$/', $_FILES['oa_unit_file']['name'])) {
            require_once plugin_dir_path(__FILE__) . '../vendor/autoload.php';

            $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $objReader->setReadDataOnly(true);
            $objReader->setLoadSheetsOnly(array("All"));
            $objSpreadsheet = $objReader->load($_FILES["oa_unit_file"]["tmp_name"]);
            $objWorksheet = $objSpreadsheet->getActiveSheet();
            $columnMap = array(
            'Chapter'           => 'chapter_id', # REFERENCE!
            'District'          => 'district_id', # REFERENCE!
            'Unit Type'         => 'unit_type',
            'Unit Num.'         => 'unit_num',
            'Unit Des.'         => 'unit_desig',
            'City'              => 'unit_city',
            'State'             => 'unit_state',
            'County'            => 'unit_county',
            'Charter Org.'      => 'charter_org',
            );
            $editable_columns = [
            'chapter_id',
            'unit_city',
            'unit_state',
            'unit_county',
            'charter_org',
            ];
            $complete = 0;
            $insertrecordcount = 0;
            $updaterecordcount = 0;
            $deleterecordcount = 0;
            $alreadyexists = 0;
            $error_output = "";
            $districts = $wpdb->get_results("SELECT district_name, id FROM ${dbprefix}districts", OBJECT_K);
            $chapters = $wpdb->get_results("SELECT oalm_chapter_name, id FROM ${dbprefix}chapters", OBJECT_K);

            foreach ($objWorksheet->getRowIterator() as $row) {
                $rowData = array();
                if ($row->getRowIndex() == 1) {
                    # this is the header row, grab the headings
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $cellValue = $cell->getValue();
                        if (isset($columnMap[$cellValue])) {
                            $rowData[$columnMap[$cellValue]] = 1;
                            #echo "Found column " . htmlspecialchars($cell->getColumn()) . " with title '" . htmlspecialchars($cellValue) . "'<br>" . PHP_EOL;
                        } else {
                            #echo "Discarding unknown column " . htmlspecialchars($cell->getColumn()) . " with title '" . htmlspecialchars($cellValue) . "'<br>" . PHP_EOL;
                        }
                    }
                    $missingColumns = array();
                    foreach ($columnMap as $key => $value) {
                        if (!isset($rowData[$value])) {
                            $missingColumns[] = $key;
                        }
                    }
                    if ($missingColumns) {
                        ?><div class="error"><p><strong>Import failed.</strong></p><p>Missing required columns: <?php esc_html_e(implode(", ", $missingColumns)) ?></div><?php
                    $complete = 1; # Don't show "may have failed" box at the bottom
                    break;
                    } else {
                        #echo "<strong>Data format validated:</strong> Importing new data...<br>" . PHP_EOL;
                        # we just validated that we have a good data file, start handling data
                        $wpdb->show_errors();
                        ob_start();
                        # now we're ready for the incoming from the rest of the file.
                    }
                } else {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    foreach ($cellIterator as $cell) {
                        $columnName = $objWorksheet->getCell($cell->getColumn() . "1")->getValue();
                        $value = "";
                        if ($columnName === "Chapter") {
                            # the data will have a name, we need the foreign key reference ID
                            $chapter_name = $cell->getValue();
                            if (!$chapter_name) { $chapter_name = ""; } # null -> empty string
                            if (empty($chapters[$chapter_name])) {
                                // if it doesn't exist, create it, then fetch the newly created ID and add it to the list
                                // take a guess at a human-readable name by cutting stuff before a leading hyphen
                                $human_chapter_name = preg_replace('/^.*- /', '', $chapter_name);
                                $wpdb->insert("${dbprefix}chapters", [
                                    'oalm_chapter_name' => $chapter_name,
                                    'chapter_name' => $human_chapter_name
                                ], [ '%s', '%s' ]);
                                $chapter_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ${dbprefix}chapters WHERE oalm_chapter_name = %s", $chapter_name));
                                $chapters[$chapter_name] = (object) ["oalm_chapter_name" => $chapter_name, "id" => $chapter_id];
                            }
                            $chapter_row = $chapters[$chapter_name];
                            $value = $chapter_row->id;
                        } elseif ($columnName === "District") {
                            # the data will have a name, we need the foreign key reference ID
                            $district_name = $cell->getValue();
                            if (!$district_name) { $district_name = ""; } # null -> empty string
                            if (empty($districts[$district_name])) {
                                // if it doesn't exist, create it, then fetch the newly created ID and add it to the list
                                $wpdb->insert("${dbprefix}districts", [ 'district_name' => $district_name ], [ '%s' ]);
                                $district_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM ${dbprefix}districts WHERE district_name = %s", $district_name));
                                $districts[$district_name] = (object) ["oalm_district_name" => $district_name, "id" => $district_id];
                            }
                            $district_row = $districts[$district_name];
                            $value = $district_row->id;
                        } else {
                            $value = $cell->getValue();
                        }
                        if (isset($columnMap[$columnName])) {
                            $rowData[$columnMap[$columnName]] = $value;
                        }
                    }
                    $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM ${dbprefix}units WHERE district_id = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s", $rowData['district_id'], $rowData['unit_type'], $rowData['unit_num'], $rowData['unit_desig']));
                    if ((null === $existing) && ($rowData['unit_desig'] == "BT")) {
                        # if the unit is designated Boy Troop but we didn't
                        # get a match, look it up again without a designator
                        # since all undesignated troops used to be boy troops.
                        $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM ${dbprefix}units WHERE district_id = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s", $rowData['district_id'], $rowData['unit_type'], $rowData['unit_num'], ""));
                    }
                    if (null === $existing) {
                        # still didn't get a match, it's a new unit
                        $unit_desig = $rowData['unit_desig'];
                        if ($unit_desig == "" && $unit_desig !== "") { $rowData['unit_desig'] = ""; }
                        if ($rowData['unit_desig'] != "") { $unit_desig = "-" . $rowData['unit_desig']; }
                        echo "[+] Adding new unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                        if ($wpdb->insert("${dbprefix}units", $rowData, array('%d','%d','%s','%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'))) {
                            $insertrecordcount++;
                        }
                    } else {
                        # if we got here, there's an existing row for this troop. Check if it needs updating.
                        $updated = 0;
                        $alreadyexists++;
                        $unit_desig = $rowData['unit_desig'];
                        if ($rowData['unit_desig'] != "") { $unit_desig = "-" . $rowData['unit_desig']; }
                        if ($existing->unit_desig != $rowData['unit_desig']) {
                            # should only happen with "" -> "BT"
                            if ($updated == 0) {
                                echo "### processing existing unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                            }
                            echo "   => updating unit_desig from '' to 'BT'\n";
                            $wpdb->update("${dbprefix}units", ['unit_desig' => $rowData['unit_desig']], ['id' => $existing->id], ["%s"], ["%d"]);
                            $updated++;
                        }
                        foreach ($editable_columns as $column) {
                            if ($existing->$column != $rowData[$column]) {
                                if ($updated == 0) {
                                    echo "### processing existing unit: " . $district_name . " " . $rowData['unit_type'] . " " . $rowData['unit_num'] . $unit_desig . "\n";
                                }
                                echo "   => updating " . $column . " from '" . $existing->$column . "' to '" . $rowData[$column] . "'\n";
                                $wpdb->update("${dbprefix}units", [$column => $rowData[$column]], ['id' => $existing->id], ["%s"], ["%d"]);
                                $updated++;
                            }
                        }
                        if ($updated) { $updaterecordcount++; }
                    }
                }
            }

            #####################
            # Check for units that no longer exist
            #####################

            # Get the list of units from the spreadsheet as an array so we can index it
            $totalRows = $row->getRowIndex();
            $sheetUnits = $objWorksheet->rangeToArray("B2:F" . $totalRows);
            $sheetUnitIndex = array();
            foreach ($sheetUnits as $su) {
                $sheetUnitIndex[] = $su[0] . ":" . $su[1] . ":" . $su[2] . ":" . $su[3];
            }

            # Grab a list of units from the database
            $units = $wpdb->get_results("SELECT district_id, unit_type, unit_num, unit_desig FROM ${dbprefix}units", ARRAY_A);
            $districts = $wpdb->get_results("SELECT id, district_name FROM ${dbprefix}districts", OBJECT_K);
            foreach ($units as $unit) {
                $district_id = $unit['district_id'];
                $district_row = $districts[$district_id];
                $district_name = $district_row->district_name;
                $unit_type = $unit['unit_type'];
                $unit_num = $unit['unit_num'];
                $unit_desig = $unit['unit_desig'];
                $indexkey = $district_name . ":" . $unit_type . ":" . $unit_num . ":" . $unit_desig;
                if (!in_array($indexkey, $sheetUnitIndex)) {
                    # if we get here, unit exists in database but not in the spreadsheet
                    echo "[-] Unit to be removed: $district_name $unit_type $unit_num $unit_desig<br>";
                    echo "--> Checking for references....<br>";
                    $refcount = 0;
                    // if we ever add any tables that reference units, add them to this list.
                    $tablelist = array(
                    );
                    foreach ($tablelist as $table) {
                        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE district_id = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s",array($district_id, $unit_type, $unit_num, $unit_desig)));
                        if ($count > 0) {
                            echo "--> ** found $count record(s) in $table.<br>";
                            $refcount += $count;
                        }
                    }
                    if ($refcount == 0) {
                        echo "**> No references found, Removing unit!<br>";
                        $wpdb->query($wpdb->prepare("DELETE FROM ${dbprefix}units WHERE district_id = %s AND unit_type = %s AND unit_num = %s AND unit_desig = %s",array($unit['district_id'], $unit_type, $unit_num, $unit_desig)));
                        $deleterecordcount += 1;
                    }
                    else {
                        echo "--> Not removing unit.<br>";
                    }
                }
            }

            #####################
            # end of unit removal code
            #####################

            $output = ob_get_clean();
            ?><div class="updated"><p><strong>Read <?php esc_html_e($row->getRowIndex() - 1) ?> records from file.<br>
            Added <?php esc_html_e($insertrecordcount) ?> new units.<br>
            Updated <?php esc_html_e($updaterecordcount) ?> existing units.<br>
            Deleted <?php esc_html_e($deleterecordcount) ?> old units.<br>
            Encountered <?php esc_html_e($alreadyexists) ?> units already in DB.</strong></p>
            <?php
            if ($output) {
                ?><p>Detail follows:</p>
                <pre><?php echo $output ?></pre>
                <?php
            }
            ?></div><?php
        } else {
            ?><div class="error"><p><strong>Invalid file upload.</strong> Not an XLSX file.</p></div><?php
        }
    }

    // ============================
    // screens and forms start here
    // ============================

    $unit_count = $wpdb->get_var("SELECT COUNT(*) FROM ${dbprefix}units");
    ?>
<div class="wrap">
<h2>Update Unit List</h2>
<p>The unit list is used for the unit selector widget on some forms on the website.</p>
<p>There are currently <b><?php echo esc_html($unit_count) ?></b> units in the database.</p>
</div>
<h3>Import unit data from OALM</h3>
<p>Export file from OALM Must use the <b>Units for export to website</b> view in the Units module.</p>
<form action="" method="post" enctype="multipart/form-data">
<label for="oa_unit_file">Click Browse, then select the xlsx file exported from OALM's grid export, then click "Upload":</label><br>
<input type="file" name="oa_unit_file" id="oa_unit_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
<input type="submit" class="button button-primary" name="submit" value="Upload"><br>
</form>
    <?php

}
function mish_config_chapters() {
    global $wpdb;
    $dbprefix = $wpdb->prefix . "mish_";

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    ?><div class="wrap">
    <h2>Manage Chapter List</h2>
    <p>New chapters are automatically added when found during Unit import. Old unused chapters will need to be removed manually. Additional fields that aren't part of the import from LodgeMaster can be edited here.</p>
    <table class="widefat striped"><thead><tr><th>OALM Name</th><th>Human Readable Name</th><th>Chief Email</th><th>Adviser Email</th><th>Actions</th></tr></thead><tbody>
    <?php
    $chapters = $wpdb->get_results("SELECT id, oalm_chapter_name, chapter_name, chief_email, adviser_email FROM ${dbprefix}chapters", OBJECT_K);
    foreach ($chapters as $chapter) {
        ?><tr><?php
        foreach ($chapter as $key => $value) {
            if ($key != 'id') {
                ?><td id="<?php echo htmlspecialchars($key . "-" . $chapter->id); ?>"><?php echo htmlspecialchars($value); ?></td><?php
            }
        }
        ?><td>
            <a href="javascript:alert('Not implemented yet.');">Edit</a>
            <a href="javascript:alert('Not implemented yet.');">Delete</a>
        </td></tr><?php
    }
    ?></tbody></table>
    </div>
    <?php
}
