<?php

namespace Drupal\e2e_paragraphs_map_overview\Includes;

use Drupal\Core\Url;
use Drupal\Core\Link;

class MapOverviewParagraph {
  
  private $entity;
  private $locations;
  private $markers;
  private $settings;
  private $tids;
  
  //constructor
  public function  __construct($entity){
    $this->entity =  $entity;
  }
  
  //get the map overview filters
  public function getFilters(){
    
    $filters = [];
    
    if($this->getSetting('filters'))
      $filters = \Drupal::formBuilder()->getForm('\Drupal\e2e_paragraphs_map_overview\Form\ParagraphsMapOverviewForm', array('tids' => $this->tids));
    
    return $filters;
    
  }
  
  //get drupal javascript settings
  public function getDrupalSettings(){
    
    $settings = $this->getSettings();
    
    $config = \Drupal::config('e2e_location_field.settings');
    
    $drupalSettings = [
      'markers' => $this->getMarkers(),
      'lat' => floatval($config->get('e2e_location_field.lat')),
      'lng' => floatval($config->get('e2e_location_field.lng')),
      'zoom' => 15,
      'show_filters' => $settings['filters'],
      'show_search' => $settings['search'],
      'show_places' => $settings['places'],
    ];
    
    return $drupalSettings;
  }
  
  //get array of location nodes
  private function getLocations(){
    
    if(!$this->locations)
      $this->setLocations();
      
    return $this->locations;
  }
  
  //get all map markers
  private function getMarkers(){
    
    if(!$this->markers)
      $this->setMarkers();
    
    return $this->markers;
    
  }
  
  //get marker info popup
  private function getPopup($node){
    
    $location = $node->field_location;
    
    $link = Link::fromTextAndUrl($node->title->value, Url::fromUri('internal:/node/' . $node->id()));
    
    $image = $node->field_image->view(['label' => 'hidden', ['image_style' => 'small']]);
	$title = '<h4 class="node-title">' . $link->toString() . '</h4>';
	$address = $location->street . ' ' . $location->street_nr . '<br/>' . $location->zip_code . ' ' . $location->city . '<br/>';

    return '<div class="marker-popup grid"><div class="col">' . render($image) . '</div><div class="col">' . $title . $address . '</div></div>';

  }
  
  //get specific map overview setting
  public function getSetting($key){
    
    $settings= $this->getSettings();
    
    if(isset($settings[$key]))
      return $settings[$key];
    
    return FALSE;
  }
  
  
  //get map overview settings
  private function getSettings(){
        
    if(!$this->settings)
      $this->setSettings();
    
    return $this->settings;
  }
  
  //set all map markers
  private function setMarkers(){
    
    $markers = [];
    $nodes = $this->getLocations();
    $tidsAll = [];
    
    if($nodes){
      
      
      
      foreach($nodes as $nid => $node){
        
        $location = $node->field_location;
        
        $markers[$nid] = [
          'title' => $node->title->value,
          'info' => $this->getPopup($node),
          'lat' => floatval($location->lat),
          'lng' => floatval($location->lng),
          'icon' => !empty($location->marker) ? base_path() . 'sites/all/markers/' . $location->marker . '.png' : base_path() . 'sites/all/markers/marker-red.png',
		];
        
        if($tids = array_column($node->field_location_type->getValue(), 'target_id')){
          $tidsAll = array_merge($tidsAll, $tids);
          $markers[$nid]['type'] = preg_filter('/^/', 'type', $tids);
        }
        
      }
    }
    
    $this->tids = $tidsAll;
    $this->markers = $markers;
  }
  
  //set locations for the map overview
  private function setLocations(){
    
    $settings = $this->getSettings();
    
    $query = \Drupal::entityQuery('node');
    
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    
    $query->condition('type', ['location'], 'IN')
          ->condition('status', 1)
          ->condition('langcode', $langcode);
    
    if($settings['tags'])
      $query->condition('field_tags.target_id', $settings['tags']);
    
	if($settings['locationtypes'])
      $query->condition('field_location_type.target_id', $settings['locationtypes']);
    
    if($settings['popular'])
      $query->condition('popular.value', $settings['popular']);

	$nids = $query->execute();
    
    if(!empty($nids))
      $this->locations = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
    
  }
  
  //set map overview settings
  private function setSettings(){
   
    $entity = $this->entity;
    
    $this->settings = [
      'tags' => array_column($entity->field_tags->getValue(), 'target_id'),
      'locationtypes' => array_column($entity->field_location_type->getValue(), 'target_id'),
      'filters' => $entity->field_map_filters->value,
      'places' => $entity->field_map_places->value,
      'search' => $entity->field_map_search->value,
      'popular' => $entity->field_popular->value,
    ];
    
  }
  
}

