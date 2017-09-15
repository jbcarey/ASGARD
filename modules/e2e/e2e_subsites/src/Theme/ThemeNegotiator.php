<?php

namespace Drupal\e2e_subsites\Theme;

use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\Routing\RouteMatchInterface;

use Drupal\e2e_subsites\Includes\SubsiteHelper;

class ThemeNegotiator implements ThemeNegotiatorInterface{
  
  /*
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match){
    return (bool)$this->getSubsite();
  }
  
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    
    $subsite = $this->getSubsite();
    
    return $subsite->theme;
  }
  
  private function getSubsite(){
    
    $uri = trim(\Drupal::request()->getRequestUri(), '/');
    $parts = explode('/',$uri);
    
    if(!empty($parts[0])){
      
      $subsites = SubsiteHelper::getSubsites([['field' => 'paths', 'value' => $parts[0], 'operator' => 'CONTAINS']]);
      
      if($subsites){
        
        foreach($subsites as $subsite){
          if($this->hasPath($subsite->paths, $parts[0]))
            return $subsite;
        }
      }
    }
    
    return FALSE;
  }

  //check if the current path applies to subsite entity paths
  private function hasPath($subsitepaths, $uripath){
    
    $paths = explode(',', $subsitepaths);
                
    foreach($paths as &$path){
      $path = trim($path);
    }
                
    if(in_array($uripath, $paths))
      return TRUE;
  }
  
}

