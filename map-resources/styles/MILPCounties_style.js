var style_MILPCounties = function(feature, resolution){
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

    var labelFill = "#999999";
    var bufferColor = "";
    var bufferWidth = 0;
    var textAlign = "center";
    var offsetX = 8;
    var offsetY = 3;
    var placement = 'point';
    if (feature.get("Name") !== null) {
        labelText = String(feature.get("Name"));
    }
    var style = [ new ol.style.Style({
        stroke: new ol.style.Stroke({color: 'rgba(185,185,185,1.0)', lineDash: null, lineCap: 'square', lineJoin: 'bevel', width: 0}),
        text: createTextStyle(feature, resolution, labelText, labelFont,
                              labelFill, placement, bufferColor,
                              bufferWidth)
    })];

    return style;
};
