<?php

namespace Drupal\e2e_folders_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

use Drupal\e2e_folders_field\Includes\FoldersFieldHelper;

/**
 * Plugin implementation of the 'FoldersFormatter' formatter.
 *
 * @FieldFormatter(
 *   id = "FoldersFormatter",
 *   label = @Translation("Folders"),
 *   field_types = {
 *     "Folders"
 *   }
 * )
 */

class FoldersFormatter extends FormatterBase {
  
  /**
  * {@inheritdoc}
  */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    
    $elements = [];
    
    foreach ($items as $delta => $item) {
      $folders = FoldersFieldHelper::getFolders();
      
      if(isset($folders[$item->folders]))
        $elements[$delta]['#markup'] = $folders[$item->folders];
    }
    
    return $elements;
    
  }
}