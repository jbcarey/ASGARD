<?php

namespace Drupal\e2e_location_field\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\e2e_location_field\Includes\LocationFieldHelper;

class LocationFieldForm extends ConfigFormBase{

  /*
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'e2e_location_field_form';
  }
  
  /*
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $form = parent::buildForm($form, $form_state);
    
    $config = $this->config('e2e_location_field.settings');
    $settingsfields = LocationFieldHelper::getLocationSettingFields();
    
    foreach($settingsfields as $fieldname => $settingfield){
      $form[$fieldname] = array(
        '#type' => 'textfield',
        '#title' => $settingfield,
        '#default_value' => $config->get('e2e_location_field.' . $fieldname),
      );
    }

    return $form;
  }
  
  /*
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
  
  /*
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $config = $this->config('e2e_location_field.settings');
    $settingsfields = LocationFieldHelper::getLocationSettingFields();
    
    foreach(array_keys($settingsfields) as $fieldname){
      $config->set('e2e_location_field.' . $fieldname, $form_state->getValue($fieldname));
    }
    
    $config->save();
    
    parent::submitForm($form, $form_state);
  }
  
  /*
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['e2e_location_field.settings'];
  }
  
}
