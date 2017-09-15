<?php

namespace Drupal\e2e_content_types_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;

/**
 * Plugin implementation of the 'ContentTypes' field type.
 *
 * @FieldType(
 *   id = "ContentTypes",
 *   label = @Translation("Content Types"),
 *   description = @Translation("Select content types."),
 *   category = @Translation("E2E"),
 *   default_widget = "ContentTypesWidget",
 *   default_formatter = "ContentTypesFormatter"
 * )
 */
class ContentTypes extends FieldItemBase {
  
  /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(StorageDefinition $storage) {
    $properties = [];
    
    $properties['contenttypes'] = DataDefinition::create('string')->setLabel(t('Content types'));
    
    return $properties;
  }
  
  /**
  * {@inheritdoc}
  */
  public static function schema(StorageDefinition $storage) {
    
    $columns = [];
    
    $columns['contenttypes'] = ['type' => 'char', 'length' => 255,];
    
    return ['columns' => $columns, 'indexes' => [],];
  }
  
  /**
  * {@inheritdoc}
  */
  public function isEmpty(){
    return empty($this->get('contenttypes')->getValue());
  }
  
}
