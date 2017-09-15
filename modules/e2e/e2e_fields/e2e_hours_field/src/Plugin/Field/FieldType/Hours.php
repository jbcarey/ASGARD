<?php

namespace Drupal\e2e_hours_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;

/**
 * Plugin implementation of the 'Hours' field type.
 *
 * @FieldType(
 *   id = "Hours",
 *   label = @Translation("Hours"),
 *   description = @Translation("Select hours."),
 *   category = @Translation("E2E"),
 *   default_widget = "HoursWidget",
 *   default_formatter = "HoursFormatter"
 * )
 */
class Hours extends FieldItemBase {
  
  /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(StorageDefinition $storage) {
    $properties = [];
    
    $properties['time_from'] = DataDefinition::create('string')->setLabel(t('From'));
    $properties['time_until'] = DataDefinition::create('string')->setLabel(t('Until'));
    $properties['time_note'] = DataDefinition::create('string')->setLabel(t('Note'));
    
    return $properties;
  }
  
  /**
  * {@inheritdoc}
  */
  public static function schema(StorageDefinition $storage) {
    
    $columns = [];
    
    $columns['time_from'] = ['type' => 'varchar', 'length' => 5,];
    $columns['time_until'] = ['type' => 'varchar', 'length' => 5,];
    $columns['time_note'] = ['type' => 'varchar', 'length' => 40,];
    
    return ['columns' => $columns, 'indexes' => [],];
  }
  
  /**
  * {@inheritdoc}
  */
  public function isEmpty(){
    return empty($this->get('time_from')->getValue()) && empty($this->get('time_until')->getValue());
  }
  
}
