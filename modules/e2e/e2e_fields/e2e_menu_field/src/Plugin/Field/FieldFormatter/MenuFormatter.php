<?php

namespace Drupal\e2e_menu_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\e2e_menu_field\Includes\MenuField;

/**
 * Plugin implementation of the 'MenuFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "MenuFormatter",
 *   label = @Translation("Menu display"),
 *   field_types = {
 *     "Menu"
 *   }
 * )
 */

class MenuFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = array();
    
    foreach($items as $delta => $item){
      
      $field = new MenuField($item);
      $treeoutput = $field->getTreeOutput();
      
      $elements[$delta] = [
        '#theme' => 'field_menu_tree',
        '#tree' => $treeoutput,
        '#columns' => $item->columns,
      ];
    }
    
    return $elements;
    
  }
  
}