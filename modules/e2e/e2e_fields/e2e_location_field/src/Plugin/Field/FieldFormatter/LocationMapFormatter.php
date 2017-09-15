<?php

namespace Drupal\e2e_location_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\e2e_location_field\Includes\LocationFieldHelper;

/**
 * Plugin implementation of the 'LocationMapFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "LocationMapFormatter",
 *   label = @Translation("Map with marker"),
 *   field_types = {
 *     "Location"
 *   }
 * )
 */

class LocationMapFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = array();
    
    $settings = array(
      'markers' => array(),
      'open' => FALSE,
      'route' => FALSE,
      'markers_path' => base_path() . 'sites/all/markers/',
    );
    
    foreach($items as $delta => $item){
      
      $element = array(
        '#theme' => 'field_location_formatter',
		'#addresses' => FALSE,
		'#map' => TRUE,
      );
      
      $settings['markers'][] = LocationFieldHelper::getLocationFieldValues($item);
      
      $elements[$delta] = $element;
      
    }
    
    $elements['#attached']['library'][] = 'e2e_location_field/location_field';
    $elements['#attached']['library'][] = 'e2e_location_field/location_field_map';
    $elements['#attached']['drupalSettings']['e2e_location_field_map'] = $settings;
    
    return $elements;
    
  }
  
  
  
}