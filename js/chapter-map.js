var $j = jQuery.noConflict();

// utility function used later
var createTextStyle = function(feature, resolution, labelText, labelFont,
                               labelFill, placement, bufferColor,
                               bufferWidth) {

    if (feature.hide || !labelText) {
        return;
    }

    if (bufferWidth == 0) {
        var bufferStyle = null;
    } else {
        var bufferStyle = new ol.style.Stroke({
            color: bufferColor,
            width: bufferWidth
        })
    }

    var textStyle = new ol.style.Text({
        font: labelFont,
        text: labelText,
        textBaseline: "middle",
        textAlign: "center",
        offsetX: 8,
        offsetY: 3,
        placement: placement,
        maxAngle: 0,
        fill: new ol.style.Fill({
          color: labelFill
        }),
        stroke: bufferStyle
    });

    return textStyle;
};

$j(document).ready(function(){

// This is the bounds lock for scrolling the map
var maxExtent = ol.proj.transformExtent([-87,41,-81.75,46], 'EPSG:4326', 'EPSG:3857')

// lyr_MILPSchoolDistricts_0

var format_MILPSchoolDistricts_0 = new ol.format.GeoJSON();
var features_MILPSchoolDistricts_0 = format_MILPSchoolDistricts_0.readFeatures(json_MILPSchoolDistricts_0, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MILPSchoolDistricts_0 = new ol.source.Vector({
       attributions: '<nobr>&copy; <a href="https://michigan.gov/gis" target="_blank">State of Michigan</a></nobr>',
});
jsonSource_MILPSchoolDistricts_0.addFeatures(features_MILPSchoolDistricts_0);
var lyr_MILPSchoolDistricts_0 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MILPSchoolDistricts_0, 
    style: style_MILPSchoolDistricts_0,
    interactive: false,
    extent: maxExtent,
    title: 'MI LP School Disticts'
});
lyr_MILPSchoolDistricts_0.setVisible(false);

// lyr_MILPCounties_1

var format_MILPCounties_1 = new ol.format.GeoJSON();
var features_MILPCounties_1 = format_MILPCounties_1.readFeatures(json_MILPCounties_1, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MILPCounties_1 = new ol.source.Vector({
       attributions: '<nobr>&copy; <a href="https://michigan.gov/gis" target="_blank">State of Michigan</a></nobr>',
});
jsonSource_MILPCounties_1.addFeatures(features_MILPCounties_1);
var lyr_MILPCounties_1 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MILPCounties_1, 
    style: style_MILPCounties_1,
    interactive: false,
    extent: maxExtent,
    title: 'MI LP Counties'
});
lyr_MILPCounties_1.setVisible(true);

// lyr_MCCDistricts_2

var format_MCCDistricts_2 = new ol.format.GeoJSON();
var features_MCCDistricts_2 = format_MCCDistricts_2.readFeatures(json_MCCDistricts_2, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MCCDistricts_2 = new ol.source.Vector({
       attributions: '<nobr>&copy; <a href="https://michiganscouting.org/" target="_blank">Michigan Crossroads Council</a></nobr>',
});
jsonSource_MCCDistricts_2.addFeatures(features_MCCDistricts_2);
var lyr_MCCDistricts_2 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MCCDistricts_2, 
    style: style_MCCDistricts_2,
    interactive: false,
    extent: maxExtent,
    title: 'MCC Districts'
});
lyr_MCCDistricts_2.setVisible(false);

// lyr_MishigamiChapters_3

var format_MishigamiChapters_3 = new ol.format.GeoJSON();
var features_MishigamiChapters_3 = format_MishigamiChapters_3.readFeatures(json_MishigamiChapters_3, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MishigamiChapters_3 = new ol.source.Vector({
       attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
});
jsonSource_MishigamiChapters_3.addFeatures(features_MishigamiChapters_3);
var lyr_MishigamiChapters_3 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MishigamiChapters_3, 
    style: style_MishigamiChapters_3,
    interactive: false,
    extent: maxExtent,
    title: 'Mishigami Lodge Chapters'
});
lyr_MishigamiChapters_3.setVisible(true);

// lyr_MishigamiAreas_0

var format_MishigamiAreas_0 = new ol.format.GeoJSON();
var features_MishigamiAreas_0 = format_MishigamiAreas_0.readFeatures(json_MishigamiAreas_0, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MishigamiAreas_0 = new ol.source.Vector({
       attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
});
jsonSource_MishigamiAreas_0.addFeatures(features_MishigamiAreas_0);
var lyr_MishigamiAreas_0 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MishigamiAreas_0, 
    style: style_MishigamiAreas_0,
    interactive: false,
    extent: maxExtent,
    title: 'Mishigami Lodge Areas'
});
lyr_MishigamiAreas_0.setVisible(true);

// baseLayer

var baseLayer = new ol.layer.Tile({
    //source: new ol.source.OSM()
    source: new ol.source.OSM({
        url: 'https://c.tile.openstreetmap.org/{z}/{x}/{y}.png',
        //url: 'https://tile.openstreetmap.be/osmbe/{z}/{x}/{y}.png',
        extent: maxExtent,
    })
});
baseLayer.setVisible(true);

// kml_chapters (attempt at kml version)

var kml_chapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        url: mish_map.layersdir + 'MishigamiChapters.kml',
        format: new ol.format.KML({
            extractStyles: true,
            extractAttributes: true,
           }),
        extent: maxExtent,
    }),
    //style: style_MishigamiChapters_3,
    interactive: false,
    title: 'Mishigami Lodge Chapters',
});

//chapterLayer = kml_chapters;
chapterLayer = lyr_MishigamiChapters_3;

//countyLayer = kml_counties;
countyLayer = lyr_MILPCounties_1;

schooldistLayer = lyr_MILPSchoolDistricts_0;
districtLayer = lyr_MCCDistricts_2;

var layersList = [ baseLayer, schooldistLayer, countyLayer, districtLayer, chapterLayer ];

var attribution = new ol.control.Attribution({
    collapsible: false
});

var map = new ol.Map({
  controls: ol.control.defaults({attribution: false}).extend([attribution]),
  layers: layersList, 
  target: 'mish_map',
  view: new ol.View({
    center: ol.proj.fromLonLat([-84.8037517,43.7816099]),
    extent: maxExtent,
    minZoom: 6.75,
    maxZoom: 18,
    zoom: 6.75,
  })
});

var displayFeatureInfo = function (pixel) {
  var features = [];
  map.forEachFeatureAtPixel(pixel, function (feature, layer) {
    if (layer.getProperties().title == 'Mishigami Lodge Chapters') {
      features.push(feature);
    }
  });
  if (features.length > 0) {
    document.getElementById('mish_map_info').innerHTML = '<h4>' + features[0].get('Name') + '</h4><p>Loading...';
    $j.ajax({
      url : mish_map.ajaxurl,
      type : 'get',
      data : {
        action : 'mish_load_chapter_blurb',
        chapter : features[0].get('Name'),
      },
      success : function( response ) {
        document.getElementById('mish_map_info').innerHTML = '<h4>' + features[0].get('Name') + '</h4>' + response.content;
      },
    });
    //document.getElementById('mish_map_info').innerHTML = 'Chapter: ' + features[0].get('Name');
    //map.getTarget().style.cursor = 'pointer';
  } else {
    document.getElementById('mish_map_info').innerHTML = '&nbsp;';
    //map.getTarget().style.cursor = '';
  }
};

// set immediately to restore state on page reload, then catch any time it changes.
baseLayer.setVisible($j("#baselayer").is(":checked"));
$j("#baselayer").on("change", function () {
    baseLayer.setVisible($j(this).is(":checked"));
});

schooldistLayer.setVisible($j("#schooldistlayer").is(":checked"));
$j("#schooldistlayer").on("change", function () {
    schooldistLayer.setVisible($j(this).is(":checked"));
});

countyLayer.setVisible($j("#countylayer").is(":checked"));
$j("#countylayer").on("change", function () {
    countyLayer.setVisible($j(this).is(":checked"));
});

districtLayer.setVisible($j("#districtlayer").is(":checked"));
$j("#districtlayer").on("change", function () {
    districtLayer.setVisible($j(this).is(":checked"));
});

chapterLayer.setVisible($j("#chapterlayer").is(":checked"));
$j("#chapterlayer").on("change", function () {
    chapterLayer.setVisible($j(this).is(":checked"));
});

map.on('click', function (evt) {
  displayFeatureInfo(evt.pixel);
});

/*map.on('pointermove', function (evt) {
  if (evt.dragging) {
    return;
  }
  var pixel = map.getEventPixel(evt.originalEvent);
  displayFeatureInfo(pixel);
});
*/

});
