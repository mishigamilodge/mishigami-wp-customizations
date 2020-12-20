var size = 0;
var placement = 'point';

var style_MCCDistricts_2 = function(feature, resolution){
    var context = {
        feature: feature,
        variables: {}
    };
    var value = ""
    var labelText = "";
    size = 0;
    var labelFont = "18px \'.SF NS Text\', sans-serif";
    if (resolution > 1300) {
        labelFont = "6px \'.SF NS Text\', sans-serif";
    }
    else if (resolution > 400) {
        labelFont = "10px \'.SF NS Text\', sans-serif";
    }
    else if (resolution > 350) {
        labelFont = "14px \'.SF NS Text\', sans-serif";
    }
    var labelFill = "rgba(0,0,165,1.0)";
    var bufferColor = "";
    var bufferWidth = 0;
    var textAlign = "center";
    var offsetX = 8;
    var offsetY = 3;
    var placement = 'point';
    if (feature.get("name") !== null) {
        labelText = String(feature.get("name")).replaceAll(" ","\n");
    }
    var style = [ new ol.style.Style({
        stroke: new ol.style.Stroke({color: 'rgba(0,0,165,1.0)', lineDash: null, lineCap: 'butt', lineJoin: 'miter', width: 2}),fill: new ol.style.Fill({color: 'rgba(183,72,75,0.0)'}),
        text: createTextStyle(feature, resolution, labelText, labelFont,
                              labelFill, placement, bufferColor,
                              bufferWidth)
    })];

    return style;
};
