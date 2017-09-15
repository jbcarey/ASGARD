<?php

namespace Drupal\e2e_location_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;
use Drupal\e2e_location_field\Includes\LocationFieldHelper;

/**
 * Plugin implementation of the 'Location' field type.
 *
 * @FieldType(
 *   id = "Location",
 *   label = @Translation("Location"),
 *   description = @Translation("Location fields."),
 *   category = @Translation("E2E"),
 *   default_widget = "LocationWidget",
 *   default_formatter = "LocationAddressFormatter"
 * )
 */
class Location extends FieldItemBase {
  
  /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(StorageDefinition $storage) {
    
    $properties = [];
    
    $locationfields = LocationFieldHelper::getLocationFields();
    
    foreach($locationfields as $fieldname => $locationfield){
      $properties[$fieldname] = DataDefinition::create('string')->setLabel(t($locationfield['title']));
    }
    
    return $properties;
  }
  
  /**
  * {@inheritdoc}
  */
  public static function schema(StorageDefinition $storage) {
    
    $columns = [];
    
    $locationfields = LocationFieldHelper::getLocationFields();
    
    foreach($locationfields as $fieldname => $locationfield){
      $columns[$fieldname] = ['type' => 'varchar', 'length' => $locationfield['length'],];
    }
    
    return ['columns' => $columns, 'indexes' => [],];
  }
}
