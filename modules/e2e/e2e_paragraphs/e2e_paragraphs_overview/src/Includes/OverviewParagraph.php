<?php

namespace Drupal\e2e_paragraphs_overview\Includes;

use Drupal\Component\Utility\Html;
use Drupal\e2e_paragraphs_overview\Includes\OverviewMarkup;
use Drupal\e2e_paragraphs_overview\Includes\OverviewQuery;

class OverviewParagraph{
  
  private $entity;
  private $nodes;
  private $settings;
  
  
  public function __construct($entity){
    $this->entity = $entity;
  }
  
  public function getFilters(){
    
    $filters = array();
    
    $settings = $this->getSettings();
    
    if($settings['filters']){
      $filters['form'] = \Drupal::formBuilder()->getForm('\Drupal\e2e_paragraphs_overview\Form\ParagraphsOverviewForm', $settings);
      $filters['position'] = ($settings['filters_position']) ? $settings['filters_position']: 'top';
    }
    
    return $filters;
  }
  
  //get markup for the overview
  public function getMarkup(){
    
    $markup = '<p>' . t('No results found') . '</p>';
    
    $settings = $this->getSettings();
    $nodes = $this->getNodes();
    
    if($nodes){
      
      if($settings['display'] == 'glossary'){ 
        $markup = OverviewMarkup::getGlossary($nodes, $settings['columns'], $settings['letter']);
      }else{
        
        if($settings['display'] == 'links'){
          $view = OverviewMarkup::getLinks($nodes);
          $direction = 'vertical';
        }else{
          $view = node_view_multiple($nodes, $settings['display']);
          $direction = 'horizontal';
        }
        
        if($settings['columns']){

          if($settings['display'] == 'thumbnail')
            $class = 'gutter-small';
          else
            $class = 'gutter';

          $markup = OverviewMarkup::getColumns($view, $settings['columns'], $class, $direction);
        }else{
          $markup = render($view);
        }
      }
    }
    
    return array(
      '#prefix' => '<div class="overview view-mode-' . Html::getClass($settings['display']) . '">',
      '#suffix' => '</div>',
      'content' => array('#markup' => $markup),
    );
    
  }
  
  //get nodes for overview
  private function getNodes(){
    if(!$this->nodes)
      $this->setNodes();
      
    return $this->nodes;
  }
  
  //get settings for overview
  private function getSettings(){
    if(!$this->settings)
      $this->setSettings();
      
    return $this->settings;
  }
  
  //don't cache overview when filters available or in glossaries
  public function isCached(){
    
    $settings = $this->getSettings();
    
    if($settings['display'] == 'glossary' || $settings['filters'])
      return FALSE;
    
    return TRUE;
    
  }
  
  //set nodes for overview
  private function setNodes(){
    
    $nodes = array();
    $settings = $this->getSettings();
    
    $query = new OverviewQuery();
    $query->baseConditions($settings['content_types'], $settings['nid'], $settings['langcode']);
    
    if($settings['popular'])
      $query->popularCondition($settings['popular']);
    
    if($settings['keywords'])
      $query->keywordConditions($settings['keywords']);
    
    if($settings['tags'])
      $query->tagConditions($settings['tags']);
    
    if($settings['sections'])
      $query->sectionConditions($settings['sections']);
    
    if($settings['taxonomy'])
      $query->taxonomyConditions($settings['taxonomy']);
    
    if($settings['items_per_page'])
      $query->range($settings['items_per_page']);
    
    if(count($settings['content_types']) == 1){ 
      if(in_array('event',$settings['content_types'])){
        $query->eventConditions($settings['date_from'], $settings['date_to']);

        if($settings['order'] == 'created')
          $settings['order'] = 'field_event_date.value';
        
      }elseif(in_array('entity',$settings['content_types']) && $settings['entitytype']){
        $query->entityConditions($settings['entitytype']);
      }
    }
    
    if($settings['display'] == 'glossary'){
      $settings['order'] = 'title';
      $settings['sort'] = 'ASC';
    }
    
    if($settings['order'])
      $query->sort($settings['order'], $settings['sort']);
    
    $nids = $query->getNids();
    
    if(!empty($nids))
      $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    
    $this->nodes = $nodes;
  }
  
  //set settings for overview
  private function setSettings(){
    
    $entity = $this->entity;
    
    $parameters = \Drupal::request()->query->all();
    
    $this->settings = array(
            'nid' => $entity->parent_id->value,
            'langcode' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
            'content_types' => array_column($entity->field_content_type->getValue(), 'contenttypes'),
            'tags' => array_column($entity->field_tags->getValue(), 'target_id'),
            'display' => isset($parameters['display']) ? $parameters['display'] : $entity->field_display->value,
            'columns' => $entity->field_columns_amount->value,
            'pager' => $entity->field_pagination->value,
            'items_per_page' => $entity->field_items_per_page->value,
            'order' => isset($parameters['order']) ? $parameters['order'] : $entity->field_order->value,
            'sort' => isset($parameters['sort']) ? $parameters['sort'] : $entity->field_sortorder->value,
            'id' => $entity->id->value,
            'filters' => array_column($entity->field_filters->getValue(), 'value'),
            'filters_position' => $entity->field_filters_position->value,
            'keywords' => isset($parameters['keywords']) ? $parameters['keywords'] : FALSE,
            'taxonomy' => isset($parameters['taxonomy']) ? $parameters['taxonomy'] : FALSE,
            'letter' => isset($parameters['letter']) ? $parameters['letter'] : 'all',
            'date_from' => isset($parameters['date_from']) ? $parameters['date_from'] : FALSE,
            'date_to' => isset($parameters['date_to']) ? $parameters['date_to'] : FALSE,
            'sections' => array_column($entity->field_sections->getValue(), 'target_id'),
            'popular' => $entity->field_popular->value,
            'entitytype' => $entity->field_entity_type->value,
    );
  }
}