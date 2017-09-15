<?php

namespace Drupal\e2e_subsites\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubsiteForm extends EntityForm{
  
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
    
    $selectfields = [
      'theme' => 'Theme', 'section' => 'Section', 'role' => 'Role', 'weight' => 'Weight',
    ];
    
    foreach($selectfields as $fieldname => $title){
      
      $method = 'get' . ucfirst($fieldname) . 'Options';
      
      $form[$fieldname] = [
        '#title' => $this->t($title),
        '#type' => 'select',
        '#options' => call_user_func([$this, $method]),
        '#default_value' => $entity->$fieldname,
        '#multiple' => FALSE,
      ];
    }
    
    $form['paths'] = [
      '#title' => $this->t('Paths'),
      '#type' => 'textarea',
      '#default_value' => $entity->paths,
      '#description' => $this->t('Use comma seperation for multiple paths.'),
      '#wysiwyg' => FALSE,
    ];
    
    $form['nodetypepaths'] = [
      '#title' => $this->t('Node type paths'),
      '#type' => 'textarea',
      '#default_value' => $entity->nodetypepaths,
      '#description' => $this->t('Use nodetype|language|path. Use new line for each alias.'),
      '#wysiwyg' => FALSE,
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
      $message = 'Saved subsite %label';
      $type = 'status';
    }else{
      $message = 'Subsite %label was not saved';
      $type = 'error';
    }
    
    drupal_set_message($this->t($message, ['%label' => $entity->label()]), $type);
    
    $form_state->setRedirect('entity.subsite.list');
  }
  
  
  //get array of user roles
  private function getRoleOptions(){
    
    $options = ['' => ' --- '];
    
    $roles = user_roles(TRUE);
    
    foreach($roles as $alias => $role){
      if(!in_array($alias, ['anonymous', 'authenticated']))
          $options[$alias] = $role->label(); 
    }
    
    return $options;
  }
  
  //get array of sections
  private function getSectionOptions(){
    
    $sections = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('section');
    $options = ['' => ' --- '];
    
    foreach($sections as $section){
      $options[$section->tid] = $section->name;
    }
    
    return $options;
  }
  
  //get array of available themes
  private function getThemeOptions(){
    
    $themes = array_keys(\Drupal::service('theme_handler')->listInfo());
    
    $options = ['' => ' --- '] + array_combine($themes, $themes);
    
    return $options;
  }
  
  //get array of available weights
  private function getWeightOptions(){
    $weights = range(-50, 50);
    
    return array_combine($weights, $weights);
  }
  
  //check if configuration entity already exists
  public function exists($id){
    $entity = $this->entityQuery->get('subsite')
        ->condition('id', $id)
        ->execute();
    
    return (bool)$entity;
  }
  
}