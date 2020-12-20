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

// lyr_MILPCounties_1

var format_MILPCounties_1 = new ol.format.GeoJSON();
var features_MILPCounties_1 = format_MILPCounties_1.readFeatures(json_MILPCounties_1, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MILPCounties_1 = new ol.source.Vector({
	attributions: '&copy; <a href="https://michigan.gov/gis" target="_blank">State of Michigan</a>',
});
jsonSource_MILPCounties_1.addFeatures(features_MILPCounties_1);
var lyr_MILPCounties_1 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MILPCounties_1, 
    style: style_MILPCounties_1,
    interactive: false,
    title: 'MI LP Counties'
});
lyr_MILPCounties_1.setVisible(true);

var format_MishigamiChapters_3 = new ol.format.GeoJSON();
var features_MishigamiChapters_3 = format_MishigamiChapters_3.readFeatures(json_MishigamiChapters_3, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_MishigamiChapters_3 = new ol.source.Vector({
	attributions: '&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge, Order of the Arrow, BSA</a>',
});
jsonSource_MishigamiChapters_3.addFeatures(features_MishigamiChapters_3);
var lyr_MishigamiChapters_3 = new ol.layer.Vector({
    declutter: true,
    source: jsonSource_MishigamiChapters_3, 
    style: style_MishigamiChapters_3,
    interactive: false,
    title: 'Mishigami Lodge Chapters'
});
lyr_MishigamiChapters_3.setVisible(true);

var baseLayer = new ol.layer.Tile({
    //source: new ol.source.OSM()
    source: new ol.source.OSM({
        url: 'https://c.tile.openstreetmap.org/{z}/{x}/{y}.png',
        //url: 'https://tile.openstreetmap.be/osmbe/{z}/{x}/{y}.png',
    })
});
baseLayer.setVisible(true);

var kml_chapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        url: mish_map.layersdir + 'MishigamiChapters.kml',
        format: new ol.format.KML({
            extractStyles: true,
            extractAttributes: true,
	}),
    }),
    //style: style_MishigamiChapters_3,
    interactive: false,
    title: 'Mishigami Lodge Chapters',
});

//chapterLayer = kml_chapters;
chapterLayer = lyr_MishigamiChapters_3;

//countyLayer = kml_counties;
countyLayer = lyr_MILPCounties_1;

var layersList = [ baseLayer, countyLayer, chapterLayer ];

var attribution = new ol.control.Attribution({
    collapsible: false
});

var map = new ol.Map({
  controls: ol.control.defaults({attribution: false}).extend([attribution]),
  layers: layersList, 
  target: 'mish_map',
  view: new ol.View({
    center: ol.proj.fromLonLat([-84.8037517,43.7816099]),
    maxZoom: 18,
    zoom: 6.75
  })
});

var displayFeatureInfo = function (pixel) {
  var features = [];
  map.forEachFeatureAtPixel(pixel, function (feature) {
    features.push(feature);
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

/*map.on('pointermove', function (evt) {
  if (evt.dragging) {
    return;
  }
  var pixel = map.getEventPixel(evt.originalEvent);
  displayFeatureInfo(pixel);
});
*/
$j("#baselayer").on("change", function () {
    baseLayer.setVisible($j(this).is(":checked"));
});

$j("#countylayer").on("change", function () {
    countyLayer.setVisible($j(this).is(":checked"));
});

$j("#chapterlayer").on("change", function () {
    chapterLayer.setVisible($j(this).is(":checked"));
});

map.on('click', function (evt) {
  displayFeatureInfo(evt.pixel);
});

});
