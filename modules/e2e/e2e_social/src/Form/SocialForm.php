<?php

namespace Drupal\e2e_social\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class SocialForm extends ConfigFormBase{

  private static $fields = [
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
    'linkedin' => 'Linkedin',
    'googleplus' => 'Google+',
    'instagram' => 'Instagram',
    'pinterest' => 'Pinterest',
  ];
  
  /*
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'e2e_social_form';
  }
  
  /*
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = $this->config('e2e_social.settings');
    $form = parent::buildForm($form, $form_state);
    
    foreach(self::$fields as $fieldname => $fieldtitle){
      
      $form[$fieldname] = [
        '#type' => 'checkbox',
        '#title' => $this->t($fieldtitle),
        '#default_value' => $config->get('e2e_social.' . $fieldname),
      ];
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
    
    $config = $this->config('e2e_social.settings');
    
    foreach(array_keys(self::$fields) as $fieldname){
      $config->set('e2e_social.' . $fieldname, $form_state->getValue($fieldname));
    }
    
    $config->save();
    
    parent::submitForm($form, $form_state);
  }
  
  /*
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['e2e_social.settings'];
  }
  
}
