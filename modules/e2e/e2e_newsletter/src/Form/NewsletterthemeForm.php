<?php

namespace Drupal\e2e_newsletter\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsletterthemeForm extends EntityForm{
  
  //constructor
  public function __construct(QueryFactory $entity_query) {
    $this->entityQuery = $entity_query;
  }

  /*
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.query'));
  }
  
  /*
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state){
    
    $form = parent::form($form, $form_state);
    
    $entity = $this->entity;
    
    $form['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];
    
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => ['exists' => [$this, 'exists']],
      '#disabled' => !$entity->isNew(),
    ];
    
    $form['theme'] = [
      '#title' => $this->t('Theme'),
      '#type' => 'select',
      '#options' => $this->getThemeOptions(),
      '#default_value' => $entity->theme,
      '#multiple' => FALSE,
    ];
    
    return $form;
    
  }
  
  /*
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state){
    
    $entity = $this->entity;
    $status = $entity->save();
    
    if($status){
      $message = 'Saved newsletter theme %label';
      $type = 'status';
    }else{
      $message = 'Newsletter theme %label was not saved';
      $type = 'error';
    }
    
    drupal_set_message($this->t($message, ['%label' => $entity->label()]), $type);
    
    $form_state->setRedirect('entity.newslettertheme.list');
  }
  
 
  //get array of available themes
  private function getThemeOptions(){
    
    $themes = array_keys(\Drupal::service('theme_handler')->listInfo());
    
    $options = ['' => ' --- '] + array_combine($themes, $themes);
    
    return $options;
  }
  
  //check if configuration entity already exists
  public function exists($id){
    $entity = $this->entityQuery->get('newslettertheme')
        ->condition('id', $id)
        ->execute();
    
    return (bool)$entity;
  }
  
}