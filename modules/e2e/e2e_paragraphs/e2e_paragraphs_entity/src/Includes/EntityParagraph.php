<?php

namespace Drupal\e2e_paragraphs_entity\Includes;

use Drupal\Core\Url;
use Drupal\Core\Link;

class EntityParagraph {
  
  private $entity;
  private $hoursfield;
  private $hoursnode;
  private $node;
  private $options;
  
  //constructor
  public function  __construct($entity){
    $this->entity =  $entity;
  }
  
  //get markup
  public function getMarkup(){
    
    $fields = [];
    
    $node = $this->getNode();
    $options = $this->getOptions();
    
    if($node && $options){
      foreach($this->getOptions() as $option){
        
        //dpm($option);
        
        if($option == 'show_hours' || $option == 'show_today')
          $fields['hours'] = $this->getHoursField();
        elseif($option == 'show_readmore'){
          $link = Link::fromTextAndUrl(t('More info'), Url::fromUri('internal:/node/' . $node->id()));
          $fields['readmore'] = ['#markup' => '<li>' . $link->toString() . '</li>', '#weight' => 100];
        }elseif($option == 'field_location_reference')
          $fields['location'] = $this->getLocation();
        else
          $fields[$option] = $node->$option->view('full');
      }
    }
    
    return $fields;
    
  }
  
  //get field displaying hours
  private function getHoursField(){
    
    if(!$this->hoursfield)
      $this->setHoursField();
    
    return $this->hoursfield;
  }
  
  
  //get node containing hours data
  private function getHoursNode(){
    
    if(!$this->hoursnode)
      $this->setHoursNode();
    
    return $this->hoursnode;
  }
  
  //get entity node
  private function getNode(){
    if(!$this->node)
      $this->setNode();
    
    return $this->node;
  }
  
  //get display options
  private function getOptions(){
    if(!$this->options)
      $this->setOptions();
    
    return $this->options;
  }
  
  //get location reference field
  private function getLocation(){
    
    $location = [];
    $node = $this->getNode();
    $locationreference = $node->field_location_reference->entity;
    
    if($locationreference){
      $location = $locationreference->field_location->view(array('type' => 'LocationAddressFormatter', 'label' => 'hidden'));
      $location[0]['#prefix'] = '<i class="icon icon-home icon-smaller"></i>';
    }
    
    return $location;
    	
  }
  
  //set values for hoursfield
  private function setHoursField(){
    
    $hoursnode = $this->getHoursNode();
    
    $hoursfield = $hoursnode->field_hours_paragraph->view(array('type' => 'full', 'label' => 'hidden')); 
    
    if(in_array('show_today', $this->getOptions())){
      $hoursfield['#prefix'] = '<div class="field today-only">';
      $hoursfield['#suffix'] = '</div>';
    }
    
    $this->hoursfield = $hoursfield;
  }
  
  //set node containing hours data
  private function setHoursNode(){
    
    $node = $this->getNode();
    $hoursnode = NULL;
    
    if(!$node->field_hours_paragraph->getValue() && $node->field_hoursdays_reference->entity)
      $hoursnode = $node->field_hoursdays_reference->entity;
        
    if(!$hoursnode)
      $hoursnode = $node;
    
    $this->hoursnode = $hoursnode;
  }
  
  //set entity node
  private function setNode(){
    $this->node = $this->entity->field_entity_reference->entity;
  }
  
  //set display options
  private function setOptions(){
    
    if($this->entity->field_entity_options->getValue())
      $this->options = array_column($this->entity->field_entity_options->getValue(), 'value');
    
  }
}

