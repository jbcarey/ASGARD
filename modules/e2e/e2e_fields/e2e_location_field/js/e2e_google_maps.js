function initMap() {

  // defaultClickEvent
  window.defaultClickEvent = ('ontouchstart' in document.documentElement) ? 'touchend' : 'click';

  // get map elements
  var maps = document.getElementsByTagName('map');

  // set each map
  for (var i = 0; i < maps.length; i++) {
    setMap(maps[i]);
  }

  // set map
  function setMap(element) {

    var locations = element.getElementsByTagName('location');
    var routes = element.getElementsByTagName('route');
    var container = document.createElement('div');
    var bounds = new google.maps.LatLngBounds();
    var zoom = parseFloat(element.getAttribute('data-zoom'));
    var lat = parseFloat(element.getAttribute('data-lat'));
    var lng = parseFloat(element.getAttribute('data-lng'));
    var places = element.getAttribute('data-places');
    var placesSearch = element.querySelector('input[type="search"]');
    var filters = element.getElementsByTagName('ul');
    var openInfoWindow = 0;

    // add map as first child of container
    element.insertBefore(container, element.firstChild);

    // create map
    var map = new google.maps.Map(container, {
      center: {lat: lat, lng: lng},
      zoom: zoom
    });

    // create a marker for each location
    for (var i = 0; i < locations.length; i++) {
      locations[i].marker = addMarker(locations[i]);
    }

    // set bounds when zoom is 0
    if (zoom === 0) {
      fitBoundsToVisibleMarkers(12);
    }

    // set group filters when element has ul element
    if (filters.length > 0) {
      filterGroups(filters[0]);
    }

    // show places
    if (places) {
      var placesRange = element.getAttribute('data-places-range');
      placesRange = placesRange ? placesRange : 2500;
      showPlaces();
    }

    if (routes.length > 0) {
      setRoutes();
    }

    // CHILD FUNCTION setMap.addMarker
    // add locations to map
    function addMarker(location) {

      var lat = parseFloat(location.getAttribute('data-lat'));
      var lng = parseFloat(location.getAttribute('data-lng'));
      var title = location.getElementsByTagName('h4')[0].innerHTML;
      var icon = location.hasAttribute('data-marker') ? location.getAttribute('data-marker') : '';

      // create marker
      var marker = new google.maps.Marker({
        position: {lat: lat, lng: lng},
        title: title,
        map: map,
        icon: icon
      });

      // create marker info window
      var infowindow = new google.maps.InfoWindow({
        content: location.innerHTML
      });

      // change default click event to mousedown on mobile (touchstart/touchend not working on map?)
      var mapClickEvent = window.defaultClickEvent == 'touchend' ? 'mousedown' : window.defaultClickEvent;

      // marker click event
      marker.addListener(mapClickEvent, function() {
        openMarker();
      });

      // location click event
      location.addEventListener(window.defaultClickEvent, function() {
        openMarker();
        map.panTo(marker.position);
      });

      // add marker to bounds (only used with filters ???)
      bounds.extend(marker.getPosition());

      // CHILD FUNCTION setMap.addMarker.openMarker
      // open marker & close previous marker function
      function openMarker() {
        if (openInfoWindow !== 0) {
          openInfoWindow.close();
        }
        infowindow.open(map, marker);
        openInfoWindow = infowindow;
      }

      // return marker object for further use
      return marker;
    }

    // CHILD FUNCTION setMap.filterGroups
    // filter groups
    function filterGroups(filters) {

      var groups = filters.querySelectorAll('input[type="checkbox"]');
      var activeGroups = 0; // check if there are filters set
      var triggerState = 0; // check if filters are triggered

      // set handler for all groups
      for (var i = 0; i < groups.length; i++) {
        setGroup(groups[i]);
      }

      // CHILD FUNCTION setMap.filterGroups.setGroup
      // set group checkbox
      function setGroup(checkbox) {

        // set all checkboxes false (for browsers keeping form states)
        groups[i].checked = false;

        // add change event
        checkbox.addEventListener('change', function() {

          // toggle groups
          var status = checkbox.checked === true ? true : false;
          activeGroups = status ? activeGroups + 1 : activeGroups - 1;
          toggleGroups(checkbox.getAttribute('value'), status);

          // reset when no groups are checked
          if (activeGroups === 0) {
            triggerState = 0;
            for (var i = 0; i < locations.length; i++) {
              toggleMarker(locations[i], true);
            }
          }

        });
      }

      // CHILD FUNCTION setMap.filterGroups.toggleGroups
      // toggle locations visibility
      function toggleGroups(groupName, status) {
        for (var i = 0; i < locations.length; i++) {
          if (locations[i].getAttribute('data-group') == groupName) {
            toggleMarker(locations[i],status);
          } else if (triggerState === 0) {
            toggleMarker(locations[i],false);
          }
        }
        triggerState = 1;

        if (zoom === 0) {
          fitBoundsToVisibleMarkers(12);
        }

      }

      // CHILD FUNCTION setMap.filterGroups.toggleMarker
      // toggle marker
      function toggleMarker(location, status) {
        if (openInfoWindow !== 0) openInfoWindow.close();
        var display = status ? 'block' : 'none';
        location.marker.setVisible(status);
        location.style.display = display;
      }

    }


    // CHILD FUNCTION setMap.toggleGroups
    // fitbounds to visible markers
    function fitBoundsToVisibleMarkers(maxZoom) {

      // create second bounds object with only visible markers
      visibleBounds = new google.maps.LatLngBounds();

      // extend visibleBounds with visible markers
      for (var i=0; i < locations.length; i++) {
        if(locations[i].marker.getVisible()) {
          visibleBounds.extend(locations[i].marker.getPosition());
        }
      }

      // replace visibleBounds with bounds if visibleBounds is empty
      visibleBounds = visibleBounds.isEmpty() ? bounds : visibleBounds;

      // set max zoom level when fitBounds
      google.maps.event.addListener(map, 'zoom_changed', function() {
        zoomChangeBoundsListener = google.maps.event.addListener(map, 'bounds_changed', function(event) {
          if (this.getZoom() > maxZoom && this.initialZoom === true) {
            this.setZoom(maxZoom);
            this.initialZoom = false;
          }
          google.maps.event.removeListener(zoomChangeBoundsListener);
        });
      });

      // set initialZoom && fitBounds
      map.initialZoom = true;
      map.fitBounds(visibleBounds);

    }


    // CHILD FUNCTION setMap.showPlaces
    // show google places
    function showPlaces() {

      // places request
      var request = {
        location: map.center,
        radius: placesRange,
        types: places
      };

      // places service & infowindows
      var placesService = new google.maps.places.PlacesService(map);
      var placesInfoWindow = new google.maps.InfoWindow();
      placesService.nearbySearch(request, callback);

      // get places
      function callback(results, status) {
        if (status == google.maps.places.PlacesServiceStatus.OK) {
          for (var i = 0; i < results.length; i++) {
            var place = results[i];
            addPlacesMarker(results[i]);
          }
        }
      }

      // add places marker
      function addPlacesMarker(place) {

        var marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location,
          title: place.title,
          icon: {
            url: place.icon,
            size: new google.maps.Size(18, 18),
            scaledSize: new google.maps.Size(18, 18)
          }
        });

        // add places marker infowindows
        google.maps.event.addListener(marker, 'click', function() {
          placesService.getDetails(place, function(result, status) {
            if (status !== google.maps.places.PlacesServiceStatus.OK) {
              return;
            }
            placesInfoWindow.setContent(result.name);
            placesInfoWindow.open(map, marker);
          });
        });

        return marker;
      }

      // show places search box
      if (placesSearch) {
        showPlacesSearch();
      }

      // CHILD FUNCTION setMap.showPlaces.showPlacesSearch
      // show google places search box
      function showPlacesSearch() {

    		// Create the search box and link it to the UI element.
    		var searchBox = new google.maps.places.SearchBox(placesSearch);

    		// Bias the SearchBox results towards current map's viewport.
    		map.addListener('bounds_changed', function() {
    			searchBox.setBounds(map.getBounds());
    		});

    		var markers = [];

    		// Listen for the event fired when the user selects a prediction and retrieve
    		// more details for that place.
    		searchBox.addListener('places_changed', function() {

    			var places = searchBox.getPlaces();

    			if (places.length === 0) {
    				return;
    			}

    			// Clear out the old markers.
    			for (var i = 0; i < markers.length; i++) {
    				markers[i].setMap(null);
    			}

          // reset markers array
    			markers = [];

    			// For each place, get the icon, name and location.
    			var bounds = new google.maps.LatLngBounds();

    			for (var i = 0; i < places.length; i++) {
            marker = addPlacesMarker(places[i]);
            markers.push(marker);
    			}

    		});
    	}

    }


    function setRoutes() {

      // set routes
      for (var i = 0; i < routes.length; i++) {
        setRoute(routes[i]);
      }

      // set routes
      function setRoute(route) {

        var waypoints = route.getElementsByTagName('location');
        var markers = [];
        var color = route.getAttribute('data-color');

        for (var i = 0; i < waypoints.length; i++) {
          markers.push(waypoints[i].marker);
        }

        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({
          polylineOptions: { strokeColor: color }
        });

        calculateAndDisplayRoute(markers, directionsService, directionsDisplay);
        directionsDisplay.setMap(map);

        function calculateAndDisplayRoute(markers, directionsService, directionsDisplay) {

          var waypoints = [];

          for (var i = 0; i < markers.length; i++) {
            var stop = i === 0 || i == (markers.length - 1) ? false : true;
            waypoints[i] = {
              location: markers[i].position,
              stopover: stop,
            };
            markers[i].setMap(null);
          }

          directionsService.route({
            origin: markers[0].position,
            destination: markers[markers.length - 1].position,
            waypoints: waypoints,
            travelMode: google.maps.TravelMode.WALKING
          }, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) directionsDisplay.setDirections(response);
            else window.alert('Directions request failed due to ' + status);
          });

        }
      }


    }

  }

}

// create global function name (closure)
window['initMap'] = initMap;
