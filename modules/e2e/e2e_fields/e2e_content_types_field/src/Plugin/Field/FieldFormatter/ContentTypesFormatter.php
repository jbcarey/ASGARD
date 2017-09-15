<?php

namespace Drupal\e2e_content_types_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'ContentTypesFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "ContentTypesFormatter",
 *   label = @Translation("Content Types"),
 *   field_types = {
 *     "ContentTypes"
 *   }
 * )
 */

class ContentTypesFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = [];
    
    foreach($items as $delta => $item){
      $elements[$delta] = [
        '#type' => 'markup',
        '#markup' => $item->contenttypes,
      ];
    }
    
    return $elements;
    
  }
  
}