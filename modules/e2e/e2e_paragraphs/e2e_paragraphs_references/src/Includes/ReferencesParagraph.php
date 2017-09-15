<?php

namespace Drupal\e2e_paragraphs_references\Includes;

use Drupal\node\Entity\Node;

class ReferencesParagraph {
  
  private $display;
  private $entity;
 
  //constructor
  public function  __construct($entity){
    $this->entity =  $entity;
  }
  
  //get display mode
  public function getDisplay(){
    
    if(!$this->display)
      $this->setDisplay();
    
    return $this->display;
  }
  
  //get referenced node markup
  public function getMarkup(){
    
    $markup = '';
    $entity = $this->entity;
    $display = $this->getDisplay();
    $parentid = $entity->parent_id->value;
    $showtitle = $entity->field_show_title->getValue();
    
    $nids = array_column($entity->field_content_reference->getValue(), 'target_id');
    
    if($parentid && ($key = array_search($entity->parent_id->value, $nids)) !== FALSE)
      unset($nids[$key]);
    
    if($nids){
      $nodes = Node::loadMultiple($nids);
      
      if($display == 'full'){
        foreach($nodes as &$node){
          $node->field_block_tags->setValue([]);
          $node->show_title->setValue($showtitle);
        }
      }
      $markup = node_view_multiple($nodes, $display);
    }
    
    return $markup;
  }
  
  //set display mode
  private function setDisplay(){
    
    $this->display = ($this->entity->field_display->value)?$this->entity->field_display->value:'teaser';
  }
}