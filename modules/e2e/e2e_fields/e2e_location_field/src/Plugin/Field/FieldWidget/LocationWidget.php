<?php

namespace Drupal\e2e_location_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\e2e_location_field\Includes\LocationFieldHelper;

/**
 * Plugin implementation of the 'LocationWidget' widget.
 *
 * @FieldWidget(
 *   id = "LocationWidget",
 *   label = @Translation("Location"),
 *   field_types = {
 *     "Location"
 *   },
 * )
 */
class LocationWidget extends WidgetBase implements WidgetInterface {

  /**
  * {@inheritdoc}
  */
  public static function defaultSettings() {
    
    $settings = parent::defaultSettings();
    
    $settingfields = LocationFieldHelper::getLocationWidgetSettingFields();
    
    foreach($settingfields as $fieldname => $settingfield){
      $settings[$fieldname] = $settingfield['default'];
    }
    
    return $settings;
  }
  
  /**
  * {@inheritdoc}
  */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    
    $settingfields = LocationFieldHelper::getLocationWidgetSettingFields();
    
    foreach($settingfields as $fieldname => $settingfield){
      
      $element[$fieldname] = array(
		'#type' => 'checkbox',
		'#title' => $settingfield['title'],
		'#default_value' => $this->getSetting($fieldname),
      );
      
    }
    
    return $element;
  }
  
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    
    $locationfields = LocationFieldHelper::getLocationFields(FALSE);
    
    $config = \Drupal::config('e2e_location_field.settings');
    
    $settings = array();
    
    $settingsfields = LocationFieldHelper::getLocationSettingFields();
    
    foreach(array_keys($settingsfields) as $fieldname){
      $settings[$fieldname] = $config->get('e2e_location_field.' . $fieldname);
    }
    
    foreach($locationfields as $fieldname => $locationfield){
      
      if(isset($items[$delta]->$fieldname)){
        
        $defaultvalue = $items[$delta]->$fieldname;
        
        if(isset($settings[$fieldname]))
          $settings[$fieldname] = $defaultvalue;
        
      }elseif(isset($settings[$fieldname]))
        $defaultvalue = $settings[$fieldname];
      else
        $defaultvalue = NULL;
      
      
      if(!isset($locationfield['settingfield']) || $this->getSetting($locationfield['settingfield'])){
        
        $element[$fieldname] = array(
          '#title' => $locationfield['title'],
          '#type' => 'textfield',
          '#size' => $locationfield['size'],
          '#default_value' => $defaultvalue,
          '#empty_value' => '',
        );
        
        if(isset($locationfield['dataname']))
          $element[$fieldname]['#attributes'] = array('data-name' => array($locationfield['dataname']));
      }
      
    }
    
    if($this->getSetting('show_map')) {
      
      if($this->getSetting('show_markers')) {
          
        // markers
        $marker_options = array(
          'marker-red' => 'Red', 'marker-blue' => 'Blue', 'marker-grey' => 'Grey',
          'marker-green' => 'Green', 'marker-iblue' => 'Light blue', 
          'marker-orange' => 'Orange', 'marker-pink' => 'Pink', 'marker-purple' => 'Purple', 
          'marker-white' => 'White', 'marker-yellow' => 'Yellow',
        );
        
        
        $element['coordupdate'] = array(
          '#type' => 'inline_template',
          '#template' => '{{ coordmarkup | raw}}',
          '#context' => array('coordmarkup' =>
            '<span class="field button map-update">' . t('Update coördinates and map') . '</span>'
          )
        );
        

        $element['marker'] = array(
          '#type' => 'radios',
          '#title' => 'Marker',
          '#options' => $marker_options,
          '#prefix' => '<div class="field-name-field-marker">',
          '#suffix' => '</div>',
          '#default_value' => isset($items[$delta]->marker) ? $items[$delta]->marker : 'marker-red',
        );
      }
      
      $element['map'] = array(
        '#type' => 'inline_template',
        '#template' => '{{ mapmarkup | raw}}',
        '#context' => array('mapmarkup' =>
          '<div class="label">' . t('Drag the marker to set a location') . '</div><div class="map" style="width:400px;height:320px;"></div>'
         )
      );
      
      $settings['icon'] = isset($items[$delta]->marker) ? base_path() . 'sites/all/markers/' . $items[$delta]->marker . '.png' : base_path() . 'sites/all/markers/marker-red.png';
        
      $element['#attached']['library'][] = 'e2e_location_field/location_field_widget';
      $element['#attached']['drupalSettings']['e2e_location_field_widget'] = $settings;
        
    }
    
    return $element;
  }
}