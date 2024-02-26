mapboxgl.accessToken = 'pk.eyJ1Ijoicm9uaS1zaGFsZXYiLCJhIjoiY2thaDEzdGwyMDN1ejJzdGl5Z3Nhems1ZCJ9.C36_Th7LAixAB1t6WHodMw';

// get your key from app.tomorrow.io/development/keys
const API_KEY = 'EnFoIZ68oO9hcDp885e97kmIIZTjDBWz'; 

// pick the field (like temperature, precipitationIntensity or cloudCover)
const DATA_FIELD = 'precipitationIntensity';

// set the ISO timestamp (now for all fields, up to 6 hour out for precipitationIntensity)
const TIMESTAMP = (new Date()).toISOString();

// initialize the map
var map = (window.map = new mapboxgl.Map({
  container: 'map',
  zoom: 3,
  center: [7.5, 58],
  style: 'mapbox://styles/mapbox/light-v10',
  antialias: true
}));

// inject the tile layer
map.on('load', function() {
  map.addSource('tomorrow-io-api', {
    "type": 'raster',
    "tiles": [`https://api.tomorrow.io/v4/map/tile/{z}/{x}/{y}/${DATA_FIELD}/${TIMESTAMP}.png?apikey=${API_KEY}`],
    "tileSize": 256,
    "attribution": '&copy; <a href="https://www.tomorrow.io/weather-api">Powered by Tomorrow.io</a>'
  });
  map.addLayer({
    "id": "radar-tiles",
    "type": "raster",
    "source": "tomorrow-io-api",
    "minzoom": 1,
    "maxzoom": 12,
    "paint":{
        'raster-opacity':
        0.5
    }
  });
});