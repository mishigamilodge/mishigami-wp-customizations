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

// layer_MILPSchoolDistricts

var layer_MILPSchoolDistricts = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'MILPSchoolDistricts.geojson',
        attributions: '<nobr>&copy; <a href="https://michigan.gov/gis" target="_blank">State of Michigan</a></nobr>',
    }),
    style: style_MILPSchoolDistricts,
    interactive: false,
    title: 'MI LP School Districts'
});
layer_MILPSchoolDistricts.setVisible(false);

// layer_MILPCounties

var layer_MILPCounties = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'MILPCounties.geojson',
        attributions: '<nobr>&copy; <a href="https://michigan.gov/gis" target="_blank">State of Michigan</a></nobr>',
    }),
    style: style_MILPCounties,
    interactive: false,
    title: 'MI LP Counties'
});
layer_MILPCounties.setVisible(true);

// layer_MCCDistricts

var layer_MCCDistricts = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'MCCDistricts.geojson',
        attributions: '<nobr>&copy; <a href="https://michiganscouting.org/" target="_blank">Michigan Crossroads Council</a></nobr>',
    }),
    style: style_MCCDistricts,
    interactive: false,
    title: 'MCC Districts'
});
layer_MCCDistricts.setVisible(false);

// layer_MishigamiChapters

var layer_NSAreaChapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'NSAreaChapters.geojson',
        attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
    }),
    visible: true,
    style: style_MishigamiChapters,
    interactive: false,
    title: 'Mishigami Lodge Chapters'
});

var layer_NoquetAreaChapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'NoquetAreaChapters.geojson',
        attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
    }),
    visible: true,
    style: style_MishigamiChapters,
    interactive: false,
    title: 'Mishigami Lodge Chapters'
});

var layer_KishahtekAreaChapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'KishahtekAreaChapters.geojson',
        attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
    }),
    visible: true,
    style: style_MishigamiChapters,
    interactive: false,
    title: 'Mishigami Lodge Chapters'
});

var layer_AMAreaChapters = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'AMAreaChapters.geojson',
        attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
    }),
    visible: true,
    style: style_MishigamiChapters,
    interactive: false,
    title: 'Mishigami Lodge Chapters'
});

// layer_MishigamiAreas

var layer_MishigamiAreas = new ol.layer.Vector({
    declutter: true,
    source: new ol.source.Vector({
        format: new ol.format.GeoJSON(),
        url: mish_map.layersdir + 'MishigamiAreas.geojson',
        attributions: '<nobr>&copy; <a href="https://mishigami.org/" target="_blank">Mishigami Lodge</a></nobr>',
    }),
    style: style_MishigamiAreas,
    interactive: false,
    title: 'Mishigami Lodge Areas'
});

// baseLayer

var layer_OpenStreetMap = new ol.layer.Tile({
    source: new ol.source.OSM({
        url: 'https://c.tile.openstreetmap.org/{z}/{x}/{y}.png',
    })
});

// This is the bounds lock for scrolling the map
var bbox_kishahtek = [-86.5634110714137108,41.6961255762930989,-83.1785879841581988,42.7816981728140391];
var bbox_shohpe = [-86.5411397388651977,42.4188196118341025,-83.1906484381760976,45.8409315959475023];
var bbox_agaming = [-85.0888199290285030,42.4214358985140976,-82.4134779482331936,44.8595598328777001];
var bbox_noquet = [-83.7499558037006011,42.0279383906226016,-82.6378937448066040,42.9396080725121010];
var bbox_mishigami = [-86.5634110714137108,41.6961255762930989,-82.4134779482331936,45.8409315959475023];
var maxExtent = ol.proj.transformExtent([-87,41,-81.75,46], 'EPSG:4326', 'EPSG:3857');

var map = new ol.Map({
  controls: ol.control.defaults({attribution: false}).extend([
    new ol.control.Attribution({collapsible: false}),
    new ol.control.Control({element: document.getElementById('mish_map_layers')}),
    new ol.control.Control({element: document.getElementById('mish_map_buttons')}),
  ]),
  layers: [
    layer_OpenStreetMap,
    layer_MILPSchoolDistricts,
    layer_MILPCounties,
    layer_MCCDistricts,
    layer_NSAreaChapters,
    layer_NoquetAreaChapters,
    layer_KishahtekAreaChapters,
    layer_AMAreaChapters,
    layer_MishigamiAreas
  ],
  target: 'mish_map',
  view: new ol.View({
    center: ol.proj.fromLonLat([-84.8037517,43.7816099]),
    extent: maxExtent,
    minZoom: 6.75,
    maxZoom: 18,
    zoom: 6.75,
  })
});

$j('#mish_map_reset_map').on("click", function() {
    setAreaVisible('none');
    layer_MishigamiAreas.setVisible(true);
    $j("#chapterlayer").prop('checked',false);
    $j("#arealayer").prop('checked',true);

    map.getView().fit(maxExtent,{
        size: map.getSize(),
        padding: [10,10,10,10],
        duration: 500
    });
    return true;
});
$j('#mish_map_show_layers').on("click", function() {
    $j('#mish_map_layers').toggle();
    return true;
});
function setAreaVisible(area) {
    if (area == 'Nataepu Shohpe') {
        layer_NSAreaChapters.setVisible(true);
    } else {
        layer_NSAreaChapters.setVisible(false);
    }
    if (area == 'Noquet') {
        layer_NoquetAreaChapters.setVisible(true);
    } else {
        layer_NoquetAreaChapters.setVisible(false);
    }
    if (area == 'Kishahtek') {
        layer_KishahtekAreaChapters.setVisible(true);
    } else {
        layer_KishahtekAreaChapters.setVisible(false);
    }
    if (area == 'Agaming Maangogwan') {
        layer_AMAreaChapters.setVisible(true);
    } else {
        layer_AMAreaChapters.setVisible(false);
    }
}

var displayFeatureInfo = function (pixel) {
  var features = [];
  var layers = [];
  map.forEachFeatureAtPixel(pixel, function (feature, layer) {
    if ((layer.getProperties().title == 'Mishigami Lodge Chapters') ||
       (layer.getProperties().title == 'Mishigami Lodge Areas')) {
      features.push(feature);
      layers.push(layer);
    }
  });
  if (features.length > 0) {
    document.getElementById('mish_map_info').innerHTML = '<h4>' + features[0].get('name') + '</h4><p>Loading...';
    if (layers[0].getProperties().title == 'Mishigami Lodge Areas') {
        setAreaVisible(features[0].get('name'));
        layer_MishigamiAreas.setVisible(false);
        $j("#chapterlayer").prop('checked',false);
        $j("#arealayer").prop('checked',false);
    }
    map.getView().fit(features[0].getGeometry(),{
        size: map.getSize(),
        padding: [10,10,25,10],
        duration: 500
    });
    //features[0].setStyle(new ol.style.Style({}));
    //setTimeout(function(){ features[0].setStyle(); }, 2000);
    $j.ajax({
      url : mish_map.ajaxurl,
      type : 'get',
      data : {
        action : 'mish_load_chapter_blurb',
        chapter : features[0].get('name'),
      },
      success : function( response ) {
        document.getElementById('mish_map_info').innerHTML = '<h4>' + features[0].get('name') + '</h4>' + response.content;
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
layer_OpenStreetMap.setVisible($j("#baselayer").is(":checked"));
$j("#baselayer").on("change", function () {
    layer_OpenStreetMap.setVisible($j(this).is(":checked"));
});

layer_MILPSchoolDistricts.setVisible($j("#schooldistlayer").is(":checked"));
$j("#schooldistlayer").on("change", function () {
    layer_MILPSchoolDistricts.setVisible($j(this).is(":checked"));
});

layer_MILPCounties.setVisible($j("#countylayer").is(":checked"));
$j("#countylayer").on("change", function () {
    layer_MILPCounties.setVisible($j(this).is(":checked"));
});

layer_MCCDistricts.setVisible($j("#districtlayer").is(":checked"));
$j("#districtlayer").on("change", function () {
    layer_MCCDistricts.setVisible($j(this).is(":checked"));
});

layer_NSAreaChapters.setVisible($j("#chapterlayer").is(":checked"));
layer_NoquetAreaChapters.setVisible($j("#chapterlayer").is(":checked"));
layer_KishahtekAreaChapters.setVisible($j("#chapterlayer").is(":checked"));
layer_AMAreaChapters.setVisible($j("#chapterlayer").is(":checked"));
$j("#chapterlayer").on("change", function () {
    layer_NSAreaChapters.setVisible($j(this).is(":checked"));
    layer_NoquetAreaChapters.setVisible($j(this).is(":checked"));
    layer_KishahtekAreaChapters.setVisible($j(this).is(":checked"));
    layer_AMAreaChapters.setVisible($j(this).is(":checked"));
});

layer_MishigamiAreas.setVisible($j("#arealayer").is(":checked"));
$j("#arealayer").on("change", function () {
    layer_MishigamiAreas.setVisible($j(this).is(":checked"));
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
