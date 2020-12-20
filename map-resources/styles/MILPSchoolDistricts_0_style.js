var size = 0;
var placement = 'point';

var style_MILPSchoolDistricts_0 = function(feature, resolution){
    var context = {
        feature: feature,
        variables: {}
    };
    var value = ""
    var labelText = "";
    size = 0;
    var labelFont = "14px \'.SF NS Text\', sans-serif";
    if (resolution > 1300) {
        labelFont = "4px \'.SF NS Text\', sans-serif";
    }
    else if (resolution > 400) {
        labelFont = "6px \'.SF NS Text\', sans-serif";
    }
    else if (resolution > 350) {
        labelFont = "7px \'.SF NS Text\', sans-serif";
    }
    else if (resolution > 170) {
        labelFont = "10px \'.SF NS Text\', sans-serif";
    }
    var labelFill = "#000000";
    var bufferColor = "";
    var bufferWidth = 0;
    var textAlign = "left";
    var offsetX = 8;
    var offsetY = 3;
    var placement = 'point';
    if (feature.get("Name") !== null) {
        labelText = String(feature.get("Name")).replaceAll(" ","\n");
    }
    var style = [ new ol.style.Style({
        stroke: new ol.style.Stroke({color: 'rgba(35,35,35,1.0)', lineDash: null, lineCap: 'butt', lineJoin: 'miter', width: 0}),
        text: createTextStyle(feature, resolution, labelText, labelFont,
                              labelFill, placement, bufferColor,
                              bufferWidth)
    })];

    return style;
};
