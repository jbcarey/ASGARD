<?php

namespace Drupal\e2e_location_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\e2e_location_field\Includes\LocationFieldHelper;

/**
 * Plugin implementation of the 'LocationAddressFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "LocationAddressFormatter",
 *   label = @Translation("Address as text"),
 *   field_types = {
 *     "Location"
 *   }
 * )
 */

class LocationAddressFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = array();
    
    foreach($items as $delta => $item){
      
      $elements[$delta] = array(
        '#theme' => 'field_location_formatter',
		'#addresses' => array(LocationFieldHelper::getLocationFieldValues($item)),
		'#map' => FALSE,
      );
      
    }
    
    return $elements;
    
  }
  
}