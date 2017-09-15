<?php

namespace Drupal\e2e_location_field\Includes;

class LocationFieldHelper {
  
  //get data for location fields
  public static function getLocationFields($marker = TRUE){
    
    $locationfields = array(
      'title' => array('title' => 'Title', 'size' => 55, 'length' => 128, 'settingfield' => 'show_title', 'dataname' => 'title'),
      'street' => array('title' => 'Street', 'size' => 47, 'length' => 128, 'dataname' => 'route'),
      'street_nr' => array('title' => 'Nr', 'size' => 8, 'length' => 32, 'dataname' => 'street_number'),
      'zip_code' => array('title' => 'Zip code', 'size' => 8, 'length' => 16, 'dataname' => 'postal_code'),
      'city' => array('title' => 'City', 'size' => 47, 'length' => 128, 'dataname' => 'locality'),
      'state' => array('title' => 'Province', 'size' => 27, 'length' => 128, 'dataname' => 'administrative_area_level_1'),
      'country' => array('title' => 'Country', 'size' => 28, 'length' => 128, 'dataname' => 'country'),
      'lat' => array('title' => 'Latitude', 'size' => 27, 'length' => 64, 'settingfield' => 'show_map', 'dataname' => 'lat'),
      'lng' => array('title' => 'Longitude', 'size' => 28, 'length' => 64, 'settingfield' => 'show_map', 'dataname' => 'lng'),
      'info' => array('title' => 'Info', 'size' => 255, 'length' => 255, 'settingfield' => 'show_info'),
    );
    
    if($marker)
      $locationfields['marker'] = array('title' => 'Markers', 'length' => 255);
    
    return $locationfields;
  }
  
  //get location field module settings
  public static function getLocationSettingFields(){
    
    $settingfields = array(
      'city' => 'City',
      'zip_code' => 'Zip code',
      'country' => 'Country',
      'state' => 'Province',
      'lat' => 'Latitude',
      'lng' => 'Longitude',
      'google_maps_key' => 'Google maps API key',
    );
    
    return $settingfields;
    
  }
  
  //get settings for location field widget
  public static function getLocationWidgetSettingFields(){
      
    $settingfields = array(
      'show_map' => array('title' => 'Show map and coördinates on form', 'default' => TRUE),
      'show_markers' => array('title' => 'Show markers', 'default' => TRUE),
      'show_title' => array('title' => 'Show title', 'default' => FALSE),
      'show_info' => array('title' => 'Show info', 'default' => FALSE),
    );
    
    return $settingfields;
  }
  
  //get array of values from location field entity
  public static function getLocationFieldValues($item){
    
    $locationfields = array_keys(self::getLocationFields());
      
    $fields = array();
      
    foreach($locationfields as $locationfield){
      if(isset($item->$locationfield))
        $fields[$locationfield] = $item->$locationfield;
    }
    
    return $fields;
  }
  
}

