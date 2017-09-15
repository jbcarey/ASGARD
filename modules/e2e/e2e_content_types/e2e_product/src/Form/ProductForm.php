<?php

namespace Drupal\e2e_product\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ProductForm extends ConfigFormBase{

  /*
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'e2e_product_form';
  }
  
  /*
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = $this->config('e2e_product.settings');
    $form = parent::buildForm($form, $form_state);
    
    $form['baseurl'] = [
      '#type' => 'textfield',
      '#title' => 'Base URL',
      '#default_value' => $config->get('e2e_product.baseurl'),
      '#description' => 'eg: http://test-cache-productencatalogus.vlaanderen.be/',
    ];
    
    $form['uuid'] = [
      '#type' => 'textfield',
      '#title' => t('UUID'),
      '#default_value' => $config->get('e2e_product.uuid'),
      '#description' => 'eg: 2ad7b2a5-da41-9804-71f7-f503bde35e7c',
    ];
    
    $form['section'] = [
      '#title' => 'Section',
      '#type' => 'select',
      '#options' => $this->getSectionOptions(),
      '#default_value' => $config->get('e2e_product.section'),
      '#multiple' => FALSE,
      '#description' => 'Imported product section',
    ];

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
    
    $config = $this->config('e2e_product.settings');
    
    $config->set('e2e_product.baseurl', $form_state->getValue('baseurl'));
    $config->set('e2e_product.uuid', $form_state->getValue('uuid'));
    $config->set('e2e_product.section', $form_state->getValue('section'));
    
    $config->save();
    
    parent::submitForm($form, $form_state);
  }
  
  /*
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['e2e_product.settings'];
  }
  
  //get sections
  private function getSectionOptions(){
    
    $sections = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('section');
    $options = [];
    
    foreach($sections as $section){
      $options[$section->tid] = $section->name;
    }
    
    return $options;
  }
  
}
