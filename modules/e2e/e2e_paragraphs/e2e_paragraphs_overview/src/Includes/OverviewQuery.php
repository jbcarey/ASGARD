<?php

namespace Drupal\e2e_paragraphs_overview\Includes;

class OverviewQuery{
  
  private $query;
  private $nids;
  
  public function __construct(){
    $this->query = \Drupal::entityQuery('node');
  }
  
  //base conditions for every overview query
  public function baseConditions($content_types, $nid, $langcode){
    
    $this->query
        ->condition('type', $content_types, 'IN')
        ->condition('status', 1)
        ->condition('nid', $nid, '!=')
        ->condition('langcode', $langcode);
  }
  
  //keywords conditions
  public function keywordConditions($keywords){
    
    $group = $this->query->orConditionGroup();
    $group->condition('title', $keywords, 'CONTAINS')
      ->condition('body.value', $keywords, 'CONTAINS')
      ->condition('field_keywords.value', $keywords, 'CONTAINS');
    
    $this->query->condition($group);
  }
  
  //popularconditions
  public function popularCondition($popular){
    $this->query->condition('popular.value', $popular);
  }
  
  //taxonomy conditions
  public function taxonomyConditions($taxonomy){
    $this->query->addTag('overview_taxonomy');
	$this->query->addMetaData('taxonomy', $taxonomy);
  }
  
  //tag conditions
  public function tagConditions($tags){
    $this->query->condition('field_tags.target_id', $tags);
  }
  
  //section conditions
  public function sectionConditions($sections){
    $this->query->condition('field_section.target_id', $sections);
  }
  
  //event conditions 
  public function eventConditions($date_from, $date_to){
    
    if(!$date_from)
      $date_from = \Drupal::service('date.formatter')->format(time(),'custom','Y-m-d');
    
    if($date_to){
      $this->query->condition('field_event_date.value', $date_from, '>=');
      $this->query->condition('field_event_date.end_value', $date_to, '<=');
    }else{
      $group = $this->query->orConditionGroup();
      $group->condition('field_event_date.value', $date_from, '>=')
            ->condition('field_event_date.end_value', $date_from, '>=')
            ->notExists('field_event_date');
      
      $this->query->condition($group);
    }
  }
  
  //entity conditions
  public function entityConditions($entitytype){
    $this->query->condition('field_entity_type.value', $entitytype);
  }
  
  //range
  public function range($items_per_page){
    if($items_per_page)
      $this->query->pager($items_per_page);
    else
      $this->query->range(0, $items_per_page);
  }
  
  public function sort($order, $sort){
    $this->query->sort($order, $sort);
  }
  
  //get array of node ids
  public function getNids(){
    if(!$this->nids)
      $this->setNids();
    
    return $this->nids;
  }
  
  //set node ids based on query
  private function setNids(){
    $this->nids = $this->query->execute();
  }

}

