(function($) {
  Drupal.behaviors.e2e_location_field_widget = {
    attach: function(context, settings) {

      var geocoder = new google.maps.Geocoder();

			// fix map with tabs
			if ($('.field-group-htabs-wrapper').length) {
				var eq = $('.map').parents('.panes > fieldset').index();
				$('.tabs .tab').eq(eq).click(function() {
					if (!$(this).hasClass('tab-map-loaded')) setTimeout(load_map, 500);
					$(this).addClass('tab-map-loaded');
				});
			}
			else load_map();

      // fix repeatable
      $(document).ajaxComplete(function() {
        load_map();
      });



			function load_map() {
			// create map for every field widget
				$('.map').each(function(key,element) {

					var parent = $(element).parent().parent();
					var dataLat = $('[data-name="lat"]',parent).val() ? $('[data-name="lat"]',parent).val() : settings.e2e_location_field_widget.lat;
					var dataLng = $('[data-name="lng"]',parent).val() ? $('[data-name="lng"]',parent).val() : settings.e2e_location_field_widget.lng;
					var place = {lat: parseFloat(dataLat), lng: parseFloat(dataLng)};

					// add map to wrapper
					var map = new google.maps.Map(element, {
						zoom: 15,
						center: place
					});

					// add marker to map
					var marker = new google.maps.Marker({
						map: map,
						parent: parent,
						position: place,
						draggable: true,
						icon: settings.e2e_location_field_widget.icon,
					});
                    
					// update fields on marker drag
					google.maps.event.addListener(marker,'dragend',function() {
						$(element).attr('data-lat',marker.getPosition().lat());
						$(element).attr('data-lng',marker.getPosition().lng());
						geocodePosition(marker);
					});

					$('.map-update',parent).click(function() {
                        
						var address = getAddressFields(marker);
                        
                        console.log(address);
                        
						updateCoordinates(address,marker);
					});

				});
			}

      // components
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name',
      };


      // upadate coordinates
      function updateCoordinates(address,marker) {
        geocoder.geocode({'address': address}, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            marker.map.setCenter(results[0].geometry.location);
            marker.setPosition(results[0].geometry.location);
            updateFields(results,marker);
          }
          else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }

      // geocode position
      function geocodePosition(marker) {
        geocoder.geocode({latLng: marker.getPosition()}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) updateFields(results,marker);
          else alert('Geocode was not successful for the following reason: ' + status);
        });
      }


      // update location fields
      function updateFields(results,marker) {
        for (var i = 0; i < results[0].address_components.length; i++) {
          var addressType = results[0].address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = results[0].address_components[i][componentForm[addressType]];
            $('[data-name="' + addressType + '"]',marker.parent).val(val);
          }
        }
        $('[data-name="lat"]',marker.parent).val(marker.getPosition().lat());
        $('[data-name="lng"]',marker.parent).val(marker.getPosition().lng());
      }


      // Get address values
      function getAddressFields(marker) {
        var address = [];
        
        for (var field in componentForm) {
          
          if (componentForm.hasOwnProperty(field)) {
            
            console.log(field);
            address.push($('[data-name="' + field + '"]',marker.parent).val());
          }
        }
        return address.join(' ');
      }

    }
  };
})(jQuery);
