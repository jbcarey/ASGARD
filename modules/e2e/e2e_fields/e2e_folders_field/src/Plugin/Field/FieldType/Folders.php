<?php

namespace Drupal\e2e_folders_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;

/**
 * Plugin implementation of the 'Folders' field type.
 *
 * @FieldType(
 *   id = "Folders",
 *   label = @Translation("Folders"),
 *   description = @Translation("Select folders."),
 *   category = @Translation("E2E"),
 *   default_widget = "FoldersWidget",
 *   default_formatter = "FoldersFormatter"
 * )
 */
class Folders extends FieldItemBase {
  
  /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(StorageDefinition $storage) {
    $properties = [];
    
    $properties['folders'] = DataDefinition::create('string')->setLabel(t('Folders'));
    
    return $properties;
  }
  
  /**
  * {@inheritdoc}
  */
  public static function schema(StorageDefinition $storage) {
    
    $columns = [];
    
    $columns['folders'] = ['type' => 'char', 'length' => 255,];
    
    return ['columns' => $columns, 'indexes' => [],];
  }
  
  /**
  * {@inheritdoc}
  */
  public function isEmpty(){
    return empty($this->get('folders')->getValue());
  }
  
}
