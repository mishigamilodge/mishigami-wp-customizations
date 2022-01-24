var $j = jQuery.noConflict();

class OAUnitPicker {
    callbacks = [];
    id = '';
    oa_units_only = true;
    unit = null;
    constructor(id, oa_units_only) {
        this.id = id;
        this.oa_units_only = oa_units_only;
        this.unit = null;
        $j("#" + id).empty();
        $j("#" + id).append(
            '<div id="' + id + '_unit_picker">' +
            '<p style="margin-bottom: 0px;">Enter a partial or complete unit number, then choose the correct unit from the choices. If the unit is not listed, please contact the webmaster.</p>' +
            '<p style="margin-bottom: 0px;"><b>Unit:</b> <input type="text" id="' + id + '_unit_search"></p>' +
            '<p id="' + id + '_unit_search_nomatch" style="display: none; color: red;">No matches found</p>' +
            '<p style="color: red;">Unit number has not been selected yet.  Please choose a unit from the drop-down after typing the unit number.</p>' +
            '</div>' +
            '<div id="' + id + '_unit_picked">' +
            '<p style="margin-top: 1em; margin-bottom: 2em;"><span style="border: 1px solid black; padding: 0.5em;"><b>Selected unit:</b> <span id="' + id + '_unit_result"></span> &nbsp; <a href="#" id="' + id + '_change_unit">(change)</a></span></p>' +
            '</div>'
        );
        $j("#" + id + "_unit_search").data("unitpicker", this);
        $j("#" + id + "_unit_search").autocomplete({
            source: mish.ajaxurl + '?action=mish_get_units_autocomplete&oaonly=' + (this.oa_units_only ? "1" : "0"),
            select: function( event, ui ) {
                let id = event.target.id;
                let picker_id = id.replace(/_unit_search$/, "");
                let picker = $j("#" + picker_id).data('unitpicker');
                picker.unit = ui.item;
                $j('#' + picker_id + '_unit_result').html(ui.item.label);
                $j('#' + picker_id + '_unit_picker').hide();
                $j('#' + picker_id + '_unit_picked').show();
                $j('#' + picker_id + '_unit_search').val("");
                // if these input fields exist, drop the results into them
                $j('input[name="ChapterName"]').val(ui.item.oalm_chapter_name);
                $j('input[name="DistrictName"]').val(ui.item.district_name);
                $j('input[name="UnitType"]').val(ui.item.unit_type);
                $j('input[name="UnitNumber"]').val(ui.item.unit_num);
                $j('input[name="UnitDesignator"]').val(ui.item.unit_desig);
                // trigger any other change hooks any other scripts have registered
                picker.change();
                return false;
            },
            response: function( event, ui ) {
                let id = event.target.id;
                if (ui.content.length === 0) {
                    $j('#' + id + '_nomatch').show();
                } else {
                    $j('#' + id + '_nomatch').hide();
                }
            },
        }).autocomplete("instance")._renderItem = function( ul, item ) {
            //alert(JSON.stringify(item))
            let city = item.unit_city;
            if (!city) { city = "" }
            if (city.length > 2) {
                city = ' - ' + city;
            }
            let desig = item.unit_desig;
            if (!desig) { desig = "" }
            else { desig = ' ' + desig.substring(0,1); }
            item.label = item.district_name + " - " + item.unit_type + " " + item.unit_num + desig + city + " (" + item.chapter_name + ")";
            let li = $j('<li>')
                .attr("data-value", JSON.stringify(item))
                .append(item.label)
                .appendTo(ul);
            return li;
        }
        $j('#' + id + '_unit_picked').hide();
        $j('#' + id + '_change_unit').click(this.change_unit);
    }
    get oalm_chapter() {
        if (!this.unit) return "";
        return this.unit.oalm_chapter;
    }
    get chapter() {
        if (!this.unit) return "";
        return this.unit.chapter;
    }
    get district() {
        if (!this.unit) return "";
        return this.unit.district;
    }
    get unit_type() {
        if (!this.unit) return "";
        return this.unit.unit_type;
    }
    get unit_num() {
        if (!this.unit) return "";
        return this.unit.unit_num;
    }
    get unit_desig() {
        if (!this.unit) return "";
        return this.unit.unit_desig;
    }
    get unit_city() {
        if (!this.unit) return "";
        return this.unit.unit_city;
    }
    onchange(callback) {
        // TODO: check if callback already exists before adding it again
        // TODO: make sure it's a function call
        // TODO: make sure this is the correct way to append to an array
        this.callbacks.push(callback);
    }
    change() {
        let selfref = this;
        this.callbacks.forEach(function(callback){
            console.log('picker.change(): this=%o',selfref);
            callback(selfref);
        });
    }
    change_unit( event ) {
        let id = event.target.id;
        let picker_id = id.replace(/_change_unit$/, "");
        let picker = $j("#" + picker_id).data('unitpicker');
        $j('#' + picker.id + '_unit_picked').hide();
        $j('#' + picker.id + '_unit_picker').show();
        picker.unit = null;
        return false;
    }
}

$j(document).ready(function(){
    $j('.unit_picker_widget').each(function() {
        $j(this).data('unitpicker', new OAUnitPicker($j(this).attr("id"),$j(this).hasClass('oa_units_only')));
    });
});

