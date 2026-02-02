<div id="map" style="height :79vh;"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

/* .navbar {
  display: none;
}

.leaflet-left{
  display:none;
} */

  .styleLabelZNT {
    background: rgba(255, 255, 255, 0);
    border: 0;
    border-radius: 0px;
    box-shadow: 0 0px 0px;
    font-size: 8pt;
    color: white;
    text-shadow: 2px 2px 5px black;
    font-weight: bold;
  }

  .leaflet-panel-layers-list {
    height:56vh;
  }

  .judul-page {
    display:none;
  }

  .text-subtitle {
    display:none;
  }

  #sidebar {
    position: absolute;
    z-index: 9000;
    /* This will make the sidebar appear above the map */
    width: 20%;
    /* Adjust as needed */
  }

  .info.legend {
    display: inline-block;
  }

  #main .main-content {
    padding: 0;
  }

  .leaflet-panel-layers.expanded {
  border-bottom-left-radius: 10px;
  border-top-left-radius: 10px;
}

.leaflet-control-attribution {
  display: none;
}

.leaflet-top.leaflet-right .leaflet-panel-layers:not(.compact) {
  display:none;
}

</style>
<script>
  // Data admin padukuhan
  var layeradminkarangwuni = {
    "type": "FeatureCollection",
    "name": "admin_karangwuni",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($adminpadukuhan as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>","Kalurahan": "<?= $value->Kalurahan ?>", "Padukuhan": "<?= $value->Padukuhan ?>", "LUAS": <?= $value->LUAS ?>, "JUMLAH_KK": <?= $value->JUMLAH_KK ?>, "JUM_PDDK": <?= $value->JUM_PDDK ?>, "JUM_LAKI2": <?= $value->JUM_LAKI2 ?>, "JUM_PEREMP": <?= $value->JUM_PEREMP ?> }, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_poligon ?> } },
      <?php endforeach ?>
    ]
  };

  // Data bangunan
  var layerbangunankarangwuni = {
    "type": "FeatureCollection",
    "name": "bangunan_desa",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($bangunan as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>","KKArray": <?=json_encode($this->m_bangunan->getdatabybangunanid($value->idbgn) )?>, "foto": "<?= $value->foto ?>" }, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_poligon ?> } },
      <?php endforeach ?>
    ]
  };

  // Tanah Kas Desa
  var layer_tanah_kas_desa = {
    "type": "FeatureCollection",
    "name": "layer_tanah_kas_desa",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($tanahkasdesa as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Alas_Hak": "<?= $value->Alas_Hak ?>", "Tanggal": "<?= $value->Tanggal ?>", "Lokasi": "<?= $value->Lokasi ?>", "Luas": <?= $value->Luas ?>, "Nama": "<?= $value->Nama ?>", "LuasQGIS": <?= $value->LuasQGIS ?>, "FOTO": "<?= $value->foto ?>" }, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  // Fas Ekonomi
  var layerekonomi = {
    "type": "FeatureCollection",
    "name": "layerekonomi",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($fasilitasekonomi as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "OtorityKw": "<?= $value->OtorityKw ?>"}, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  // Data Masjid
  var layermasjidpolyy = {
    "type": "FeatureCollection",
    "name": "masjidpoligon",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($masjidpoligon as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "foto": "<?= $value->foto ?>"}, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  // Data Pendidikan
  var layerpendidikanpoly = {
    "type": "FeatureCollection",
    "name": "pendidikan",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($pendidikan as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "foto": "<?= $value->foto ?>", "kategori": "<?= $value->kategori ?>"}, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  var layerdrainase = {
    "type": "FeatureCollection",
    "name": "drainase",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($drainase as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "PANJANG": <?= $value->PANJANG ?>, "LEBAR": <?= $value->LEBAR ?>, "KONSTRUKSI": "<?= $value->KONSTRUKSI ?>", "KONDISI": "<?= $value->KONDISI ?>"}, "geometry": { "type": "MultiLineString", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  var layerspamm = {
    "type": "FeatureCollection",
    "name": "spam",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($spam as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "PANJANG": <?= $value->PANJANG ?>, "DIAPIPA": <?= $value->DIAPIPA ?>, "VolM3BAK": "<?= $value->VolM3BAK ?>", "DebLdet": "<?= $value->DebLdet ?>"}, "geometry": { "type": "MultiLineString", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  var layeririgasii = {
    "type": "FeatureCollection",
    "name": "irigasi",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($irigasi as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>","INTAKE": "<?= $value->INTAKE ?>", "PANJANG": <?= $value->PANJANG ?>, "LEBAR": <?= $value->LEBAR ?>, "LUAS": <?= $value->LUAS ?>, "KEWENANGAN": "<?= $value->KEWENANGAN ?>", "KONDISI": "<?= $value->KONDISI ?>", "ASSETTANAH": "<?= $value->ASSETTANAH ?>", "ASSETBANG": "<?= $value->ASSETBANG ?>"}, "geometry": { "type": "MultiLineString", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  var layerjalan = {
    "type": "FeatureCollection",
    "name": "jalan",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($jalan as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>","Panjang": <?= $value->Panjang ?>, "Luas": <?= $value->Luas ?>, "Asettanah": <?= $value->Asettanah ?>, "Kondisi": "<?= $value->Kondisi ?>", "Kewenangan": "<?= $value->Kewenangan ?>", "FotoAwal": "<?= $value->FotoAwal ?>", "FotoAkhir": "<?= $value->FotoAkhir ?>", "RERNJOP": <?= $value->RERNJOP ?>, "Status": "<?= $value->Status ?>", "Lebar": <?= $value->Lebar ?>, "Asal": "<?= $value->Asal ?>", "layer": "<?= $value->layer ?>"}, "geometry": { "type": "MultiLineString", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  var layermakam = {
    "type": "FeatureCollection",
    "name": "makam",
    "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
    "features": [
      <?php foreach ($makam as $key => $value): ?>{ "type": "Feature", "properties": { "id": "<?= $value->id ?>", "Nama": "<?= $value->Nama ?>", "statustanah": "<?= $value->statustanah ?>", "luas": "<?= $value->luas ?>", "luasqgis": "<?= $value->luasqgis ?>", "luasshm": "<?= $value->luasshm ?>", "foto": "<?= $value->foto ?>"}, "geometry": { "type": "MultiPolygon", "coordinates": <?= $value->data_spasial ?> } },
      <?php endforeach ?>
    ]
  };

  //basemap
  var map = L.map('map', {
    zoom: 15,
    center: L.latLng([-7.916181, 110.095629]),
  }),
    osmLayer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    crossOrigin: true  // Penting untuk memungkinkan akses ke tile dari domain berbeda
});

  var measureControl = new L.Control.Measure({
    position: 'topleft',
    primaryLengthUnit: 'meters',
    primaryAreaUnit: 'sqmeters',

  });
  measureControl.addTo(map);

map.addControl(
    L.control.locate({
      locateOptions: {
        flyTo: true,
        minzoom: 15,
        initialZoomLevel: 17,
        watch: true,  // This will continuously track the user's location
      }
    })
);

  // Administrasi Padukuhan
  var viewadminkarangwuni = new L.geoJSON(layeradminkarangwuni, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: 'black',
        fillColor: 'black',
        weight: 1.5,
        transparent: true,
        dashArray: '5,5'
      };
    },
    onEachFeature: function (feature, layer) {
      var content = feature.properties.Padukuhan.toString();
      layer.bindTooltip(content, {
        direction: 'center',
        permanent: true,
        className: 'styleLabelZNT'
      });

// JS Chart Admin
layer.on('popupopen', function () {
    setTimeout(function() {
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: ['Penduduk', 'Perempuan', 'Laki-laki', 'Jumlah KK'],
            datasets: [{
              label: 'Demografi Penduduk',
              data: [feature.properties.JUM_PDDK, feature.properties.JUM_PEREMP, feature.properties.JUM_LAKI2, feature.properties.JUMLAH_KK],
              backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(153, 102, 255, 0.2)'
              ],
              borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)'
              ],
              borderWidth: 1
            }]
          },
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true
                }
              }]
            }
          }
        });
    }, 300);
});


      // Popup atribut
      layer.bindPopup('<b><h4>PADUKUHAN ' + feature.properties.Padukuhan +
        '</h4></b><br>' + '<canvas id="myChart" height="250px"></canvas>' + '<br><h6>Luas : '+ feature.properties.LUAS + ' ㎡ </h6>'
      );
    }
  });

  map.addLayer(viewadminkarangwuni);

  function zoomToDukuh(iddukuh) {
    viewadminkarangwuni.eachLayer(function (layer) {
      if (layer.feature.properties.id == iddukuh) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }
  // Get the ID from PHP
  var iddukuh = <?= isset($iddukuh) ? $iddukuh : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (id !== null) {
    zoomToDukuh(iddukuh);
  }

  resetLabels([viewadminkarangwuni]);
  map.on("zoomend", function () {
    if (map.getZoom() <= 12) {
      map.removeLayer(viewadminkarangwuni);
      resetLabels([viewadminkarangwuni]);
    } else if (map.getZoom() > 12) {
      map.addLayer(viewadminkarangwuni);
      resetLabels([viewadminkarangwuni]);
    }
  });
  map.on("move", function () {
    resetLabels([viewadminkarangwuni]);
  });
  map.on("layeradd", function () {
    resetLabels([viewadminkarangwuni]);
  });
  map.on("layerremove", function () {
    resetLabels([viewadminkarangwuni]);
  });


    // Tanah Kas Desa
    var viewtanahkasdesa = new L.geoJSON(layer_tanah_kas_desa, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: 'green',
        fillColor: 'green',
        weight: 1.5,
        transparent: true,
      };
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup('<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fototanahkasdesa/' + layer.feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fototanahkasdesa/' + layer.feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>Alas Hak</th><td>" + " : " + feature.properties.Alas_Hak + "</td></tr>" +
        "<tr><th>Lokasi</th><td>" + " : " + feature.properties.Lokasi + "</td></tr>" +
        "<tr><th>Luas</th><td>" + " : " + feature.properties.Luas + "㎡ </td></tr>" +
        "<tr><th>Luas QGIS</th><td>" + " : " + feature.properties.LuasQGIS + "㎡ </td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewtanahkasdesa);

  function zoomToTanahKasDesa(idtanahkasdesa) {
    viewtanahkasdesa.eachLayer(function (layer) {
      if (layer.feature.properties.id == idtanahkasdesa) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }
  // Get the ID from PHP
  var idtanahkasdesa = <?= isset($idtanahkasdesa) ? $idtanahkasdesa : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (id !== null) {
    zoomToTanahKasDesa(idtanahkasdesa);
  }

  // Bangunan-bangunan
// Membuat layer GeoJSON baru dengan data dari 'layerbangunankarangwuni'
var viewbangunankarangwuni = new L.geoJSON(layerbangunankarangwuni, {
    // Mengatur style untuk setiap fitur di layer
    style: function (feature) {
      return {
        opacity: 1.5,
        color: 'red',
        fillColor: 'red',
        weight: 1.5,
        transparent: true,
      };
    },

    // Menambahkan fungsi yang akan dijalankan untuk setiap fitur di layer
    onEachFeature: function (feature, marker, latlng, title, map) {
      // Membuat popup untuk setiap marker dengan informasi dari fitur
      marker.bindPopup(
        '<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotobangunan/' + feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fotobangunan/' + feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+
        // Membuat elemen accordion untuk setiap item di 'KKArray'
        feature.properties.KKArray.map((item,index)=>{
          return(`  <div class="accordion-item" style="width:300px;">
          <h2 class="accordion-header">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#target${index}" aria-expanded="true" aria-controls="collapseOne">
              ${item.kepkel}
            </button>
          </h2>
          <div id="target${index}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
            <div class="accordion-body">
            <table>
                <tr><th>Dukuh </th><td> : ${item.dukuh} </td></tr>
                <tr><th>RW </th><td> : ${item.RW} </td></tr>
                <tr><th>Kepala Keluarga </th><td> : ${item.kepkel} </td></tr>
                <tr><th>Catatan </th><td> : ${item.catatan} </td></tr>
              </table>
            </div>
          </div>
        </div>`)
        }
        ).join("") + `
        </div>
      </div>`);
    }
    }
    );

    // Menambahkan layer ke peta
    map.addLayer(viewbangunankarangwuni);


  // search from manage
  function zoomToBangunan(idbangunan) {
    viewbangunankarangwuni.eachLayer(function (layer) {
      if (layer.feature.properties.id == idbangunan) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }
  // Get the ID from PHP
  var idbangunan = <?= isset($idbangunan) ? $idbangunan : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (idbangunan !== null) {
    zoomToBangunan(idbangunan);
  }

  // Drainase
  var viewdrainase = new L.geoJSON(layerdrainase, {
    style: function (feature) {
      return {
        color: 'blue',
        weight: 3,
        opacity: .7,
      };
    },
    onEachFeature: function (feature, marker) {
      marker.bindPopup("<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>Panjang</th><td>" + " : " + feature.properties.PANJANG + " m </td></tr>" +
        "<tr><th>Lebar</th><td>" + " : " + feature.properties.LEBAR + " m </td></tr>" +
        "<tr><th>Konstruksi</th><td>" + " : " + feature.properties.KONSTRUKSI + "</td></tr>" +
        "<tr><th>Kondisi</th><td>" + " : " + feature.properties.KONDISI + "</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewdrainase);

  function zoomToDrainase(id) {
    var selectedFeatureLayer;

    viewdrainase.eachLayer(function (layer) {
      if (layer.feature.properties.id == iddrainase) {
        var coordinates = layer.getLatLngs()[0];

        // Calculate the middle index of the coordinates array
        var middleIndex = Math.floor(coordinates.length / 2);

        // Get the middle coordinates
        var middleLatLng = coordinates[middleIndex];

        // Create an invisible marker at the middle of the LineString
        var markerdrainase = L.circleMarker([middleLatLng.lat, middleLatLng.lng], { opacity: 0 }); // set opacity to 0

        // Add the circle marker to the map
        markerdrainase.addTo(map);
        markerdrainase.bindPopup(layer.getPopup().getContent());
        markerdrainase.openPopup();
        // Zoom to the popup
        map.setView(markerdrainase.getLatLng(), 20); // Adjust zoom level as needed
      }
    });
  }

    // Get the ID from PHP
    var iddrainase = <?= isset($iddrainase) ? $iddrainase : 'null' ?>;

    // Call this function with the iddrainase of the feature you want to zoom to, if it's set
    if (iddrainase !== null) {
    zoomToDrainase(iddrainase);
  }

  // Jalan
  var viewjalanoutline = new L.geoJSON(layerjalan, {
    filter: function(feature) {
        return feature.properties.layer === 'Jalan_Kabupaten';
    },
    style: function (feature) {
        return {
            color: 'black',
            weight: 5,
            opacity: 1,
        };
    },
});

  var viewjalan = new L.geoJSON(layerjalan, {
    style: function (feature) {
      var color;
      switch (feature.properties.layer) {
        case 'Jalan_Kabupaten':
          color = '#ff7f00';
          break;
        case 'Jalan_BBWSO':
          color = '#ffb200';
          break;
        case 'Jalan_Kalurahan':
          color = '#b2b2ff';
          break;
      }
      return {
        color: color,
        weight: 3,
        opacity: 1,
      };
    },
    onEachFeature: function (feature, marker) {
      marker.bindPopup("<div style='height:200px;overflow:auto;'>"+'<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotojalan/' + feature.properties.FotoAwal + '" target="_blank"><img src = "<?= base_url() ?>fotojalan/' + feature.properties.FotoAwal + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">` + '<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotojalan/' + feature.properties.FotoAkhir + '" target="_blank"><img src = "<?= base_url() ?>fotojalan/' + feature.properties.FotoAkhir + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>Tipe jalan</th><td>" + " : " + feature.properties.layer + "</td></tr>" +
        "<tr><th>Panjang</th><td>" + " : " + feature.properties.Panjang + " m </td></tr>" +
        "<tr><th>Lebar</th><td>" + " : " + feature.properties.Lebar + " m </td></tr>" +
        "<tr><th>Luas</th><td>" + " : " + feature.properties.Luas + " ㎡ </td></tr>" +
        "<tr><th>Aset Tanah</th><td>" + " : " + feature.properties.Asettanah + "</td></tr>" +
        "<tr><th>Kondisi</th><td>" + " : " + feature.properties.Kondisi + "</td></tr>" +
        "<tr><th>Kewenangan</th><td>" + " : " + feature.properties.Kewenangan + "</td></tr>" +
        "<tr><th>RERNJOP</th><td>" + " : " + feature.properties.RERNJOP + "</td></tr>" +
        "<tr><th>Status</th><td>" + " : " + feature.properties.Status + "</td></tr>" +
        "<tr><th>Asal</th><td>" + " : " + feature.properties.Asal + "</td></tr>" +
        "</table></div>")
    }
  });

var groupjalan = L.layerGroup([viewjalan, viewjalanoutline]).addTo(map);

var legendajalan = L.control({ position: 'bottomleft' });

legendajalan.onAdd = function (map) {

  var div = L.DomUtil.create('div', 'info legend'),
    grades = ['Jalan BBWSO','Jalan Kalurahan'],
    labels = [],
    colors = ['#ffb200','#b2b2ff'];

  // loop through our density intervals and generate a label with a colored square for each interval
  labels.push(
  '<i style="background:' + '#ff7f00' + '; border-top: 1px solid black;border-bottom: 1px solid black; width: 18px; height: 4px; float: left; margin-right: 8px; margin-top: 7px;"></i> ' +
  'Jalan Kabupaten'
);
  for (var i = 0; i < grades.length; i++) {
    labels.push(
      '<i style="background:' + colors[i] + '; width: 18px; height: 3px; float: left; margin-right: 8px; margin-top: 7px;"></i> ' +
      grades[i]
    );
  }
  div.innerHTML = "<div style='background-color: #fff; padding: 10px; border-radius: 3px;'><h5>Legenda Jalan</h5>" + labels.join('<br>') + "</div>";
  return div;
};

map.addControl(legendajalan);

  function zoomTojalan(id) {
    var selectedFeatureLayer;

    viewjalan.eachLayer(function (layer) {
      if (layer.feature.properties.id == idjalan) {
        var coordinates = layer.getLatLngs()[0];

        // Calculate the middle index of the coordinates array
        var middleIndex = Math.floor(coordinates.length / 2);

        // Get the middle coordinates
        var middleLatLng = coordinates[middleIndex];

        // Create an invisible marker at the middle of the LineString
        var markerjalan = L.circleMarker([middleLatLng.lat, middleLatLng.lng], { opacity: 0 }); // set opacity to 0

        // Add the circle marker to the map
        markerjalan.addTo(map);
        markerjalan.bindPopup(layer.getPopup().getContent());
        markerjalan.openPopup();
        // Zoom to the popup
        map.setView(markerjalan.getLatLng(), 20); // Adjust zoom level as needed
      }
    });
  }

    // Get the ID from PHP
    var idjalan = <?= isset($idjalan) ? $idjalan : 'null' ?>;

    // Call this function with the idjalan of the feature you want to zoom to, if it's set
    if (idjalan !== null) {
    zoomTojalan(idjalan);
  }


  // Ekonomi
  var viewekonomi = new L.geoJSON(layerekonomi, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: 'orange',
        fillColor: 'orange',
        weight: 1.5,
        transparent: true,
      };
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup("<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>OtorityKw</th><td>" + " : " + feature.properties.OtorityKw + "</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewekonomi);

  function zoomToFasilitasEkonomi(idfasilitasekonomi) {
    viewekonomi.eachLayer(function (layer) {
      if (layer.feature.properties.id == idfasilitasekonomi) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }
  // Get the ID from PHP
  var idfasilitasekonomi = <?= isset($idfasilitasekonomi) ? $idfasilitasekonomi : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (idfasilitasekonomi !== null) {
    zoomToFasilitasEkonomi(idfasilitasekonomi);
  }

    // SPAM
    var viewspam = new L.geoJSON(layerspamm, {
    style: function (feature) {
      return {
        color: '#0083FF',
        weight: 3,
        opacity: .7,
      };
    },
    onEachFeature: function (feature, marker) {
      marker.bindPopup("<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>Panjang</th><td>" + " : " + feature.properties.PANJANG + " m</td></tr>" +
        "<tr><th>Diameter Pipa</th><td>" + " : " + feature.properties.DIAPIPA + " cm </td></tr>" +
        "<tr><th>Vol M3-BAK</th><td>" + " : " + feature.properties.VolM3BAK + " cm </td></tr>" +
        "<tr><th>Deb (L/det)</th><td>" + " : " + feature.properties.DebLdet + " cm </td></tr>" +
        "</table>")
    }
  });
  map.addLayer(viewspam);

  function zoomToSpam(idspam) {
    var selectedFeatureLayer;

    viewspam.eachLayer(function (layer) {
      if (layer.feature.properties.id == idspam) {
        var coordinates = layer.getLatLngs()[0];

        // Calculate the middle index of the coordinates array
        var middleIndex = Math.floor(coordinates.length / 2);

        // Get the middle coordinates
        var middleLatLng = coordinates[middleIndex];

        // Create an invisible marker at the middle of the LineString
        var markerspam = L.circleMarker([middleLatLng.lat, middleLatLng.lng], { opacity: 0 }); // set opacity to 0

        // Add the circle marker to the map
        markerspam.addTo(map);
        markerspam.bindPopup(layer.getPopup().getContent());
        markerspam.openPopup();
        // Zoom to the popup
        map.setView(markerspam.getLatLng(), 20); // Adjust zoom level as needed
      }
    });
  }

    // Get the ID from PHP
    var idspam = <?= isset($idspam) ? $idspam : 'null' ?>;

    // Call this function with the idspam of the feature you want to zoom to, if it's set
    if (idspam !== null) {
    zoomToSpam(idspam);
  }

  // Irigasi
  var viewirigasi = new L.geoJSON(layeririgasii, {
    style: function (feature) {
      return {
        color: '#9C5500',
        weight: 3,
        opacity: .7,
      };
    },
    onEachFeature: function (feature, marker) {
      marker.bindPopup("<table>" +
        "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "<tr><th>Intake</th><td>" + " : " + feature.properties.INTAKE + "</td></tr>" +
        "<tr><th>Panjang</th><td>" + " : " + feature.properties.PANJANG + " m </td></tr>" +
        "<tr><th>Lebar</th><td>" + " : " + feature.properties.LEBAR + " m </td></tr>" +
        "<tr><th>Luas</th><td>" + " : " + feature.properties.LUAS + " ㎡ </td></tr>" +
        "<tr><th>Kewenangan</th><td>" + " : " + feature.properties.KEWENANGAN + "</td></tr>" +
        "<tr><th>Kondisi</th><td>" + " : " + feature.properties.KONDISI + "</td></tr>" +
        "<tr><th>Aset Tanah</th><td>" + " : " + feature.properties.ASSETTANAH + "</td></tr>" +
        "<tr><th>Aset Bank</th><td>" + " : " + feature.properties.ASSETBANG + "</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewirigasi);

  function zoomToIrigasi(idirigasi) {
    var selectedFeatureLayer;

    viewirigasi.eachLayer(function (layer) {
      if (layer.feature.properties.id == idirigasi) {
        var coordinates = layer.getLatLngs()[0];

        // Calculate the middle index of the coordinates array
        var middleIndex = Math.floor(coordinates.length / 2);

        // Get the middle coordinates
        var middleLatLng = coordinates[middleIndex];

        // Create an invisible marker at the middle of the LineString
        var markeririgasi = L.circleMarker([middleLatLng.lat, middleLatLng.lng], { opacity: 0 }); // set opacity to 0

        // Add the circle marker to the map
        markeririgasi.addTo(map);
        markeririgasi.bindPopup(layer.getPopup().getContent());
        markeririgasi.openPopup();
        // Zoom to the popup
        map.setView(markeririgasi.getLatLng(), 20); // Adjust zoom level as needed
      }
    });
  }

    // Get the ID from PHP
    var idirigasi = <?= isset($idirigasi) ? $idirigasi : 'null' ?>;

    // Call this function with the idirigasi of the feature you want to zoom to, if it's set
    if (idirigasi !== null) {
    zoomToIrigasi(idirigasi);
  }

  // Masjid
  // Poly Masjid
  var viewmasjidpoly = new L.geoJSON(layermasjidpolyy, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: 'purple',
        fillColor: 'purple',
        weight: 1.5,
        transparent: true,
      };
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup('<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotomasjid/' + feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fotomasjid/' + feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
      "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewmasjidpoly);

  function zoomToMasjidPoligon(idmasjidpoligon) {
    viewmasjidpoly.eachLayer(function (layer) {
      if (layer.feature.properties.id == idmasjidpoligon) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }

  // Get the ID from PHP
  var idmasjidpoligon = <?= isset($idmasjidpoligon) ? $idmasjidpoligon : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (idmasjidpoligon !== null) {
    zoomToMasjidPoligon(idmasjidpoligon);
  }

  // // Icon Masjid

  var masjidd = L.icon({
    iconUrl: '<?= base_url('assets/icon/masjid.png') ?>',

    iconSize: [25, 37], // size of the icon
    iconAnchor: [13, 35], // point of the icon which will correspond to marker's location
    popupAnchor: [0, 0] // point from which the popup should open relative to the iconAnchor
  });

// Membuat layer baru untuk titik tengah poligon
var viewmasjidpoint = L.layerGroup();

// Mengubah layermasjidpolyy menjadi objek L.geoJSON
var geojson = L.geoJSON(layermasjidpolyy);

// Iterasi melalui setiap fitur di geojson
geojson.eachLayer(function (layer) {
    // Menghitung titik tengah dari setiap poligon
    var center = layer.getBounds().getCenter();

    // Membuat marker dengan ikon masjid di titik tengah
    var marker = L.marker(center, {icon: masjidd});

    // Menambahkan popup yang menampilkan field Nama
    marker.bindPopup('<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotomasjid/' + layer.feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fotomasjid/' + layer.feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
        "<tr><th>Nama</th><td>" + " : " + layer.feature.properties.Nama + "</td></tr>" +"</table>");

    // Menambahkan marker ke layer group
    viewmasjidpoint.addLayer(marker);
});

// Menambahkan layer baru ke peta
map.addLayer(viewmasjidpoint);




// var viewmasjid = L.geoJSON(layermasjidpolyy, {
//     pointToLayer: function (feature, latlng) {
//       // Menghitung centroid dari poligon
//       var centroid = turf.centerOfMass(feature);
//       // Menggunakan koordinat centroid sebagai titik untuk marker
//       var centroidLatLng = [centroid.geometry.coordinates[0], centroid.geometry.coordinates[1]];
//       return L.marker(centroidLatLng, { icon: masjidd });
//     },
//     onEachFeature: function (feature, layer) {
//       var lines = ("<table>" +
//         "<tr><th>"+ feature.properties.Nama + "</th></tr>" +
//         "</table>");
//       layer.bindPopup(lines);
//     }
// }).addTo(map);

  // Poly Pendidikan
  var viewpendidikanpoly = new L.geoJSON(layerpendidikanpoly, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: '#13FF00',
        fillColor: '#13FF00',
        weight: 1.5,
        transparent: true,
      };
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup('<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotopendidikan/' + feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fotopendidikan/' + feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
      "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
      "<tr><th>Kategori</th><td>" + " : " + feature.properties.kategori + "</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewpendidikanpoly);

  function zoomToPendidikan(idpendidikan) {
    viewpendidikanpoly.eachLayer(function (layer) {
      if (layer.feature.properties.id == idpendidikan) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }
  // Get the ID from PHP
  var idpendidikan = <?= isset($idpendidikan) ? $idpendidikan : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (id !== null) {
    zoomToPendidikan(idpendidikan);
  }

  // Makam
  var viewmakam = new L.geoJSON(layermakam, {
    style: function (feature) {
      return {
        opacity: 1.5,
        color: '#FA5F55',
        fillColor: '#FA5F55',
        weight: 1.5,
        transparent: true,
      };
    },
    onEachFeature: function (feature, layer) {
      layer.bindPopup('<div style="text-align: center;margin-bottom:-15px;"><a href="<?= base_url() ?>fotomakam/' + feature.properties.foto + '" target="_blank"><img src = "<?= base_url() ?>fotomakam/' + feature.properties.foto + '" width = "250px;"></a></div><br><br>'+`<div class="accordion" id="accordionExample">`+"<table>" +
      "<tr><th>Nama</th><td>" + " : " + feature.properties.Nama + "</td></tr>" +
      "<tr><th>Status Tanah</th><td>" + " : " + feature.properties.statustanah + "</td></tr>" +
      "<tr><th>Luas</th><td>" + " : " + feature.properties.luas + " ㎡</td></tr>" +
      "<tr><th>Luas QGIS</th><td>" + " : " + feature.properties.luasqgis + " ㎡</td></tr>" +
      "<tr><th>Luas SHM</th><td>" + " : " + feature.properties.luasshm + " ㎡</td></tr>" +
        "</table>")
    }
  });

  map.addLayer(viewmakam);

  function zoomtomakam(idmakam) {
    viewmakam.eachLayer(function (layer) {
      if (layer.feature.properties.id == idmakam) {
        map.fitBounds(layer.getBounds());
        // layer.setStyle({
        //   color: 'green', // Set the color of the selected feature's outline to green
        //   fillColor: 'green' // Set the fill color of the selected feature to green
        // });
        layer.openPopup();

      }
    });
  }

  // Get the ID from PHP
  var idmakam = <?= isset($idmakam) ? $idmakam : 'null' ?>;

  // Call this function with the ID of the feature you want to zoom to, if it's set
  if (idmakam !== null) {
    zoomtomakam(idmakam);
  }

  //pencarian geojson

//   var lay = L.featureGroup([viewbangunankarangwuni, viewtanahkasdesa, viewdrainase, viewekonomi, viewirigasi, viewmasjidpoly, viewpendidikanpoly, viewspam, viewmakam]);	//layer contain searched elements

//   var searchControl = new L.Control.Search({
//     layer: lay,
//     marker: false,
//     initial: false,
//     zoom: 17,
//     searchMethod: function(text, layer) {
//         return layer.feature.properties.Nama.includes(text) || layer.feature.properties.kepkel.includes(text);
//     },
//     moveToLocation: function (latlng, title, map) {
//         var zoom = map.getBoundsZoom(latlng.layer.getBounds());
//         map.setView(latlng, zoom); // access the zoom
//     }
// });


//   searchControl.on('search:locationfound', function (e) {

//     //console.log('search:locationfound', );

//     //map.removeLayer(this._markerSearch)

//     e.layer.setStyle({ fillColor: '#3f0', color: '#0f0' });
//     if (e.layer._popup)
//       e.layer.openPopup();

//   }).on('search:collapsed', function (e) {

//     e.eachLayer(function (layer) {	//restore feature color
//       e.resetStyle(layer);
//     });
//   });

//   map.addControl(searchControl);  //inizialize search control

  //PANELLING LAYER
  // panel base layer
  var baseLayers = [
    {
      active: false,
      name: "Open Street Map",
      layer: osmLayer
    },
    {
      active:true,
      name: "Satellite",
      layer: L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
      })
    },
    {
      active:true,
      name: "ESRI Light Gray",
      layer: L.tileLayer('http://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        crossOrigin: true,
        maxZoom: 22
    })},
  ];
  // panel layer
  var overLayers = [
    {
      group: "Polygon",
      layers: [
        {
          active: true,
          name: "Bangunan Desa",
          icon: '<i class="icon" style="background-color:red"></i>',
          layer: viewbangunankarangwuni
        },
        {
          active: true,
          name: "Tanah Kas Desa",
          icon: '<i class="icon" style="background-color:green"></i>',
          layer: viewtanahkasdesa
        },
        {
          active: true,
          name: "Administrasi Desa",
          icon: '<i class="icon" style="background-color:black"></i>',
          layer: viewadminkarangwuni
        },
        {
          active: true,
          name: "Fasilitas Ekonomi",
          icon: '<i class="icon" style="background-color:orange"></i>',
          layer: viewekonomi
        },
        {
          active: true,
          name: "Masjid",
          icon: '<i class="icon" style="background-color:purple;"></i>',
          layer: viewmasjidpoly
        },
        {
          active: true,
          name: "Pendidikan",
          icon: '<i class="icon" style="background-color:#13FF00;"></i>',
          layer: viewpendidikanpoly
        },
        {
          active: true,
          name: "Makam",
          icon: '<i class="icon" style="background-color:#FA5F55;"></i>',
          layer: viewmakam
        },
      ]
    },
    {
      group: "Line",
      layers: [
        {
          active: true,
          name: "Drainase",
          icon: '<i class="icon" style="background-color:blue;height: 2px;"></i>',
          layer: viewdrainase
        },
        {
          active: true,
          name: "Irigasi",
          icon: '<i class="icon" style="background-color:#9C5500;height: 2px;"></i>',
          layer: viewirigasi
        },
        {
          active: true,
          name: "SPAM",
          icon: '<i class="icon" style="background-color:#0083FF;height: 2px;"></i>',
          layer: viewspam
        },
        {
          active: true,
          name: "Jalan",
          layer: groupjalan
        },
      ]
    },
    {
      group: "Markers",
      layers: [
        {
          active: true,
          name: "Masjid",
          icon: '<img src="<?= base_url() ?>leaflet/leaflet-panel-layers-master/examples/images/icons/masjid.png">',
          layer: viewmasjidpoint
        },
      ]
    }
  ];

  var panelbutton = L.Control.extend({
    options: {
        position: 'topright'
    },

    onAdd: function (map) {
        var container = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');

        container.style.backgroundColor = 'white';
        container.style.backgroundSize = "30px 30px";
        container.style.width = '40px';
        container.style.marginBottom = '10px';
        container.style.height = '40px';

        // Membuat tombol dengan ikon SVG
        container.innerHTML = '<img src="<?=base_url()?>assets/stack.svg" width="25" height="25" style="position: relative; top: 43%; left: 50%; transform: translate(-50%, -50%);">';

        container.onclick = function(){
            var panel = $(panelLayers.getContainer());
            if(panel.css('display') === "flex") {
                panel.animate({width: 'toggle'}); // Menambahkan animasi geser ke kanan saat membuka
            } else {
                panel.animate({width: 'toggle'}); // Menambahkan animasi geser ke kiri saat menutup
            }
        }

        return container;
    },
});

map.addControl(new panelbutton());

var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {
    selectorGroup: true,
    collapsibleGroups: true,
    collapsed: false // Panel layer selalu dalam keadaan collapsed
});

map.addControl(panelLayers);

  map.on('overlayadd', function (e) {
    if (map.hasLayer(groupjalan)) {
      legendajalan.addTo(map);
    }
  });

  map.on('overlayremove', function (e) {
    if (!map.hasLayer(groupjalan)) {
      map.removeControl(legendajalan);
    }
  });


</script>
