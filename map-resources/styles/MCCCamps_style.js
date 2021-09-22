var style_MCCCamps = function(feature, resolution){
    var context = {
        feature: feature,
        variables: {}
    };
    var value = ""
    var labelText = "";
    size = 0;
    var offsetY = -15;
    var labelFont = "18px \'.SF NS Text\', sans-serif";
    if (resolution > 1300) {
        labelFont = "6px \'.SF NS Text\', sans-serif";
        offsetY = -10;
    }
    else if (resolution > 400) {
        labelFont = "10px \'.SF NS Text\', sans-serif";
        offsetY = -12;
    }
    else if (resolution > 350) {
        labelFont = "14px \'.SF NS Text\', sans-serif";
        offsetY = -14;
    }

    var labelFill = "#000000";
    var bufferColor = "";
    var bufferWidth = 0;
    var textAlign = "center";
    var offsetX = 0;
    var placement = 'point';
    if (feature.get("name") !== null) {
        labelText = String(feature.get("name"));
    }
    var style = [ new ol.style.Style({
        stroke: new ol.style.Stroke({color: 'rgba(185,185,185,1.0)', lineDash: null, lineCap: 'square', lineJoin: 'bevel', width: 0}),
        text: new ol.style.Text({
            font: labelFont,
            text: labelText,
            textBaseline: "middle",
            textAlign: "center",
            offsetX: offsetX,
            offsetY: offsetY,
            placement: placement,
            maxAngle: 0,
            fill: new ol.style.Fill({
              color: labelFill
            }),
            stroke: new ol.style.Stroke({
                color: bufferColor,
                width: bufferWidth
            }),
        }),
        image: new ol.style.Icon({
            src: mish_map.imagedir + 'camping-icon.svg',
        }),
    })];

    return style;
};
