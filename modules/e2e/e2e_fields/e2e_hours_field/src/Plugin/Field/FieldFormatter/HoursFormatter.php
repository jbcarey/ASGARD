<?php

namespace Drupal\e2e_hours_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'HoursFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "HoursFormatter",
 *   label = @Translation("Hours"),
 *   field_types = {
 *     "Hours"
 *   }
 * )
 */

class HoursFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = array();
    
    foreach($items as $delta => $item){
      $elements[$delta] = array(
        '#theme' => 'field_hours',
        '#time_from' => $item->time_from,
        '#time_until' => $item->time_until,
        '#time_note' => $item->time_note,
      );
    }
    return $elements;
    
  }
  
}