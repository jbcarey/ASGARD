<?php

namespace Drupal\e2e_block\Includes;

class BlockMarkup {
  
  private $blockpositions;
  private $blocks;
  private $entity;
  private $positions = ['left' => 'left', 'right' => 'right', 'top' => 'innertop', 'bottom' => 'innerbottom'];
  
  //constructor
  public function __construct($entity){
    $this->entity = $entity;
  }
  
  //get block nodes
  private function getBlocks(){
    if(!$this->blocks)
      $this->setBlocks();
    
    return $this->blocks;
  }
  
  public function getMarkup(){
    
    $markup = [];
    $blocks = $this->getBlocks();
    
    foreach($blocks as $block){
      $this->setBlockPosition($block);
    }
    
    foreach($this->blockpositions as $blockposition => $nodes){
      $markup[$this->positions[$blockposition]] = [node_view_multiple($nodes, 'full')]; 
    }
    
    return $markup;
  }
  
  //store block node in position
  private function setBlockPosition($node){
    
    $position = $node->field_position->value;
    
    if($position && isset($this->positions[$position])){
      if(!isset($this->blockpositions[$position]))
        $this->blockpositions[$position] = [];
      
      $this->blockpositions[$position][] = $node;
      
    }
  }
  
  //store block nodes based on entity block tags
  private function setBlocks(){
    
    $tids = array_column($this->entity->field_block_tags->getValue(), 'target_id');
    
    if($tids){
    
      $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

      $query = \Drupal::entityQuery('node');

      $query
          ->condition('type', 'block', '=')
          ->condition('status', 1)
          ->condition('langcode', $langcode)
          ->condition('field_block_tags.target_id', $tids, 'IN')
          ->sort('field_weight', 'ASC');

      $nids = $query->execute();

      if($nids)
        $this->blocks =  \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    }
    
  }
  
}

