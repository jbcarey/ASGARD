<?php

namespace Drupal\e2e_menu_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface as StorageDefinition;

/**
 * Plugin implementation of the 'Menu' field type.
 *
 * @FieldType(
 *   id = "Menu",
 *   label = @Translation("Menu"),
 *   description = @Translation("Menu fields."),
 *   category = @Translation("Menu"),
 *   default_widget = "MenuWidget",
 *   default_formatter = "MenuFormatter"
 * )
 */
class Menu extends FieldItemBase {
  
  /**
  * {@inheritdoc}
  */
  public static function propertyDefinitions(StorageDefinition $storage) {
    
    $properties = [];
    
    $properties['menu_name'] = DataDefinition::create('string')->setLabel(t('Menu'));
    $properties['menu_plid'] = DataDefinition::create('string')->setLabel(t('Menu parent'));
    $properties['menu_level'] = DataDefinition::create('integer')->setLabel(t('Show links below until level'));
    $properties['columns'] = DataDefinition::create('integer')->setLabel(t('Columns'));
    $properties['display'] = DataDefinition::create('string')->setLabel(t('Display'));
    
    return $properties;
  }
  
  /**
  * {@inheritdoc}
  */
  public static function schema(StorageDefinition $storage) {
    
    $columns = [];
    
    $columns['menu_name'] = ['type' => 'varchar', 'length' => 128,];
    $columns['menu_plid'] = ['type' => 'varchar', 'length' => 128,];
    $columns['menu_level'] = ['type' => 'int', 'length' => 8,];
    $columns['columns'] = ['type' => 'int', 'length' => 8,];
    $columns['display'] = ['type' => 'varchar', 'length' => 128,];
    
    return ['columns' => $columns, 'indexes' => [],];
  }
}