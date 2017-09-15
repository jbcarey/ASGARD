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
          icon: marker.icon,
        });

        var infowindow = new google.maps.InfoWindow({
					content: marker.street + ' ' + marker.street_nr,
				});

				markers[i].addListener('click', function() {
					infowindow.open(map,markers[i]);
				});

        bounds.extend(markers[i].getPosition());

        markers[i].setVisible(false);
      });




      // If route...
      if (markers.length > 1) {

        map.fitBounds(bounds);

        var directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer({
          polylineOptions: { strokeColor: "red" }
        });
        calculateAndDisplayRoute(markers, directionsService, directionsDisplay);
        directionsDisplay.setMap(map);

        function calculateAndDisplayRoute(markers, directionsService, directionsDisplay) {

          var start = markers.shift();
          var end = markers.pop();
          var waypoints = [];

          for (var i = 0; i < markers.length; i++) {
            waypoints.push({
              location: markers[i].position,
              stopover: true,
            });
          }

          directionsService.route({
            origin: start.position,
            destination: end.position,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.WALKING
          }, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) directionsDisplay.setDirections(response);
            else window.alert('Directions request failed due to ' + status);
          });

        }
      }

      else {
        markers[0].setVisible(true);
        map.setCenter(bounds.getCenter());
        map.setZoom(16);
      }

    }
  };
})(jQuery);
