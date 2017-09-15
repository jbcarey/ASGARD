(function($) {
  Drupal.behaviors.e2e_location_field_widget = {
    attach: function(context, settings) {

      var map = new google.maps.Map($('.map')[0]);
      var bounds = new google.maps.LatLngBounds();
      var markers = [];

      $.each(settings.e2e_location_field_map.markers, function(i,marker) {
        
        markers[i] = new google.maps.Marker({
          map: map,
          position: {lat: parseFloat(marker.lat), lng: parseFloat(marker.lng)},
          title: marker.street + ' ' + marker.street_nr,
          icon: settings.e2e_location_field_map.markers_path + marker.marker + '.png',
        });
        
        if(settings.e2e_location_field_map.open == true){
          
          var infowindow = new google.maps.InfoWindow({
            content: marker.street + ' ' + marker.street_nr,
          });
          
          markers[i].addListener('click', function() {
            infowindow.open(map,markers[i]);
          });

        }

        bounds.extend(markers[i].getPosition());

      });

      map.setCenter(bounds.getCenter());
      map.setZoom(16);

    }
  };
})(jQuery);
