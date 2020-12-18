var wms_layers = [];

var format_MILPSchoolDistricts_0 = new ol.format.GeoJSON();
var features_MILPSchoolDistricts_0 = format_MILPSchoolDistricts_0.readFeatures(json_MILPSchoolDistricts_0, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MILPSchoolDistricts_0 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_MILPSchoolDistricts_0.addFeatures(features_MILPSchoolDistricts_0);
var lyr_MILPSchoolDistricts_0 = new ol.layer.Vector({
                declutter: true,
                source:jsonSource_MILPSchoolDistricts_0, 
                style: style_MILPSchoolDistricts_0,
                interactive: false,
                title: '<img src="styles/legend/MILPSchoolDistricts_0.png" /> MI LP School Districts'
            });
var format_MILPCounties_1 = new ol.format.GeoJSON();
var features_MILPCounties_1 = format_MILPCounties_1.readFeatures(json_MILPCounties_1, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MILPCounties_1 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_MILPCounties_1.addFeatures(features_MILPCounties_1);
var lyr_MILPCounties_1 = new ol.layer.Vector({
                declutter: true,
                source:jsonSource_MILPCounties_1, 
                style: style_MILPCounties_1,
                interactive: false,
                title: '<img src="styles/legend/MILPCounties_1.png" /> MI LP Counties'
            });
var format_MCCDistricts_2 = new ol.format.GeoJSON();
var features_MCCDistricts_2 = format_MCCDistricts_2.readFeatures(json_MCCDistricts_2, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MCCDistricts_2 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_MCCDistricts_2.addFeatures(features_MCCDistricts_2);
var lyr_MCCDistricts_2 = new ol.layer.Vector({
                declutter: true,
                source:jsonSource_MCCDistricts_2, 
                style: style_MCCDistricts_2,
                interactive: false,
                title: '<img src="styles/legend/MCCDistricts_2.png" /> MCC Districts'
            });
var format_MishigamiChapters_3 = new ol.format.GeoJSON();
var features_MishigamiChapters_3 = format_MishigamiChapters_3.readFeatures(json_MishigamiChapters_3, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MishigamiChapters_3 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_MishigamiChapters_3.addFeatures(features_MishigamiChapters_3);
var lyr_MishigamiChapters_3 = new ol.layer.Vector({
                declutter: true,
                source:jsonSource_MishigamiChapters_3, 
                style: style_MishigamiChapters_3,
                interactive: false,
                title: '<img src="styles/legend/MishigamiChapters_3.png" /> Mishigami Chapters'
            });

lyr_MILPSchoolDistricts_0.setVisible(false);lyr_MILPCounties_1.setVisible(true);lyr_MCCDistricts_2.setVisible(false);lyr_MishigamiChapters_3.setVisible(true);
var layersList = [lyr_MILPSchoolDistricts_0,lyr_MILPCounties_1,lyr_MCCDistricts_2,lyr_MishigamiChapters_3];
lyr_MILPSchoolDistricts_0.set('fieldAliases', {'Name': 'Name', 'descriptio': 'descriptio', 'timestamp': 'timestamp', 'begin': 'begin', 'end': 'end', 'altitudeMo': 'altitudeMo', 'tessellate': 'tessellate', 'extrude': 'extrude', 'visibility': 'visibility', 'drawOrder': 'drawOrder', 'icon': 'icon', 'OBJECTID': 'OBJECTID', 'FIPSCODE': 'FIPSCODE', 'FIPSNUM': 'FIPSNUM', 'LABEL': 'LABEL', 'TYPE': 'TYPE', 'DCODE': 'DCODE', 'ISD': 'ISD', 'SQKM': 'SQKM', 'SQMILES': 'SQMILES', 'ACRES': 'ACRES', 'VER': 'VER', 'LAYOUT': 'LAYOUT', 'PENINSULA': 'PENINSULA', 'ShapeSTAre': 'ShapeSTAre', 'ShapeSTLen': 'ShapeSTLen', });
lyr_MILPCounties_1.set('fieldAliases', {'fid': 'fid', 'Name': 'Name', 'description': 'description', 'timestamp': 'timestamp', 'begin': 'begin', 'end': 'end', 'altitudeMode': 'altitudeMode', 'tessellate': 'tessellate', 'extrude': 'extrude', 'visibility': 'visibility', 'drawOrder': 'drawOrder', 'icon': 'icon', 'OBJECTID': 'OBJECTID', 'FIPSCODE': 'FIPSCODE', 'FIPSNUM': 'FIPSNUM', 'LABEL': 'LABEL', 'TYPE': 'TYPE', 'CNTY_CODE': 'CNTY_CODE', 'SQKM': 'SQKM', 'SQMILES': 'SQMILES', 'ACRES': 'ACRES', 'VER': 'VER', 'LAYOUT': 'LAYOUT', 'PENINSULA': 'PENINSULA', 'ShapeSTArea': 'ShapeSTArea', 'ShapeSTLength': 'ShapeSTLength', });
lyr_MCCDistricts_2.set('fieldAliases', {'id': 'id', 'Name': 'Name', });
lyr_MishigamiChapters_3.set('fieldAliases', {'id': 'id', 'Name': 'Name', 'Label': 'Label', });
lyr_MILPSchoolDistricts_0.set('fieldImages', {'Name': 'TextEdit', 'descriptio': 'Hidden', 'timestamp': 'Hidden', 'begin': 'Hidden', 'end': 'Hidden', 'altitudeMo': 'Hidden', 'tessellate': 'Hidden', 'extrude': 'Hidden', 'visibility': 'Hidden', 'drawOrder': 'Hidden', 'icon': 'Hidden', 'OBJECTID': 'Hidden', 'FIPSCODE': 'Hidden', 'FIPSNUM': 'Hidden', 'LABEL': 'Hidden', 'TYPE': 'Hidden', 'DCODE': 'Hidden', 'ISD': 'Hidden', 'SQKM': 'Hidden', 'SQMILES': 'Hidden', 'ACRES': 'Hidden', 'VER': 'Hidden', 'LAYOUT': 'Hidden', 'PENINSULA': 'Hidden', 'ShapeSTAre': 'Hidden', 'ShapeSTLen': 'Hidden', });
lyr_MILPCounties_1.set('fieldImages', {'fid': 'Hidden', 'Name': 'TextEdit', 'description': 'Hidden', 'timestamp': 'Hidden', 'begin': 'Hidden', 'end': 'Hidden', 'altitudeMode': 'Hidden', 'tessellate': 'Hidden', 'extrude': 'Hidden', 'visibility': 'Hidden', 'drawOrder': 'Hidden', 'icon': 'Hidden', 'OBJECTID': 'Hidden', 'FIPSCODE': 'Hidden', 'FIPSNUM': 'Hidden', 'LABEL': 'Hidden', 'TYPE': 'Hidden', 'CNTY_CODE': 'Hidden', 'SQKM': 'Hidden', 'SQMILES': 'Hidden', 'ACRES': 'Hidden', 'VER': 'Hidden', 'LAYOUT': 'Hidden', 'PENINSULA': 'Hidden', 'ShapeSTArea': 'Hidden', 'ShapeSTLength': 'Hidden', });
lyr_MCCDistricts_2.set('fieldImages', {'id': 'Hidden', 'Name': 'TextEdit', });
lyr_MishigamiChapters_3.set('fieldImages', {'id': 'Hidden', 'Name': 'TextEdit', 'Label': 'Hidden', });
lyr_MILPSchoolDistricts_0.set('fieldLabels', {'Name': 'no label', });
lyr_MILPCounties_1.set('fieldLabels', {'Name': 'no label', });
lyr_MCCDistricts_2.set('fieldLabels', {'Name': 'no label', });
lyr_MishigamiChapters_3.set('fieldLabels', {'Name': 'no label', });
lyr_MishigamiChapters_3.on('precompose', function(evt) {
    evt.context.globalCompositeOperation = 'normal';
});