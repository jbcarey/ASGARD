(function($) {
  Drupal.behaviors.e2e_paragraphs_map_overview = {
    attach: function(context, settings) {

      var lat = parseFloat(settings.e2e_paragraphs_map_overview.lat);
      var lng = parseFloat(settings.e2e_paragraphs_map_overview.lng);

      // map
      var map = new google.maps.Map($('#map')[0], {center: {lat: lat, lng: lng}});
      var bounds = new google.maps.LatLngBounds();

	  // markers
      var markers = [];
      $.each(settings.e2e_paragraphs_map_overview.markers, function(key,value) {
        
        markers[key] = new google.maps.Marker({
          map: map,
          position: {lat: value.lat, lng: value.lng},
          title: value.title,
          icon: value.icon,
		});
		
        var infowindow = new google.maps.InfoWindow({content: value.info,});
		markers[key].addListener('click', function() {
          infowindow.open(map,markers[key]);
		});
        
        bounds.extend(markers[key].getPosition());
      });
            
      if(markers.length <= 0)
        bounds.extend(map.getCenter());

      // fitbounds
      map.fitBounds(bounds);

      if(markers.length > 0){

        // filters
        var form = $("#map-filters");
        
        $("input:checkbox",form).bind( "change", function() {
          var types = [];
          $('input:checked',form).each(function() {
            types.push($(this).val());
          });
          $.each(settings.e2e_paragraphs_map_overview.markers, function(key,value) {

            if (arrayCompare(value.type, types) || types.length == 0) markers[key].setVisible(true);
            //if ($.inArray(value.type, types) != -1 || types.length == 0) markers[key].setVisible(true);
            else markers[key].setVisible(false);
          });
        });

        // search & places
        if(settings.e2e_paragraphs_map_overview.show_search == 1) initAutocomplete(map);
        if(settings.e2e_paragraphs_map_overview.show_places == 1) initShowPlaces(map);
      
      }
    }
  };

  // COMPARE ARRY FUNCTION
  function arrayCompare(a, b) {
    var check = false;
    $.each(a, function(key,value) {
      if ($.inArray(value, b) != -1) check = true;
    });
    return check;
  }

	// AUTOCOMPLETE SEARCHBOX
	function initAutocomplete(map) {
      
      // Create the search box and link it to the UI element.
      var input = $('#map-search')[0];
      var searchBox = new google.maps.places.SearchBox(input);
      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      // Bias the SearchBox results towards current map's viewport.
      map.addListener('bounds_changed', function() {
        searchBox.setBounds(map.getBounds());
      });

      var markers = [];
      // Listen for the event fired when the user selects a prediction and retrieve
      // more details for that place.
      searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();
        
        if (places.length == 0)
          return;

		// Clear out the old markers.
		markers.forEach(function(marker) {
          marker.setMap(null);
		});
		markers = [];

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
          var icon = {
            url: place.icon,
			size: new google.maps.Size(71, 71),
			origin: new google.maps.Point(0, 0),
			anchor: new google.maps.Point(17, 34),
			scaledSize: new google.maps.Size(25, 25)
          };

          // Create a marker for each place.
          markers.push(new google.maps.Marker({
			map: map,
			icon: icon,
			title: place.name,
			position: place.geometry.location
          }));

          if (place.geometry.viewport) {
			// Only geocodes have viewport.
			bounds.union(place.geometry.viewport);
          } else {
			bounds.extend(place.geometry.location);
          }
		});
		map.fitBounds(bounds);
      });
	}



	// SHOW GOOGLE PLACES
	function initShowPlaces(map) {
        
      var maplat = 50.8427501;
      var maplng = 4.351549900000009;
        
      if(map.center !== undefined){
        maplat = map.center.lat();
        maplng = map.center.lng();
      }
      
      var request = {
		location: {lat: maplat, lng: maplng},
		radius: '500',
		types: ['all']
      };
        
      service = new google.maps.places.PlacesService(map);
      service.nearbySearch(request, callback);
      infoWindow = new google.maps.InfoWindow();
      
      function callback(results, status) {
		if (status == google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
			var place = results[i];
			addMarker(results[i]);
          }
        }
      }

      function addMarker(place) {
		var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
          icon: {
            url: 'http://maps.gstatic.com/mapfiles/circle.png',
            anchor: new google.maps.Point(10, 10),
            scaledSize: new google.maps.Size(10, 17)
          }
		});

		google.maps.event.addListener(marker, 'click', function() {
          service.getDetails(place, function(result, status) {
			if (status !== google.maps.places.PlacesServiceStatus.OK) {
              console.error(status);
              return;
			}
			infoWindow.setContent(result.name);
			infoWindow.open(map, marker);
          });
		});
      }
    }

})(jQuery);