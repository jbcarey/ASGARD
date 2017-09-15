<?php

namespace Drupal\e2e_paragraphs_media\Includes;

class MediaParagraph {
  
  private $entity;
  private $formats;
  private $mediaId;
  private $provider;
  
  private $allowedproviders = array('youtube', 'vimeo');
  
  //constructor
  public function  __construct($entity){
    $this->entity =  $entity;
  }
  
  //get media width and height
  private function getFormats(){
    
    if(!$this->formats)
      $this->setFormats();
    
    return $this->formats;
    
  }
  
  //get markup
  public function getMedia(){
    
    $media = array();
    
    $formats = $this->getFormats();
    $provider = $this->getProvider();
    
    if($provider){
      $media['#mediaid'] = $this->getMediaId();
      $media['#width'] = $formats['width'];
      $media['#height'] = $formats['height'];
      $media['#theme'] = 'paragraph_media_' . $provider;
    }
      
    return $media;
  }
  
  //get media ID
  private function getMediaId(){
    
    if(!$this->mediaId)
      $this->setMediaId();
    
    return $this->mediaId;
  }
  
  //get media provider
  private function getProvider(){
    
    if(!$this->provider)
      $this->setProvider();
    
    return $this->provider;
  }
  
  
  //set media with and height
  private function setFormats(){
    
    $formats = array(FALSE, FALSE);
    
    $entity = $this->entity;
    $format = $entity->field_media_format->value;
    
    if(isset($format) && $format && $format != 'custom')
      $formats = explode('_', $format, 2);

    if(count($formats) < 2){
      
      $provider = $this->getProvider();
      $width = $entity->field_width->value;
      $height = $entity->field_height->value;
      
      if($width && $height)
        $formats = array($width, $height);
      elseif($provider == 'youtube')
        $formats = array(560, 315);
      elseif($provider == 'vimeo')
        $formats = array(500, 281);
    }

    $this->formats = array_combine(array('width', 'height'), $formats);
  }
  
  //set media ID based on media ID or url
  private function setMediaId(){
    
    $mediaId = $this->entity->field_media_id->value;
    
    $urlparts = parse_url($mediaId);
    
    //media ID value is an URL
    if($urlparts !== FALSE){
      
      //query paramter found, media ID in v or vi parameter
      if(isset($urlparts['query'])){
        
        parse_str($urlparts['query'], $query);

        if(isset($query['v']))
          $mediaId = $query['v'];
        else if(isset($query['vi']))
          $mediaId = $query['vi'];
        
      //mediaID is parth of path
      }elseif(isset($urlparts['path'])){
        $pathparts = explode('/', trim($urlparts['path'], '/'));
        $mediaId = end($pathparts);
      }
    }
    
    $this->mediaId = $mediaId . '?color=white&disablekb=1&iv_load_policy=3&rel=0&showinfo=0&theme=light';
    
  }
  
  //set media provider
  private function setProvider(){
    
    $provider = $this->entity->field_media_provider->value;
    
    if(in_array($provider, $this->allowedproviders))
      $this->provider = $provider;
  }
  
}